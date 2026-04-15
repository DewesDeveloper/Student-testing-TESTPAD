@extends('layouts.app')

@section('title', 'Статистика теста')
@section('header_title', 'Статистика: ' . $test->title)

@section('content')
	<!-- viewMode: 'percent' или 'count' -->
	<div class="max-w-7xl mx-auto" x-data="{ activeTab: 'table', viewMode: 'percent' }">

		<!-- Переключатель вкладок -->
		<div class="flex justify-center mb-8">
			<div class="inline-flex bg-white rounded-lg shadow-sm border p-1">
				<button @click="activeTab = 'questions'"
					:class="activeTab === 'questions' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-400 border-transparent'"
					class="px-10 py-3 rounded-md border flex flex-col items-center transition-all duration-200">
					<span class="text-xl">❓</span>
					<span class="text-[10px] uppercase font-black mt-1">По вопросам</span>
				</button>
				<button @click="activeTab = 'table'"
					:class="activeTab === 'table' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-400 border-transparent'"
					class="px-10 py-3 rounded-md border flex flex-col items-center transition-all duration-200 ml-1">
					<span class="text-xl">📋</span>
					<span class="text-[10px] uppercase font-black mt-1">Таблица результатов</span>
				</button>
			</div>
		</div>

		<!-- ВКЛАДКА 1: ТАБЛИЦА РЕЗУЛЬТАТОВ -->
		<div x-show="activeTab === 'table'" x-transition>
			<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
				<!-- Панель поиска и инструментов -->
				<div class="p-4 bg-gray-50 border-b flex justify-between items-center">
					<div class="flex gap-2">
						<a href="{{ route('tests.export-excel', $test->id) }}"
							class="bg-white border px-4 py-1.5 rounded text-xs font-bold hover:bg-gray-50 flex items-center gap-2">📥
							Excel</a>
					</div>
					<form action="{{ route('tests.statistics', $test->id) }}" method="GET" class="flex items-center gap-1">
						<input type="number" name="search" value="{{ request('search') }}" placeholder="№ результата..."
							class="border rounded-l-lg px-3 py-1.5 text-xs outline-none focus:ring-1 focus:ring-blue-400 w-32">
						<button type="submit"
							class="bg-blue-600 text-white px-4 py-1.5 rounded-r-lg text-xs font-bold hover:bg-blue-700 transition">Найти</button>
					</form>
				</div>

				<div class="overflow-x-auto">
					<table class="w-full text-[11px] text-left border-collapse">
						<thead>
							<tr class="bg-white border-b text-gray-400 uppercase font-black tracking-widest italic">
								<th class="p-4 border-r w-16 text-center">#</th>
								<th class="p-4 border-r">Студент</th>
								<th class="p-4 border-r">Группа</th>
								<th class="p-4 border-r text-center">Дата</th>
								<th class="p-4 border-r text-center">Прав. ответы</th>
								<th class="p-4 border-r text-center">Процент</th>
								<th class="p-4 text-center">Оценка</th>
							</tr>
						</thead>
						<tbody class="text-gray-600 divide-y divide-gray-50">
							@forelse($results as $res)
								<tr class="hover:bg-blue-50/50 transition-colors">
									<td class="p-4 border-r text-center text-blue-500 font-bold">
										<a href="{{ route('results.review', $res->id) }}">#{{ $res->id }}</a>
									</td>
									<td class="p-4 border-r font-bold text-gray-800">{{ $res->student->name }}</td>
									<td class="p-4 border-r font-medium">{{ $res->student->group ?? '—' }}</td>
									<td class="p-4 border-r text-center text-gray-400">
										{{ $res->completed_at->format('d.m.Y H:i') }}
									</td>
									<td class="p-4 border-r text-center font-bold text-green-600">
										{{ $res->correct_answers_count }} / {{ $test->questions->count() }}
									</td>
									<td class="p-4 border-r text-center font-black text-blue-600">
										{{ round(($res->score / ($res->total_points ?: 1)) * 100, 1) }}%
									</td>
									<td class="p-4 text-center">
										@php
											$pct = ($res->score / ($res->total_points ?: 1)) * 100;
											if ($pct >= $test->grade_5_threshold) {
												$g = 5;
												$c = 'text-green-500';
											} elseif ($pct >= $test->grade_4_threshold) {
												$g = 4;
												$c = 'text-blue-500';
											} elseif ($pct >= $test->grade_3_threshold) {
												$g = 3;
												$c = 'text-orange-500';
											} else {
												$g = 2;
												$c = 'text-red-500';
											}
										@endphp
										<span class="text-xl font-black {{ $c }}">{{ $g }}</span>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="p-20 text-center text-gray-400 italic">Результатов не найдено</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- ВКЛАДКА 2: ПО ВОПРОСАМ -->
		<div x-show="activeTab === 'questions'" x-transition>
			<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

				<div class="p-6 bg-gray-50 flex justify-between items-center border-b">
					<div class="text-gray-500 font-bold uppercase text-xs tracking-widest">Анализ ответов по каждому вопросу
					</div>

					<!-- ПЕРЕКЛЮЧАТЕЛЬ viewMode -->
					<div class="flex bg-white border rounded-lg p-1">
						<button @click="viewMode = 'percent'"
							:class="viewMode === 'percent' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400'"
							class="px-4 py-1.5 rounded text-[10px] font-black uppercase transition-all">Проценты
							(%)</button>
						<button @click="viewMode = 'count'"
							:class="viewMode === 'count' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400'"
							class="px-4 py-1.5 rounded text-[10px] font-black uppercase transition-all ml-1">Количество
							(чел.)</button>
					</div>
				</div>

				<div class="p-6">
					<table class="w-full text-xs text-left border-collapse">
						<thead>
							<tr class="text-gray-400 font-black uppercase border-b">
								<th class="pb-4 w-1/3">Текст вопроса</th>
								<th class="pb-4 text-center italic">Max баллов</th>
								<th class="pb-4 text-center text-red-500">Неправильно <span
										x-text="viewMode === 'percent' ? '%' : '(чел.)'"></span></th>
								<th class="pb-4 text-center text-blue-500">Частично <span
										x-text="viewMode === 'percent' ? '%' : '(чел.)'"></span></th>
								<th class="pb-4 text-center text-green-500">Полностью <span
										x-text="viewMode === 'percent' ? '%' : '(чел.)'"></span></th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-50">
							@foreach($questionsAnalysis as $index => $q)
								{{-- Передаем данные конкретной строки в Alpine-компонент этой строки --}}
								<tr class="hover:bg-gray-50 transition-colors" x-data="{ item: {{ json_encode($q) }} }">
									<td class="py-4 font-bold text-gray-700">
										<span class="text-gray-300 mr-2">{{ $index + 1 }}.</span>
										{{ Str::limit($q['text'], 60) }}
									</td>
									<td class="py-4 text-center font-black text-gray-400 italic">
										{{ $q['max_points'] }}
									</td>

									<!-- НЕПРАВИЛЬНО -->
									<td class="py-4 text-center">
										<span class="font-bold"
											:class="viewMode === 'percent' && item.incorrect_pct > 0 ? 'text-red-500' : 'text-gray-400'"
											x-text="viewMode === 'percent' ? item.incorrect_pct + '%' : item.incorrect_cnt">
										</span>
									</td>

									<!-- ЧАСТИЧНО -->
									<td class="py-4 text-center text-blue-500 font-bold">
										<span
											x-text="viewMode === 'percent' ? item.partial_pct + '%' : item.partial_cnt"></span>
									</td>

									<!-- ПОЛНОСТЬЮ -->
									<td class="py-4 text-center">
										<span class="font-bold"
											:class="viewMode === 'percent' && item.correct_pct > 0 ? 'text-green-500' : 'text-gray-400'"
											x-text="viewMode === 'percent' ? item.correct_pct + '%' : item.correct_cnt">
										</span>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
@endsection
