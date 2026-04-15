@extends('layouts.app')

@section('title', 'Мой кабинет')
@section('header_title', 'История моих тестирований')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Сводная статистика -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-xl">📝</div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Тестов пройдено</p>
                <p class="text-2xl font-black text-gray-800">{{ $results->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
            <div class="w-12 h-12 bg-green-50 text-green-500 rounded-full flex items-center justify-center text-xl">🏆</div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Средний балл</p>
                <p class="text-2xl font-black text-gray-800">
                    {{ $results->avg('score') ? number_format($results->avg('score'), 1) : 0 }}%
                </p>
            </div>
        </div>
        </div>
    </div>

    <!-- Таблица истории -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b bg-gray-50/50">
            <h3 class="font-bold text-gray-700">Журнал результатов</h3>
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-[10px] uppercase text-gray-400 font-black">
                <tr>
                    <th class="p-5">Название теста / Дисциплина</th>
                    <th class="p-5 text-center">Дата</th>
                    <th class="p-5 text-center">Результат</th>
                    <th class="p-5 text-right">Действие</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($results as $res)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-5">
                        <div class="font-bold text-gray-800">{{ $res->test->title }}</div>
                        <div class="text-[10px] text-blue-500 font-bold uppercase tracking-tighter">
                            {{ $res->test->discipline->name ?? 'Общая' }}
                        </div>
                    </td>
                    <td class="p-5 text-center text-sm text-gray-500">
                        {{ $res->completed_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="p-5 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-lg font-black text-blue-600">
                                {{ round(($res->score / ($res->total_points ?: 1)) * 100) }}%
                            </span>
                            <span class="text-[9px] text-gray-400 font-bold uppercase">
                                {{ number_format($res->score, 1) }} / {{ $res->total_points }} б.
                            </span>
                        </div>
                    </td>
                    <td class="p-5 text-right">
                        @if($res->test->show_result_to_user)
                            <a href="{{ route('test.result', $res->id) }}" class="inline-block bg-white border border-gray-200 px-4 py-2 rounded-lg text-xs font-bold text-gray-600 hover:border-blue-500 hover:text-blue-500 transition shadow-sm">
                                Подробно
                            </a>
                        @else
                            <span class="text-[10px] text-gray-300 italic uppercase">Скрыто учителем</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-20 text-center text-gray-300 italic">
                        Вы еще не проходили тесты.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
