@extends('layouts.app')

@section('title', 'Начальная страница')
@section('header_title', 'Настройка обложки теста')

@section('content')
	<div class="max-w-5xl mx-auto">
		<form action="{{ route('tests.start-page.update', $test->id) }}" method="POST">
			@csrf @method('PATCH')

			<div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
				<!-- Заголовок системы -->
				<div class="bg-gray-100 p-4 border-b">
					<h2 class="text-gray-600 font-medium italic">{{ $test->title }}</h2>
				</div>

				<!-- Тело редактора обложки -->
				<div class="p-12 bg-white">
					<div class="max-w-2xl mx-auto space-y-10 border-x border-dashed border-gray-100 px-10 py-4">

						<!-- Поле: Описание (вместо "Введите описание") -->
						<div class="text-center">
							<textarea name="description" rows="2"
								class="w-full text-center text-2xl font-light border-none outline-none focus:ring-0 placeholder-gray-300 italic"
								placeholder="Введите краткое описание теста...">{{ $test->description }}</textarea>
							<div class="h-0.5 bg-gray-100 w-1/2 mx-auto mt-2"></div>
						</div>

						<!-- Поле: Инструкция -->
						<div>
							<label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Инструкция
								к тесту</label>
							<textarea name="instruction" rows="4"
								class="w-full border-b border-gray-200 focus:border-blue-400 outline-none transition-colors py-2 text-gray-600 italic"
								placeholder="Введите текст инструкции для студента...">{{ $test->instruction }}</textarea>
						</div>


						<!-- Поля: Автор и Источник -->
						<div class="border-t border-gray-100 pt-6 space-y-4">
							<div class="flex gap-4">
								<input type="text" name="author" value="{{ $test->author }}"
									class="flex-1 border-b border-transparent focus:border-blue-300 outline-none text-center text-xs text-gray-500 italic"
									placeholder="Укажите автора">
								<input type="text" name="source" value="{{ $test->source }}"
									class="flex-1 border-b border-transparent focus:border-blue-300 outline-none text-center text-xs text-gray-500 italic"
									placeholder="Укажите источник">
							</div>
							<p class="text-[10px] text-gray-300 text-center uppercase tracking-tighter">Информация об авторе
								и источнике</p>
						</div>
					</div>
				</div>
			</div>

			<div class="mt-8 flex justify-end">
				<button type="submit"
					class="bg-blue-600 text-white px-12 py-3 rounded-lg font-bold shadow-lg hover:bg-blue-700 transition-all transform hover:scale-105">
					💾 Сохранить изменения
				</button>
			</div>
		</form>
	</div>
@endsection
