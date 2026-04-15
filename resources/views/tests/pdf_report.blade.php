<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "Dejavu Sans", sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #4a90e2; padding-bottom: 10px; margin-bottom: 20px; }
        .test-title { font-size: 18px; font-bold: true; color: #1a5a96; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 5px; border: 1px solid #eee; }
        .info-label { font-weight: bold; width: 30%; background: #f9f9f9; }

        .result-badge {
            text-align: center;
            padding: 15px;
            background: #f0f7ff;
            border: 1px solid #d0e3ff;
            margin-bottom: 30px;
        }
        .score-big { font-size: 24px; font-weight: bold; color: #2dcc70; }

        .question-block { margin-bottom: 25px; border: 1px solid #eee; padding: 10px; page-break-inside: avoid; }
        .question-text { font-weight: bold; font-size: 14px; margin-bottom: 10px; display: block; }

        .option { padding: 5px 10px; margin: 3px 0; border-radius: 4px; border: 1px solid #f0f0f0; }
        .picked { background-color: #f0f0f0; border-left: 4px solid #333; }
        .correct { color: #27ae60; font-weight: bold; }
        .incorrect { color: #e74c3c; font-weight: bold; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #aaa; }
        .explanation { font-style: italic; font-size: 11px; color: #888; margin-top: 5px; padding-top: 5px; border-top: 1px dashed #eee; }
    </style>
</head>
<body>

    <div class="header">
        <div class="test-title">Протокол результатов тестирования</div>
        <div>Система StudentTest</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Студент:</td>
            <td>{{ $student->name }}</td>
        </tr>
        <tr>
            <td class="info-label">Группа:</td>
            <td>{{ $student->group ?? '—' }}</td>
        </tr>
        <tr>
            <td class="info-label">Тест:</td>
            <td>{{ $test->title }}</td>
        </tr>
        <tr>
            <td class="info-label">Дата завершения:</td>
            <td>{{ $result->completed_at->format('d.m.Y H:i') }}</td>
        </tr>
    </table>

    <div class="result-badge">
        Итоговый результат: <span class="score-big">{{ round(($result->score / ($result->total_points ?: 1)) * 100) }}%</span><br>
        Набрано баллов: <b>{{ number_format($result->score, 1) }}</b> из <b>{{ $result->total_points }}</b>
    </div>

    <h3>Детальный отчет по вопросам:</h3>

    @foreach($test->questions as $index => $question)
        @php
            $data = $result->answers[$question->id] ?? null;
            $studentAns = $data['answer'] ?? null;
        @endphp

        <div class="question-block">
            <div class="question-text">{{ $index + 1 }}. {{ $question->question_text }}</div>

            <div class="options-list">
                @if(in_array($question->type, ['single_choice', 'multi_choice', 'single', 'multi']))
                    @foreach($question->options as $option)
                        @php
                            $isPicked = is_array($studentAns) ? in_array($option->id, $studentAns) : ($studentAns == $option->id);
                            $isCorrect = $option->is_correct;
                        @endphp
                        <div class="option {{ $isPicked ? 'picked' : '' }}">
                            @if($isCorrect) [v] @else [ ] @endif
                            {{ $option->option_text }}
                            @if($isPicked)
                                <span class="{{ $isCorrect ? 'correct' : 'incorrect' }}">
                                    ({{ $isCorrect ? 'Верно' : 'Ошибка' }})
                                </span>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="option picked">
                        <strong>Ответ студента:</strong> {{ is_array($studentAns) ? json_encode($studentAns) : ($studentAns ?: 'нет ответа') }}
                    </div>
                    @if($question->type !== 'free_form')
                        <div class="option">
                            <strong class="correct">Эталон:</strong>
                            {{ $question->options->where('is_correct', true)->first()->option_text ?? '—' }}
                        </div>
                    @endif
                @endif
            </div>

            @if($question->explanation)
                <div class="explanation">Пояснение: {{ $question->explanation }}</div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        Документ сформирован автоматически в системе StudentTest. Дата: {{ date('d.m.Y') }}
    </div>

</body>
</html>
