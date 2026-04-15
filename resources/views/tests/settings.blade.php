@extends('layouts.app')

@section('title', 'Настройки теста')
@section('header_title', 'Настройки: ' . $test->title)

@section('content')
	<div class="max-w-6xl mx-auto">

		<div class="flex border-b border-gray-200 mb-8 bg-white rounded-t-lg">
			<button class="px-6 py-4 text-sm text-blue-600 border-b-2 border-blue-600 font-bold flex items-center gap-2">
				<span>⚙️</span> Основные настройки
			</button>
			<a href="{{ route('tests.result-settings', $test->id) }}"
				class="px-6 py-4 text-sm text-gray-400 hover:text-gray-600 flex items-center gap-2">
				<span>⭐</span> Настройки результата
			</a>
		</div>

		<form action="{{ route('tests.settings.update', $test->id) }}" method="POST">
			@csrf @method('PATCH')

			<div class="bg-white rounded-b-lg shadow-sm border border-t-0 p-10">
				<div class="grid grid-cols-2 gap-x-20 gap-y-6">

					<!-- Левая колонка -->
					<div class="space-y-6">
						@include('tests.partials.setting_item', ['name' => 'show_numbers', 'label' => 'Показать номера вопросов', 'value' => $test->show_numbers])
						@include('tests.partials.setting_item', ['name' => 'allow_comments', 'label' => 'Разрешить комментарии', 'value' => $test->allow_comments])
						@include('tests.partials.setting_item', ['name' => 'allow_error_reports', 'label' => 'Разрешить сообщения об ошибках', 'value' => $test->allow_error_reports])

						<hr class="my-4 border-gray-100">

						@include('tests.partials.setting_item', ['name' => 'shuffle_questions', 'label' => 'Перемешать вопросы', 'value' => $test->shuffle_questions])

						<hr class="my-4 border-gray-100">

						@include('tests.partials.setting_item', ['name' => 'show_time', 'label' => 'Показать время прохождения', 'value' => $test->show_time])
						@include('tests.partials.setting_item', ['name' => 'prevent_copy', 'label' => 'Запретить копирование текста в буфер', 'value' => $test->prevent_copy])
						@include('tests.partials.setting_item', ['name' => 'confirm_next', 'label' => 'По кнопке "Далее" выдавать подтверждение', 'value' => $test->confirm_next])
						@include('tests.partials.setting_item', ['name' => 'show_correct_instantly', 'label' => 'Сразу показывать правильные ответы', 'value' => $test->show_correct_instantly])
						@include('tests.partials.setting_item', ['name' => 'show_dropdown', 'label' => 'Показать выпадающий список вопросов', 'value' => $test->show_dropdown])
					</div>

					<!-- Правая колонка -->
					<div class="space-y-6">
						@include('tests.partials.setting_item', ['name' => 'show_progress_bar', 'label' => 'Показывать Progress Bar ответов', 'value' => $test->show_progress_bar])

						<hr class="my-4 border-gray-100 opacity-0"> <!-- для выравнивания -->
						<hr class="my-4 border-gray-100 opacity-0">

						@include('tests.partials.setting_item', ['name' => 'shuffle_options', 'label' => 'Перемешать варианты ответов', 'value' => $test->shuffle_options])
						@include('tests.partials.setting_item', ['name' => 'require_all_answers', 'label' => 'Обязательны ответы на все вопросы', 'value' => $test->require_all_answers])

						<hr class="my-4 border-gray-100">

						<div class="flex items-center justify-between group">
							<div class="flex flex-col">
								<label class="text-sm text-gray-500">Ограничить время прохождения</label>
								<div class="flex items-center gap-2 mt-1" x-show="true">
									<!-- показываем всегда для удобства -->
									<input type="number" name="time_limit" value="{{ $test->time_limit }}"
										class="w-16 border-b text-xs text-center outline-none focus:border-blue-500">
									<span class="text-[10px] text-gray-400">минут</span>
								</div>
							</div>
							@include('tests.partials.setting_item_only_input', ['name' => 'limit_time', 'value' => $test->limit_time])
						</div>
						@include('tests.partials.setting_item', ['name' => 'prevent_back', 'label' => 'Запретить использование кнопки "Назад"', 'value' => $test->prevent_back])
						@include('tests.partials.setting_item', ['name' => 'confirm_finish', 'label' => 'По кнопке "Завершить" выдавать подтверждение', 'value' => $test->confirm_finish])
					</div>
				</div>

				<div class="mt-12 flex justify-end">
					<button type="submit"
						class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded-lg font-bold shadow-lg transition-all">
						Сохранить настройки
					</button>
				</div>
			</div>
		</form>
	</div>
@endsection
