<?php

namespace App\Http\Controllers;

use App\Exports\ResultsExport;
use App\Models\Discipline;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestResult;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    public function create()
    {
        $disciplines = Discipline::all();

        return view('tests.create', compact('disciplines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'discipline_id' => 'required|exists:disciplines,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $test = Test::create([
            'title' => $request->title,
            'discipline_id' => $request->discipline_id,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'is_active' => true,
        ]);

        foreach ($request->questions as $index => $qData) {
            $question = $test->questions()->create([
                'question_text' => $qData['text'],
                'type' => $qData['type'],
                'points' => $qData['points'] ?? 1,
                'explanation' => $qData['explanation'] ?? null,
                'is_required' => isset($qData['is_required']),
                'shuffle_options' => isset($qData['shuffle_options']),
            ]);

            if (isset($qData['options'])) {
                foreach ($qData['options'] as $oIndex => $oData) {

                    $isCorrect = false;
                    if ($qData['type'] === 'single_choice') {
                        $isCorrect = ($request->input("questions.$index.correct") == $oIndex);
                    } else {
                        $isCorrect = isset($oData['is_correct']);
                    }

                    $question->options()->create([
                        'option_text' => $oData['text'] ?? '',
                        'is_correct' => $isCorrect,
                        'match_text' => $oData['match_text'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('dashboard');
    }

    public function show(Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }

        return view('tests.show', compact('test'));
    }

    public function resultSettings(Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }

        return view('tests.result_settings', compact('test'));
    }

    public function updateResultSettings(Request $request, Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }
        $test->update([
            'enable_grading' => $request->has('enable_grading'),
            'grading_type' => $request->grading_type,
            'grade_5_threshold' => $request->grade_5_threshold,
            'grade_4_threshold' => $request->grade_4_threshold,
            'grade_3_threshold' => $request->grade_3_threshold,
            'grade_label' => $request->grade_label,
            'show_result_to_user' => $request->has('show_result_to_user'),
        ]);

        return back()->with('success', 'Настройки сохранены');
    }

    public function destroy(Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }
        $test->delete();

        return redirect()->route('dashboard');
    }

    public function manualReviewIndex(Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }
        $results = $test->results()->with('student')->latest()->get();

        return view('tests.manual_index', compact('test', 'results'));
    }

    public function manualReviewShow(TestResult $result)
    {
        $test = $result->test;
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }
        $result->load(['test.questions.options', 'student']);

        return view('tests.manual_review', compact('result', 'test'));
    }

    public function updateSettings(Request $request, Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }
        $test->update($request->only(['is_active', 'type', 'tags']));

        return back();
    }

    public function settings(Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }

        return view('tests.settings', compact('test'));
    }

    public function updateGeneralSettings(Request $request, Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }


        $fields = [
            'show_numbers',
            'allow_comments',
            'allow_error_reports',
            'shuffle_questions',
            'shuffle_options',
            'require_all_answers',
            'show_progress_bar',
            'show_time',
            'limit_time',
            'prevent_copy',
            'prevent_back',
            'confirm_next',
            'confirm_finish',
            'show_correct_instantly',
            'show_dropdown',
        ];

        $data = [];
        foreach ($fields as $field) {

            $data[$field] = $request->has($field);
        }

        $test->update($data);

        return back()->with('success', 'Настройки успешно сохранены');
    }

    public function startPage(Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }

        return view('tests.start_page', compact('test'));
    }

    public function updateStartPage(Request $request, Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }

        $test->update([
            'description' => $request->description,
            'instruction' => $request->instruction,
            'author' => $request->author,
            'source' => $request->source,
        ]);

        return back()->with('success', 'Начальная страница успешно обновлена');
    }

    public function statistics(Request $request, Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }


        $test->load(['questions.options', 'results.student']);

        $resultsQuery = $test->results()->with('student');
        if ($request->filled('search')) {
            $resultsQuery->where('id', $request->search);
        }
        $results = $resultsQuery->latest()->get();


        foreach ($results as $res) {
            $correctCount = 0;
            foreach ($test->questions as $question) {
                $data = $res->answers[$question->id] ?? null;

                $score = is_array($data) && isset($data['score'])
                    ? (float) $data['score']
                    : $this->calculateScoreForQuestion($question, $data);

                if ($score >= $question->points && $question->points > 0) {
                    $correctCount++;
                }
            }
            $res->correct_answers_count = $correctCount;
        }


        $questionsAnalysis = [];
        $allResults = $test->results;
        $totalStudents = $allResults->count();

        foreach ($test->questions as $question) {
            $correct = 0;
            $partial = 0;
            $incorrect = 0;

            foreach ($allResults as $res) {
                $data = $res->answers[$question->id] ?? null;
                $score = is_array($data) && isset($data['score']) ? (float) $data['score'] : 0;

                if ($score >= $question->points && $question->points > 0) {
                    $correct++;
                } elseif ($score > 0) {
                    $partial++;
                } else {
                    $incorrect++;
                }
            }

            $questionsAnalysis[] = [
                'text' => $question->question_text,
                'max_points' => $question->points,

                'incorrect_pct' => $totalStudents > 0 ? round(($incorrect / $totalStudents) * 100) : 0,
                'partial_pct' => $totalStudents > 0 ? round(($partial / $totalStudents) * 100) : 0,
                'correct_pct' => $totalStudents > 0 ? round(($correct / $totalStudents) * 100) : 0,

                'incorrect_cnt' => $incorrect,
                'partial_cnt' => $partial,
                'correct_cnt' => $correct,
            ];
        }

        return view('tests.statistics', compact('test', 'results', 'questionsAnalysis'));
    }


    private function calculateScoreForQuestion($question, $ans)
    {
        if (is_null($ans) || $ans === '') {
            return 0;
        }


        if (in_array($question->type, ['single_choice', 'single'])) {
            $correctOption = $question->options->where('is_correct', true)->first();

            return ($correctOption && $ans == $correctOption->id) ? $question->points : 0;
        }


        if (in_array($question->type, ['multi_choice', 'multi'])) {
            $correctOptions = $question->options->where('is_correct', true);
            $correctCount = $correctOptions->count();
            if ($correctCount === 0 || ! is_array($ans)) {
                return 0;
            }

            $pointsPerOption = $question->points / $correctCount;
            $qScore = 0;
            foreach ($ans as $ansId) {
                if ($correctOptions->contains('id', $ansId)) {
                    $qScore += $pointsPerOption;
                } else {
                    $qScore -= $pointsPerOption;
                }
            }

            return max(0, $qScore);
        }


        if (in_array($question->type, ['text', 'number'])) {
            $correctOption = $question->options->where('is_correct', true)->first();
            if (! $correctOption) {
                return 0;
            }

            return (trim(mb_strtolower((string) $ans)) === trim(mb_strtolower((string) $correctOption->option_text)))
                ? $question->points : 0;
        }

        return 0;
    }

    public function updateManualScore(Request $request, TestResult $result)
    {
        if ($result->test->user_id !== Auth::id()) {
            abort(403);
        }

        $newScores = $request->input('q_scores', []);
        $currentAnswers = $result->answers;
        $totalScore = 0;

        foreach ($newScores as $qId => $score) {
            $floatScore = (float) $score;
            if (isset($currentAnswers[$qId])) {
                if (! is_array($currentAnswers[$qId])) {
                    $currentAnswers[$qId] = ['answer' => $currentAnswers[$qId], 'score' => $floatScore];
                } else {
                    $currentAnswers[$qId]['score'] = $floatScore;
                }
            }
            $totalScore += $floatScore;
        }


        $result->update([
            'score' => $totalScore,
            'answers' => $currentAnswers,
            'is_reviewed' => true,
        ]);

        return redirect()->route('tests.manual-index', $result->test_id)
            ->with('success', 'Результат проверен и сохранен');
    }

    public function storeDiscipline(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:disciplines,name',
        ]);

        Discipline::create([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Дисциплина успешно добавлена');
    }

    public function exportExcel(Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }

        $fileName = 'results_test_'.$test->id.'_'.now()->format('d_m_Y').'.xlsx';

        return Excel::download(new ResultsExport($test->id), $fileName);
    }

    public function edit(Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }


        $test->load('questions.options');
        $disciplines = Discipline::all();

        return view('tests.edit', compact('test', 'disciplines'));
    }

    public function update(Request $request, Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }


        $test->update([
            'title' => $request->title,
            'discipline_id' => $request->discipline_id,
            'description' => $request->description,
        ]);


        $test->questions()->delete();


        if ($request->has('questions')) {
            foreach ($request->questions as $index => $qData) {
                $question = $test->questions()->create([
                    'question_text' => $qData['text'],
                    'type' => $qData['type'],
                    'points' => $qData['points'] ?? 1,
                    'explanation' => $qData['explanation'] ?? null,
                    'is_required' => isset($qData['is_required']),
                    'shuffle_options' => isset($qData['shuffle_options']),
                ]);

                if (isset($qData['options'])) {
                    foreach ($qData['options'] as $oIndex => $oData) {
                        $isCorrect = false;
                        if ($qData['type'] === 'single_choice') {
                            $isCorrect = ($request->input("questions.$index.correct") == $oIndex);
                        } else {
                            $isCorrect = isset($oData['is_correct']);
                        }

                        $question->options()->create([
                            'option_text' => $oData['text'] ?? '',
                            'is_correct' => $isCorrect,
                            'match_text' => $oData['match_text'] ?? null,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('tests.show', $test->id)->with('success', 'Тест успешно обновлен');
    }

    public function exportPdf(TestResult $result)
    {

        if (Auth::user()->role !== 'teacher' && Auth::user()->id !== $result->user_id) {
            abort(403);
        }

        $result->load(['test.questions.options', 'student']);


        $data = [
            'result' => $result,
            'test' => $result->test,
            'student' => $result->student,
        ];


        $pdf = Pdf::loadView('tests.pdf_report', $data);


        $fileName = 'Result_'.$result->student->name.'_'.$result->test->id.'.pdf';

        return $pdf->download($fileName);
    }

    public function exportQuestionPdf(TestResult $result, Question $question)
    {
        if (Auth::user()->role !== 'teacher' && Auth::user()->id !== $result->user_id) {
            abort(403);
        }


        if ($question->test_id !== $result->test_id) {
            abort(404);
        }


        $answers = $result->answers;
        $questionData = $answers[$question->id] ?? null;

        $data = [
            'result' => $result,
            'student' => $result->student,
            'question' => $question,
            'answerData' => $questionData,
        ];

        $pdf = Pdf::loadView('tests.pdf_single_question', $data)
            ->setOptions(['defaultFont' => 'dejavu sans']);

        $fileName = 'Q_Result_'.$result->id.'_Question_'.$question->id.'.pdf';

        return $pdf->download($fileName);
    }
}
