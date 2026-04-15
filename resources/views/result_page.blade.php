@extends('layouts.app')

@section('title', 'Результат теста')
@section('header_title', 'Ваш результат')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Основная карточка с оценкой -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-8">
        <div class="p-10 text-center bg-gradient-to-b from-gray-50 to-white">
            <h1 class="text-2xl font-bold text-gray-400 uppercase tracking-widest mb-2">{{ $result->test->title }}</h1>

            <div class="flex justify-center my-8">
                <div class="relative w-48 h-48 flex items-center justify-center">
                    <!-- Прогресс-кольцо (SVG) -->
                    <svg class="absolute inset-0 w-full h-full -rotate-90">
                        <circle cx="96" cy="96" r="88" stroke="currentColor" stroke-width="12" fill="transparent" class="text-gray-100" />
                        <circle cx="96" cy="96" r="88" stroke="currentColor" stroke-width="12" fill="transparent"
                                class="text-blue-600"
                                stroke-dasharray="{{ 2 * pi() * 88 }}"
                                stroke-dashoffset="{{ (2 * pi() * 88) * (1 - ($result->score / ($result->total_points ?: 1))) }}" />
                    </svg>
                    <div class="flex flex-col items-center">
                        <span class="text-5xl font-black text-gray-800">{{ round(($result->score / ($result->total_points ?: 1)) * 100) }}%</span>
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-tighter">правильно</span>
                    </div>
                </div>
            </div>

            <!-- Итоговая оценка по шкале -->
            @if($result->test->enable_grading)
                @php
                    $pct = ($result->score / ($result->total_points ?: 1)) * 100;
                    if ($pct >= $result->test->grade_5_threshold) { $g = 5; $c = 'text-green-500'; }
                    elseif ($pct >= $result->test->grade_4_threshold) { $g = 4; $c = 'text-blue-500'; }
                    elseif ($pct >= $result->test->grade_3_threshold) { $g = 3; $c = 'text-orange-500'; }
                    else { $g = 2; $c = 'text-red-500'; }
                @endphp
                <div class="mt-4">
                    <p class="text-gray-400 text-xs font-bold uppercase">{{ $result->test->grade_label }}</p>
                    <p class="text-7xl font-black {{ $c }}">{{ $g }}</p>
                </div>
            @endif
        </div>

        <!-- Краткая таблица баллов -->
        <div class="bg-gray-50 p-6 grid grid-cols-2 border-t">
            <div class="text-center border-r">
                <p class="text-[10px] font-black text-gray-400 uppercase">Набрано баллов</p>
                <p class="text-xl font-bold">{{ number_format($result->score, 1) }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase">Максимум</p>
                <p class="text-xl font-bold">{{ $result->total_points }}</p>
            </div>
        </div>
    </div>

    <!-- ДЕТАЛЬНЫЙ РАЗБОР (Если разрешено учителем) -->
    @if($result->test->show_result_to_user)
        <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center gap-3">
            <span>🔍</span> Подробный разбор вопросов
        </h3>

        <div class="space-y-6">
            @foreach($result->test->questions as $index => $question)
                @php
                    $data = $result->answers[$question->id] ?? null;
                    $studentScore = $data['score'] ?? 0;
                    $isCorrect = $studentScore >= $question->points;
                @endphp
                <div class="bg-white rounded-2xl border p-6 shadow-sm relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-2 {{ $studentScore > 0 ? ($isCorrect ? 'bg-green-500' : 'bg-orange-400') : 'bg-red-500' }}"></div>

                    <div class="flex justify-between items-start mb-4">
                        <span class="text-xs font-black text-gray-400 uppercase">Вопрос {{ $index + 1 }}</span>
                        <span class="text-sm font-bold {{ $studentScore > 0 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $studentScore }} / {{ $question->points }} б.
                        </span>
                    </div>

                    <p class="font-bold text-gray-800 mb-4">{{ $question->question_text }}</p>

                    <!-- Пояснение системы (если учитель заполнил вкладку "Комментарий") -->
                    @if($question->explanation && $studentScore < $question->points)
                        <div class="bg-blue-50 p-4 rounded-xl text-sm text-blue-700 italic border border-blue-100">
                            <b>Подсказка:</b> {{ $question->explanation }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-amber-50 p-6 rounded-2xl border border-amber-200 text-amber-700 text-center italic">
            Преподаватель ограничил просмотр детального отчета по вопросам.
        </div>
    @endif

    <div class="mt-12 text-center pb-20">
        <a href="{{ route('dashboard') }}" class="bg-gray-800 text-white px-10 py-4 rounded-2xl font-bold hover:bg-gray-900 transition shadow-xl">Вернуться в кабинет</a>
    </div>
</div>
@endsection
