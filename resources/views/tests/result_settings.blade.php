@extends('layouts.app')

@section('title', 'Настройка шкал')
@section('header_title', 'Результат: ' . $test->title)

@section('content')
	<div class="max-w-5xl mx-auto"
		x-data="{ grading: {{ $test->enable_grading ? 'true' : 'false' }}, type: '{{ $test->grading_type }}' }">

		<div class="flex border-b border-gray-200 mb-8 bg-white rounded-t-lg">
			<a href="{{ route('tests.settings', $test->id) }}"
				class="px-6 py-4 text-sm text-gray-400 hover:text-gray-600 flex items-center gap-2">
				<span>⚙️</span> Основные настройки
			</a>
			<a href="{{ route('tests.result-settings', $test->id) }}"
				class="px-6 py-4 text-sm text-blue-600 border-b-2 border-blue-600 font-bold flex items-center gap-2"
				<span>⭐</span> Настройки результата
			</a>
		</div>

		<form action="{{ route('tests.result-settings.update', $test->id) }}" method="POST">
			@csrf @method('PATCH')

			<div class="bg-white rounded-b-lg shadow-sm border border-t-0 p-8">
				<!-- Основной переключатель -->
				<div class="flex items-center gap-4 mb-10 pb-6 border-b border-gray-50">
					<label class="relative inline-flex items-center cursor-pointer">
						<input type="checkbox" name="enable_grading" class="hidden peer" x-model="grading" {{ $test->enable_grading ? 'checked' : '' }}>
						<div
							class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
						</div>
					</label>
					<span class="text-gray-700 font-bold">Выставлять оценку по результату теста</span>
				</div>

				<div class="grid grid-cols-2 gap-16" x-show="grading" x-transition>
					<!-- Левая колонка: Шкалы -->
					<div class="space-y-6">
						<div class="flex items-center justify-between">
							<div class="flex items-center gap-2 text-sm text-gray-500">
								Если <input type="number" name="grade_5_threshold" value="{{ $test->grade_5_threshold }}"
									class="w-16 border-b-2 border-gray-200 text-center font-bold text-gray-800 focus:border-blue-500 outline-none">
								<span x-text="type === 'percent' ? '%' : 'б.'"></span> ≤ значение
							</div>
							<span class="text-4xl font-black text-green-500">→ 5</span>
						</div>
						<div class="flex items-center justify-between">
							<div class="flex items-center gap-2 text-sm text-gray-500">
								Если <input type="number" name="grade_4_threshold" value="{{ $test->grade_4_threshold }}"
									class="w-16 border-b-2 border-gray-200 text-center font-bold text-gray-800 focus:border-blue-500 outline-none">
								<span x-text="type === 'percent' ? '%' : 'б.'"></span> ≤ значение
							</div>
							<span class="text-4xl font-black text-blue-500">→ 4</span>
						</div>
						<div class="flex items-center justify-between">
							<div class="flex items-center gap-2 text-sm text-gray-500">
								Если <input type="number" name="grade_3_threshold" value="{{ $test->grade_3_threshold }}"
									class="w-16 border-b-2 border-gray-200 text-center font-bold text-gray-800 focus:border-blue-500 outline-none">
								<span x-text="type === 'percent' ? '%' : 'б.'"></span> ≤ значение
							</div>
							<span class="text-4xl font-black text-orange-500">→ 3</span>
						</div>
					</div>

					<!-- Правая колонка: Настройки типа -->
					<div class="space-y-8 bg-gray-50 p-6 rounded-xl border border-gray-100">
						<div>
							<p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Основа для оценки
							</p>
							<div class="space-y-3">
								<label class="flex items-center gap-3 cursor-pointer group">
									<input type="radio" name="grading_type" value="points" x-model="type"
										class="w-4 h-4 text-blue-600">
									<span
										class="text-sm font-medium text-gray-600 group-hover:text-blue-600 transition">Количество
										баллов</span>
								</label>
								<label class="flex items-center gap-3 cursor-pointer group">
									<input type="radio" name="grading_type" value="percent" x-model="type"
										class="w-4 h-4 text-blue-600">
									<span
										class="text-sm font-medium text-gray-600 group-hover:text-blue-600 transition">Процент
										правильных ответов</span>
								</label>
							</div>
						</div>

						<div>
							<label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Текст
								перед оценкой</label>
							<input type="text" name="grade_label" value="{{ $test->grade_label }}"
								class="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-shadow">
						</div>
					</div>
				</div>

				<div class="mt-12 pt-6 border-t border-gray-50 flex items-center gap-4">
					<label class="relative inline-flex items-center cursor-pointer">
						<input type="checkbox" name="show_result_to_user" value="1" {{ $test->show_result_to_user ? 'checked' : '' }} class="hidden peer">
						<div
							class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
						</div>
					</label>
					<span class="text-gray-400 text-xs italic">Показывать подробный результат студенту после
						завершения</span>
				</div>
			</div>

			<div class="mt-8 flex justify-end">
				<button type="submit"
					class="bg-blue-600 hover:bg-blue-700 text-white px-12 py-4 rounded-xl font-bold shadow-lg transition-all hover:scale-105">
					💾 Сохранить настройки шкал
				</button>
			</div>
		</form>
	</div>
@endsection
