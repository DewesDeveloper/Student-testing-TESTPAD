@extends('layouts.app')

@section('title', 'Дашборд')
@section('header_title', 'Мои дисциплины')

@section('content')
	<div class="max-w-7xl mx-auto">
		<div class="flex justify-between items-center mb-8">
			<h1 class="text-2xl font-bold text-gray-700 uppercase tracking-tight">Мои тесты</h1>
			<a href="{{ route('tests.create') }}"
				class="bg-white border-2 border-gray-100 px-6 py-2 rounded shadow-sm text-gray-600 font-bold hover:bg-gray-50 transition flex items-center gap-2">
				<span class="text-blue-500 text-xl">+</span> Добавить
			</a>
		</div>

		<!-- Фильтры и кнопка добавления дисциплины -->
		<div class="flex items-center gap-3 mb-8" x-data="{ showAddDiscipline: false }">
			<div class="flex gap-3 overflow-x-auto pb-2 flex-1">
				<a href="{{ route('dashboard') }}"
					class="px-5 py-2 rounded-full border text-sm transition {{ !request('discipline_id') ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
					Все дисциплины
				</a>
				@foreach($disciplines as $d)
					<a href="?discipline_id={{ $d->id }}"
						class="px-5 py-2 rounded-full border text-sm transition {{ request('discipline_id') == $d->id ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
						{{ $d->name }}
					</a>
				@endforeach
			</div>

			<!-- Кнопка добавления дисциплины -->
			<button @click="showAddDiscipline = true"
				class="flex-shrink-0 w-10 h-10 bg-white border-2 border-dashed border-gray-300 rounded-full text-gray-400 hover:text-blue-500 hover:border-blue-500 transition-all flex items-center justify-center font-bold"
				title="Добавить дисциплину">
				+
			</button>

			<!-- МОДАЛЬНОЕ ОКНО (Alpine.js) -->
			<div x-show="showAddDiscipline" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50"
				x-cloak>
				<div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8" @click.away="showAddDiscipline = false">
					<h3 class="text-xl font-bold text-gray-800 mb-6">Новая дисциплина</h3>
					<form action="{{ route('disciplines.store') }}" method="POST">
						@csrf
						<div class="mb-6">
							<label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Название
								предмета</label>
							<input type="text" name="name" required placeholder="Напр: Квантовая физика"
								class="w-full border-2 border-gray-100 rounded-xl p-3 outline-none focus:border-blue-500 transition-all">
						</div>
						<div class="flex gap-3">
							<button type="button" @click="showAddDiscipline = false"
								class="flex-1 py-3 text-gray-400 font-bold hover:bg-gray-50 rounded-xl transition">Отмена</button>
							<button type="submit"
								class="flex-1 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-100 hover:bg-blue-700 transition">Создать</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		@if($tests->count() > 0)
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
				@foreach($tests as $test)
					<div
						class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group hover:shadow-lg transition-all relative">
						<div class="bg-[#6c757d] h-36 p-5 text-white relative">
							<div class="text-[10px] uppercase font-bold opacity-70 mb-1 tracking-widest">
								{{ $test->discipline->name ?? 'Общее' }}
							</div>
							<h3 class="text-lg font-bold leading-snug mb-2 group-hover:text-blue-200 transition-colors">
								{{ $test->title }}</h3>
							<a href="{{ route('tests.show', $test->id) }}" class="absolute inset-0"></a>
						</div>

						<div class="p-4 flex flex-col gap-4">
							<div class="flex justify-between items-center text-xs text-gray-400">
								<div class="flex items-center gap-3">
									<span>{!! $test->is_active ? '<span class="text-green-500">🔓</span>' : '<span class="text-red-500">🔒</span>' !!}</span>
									<span class="flex items-center gap-1">👥 <b>{{ $test->results->count() }}</b></span>
								</div>
								<span>{{ $test->created_at->format('d.m.Y') }}</span>
							</div>
							<div class="flex justify-between items-center pt-3 border-t border-gray-50">
								<div class="flex gap-2">
									<button onclick="copyLink('{{ route('test.take', $test->id) }}')"
										class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-blue-50 text-gray-400 hover:text-blue-500 transition">🔗</button>
									<form action="{{ route('tests.destroy', $test->id) }}" method="POST"
										onsubmit="return confirm('Удалить этот тест?')">
										@csrf @method('DELETE')
										<button type="submit"
											class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50 text-gray-400 hover:text-red-500 transition">🗑️</button>
									</form>
								</div>
								<a href="{{ route('tests.show', $test->id) }}"
									class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 transition">📊</a>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		@else
			<div class="bg-white p-20 text-center rounded-2xl border-2 border-dashed border-gray-200">
				<p class="text-gray-400">Тестов не обнаружено. Создайте новый, чтобы начать.</p>
			</div>
		@endif
	</div>
@endsection
