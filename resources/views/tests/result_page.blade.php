@extends('layouts.app')

@section('title', 'Результат теста')
@section('header_title', 'Детальный отчет: ' . $result->test->title)

@section('content')
<div class="max-w-5xl mx-auto pb-20">

    <!-- 1. ИТОГОВАЯ КАРТОЧКА -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-10">
        <div class="p-10 text-center bg-gradient-to-b from-gray-50 to-white">
            <h1 class="text-xl font-bold text-gray-400 uppercase tracking-widest mb-6">{{ $result->test->title }}</h1>

            <div class="flex flex-col md:flex-row items-center justify-center gap-12">
                <!-- Процентный круг -->
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <svg class="absolute inset-0 w-full h-full -rotate-90">
                        <circle cx="80" cy="80" r="74" stroke="currentColor" stroke-width="12" fill="transparent" class="text-gray-100" />
                        <circle cx="80" cy="80" r="74" stroke="currentColor" stroke-width="12" fill="transparent"
                                class="text-blue-600"
                                stroke-dasharray="{{ 2 * pi() * 74 }}"
                                stroke-dashoffset="{{ (2 * pi() * 74) * (1 - ($result->score / ($result->total_points ?: 1))) }}" />
                    </svg>
                    <div class="text-center">
                        <span class="text-4xl font-black text-gray-800">{{ round(($result->score / ($result->total_points ?: 1)) * 100) }}%</span>
                    </div>
                </div>

                <!-- Оценка -->
                @if($result->test->enable_grading)
                    <div class="text-center">
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">{{ $result->test->grade_label }}</p>
                        @php
                            $val = ($result->test->grading_type === 'percent') ? ($result->score / ($result->total_points ?: 1)) * 100 : $result->score;
                            if ($val >= $result->test->grade_5_threshold) { $g = 5; $c = 'text-green-500'; }
                            elseif ($val >= $result->test->grade_4_threshold) { $g = 4; $c = 'text-blue-500'; }
                            elseif ($val >= $result->test->grade_3_threshold) { $g = 3; $c = 'text-orange-500'; }
                            else { $g = 2; $c = 'text-red-500'; }
                        @endphp
                        <p class="text-8xl font-black {{ $c }}">{{ $g }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-8 flex justify-center gap-8 text-sm">
                <div class="text-gray-500">Набрано баллов: <b class="text-gray-800">{{ number_format($result->score, 1) }}</b></div>
                <div class="text-gray-500">Максимум: <b class="text-gray-800">{{ $result->total_points }}</b></div>
            </div>
        </div>
    </div>

    <!-- 2. ДЕТАЛЬНЫЙ РАЗБОР ВОПРОСОВ -->
    <div class="space-y-8">
        <h3 class="text-2xl font-bold text-gray-700 flex items-center gap-3 px-2">
            <span>🔍</span> Результаты по вопросам
        </h3>

        @foreach($result->test->questions as $index => $question)
            @php
                $data = $result->answers[$question->id] ?? null;
                $studentAns = $data['answer'] ?? null;
                $studentScore = $data['score'] ?? 0;
                $isFullCorrect = $studentScore >= $question->points && $question->points > 0;
            @endphp

            <div class="bg-white rounded-2xl border-2 {{ $studentScore > 0 ? ($isFullCorrect ? 'border-green-100' : 'border-orange-100') : 'border-red-100' }} shadow-sm overflow-hidden transition-all hover:shadow-md">
                <!-- Шапка вопроса -->
                <div class="p-5 border-b flex justify-between items-center {{ $studentScore > 0 ? ($isFullCorrect ? 'bg-green-50' : 'bg-orange-50') : 'bg-red-50' }}">
                    <span class="text-xs font-black uppercase tracking-widest text-gray-500">Вопрос {{ $index + 1 }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold {{ $studentScore > 0 ? 'text-green-700' : 'text-red-700' }}">
                            {{ $studentScore }} / {{ $question->points }} б.
                        </span>
                        @if($isFullCorrect) ✅ @elseif($studentScore > 0) ⚠️ @else ❌ @endif
                    </div>
                </div>

                <div class="p-6">
                    <p class="text-lg font-bold text-gray-800 mb-6">{{ $question->question_text }}</p>

                    <!-- Список вариантов (для выбора) -->
                    @if(in_array($question->type, ['single_choice', 'multi_choice', 'single', 'multi']))
                        <div class="grid gap-3">
                            @foreach($question->options as $option)
                                @php
                                    $isPicked = is_array($studentAns) ? in_array($option->id, $studentAns) : ($studentAns == $option->id);
                                    $isCorrect = $option->is_correct;

                                    $style = 'border-gray-100 bg-gray-50/50 text-gray-400'; // по умолчанию
                                    if ($isPicked && $isCorrect) $style = 'border-green-500 bg-green-50 text-green-700 font-bold ring-2 ring-green-100';
                                    elseif ($isPicked && !$isCorrect) $style = 'border-red-500 bg-red-50 text-red-700 font-bold ring-2 ring-red-100';
                                    elseif (!$isPicked && $isCorrect) $style = 'border-green-300 border-dashed bg-white text-green-600';
                                @endphp

                                <div class="p-4 border-2 rounded-xl flex justify-between items-center {{ $style }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $isCorrect ? 'bg-green-500 border-green-500' : 'border-gray-300' }}">
                                            @if($isCorrect) <span class="text-white text-[10px]">✔</span> @endif
                                        </div>
                                        <span class="text-sm">{{ $option->option_text }}</span>
                                    </div>
                                    @if($isPicked)
                                        <span class="text-[9px] uppercase font-black tracking-widest px-2 py-0.5 rounded {{ $isCorrect ? 'bg-green-200' : 'bg-red-200' }}">ваш ответ</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                    <!-- Текстовые ответы / Свободная форма -->
                    @elseif(in_array($question->type, ['text', 'number', 'free_form']))
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-[10px] text-gray-400 uppercase font-black mb-1">Ваш ответ:</p>
                            <p class="text-lg font-medium {{ $studentScore > 0 ? 'text-green-700' : 'text-red-700' }}">
                                {{ $studentAns ?: 'Ответ не был дан' }}
                            </p>

                            @if($question->type !== 'free_form')
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <p class="text-[10px] text-green-600 font-black uppercase mb-1">Правильный ответ:</p>
                                    <p class="text-sm text-green-700 font-bold">
                                        {{ $question->options->where('is_correct', true)->first()->option_text ?? '—' }}
                                    </p>
                                </div>
                            @endif
                        </div>

                    <!-- Соответствие -->
                    @elseif($question->type === 'matching')
                        <div class="space-y-2">
                            @foreach($question->options as $option)
                                @php
                                    $choice = $studentAns[$option->id] ?? '—';
                                    $correct = $choice === $option->match_text;
                                @endphp
                                <div class="flex justify-between items-center p-3 border rounded-xl {{ $correct ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                                    <span class="text-sm font-bold text-gray-700">{{ $option->option_text }}</span>
                                    <span class="text-gray-400">→</span>
                                    <span class="text-sm {{ $correct ? 'text-green-700' : 'text-red-700' }}">
                                        {{ $choice }} @if(!$correct) <small class="text-gray-400 ml-1">(правильно: {{ $option->match_text }})</small> @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Пояснение (explanation) -->
                    @if($question->explanation && !$isFullCorrect)
                        <div class="mt-6 bg-blue-50 p-4 rounded-xl border-l-4 border-blue-400 text-sm text-blue-700 italic">
                            <b>Пояснение:</b> {{ $question->explanation }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-12 text-center">
        <a href="{{ route('dashboard') }}" class="bg-gray-800 text-white px-10 py-4 rounded-2xl font-bold hover:bg-gray-900 transition shadow-xl uppercase tracking-widest text-sm">
            Вернуться в кабинет
        </a>
    </div>
</div>
@endsection
