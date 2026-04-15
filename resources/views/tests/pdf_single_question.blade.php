<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "Dejavu Sans", sans-serif; font-size: 12px; color: #333; }
        .box { border: 1px solid #ddd; padding: 20px; margin-top: 20px; }
        .header { background: #f4f7f9; padding: 10px; border-bottom: 2px solid #2d3a4b; }
        .student-info { margin-bottom: 20px; color: #555; }
        .question-text { font-size: 16px; font-weight: bold; margin-bottom: 15px; color: #1a5a96; }
        .answer-box { background: #fcfcfc; border: 1px solid #eee; padding: 15px; }
        .correct { color: green; font-weight: bold; }
        .incorrect { color: red; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 10px; color: #aaa; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <strong>Протокол ответа на отдельный вопрос</strong>
    </div>

    <div class="student-info">
        <p>Студент: <strong>{{ $student->name }}</strong> (Группа: {{ $student->group ?? '—' }})</p>
        <p>Тест: {{ $result->test->title }}</p>
        <p>ID результата: #{{ $result->id }}</p>
    </div>

    <div class="box">
        <div class="question-text">Вопрос: {{ $question->question_text }}</div>

        <div class="answer-box">
            <p><strong>Ответ студента:</strong></p>
            @php $ans = $answerData['answer'] ?? 'нет ответа'; @endphp

            @if(is_array($ans))
                {{ json_encode($ans, JSON_UNESCAPED_UNICODE) }}
            @else
                <p style="font-size: 14px;">{{ $ans }}</p>
            @endif

            <hr>
            <p>Начислено баллов: <strong>{{ $answerData['score'] ?? 0 }}</strong> из {{ $question->points }}</p>
        </div>
    </div>

    @if($question->explanation)
        <p style="font-style: italic; color: #777;">Пояснение системы: {{ $question->explanation }}</p>
    @endif

    <div class="footer">
        Сформировано в системе StudentTest {{ date('d.m.Y H:i') }}
    </div>
</body>
</html>
