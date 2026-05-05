<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestSessionController extends Controller
{

	public function show(Test $test)
	{
		if (!$test->is_active) {
			return 'Извините, этот тест временно закрыт.';
		}


		$questions = $test->questions()->with('options')->get();


		if ($test->shuffle_questions) {
			$questions = $questions->shuffle();
		}


		if ($test->shuffle_options) {
			foreach ($questions as $question) {

				$question->setRelation('options', $question->options->shuffle());
			}
		}

		return view('tests.take', compact('test', 'questions'));
	}

	public function submit(Request $request, Test $test)
	{
		$totalScore = 0;
		$maxPossiblePoints = $test->questions->sum('points');
		$submitted = $request->input('q', []);
		$userAnswersData = [];

		$test->load('questions.options');

		foreach ($test->questions as $question) {
			$ans = $submitted[$question->id] ?? null;
			$qScore = 0;

			// 1. Обработка файла
			if ($question->type === 'file' && $request->hasFile("q_file.{$question->id}")) {
				$file = $request->file("q_file.{$question->id}");
				$path = $file->store("student_files/test_{$test->id}", 'public');

				$ans = [
					'name' => $file->getClientOriginalName(),
					'path' => $path,
				];
				$qScore = 0; // Вручную проверяет учитель
			}

			// 2. Одиночный выбор (также работает для вопросов с изображениями)
			elseif (in_array($question->type, ['single_choice', 'single'])) {
				$correctOption = $question->options->where('is_correct', true)->first();
				if ($correctOption && $ans == $correctOption->id) {
					$qScore = $question->points;
				}
			}

			// 3. Множественный выбор
			elseif (in_array($question->type, ['multi_choice', 'multi'])) {
				$correctOptions = $question->options->where('is_correct', true);
				$correctCount = $correctOptions->count();
				if ($correctCount > 0 && is_array($ans)) {
					$pointsPerOpt = $question->points / $correctCount;
					foreach ($ans as $val) {
						if ($correctOptions->contains('id', $val)) {
							$qScore += $pointsPerOpt;
						} else {
							$qScore -= $pointsPerOpt;
						}
					}
				}
			}

			// 4. Соответствие
			elseif ($question->type === 'matching') {
				$correctPairs = 0;
				if (is_array($ans)) {
					foreach ($question->options as $opt) {
						if (isset($ans[$opt->id]) && $ans[$opt->id] === $opt->match_text) {
							$correctPairs++;
						}
					}
					$qScore = ($correctPairs / max(1, $question->options->count())) * $question->points;
				}
			}

			// 5. Последовательность
			elseif ($question->type === 'sequence') {
				$isSeqCorrect = true;
				if (is_array($ans)) {
					foreach ($question->options as $idx => $opt) {
						if (!isset($ans[$opt->id]) || $ans[$opt->id] != ($idx + 1)) {
							$isSeqCorrect = false;
						}
					}
					if ($isSeqCorrect) {
						$qScore = $question->points;
					}
				}
			}

			// 6. Текст / Число
			elseif (in_array($question->type, ['text', 'number'])) {
				$correctOption = $question->options->where('is_correct', true)->first();
				if ($correctOption && trim(mb_strtolower((string) $ans)) === trim(mb_strtolower($correctOption->option_text))) {
					$qScore = $question->points;
				}
			}

			// 7. ЗАПОЛНЕНИЕ ПРОПУСКОВ (Интегрировано в цикл)
			elseif ($question->type === 'fill_in_gaps') {
				$correctGaps = 0;
				$options = $question->options; // Правильные слова по порядку

				if (is_array($ans)) {
					foreach ($options as $oIdx => $opt) {
						$studentWord = trim(mb_strtolower($ans[$oIdx] ?? ''));
						$correctWord = trim(mb_strtolower($opt->option_text));

						if ($studentWord === $correctWord && $correctWord !== '') {
							$correctGaps++;
						}
					}
					$qScore = ($correctGaps / max(1, $options->count())) * $question->points;
				}
			}

			// Финализация балла за вопрос
			$qScore = max(0, (float) $qScore);
			$totalScore += $qScore;

			// Сохраняем в историю ответов
			$userAnswersData[$question->id] = [
				'answer' => $ans,
				'score' => round($qScore, 2),
			];
		}

		// Создаем запись результата в БД
		$result = TestResult::create([
			'user_id' => Auth::id(),
			'test_id' => $test->id,
			'score' => $totalScore,
			'total_points' => $maxPossiblePoints,
			'completed_at' => now(),
			'answers' => $userAnswersData,
			'student_comment' => $request->student_comment,
		]);

		return redirect()->route('test.result', $result->id);
	}
	public function showResult(TestResult $result)
	{

		if ($result->user_id !== Auth::id() && Auth::user()->role !== 'teacher') {
			abort(403);
		}


		$result->load(['test.questions.options']);

		return view('tests.result_page', compact('result'));
	}

	public function showDetailedResult(TestResult $result)
	{

		if (Auth::user()->role !== 'teacher' || $result->test->user_id !== Auth::id()) {
			abort(403);
		}

		$result->load(['test.questions.options', 'student']);

		return view('tests.student_detail', compact('result'));
	}
}
