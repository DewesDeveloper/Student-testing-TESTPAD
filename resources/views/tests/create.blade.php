@extends('layouts.app')

@section('title', 'Создание теста')
@section('header_title', 'Новый тест: Конструктор')

@section('content')
	<div x-data="testCreator()" class="relative flex gap-6 items-start">

		<!-- ЛЕВАЯ ЧАСТЬ: РЕДАКТОР -->
		<div class="flex-1 min-w-0">
			<!-- ГЛАВНАЯ ФОРМА -->
			<form action="{{ route('tests.store') }}" method="POST" id="mainForm" enctype="multipart/form-data">
				@csrf

				<!-- Шапка теста -->
				<div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
					<div class="mb-6">
						<label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Название
							теста</label>
						<input type="text" name="title" required placeholder="Введите название..."
							class="w-full text-2xl font-bold border-b-2 border-gray-100 focus:border-blue-500 outline-none pb-2 transition-all">
					</div>

					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<!-- Выбор дисциплины -->
						<div>
							<label
								class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Дисциплина</label>
							<div class="flex gap-2">
								<select name="discipline_id"
									class="flex-1 border rounded-lg p-2.5 text-sm bg-gray-50 outline-none focus:ring-2 focus:ring-blue-500/20"
									required>
									<option value="">-- Выберите предмет --</option>
									@foreach($disciplines as $d)
										<option value="{{ $d->id }}">{{ $d->name }}</option>
									@endforeach
								</select>
								<!-- Кнопка открытия модалки (БЕЗ отправки формы) -->
								<button type="button" @click="showModal = true"
									class="px-4 bg-gray-100 border rounded-lg text-gray-500 hover:bg-blue-600 hover:text-white transition font-bold">+</button>
							</div>
						</div>

						<!-- Краткое описание -->
						<div>
							<label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Краткое
								описание</label>
							<textarea name="description" rows="1"
								class="w-full border rounded-lg p-2.5 text-sm bg-gray-50 outline-none focus:ring-2 focus:ring-blue-500/20 resize-none"
								placeholder="О чем этот тест?"></textarea>
						</div>
					</div>
				</div>

				<!-- Список добавленных вопросов -->
				<div class="space-y-8">
					<template x-for="(question, qIndex) in questions" :key="qIndex">
						<div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden"
							x-data="{ tab: 'editor' }">

							<!-- ВКЛАДКИ -->
							<div
								class="flex bg-gray-50 border-b text-[11px] font-bold text-gray-500 uppercase tracking-wider">
								<button type="button" @click="tab = 'editor'"
									:class="tab === 'editor' ? 'bg-white border-t-2 border-t-blue-500 text-blue-600' : ''"
									class="px-6 py-3 border-r transition-all">📝 Редактор</button>
								<button type="button" @click="tab = 'comment'"
									:class="tab === 'comment' ? 'bg-white border-t-2 border-t-blue-500 text-blue-600' : ''"
									class="px-6 py-3 border-r transition-all">💬 Комментарий</button>
								<button type="button" @click="tab = 'params'"
									:class="tab === 'params' ? 'bg-white border-t-2 border-t-blue-500 text-blue-600' : ''"
									class="px-6 py-3 border-r transition-all">⚙️ Параметры</button>
							</div>

							<div class="p-6">
								<!-- ВКЛАДКА: РЕДАКТОР -->
								<div x-show="tab === 'editor'">
									<div
										class="bg-[#7e8e9b] text-white text-[10px] uppercase font-bold px-3 py-1.5 inline-block mb-4 relative">
										Текст вопроса (<span x-text="getTypeName(question.type)"></span>)
										<div
											class="absolute right-[-10px] top-0 border-y-[12px] border-y-transparent border-l-[10px] border-l-[#7e8e9b]">
										</div>
									</div>

									<div class="border rounded-lg p-4 mb-6 bg-gray-50">
										<textarea :name="`questions[${qIndex}][text]`" x-model="question.text" required
											rows="3" class="w-full bg-transparent outline-none text-gray-700 text-lg"
											placeholder="Введите текст вопроса..."></textarea>
										<input type="hidden" :name="`questions[${qIndex}][type]`" :value="question.type">
									</div>

									<div
										class="bg-[#7e8e9b] text-white text-[10px] uppercase font-bold px-3 py-1.5 inline-block mb-4 relative">
										Изображение
										<div
											class="absolute right-[-10px] top-0 border-y-[12px] border-y-transparent border-l-[10px] border-l-[#7e8e9b]">
										</div>
									</div>
									<div
										class="mb-6 flex items-center gap-4 p-4 border-2 border-dashed border-gray-100 rounded-lg">
										<input type="file" :name="`questions[${qIndex}][image]`"
											class="text-xs text-gray-400">

										<!-- Предпросмотр, если картинка уже загружена (для страницы редактирования) -->
										<template x-if="question.image">
											<div class="relative">
												<img :src="'/storage/' + question.image"
													class="h-20 rounded border shadow-sm">
												<p class="text-[9px] text-gray-400 text-center mt-1">Текущее фото</p>
											</div>
										</template>
									</div>

									<div
										class="bg-[#7e8e9b] text-white text-[10px] uppercase font-bold px-3 py-1.5 inline-block mb-4 relative">
										Варианты ответов
										<div
											class="absolute right-[-10px] top-0 border-y-[12px] border-y-transparent border-l-[10px] border-l-[#7e8e9b]">
										</div>
									</div>
									<button type="button" x-show="!['free_form', 'file'].includes(question.type)"
										@click="addOption(qIndex)"
										class="ml-4 text-blue-500 text-[10px] font-bold uppercase hover:underline">добавить</button>

									<div class="mt-4">
										<table class="w-full text-xs">
											<thead>
												<tr class="text-gray-400 text-left border-b border-gray-100">
													<th class="w-8 pb-2 text-center">#</th>
													<template x-if="question.type === 'matching'">
														<th class="pb-2 px-2 w-1/2 uppercase tracking-tighter text-[9px]">
															Левая часть (Элемент)</th>
													</template>
													<template x-if="question.type === 'matching'">
														<th class="pb-2 px-2 w-1/2 uppercase tracking-tighter text-[9px]">
															Правая часть (Пара)</th>
													</template>
													<template
														x-if="!['matching', 'free_form', 'file', 'text', 'number'].includes(question.type)">
														<th class="pb-2 uppercase tracking-tighter text-[9px]">Текст
															варианта ответа</th>
													</template>
													<template
														x-if="['single_choice', 'multi_choice', 'image_choice'].includes(question.type)">
														<th
															class="w-20 pb-2 text-center uppercase tracking-tighter text-[9px]">
															Верно?</th>
													</template>
													<th class="w-10 pb-2"></th>
												</tr>
											</thead>
											<tbody class="divide-y divide-gray-50">
												<template x-for="(option, oIndex) in question.options" :key="oIndex">
													<tr class="group hover:bg-blue-50/30 transition-colors">
														<td class="py-3 text-gray-300 font-mono text-center"
															x-text="oIndex + 1"></td>

														<!-- ЛЕВАЯ КОЛОНКА / ОСНОВНОЙ ТЕКСТ -->
														<td class="py-2 px-1">
															<input type="text"
																:name="`questions[${qIndex}][options][${oIndex}][text]`"
																x-model="option.text"
																class="w-full outline-none p-1 border-b border-transparent focus:border-blue-300 bg-transparent"
																placeholder="Введите текст...">
														</td>

														<!-- ПРАВАЯ КОЛОНКА (Только для соответствия) -->
														<template x-if="question.type === 'matching'">
															<td class="py-2 px-2">
																<div class="flex items-center gap-2">
																	<span class="text-gray-300">↔</span>
																	<input type="text"
																		:name="`questions[${qIndex}][options][${oIndex}][match_text]`"
																		x-model="option.match_text"
																		class="w-full outline-none p-1 border-b border-orange-200 bg-orange-50/30 focus:bg-orange-50 focus:border-orange-400 rounded-sm"
																		placeholder="Пара к тексту...">
																</div>
															</td>
														</template>

														<!-- ГАЛОЧКА "ВЕРНО" (Для выбора) -->
														<template
															x-if="['single_choice', 'multi_choice', 'image_choice'].includes(question.type)">
															<td class="py-2 text-center">
																<input
																	:type="['single_choice', 'image_choice'].includes(question.type) ? 'radio' : 'checkbox'"
																	:name="['single_choice', 'image_choice'].includes(question.type) ? `questions[${qIndex}][correct]` : `questions[${qIndex}][options][${oIndex}][is_correct]`"
																	:value="oIndex" :checked="option.is_correct"
																	class="w-4 h-4 text-blue-600 cursor-pointer">
															</td>
														</template>

														<!-- КНОПКА УДАЛЕНИЯ -->
														<td class="py-2 text-right pr-2">
															<button type="button" @click="removeOption(qIndex, oIndex)"
																class="text-gray-300 hover:text-red-500 transition-opacity opacity-0 group-hover:opacity-100">
																🗑
															</button>
														</td>
													</tr>
												</template>
											</tbody>
										</table>
									</div>

									<!-- ВКЛАДКА: КОММЕНТАРИЙ -->
									<div x-show="tab === 'comment'">
										<textarea :name="`questions[${qIndex}][explanation]`"
											class="w-full border-2 border-dashed p-4 outline-none" rows="4"
											placeholder="Пояснение к ответу..."></textarea>
									</div>

									<!-- ВКЛАДКА: ПАРАМЕТРЫ -->
									<div x-show="tab === 'params'">
										<div class="space-y-3 p-4">
											<label class="flex items-center gap-3"><input type="checkbox"
													:name="`questions[${qIndex}][is_required]`" class="w-4 h-4"> <span
													class="text-sm">Обязательный вопрос</span></label>
											<label class="flex items-center gap-3"><input type="checkbox"
													:name="`questions[${qIndex}][shuffle_options]`" class="w-4 h-4"> <span
													class="text-sm">Перемешать ответы</span></label>
										</div>
									</div>

									<!-- Футер вопроса -->
									<div class="mt-6 pt-6 border-t flex justify-between items-center">
										<div class="flex items-center gap-2">
											<span class="text-[10px] text-gray-400 uppercase font-black">Баллов:</span>
											<input type="number" :name="`questions[${qIndex}][points]`"
												x-model="question.points"
												class="w-16 border rounded p-1 text-center font-bold text-blue-600">
										</div>
										<button type="button" @click="removeQuestion(qIndex)"
											class="text-xs text-red-500 font-bold uppercase hover:underline">Удалить</button>
									</div>
								</div>
							</div>
					</template>
				</div>

				<!-- ФИНАЛЬНАЯ КНОПКА СОХРАНЕНИЯ -->
				<div x-show="questions.length > 0"
					class="mt-10 p-6 bg-white rounded-xl border shadow-xl flex justify-end gap-4 sticky bottom-6 z-40">
					<a href="{{ route('dashboard') }}"
						class="px-8 py-3 text-gray-400 font-bold hover:text-gray-600 transition">ОТМЕНА</a>
					<button type="submit"
						class="bg-blue-600 hover:bg-blue-700 text-white px-12 py-3 rounded-xl font-black shadow-lg transition-transform active:scale-95">
						🚀 СОХРАНИТЬ ВЕСЬ ТЕСТ
					</button>
				</div>
			</form>
		</div>

		<!-- ПРАВАЯ ПАНЕЛЬ: ТИПЫ ВОПРОСОВ -->
		<div class="w-72 sticky top-24 space-y-1">
			<div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-2">Добавить вопрос</div>
			<template x-for="type in types" :key="type.id">
				<button type="button" @click="addQuestion(type.id)"
					class="w-full flex items-center justify-between p-3 bg-white border border-gray-100 hover:bg-blue-50 transition text-sm text-gray-600 group rounded shadow-sm">
					<span class="flex items-center gap-3">
						<span x-text="type.icon"></span>
						<span x-text="type.name"></span>
					</span>
					<span class="text-blue-300 font-bold text-lg">+</span>
				</button>
			</template>
		</div>

		<!-- МОДАЛКА ДИСЦИПЛИНЫ (ВЫНЕСЕНА ИЗ ФОРМЫ) -->
		<div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm"
			x-cloak>
			<div class="bg-white p-8 rounded-2xl shadow-2xl w-80">
				<p class="font-bold text-lg mb-4 text-gray-800">Новая дисциплина</p>
				<form action="{{ route('disciplines.store') }}" method="POST">
					@csrf
					<input type="text" name="name" required
						class="w-full border-2 border-gray-100 p-2 rounded-lg mb-4 outline-none focus:border-blue-500"
						placeholder="Название...">
					<div class="flex gap-2">
						<button type="button" @click="showModal = false"
							class="flex-1 py-2 text-gray-400 font-bold">Отмена</button>
						<button type="submit"
							class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold shadow-md">Создать</button>
					</div>
				</form>
			</div>
		</div>

	</div>
@endsection

@push('scripts')
	<script>
		function testCreator() {
			return {
				questions: [],
				showModal: false,
				types: [
					{ id: 'single_choice', name: 'Одиночный выбор', icon: '🔘' },
					{ id: 'multi_choice', name: 'Множественный выбор', icon: '☑️' },
					{ id: 'number', name: 'Ввод числа', icon: '🔢' },
					{ id: 'text', name: 'Ввод текста', icon: '🔤' },
					{ id: 'free_form', name: 'Ответ в свободной форме', icon: '📝' },
					{ id: 'fill_in_gaps', name: 'Заполнение пропусков', icon: '🧩' }, // Иконка пазла
					{ id: 'sequence', name: 'Последовательность', icon: '🪜' },
					{ id: 'matching', name: 'Соответствие', icon: '🔗' },
					{ id: 'file', name: 'Загрузка файла', icon: '📁' },
				],
				addQuestion(type) {
					let options = [];
					if (!['free_form', 'file'].includes(type)) {
						options = [{ text: '', is_correct: false, match_text: '' }, { text: '', is_correct: false, match_text: '' }];
					}
					this.questions.push({ type, text: '', points: 1, options });
					setTimeout(() => window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }), 100);
				},
				removeQuestion(index) { if (confirm('Удалить вопрос?')) this.questions.splice(index, 1); },
				addOption(qIndex) { this.questions[qIndex].options.push({ text: '', is_correct: false, match_text: '' }); },
				removeOption(qIndex, oIndex) { if (this.questions[qIndex].options.length > 1) this.questions[qIndex].options.splice(oIndex, 1); },
				getTypeName(id) { return this.types.find(t => t.id === id)?.name || id; }
			}
		}
	</script>
@endpush
