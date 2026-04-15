@extends('layouts.app')
@section('title', 'Детали отчета')
@section('header_title', 'Детали отчета: ' . $result->student->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('tests.show', $result->test_id) }}" class="text-blue-600 hover:underline">← Назад к статистике</a>
        <span class="text-gray-400">Дата: {{ $result->completed_at->format('d.m.Y H:i') }}</span>
    </div>

    <!-- Блок комментария -->
    @if($result->student_comment)
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-xl mb-8 shadow-sm">
        <h3 class="text-blue-800 font-bold uppercase text-xs tracking-widest mb-1">Комментарий студента:</h3>
        <p class="text-blue-900 italic">"{{ $result->student_comment }}"</p>
    </div>
    @endif

    <div class="space-y-6">
        @foreach($result->test->questions as $question)
            @php $studentAns = $result->answers[$question->id] ?? null; @endphp
            <div class="bg-white p-6 rounded-xl border shadow-sm">
                <p class="font-bold text-gray-800 mb-4">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                <div class="space-y-2">
                    @foreach($question->options as $option)
                        @php
                            $isPicked = is_array($studentAns) ? in_array($option->id, $studentAns) : ($studentAns == $option->id);
                            $style = $option->is_correct ? 'bg-green-50 border-green-500 text-green-700' : ($isPicked ? 'bg-red-50 border-red-500 text-red-700' : 'bg-white border-gray-100');
                        @endphp
                        <div class="p-3 border-2 rounded-lg flex justify-between items-center {{ $style }}">
                            <span>{{ $option->option_text }}</span>
                            <span class="text-[10px] font-bold uppercase">
                                @if($isPicked && $option->is_correct) ✅ Верно
                                @elseif($isPicked && !$option->is_correct) ❌ Ошибка
                                @elseif(!$isPicked && $option->is_correct) 💡 Должен быть этот
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
