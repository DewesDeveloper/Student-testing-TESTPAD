@extends('layouts.app')

@section('title', 'Управление тестом')
@section('header_title', $test->title)

@section('content')
	<div class="max-w-6xl mx-auto" x-data="{ testOpen: {{ $test->is_active ? 'true' : 'false' }} }">

		<!-- Метрики -->
		<div class="grid grid-cols-4 gap-6 mb-8">
			<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
				<div class="text-gray-400 text-xs uppercase font-bold mb-1">📅 Создан</div>
				<div class="text-xl font-bold">{{ $test->created_at->format('d.m.Y') }}</div>
			</div>
			<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
				<div class="text-gray-400 text-xs uppercase font-bold mb-1">👥 Прохождений</div>
				<div class="text-xl font-bold">{{ $test->results->count() }}</div>
			</div>
			<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
				<div class="text-gray-400 text-xs uppercase font-bold mb-1">❓ Вопросов</div>
				<div class="text-xl font-bold">{{ $test->questions->count() }}</div>
			</div>
			<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
				<div class="text-gray-400 text-xs uppercase font-bold mb-1">📚 Дисциплина</div>
				<div class="text-lg font-bold text-blue-600">{{ $test->discipline->name ?? 'Нет' }}</div>
			</div>
			<a href="{{ route('tests.edit', $test->id) }}"
				class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-md flex items-center gap-2">
				<span>✏️</span> Редактировать тест
			</a>
		</div>

		<div class="grid grid-cols-3 gap-8">
			<div class="col-span-2 space-y-6">
				<!-- Ссылка -->
				<div class="bg-white p-6 rounded-xl shadow-sm border flex items-center gap-6">
					<div
						class="w-14 h-14 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl shadow-inner">
						🔗</div>
					<div class="flex-1">
						<p class="text-[10px] font-bold text-gray-400 uppercase">Публичная ссылка на тест</p>
						<a href="{{ route('test.take', $test->id) }}" target="_blank"
							class="text-blue-500 font-medium break-all hover:underline">
							{{ route('test.take', $test->id) }}
						</a>
					</div>
				</div>

				<!-- Таблица результатов -->
				<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
					<div class="p-4 border-b font-bold bg-gray-50 flex justify-between items-center">
						<span>Последние результаты</span>
						<a href="{{ route('tests.manual-index', $test->id) }}"
							class="text-xs text-blue-500 hover:underline">Смотреть всех</a>
					</div>
					<table class="w-full text-left">
						<thead class="text-[10px] uppercase text-gray-400 border-b">
							<tr>
								<th class="p-4">Студент</th>
								<th class="p-4">Результат</th>
								<th class="p-4 text-right">Действие</th>
							</tr>
						</thead>
						<tbody>
							@foreach($test->results->take(5) as $res)
								<tr class="border-b last:border-0 hover:bg-gray-50 transition">
									<td class="p-4 font-bold text-gray-700">{{ $res->student->name }}</td>
									<td class="p-4">
										<span
											class="text-blue-600 font-bold">{{ round(($res->score / ($res->total_points ?: 1)) * 100) }}%</span>
										<span
											class="text-gray-300 text-xs ml-2">({{ $res->score }}/{{ $res->total_points }})</span>
									</td>
									<td class="p-4 text-right">
										<a href="{{ route('results.details', $res->id) }}"
											class="text-xs font-bold text-blue-500 hover:text-blue-700">ОТЧЕТ →</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div class="space-y-6">
				<!-- Статус -->
				<div class="bg-white p-8 rounded-xl shadow-sm border text-center">
					<div class="text-xs font-bold text-gray-400 uppercase mb-6">Статус теста</div>
					<form action="{{ route('tests.updateSettings', $test->id) }}" method="POST">
						@csrf @method('PATCH')
						<input type="hidden" name="is_active" :value="testOpen ? 0 : 1">
						<div x-show="testOpen" class="flex flex-col items-center">
							<div class="text-green-500 text-6xl mb-4 animate-pulse">🔓</div>
							<div class="text-2xl font-black text-gray-700 mb-6">ОТКРЫТ</div>
							<button type="submit"
								class="w-full py-3 bg-red-50 text-red-600 rounded-lg font-bold border border-red-100 hover:bg-red-500 hover:text-white transition">Закрыть
								доступ</button>
						</div>
						<div x-show="!testOpen" class="flex flex-col items-center">
							<div class="text-red-500 text-6xl mb-4">🔒</div>
							<div class="text-2xl font-black text-gray-700 mb-6">ЗАКРЫТ</div>
							<button type="submit"
								class="w-full py-3 bg-green-600 text-white rounded-lg font-bold shadow-lg hover:bg-green-700 transition">Открыть
								доступ</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection
