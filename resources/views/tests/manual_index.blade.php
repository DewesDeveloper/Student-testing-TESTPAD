@extends('layouts.app')

@section('title', 'Список результатов')
@section('header_title', 'Выбор студента для проверки')

@section('content')
	<div class="max-w-5xl mx-auto">
		<div class="mb-8">
			<h1 class="text-2xl font-bold text-gray-700">{{ $test->title }}</h1>
			<p class="text-gray-400">Нажмите «Проверить», чтобы изменить баллы или посмотреть детали ответов.</p>
		</div>

		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
			<table class="w-full text-left">
				<thead class="bg-gray-50 border-b text-[10px] uppercase text-gray-400 font-extrabold tracking-widest">
					<tr>
						<th class="p-5 text-gray-400">Студент</th>
						<th class="p-5">Баллы</th>
						<th class="p-5">Статус</th>
						<th class="p-5 text-right">Действие</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-50">
					@forelse($results as $res)
						<tr class="hover:bg-blue-50/30 transition-colors">
							<td class="p-5">
								<div class="font-bold text-gray-700">{{ $res->student->name }}</div>
								<div class="text-[10px] text-gray-400">{{ $res->completed_at->format('d.m.Y в H:i') }}</div>
							</td>
							<td class="p-5">
								<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-black">
									{{ number_format($res->score, 1) }} / {{ $res->total_points }}
								</span>
							</td>
							<td class="p-5">
								@if($res->is_reviewed)
									<div class="flex items-center gap-2 text-green-600 font-bold text-xs uppercase tracking-widest">
										<span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
										Проверено
									</div>
								@else
									<div
										class="flex items-center gap-2 text-orange-400 font-bold text-xs uppercase tracking-widest">
										<span class="w-2 h-2 bg-orange-400 rounded-full"></span>
										Ждет проверки
									</div>
								@endif
							</td>
							<td class="p-5 text-right">
								<a href="{{ route('results.review', $res->id) }}"
									class="inline-block bg-white border border-gray-200 px-4 py-2 rounded-lg text-xs font-bold text-gray-600 hover:border-blue-500 hover:text-blue-500 transition-all shadow-sm">
									Проверить →
								</a>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="4" class="p-20 text-center text-gray-300">Студентов пока нет</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
@endsection
