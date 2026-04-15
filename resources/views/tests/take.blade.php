<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $test->title }} - Прохождение теста</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
	<style>
		[x-cloak] {
			display: none !important;
		}

		.no-select {
			user-select: none;
		}

		.nav-grid::-webkit-scrollbar {
			height: 4px;
		}

		.nav-grid::-webkit-scrollbar-thumb {
			background: #cbd5e1;
			border-radius: 10px;
		}
	</style>
</head>

<body class="bg-[#f0f2f5] min-h-screen {{ $test->prevent_copy ? 'no-select' : '' }}" @if($test->prevent_copy)
oncopy="return false" oncontextmenu="return false" @endif>

	<div x-data="testApp()" x-init="init()" x-cloak class="max-w-4xl mx-auto min-h-screen flex flex-col">

		<!-- 1. НАЧАЛЬНЫЙ ЭКРАН -->
		<template x-if="!started">
			<div class="flex-1 flex items-center justify-center p-4">
				<div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-hidden border border-gray-200">
					<div class="bg-gray-50 p-4 border-b text-center text-gray-400 text-sm italic">{{ $test->title }}
					</div>
					<div class="p-10 text-center">
						@if($test->description)
							<h1 class="text-3xl font-light text-gray-800 mb-8 italic leading-tight">{{ $test->description }}
							</h1>
						@endif

						<div
							class="text-left bg-gray-50 p-6 rounded-xl border border-gray-100 mb-10 text-gray-600 italic leading-relaxed">
							{!! $test->instruction ? nl2br(e($test->instruction)) : 'Инструкция отсутствует. Нажмите кнопку ниже для старта.' !!}
						</div>

						<button @click="startTest()"
							class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-xl transition-all transform hover:scale-[1.02] text-xl">
							Далее →
						</button>

						<div class="flex justify-center gap-6 text-[11px] text-gray-400 border-t mt-8 pt-6 italic">
							@if($test->author) <span>Автор: <b>{{ $test->author }}</b></span> @endif
							@if($test->source) <span>Источник: <b>{{ $test->source }}</b></span> @endif
						</div>
					</div>
				</div>
			</div>
		</template>

		<!-- 2. ЭКРАН ТЕСТИРОВАНИЯ -->
		<template x-if="started">
			<div class="flex-1 flex flex-col">
				<!-- ФИКСИРОВАННАЯ ШАПКА -->
				<header class="bg-white shadow-sm sticky top-0 z-50 border-b">
					<div class="p-4 flex justify-between items-center bg-gray-50/50">
						<h2 class="font-bold text-gray-700 truncate mr-4 text-sm uppercase tracking-wider">
							{{ $test->title }}</h2>
						@if($test->show_time || $test->limit_time)
							<div
								class="bg-white px-4 py-1 rounded-full font-mono font-bold border border-orange-200 text-orange-600 shadow-sm">
								⏱ <span x-text="formatTime(timer)"></span>
							</div>
						@endif
					</div>

					<!-- Progress Bar -->
					<div class="w-full bg-gray-100 h-1">
						<div class="bg-blue-500 h-1 transition-all duration-500" :style="`width: ${progress}%`"></div>
					</div>

					<!-- Сетка навигации -->
					<div class="p-3 flex gap-2 overflow-x-auto nav-grid bg-white">
						<template x-for="(q, index) in totalQuestions" :key="index">
							<button @click="currentQuestion = index" :class="{
                                        'bg-blue-600 text-white border-blue-600 ring-2 ring-blue-100': currentQuestion === index,
                                        'bg-green-500 text-white border-green-600': isAnswered(index) && currentQuestion !== index,
                                        'bg-white text-gray-400 border-gray-200': !isAnswered(index) && currentQuestion !== index
                                    }"
								class="min-w-[32px] h-8 rounded border text-[11px] font-black transition-all flex-shrink-0"
								x-text="index + 1"></button>
						</template>
					</div>
				</header>

				<!-- ОБЛАСТЬ ВОПРОСА -->
				<main class="flex-1 p-4 md:p-8">
					<form id="testForm" action="{{ route('test.submit', $test->id) }}" method="POST"
						enctype="multipart/form-data">
						@csrf
						@foreach($questions as $index => $question)
							<div x-show="currentQuestion === {{ $index }}" x-transition.opacity.duration.300ms
								class="space-y-6">
								<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-10">
									<div class="text-blue-500 font-black text-xs uppercase tracking-widest mb-4">
										Вопрос <span x-text="currentQuestion + 1"></span> из <span
											x-text="totalQuestions"></span>
									</div>
									<h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-10 leading-snug">
										{{ $question->question_text }}</h3>

									<div class="space-y-4">
										<!-- ВЫБОР (Одиночный / Множественный) -->
										@if(in_array($question->type, ['single_choice', 'multi_choice', 'single', 'multi']))
											@foreach($question->options as $option)
												<label
													class="flex items-center p-4 border-2 rounded-xl cursor-pointer hover:bg-blue-50 transition-all group border-gray-100"
													:class="isOptionSelected({{ $question->id }}, {{ $option->id }}) ? 'border-blue-500 bg-blue-50' : ''">
													<input
														type="{{ in_array($question->type, ['single_choice', 'single']) ? 'radio' : 'checkbox' }}"
														name="q[{{ $question->id }}]{{ in_array($question->type, ['multi_choice', 'multi']) ? '[]' : '' }}"
														value="{{ $option->id }}"
														@change="markAsAnswered({{ $index }}, 'choice', {{ $question->id }}, {{ $option->id }})"
														class="w-5 h-5 text-blue-600 border-gray-300">
													<span
														class="ml-4 text-gray-700 font-medium group-hover:text-blue-700">{{ $option->option_text }}</span>
												</label>
											@endforeach

											<!-- ТЕКСТ / ЧИСЛО -->
										@elseif(in_array($question->type, ['text', 'number']))
											<input type="{{ $question->type }}" name="q[{{ $question->id }}]"
												@input="markAsAnswered({{ $index }}, 'input', {{ $question->id }})"
												class="w-full p-5 border-2 rounded-xl focus:border-blue-500 outline-none text-lg bg-gray-50 focus:bg-white transition-all"
												placeholder="Введите ваш ответ...">

											<!-- СВОБОДНАЯ ФОРМА -->
										@elseif($question->type === 'free_form')
											<textarea name="q[{{ $question->id }}]" rows="6"
												@input="markAsAnswered({{ $index }}, 'input', {{ $question->id }})"
												class="w-full p-5 border-2 rounded-xl focus:border-blue-500 outline-none text-lg bg-gray-50"
												placeholder="Напишите развернутый ответ..."></textarea>

											<!-- ПОСЛЕДОВАТЕЛЬНОСТЬ -->
										@elseif($question->type === 'sequence')
											<div class="space-y-3">
												@foreach($question->options->shuffle() as $option)
													<div class="flex items-center gap-4 bg-white p-4 border rounded-xl shadow-sm">
														<input type="number" name="q[{{ $question->id }}][{{ $option->id }}]"
															@input="markAsAnswered({{ $index }}, 'sequence', {{ $question->id }})"
															class="w-14 p-2 border-2 border-blue-100 rounded-lg text-center font-bold outline-none"
															placeholder="№">
														<span class="text-gray-700">{{ $option->option_text }}</span>
													</div>
												@endforeach
											</div>

											<!-- СООТВЕТСТВИЕ -->
										@elseif($question->type === 'matching')
											@php $matches = $question->options->pluck('match_text')->filter()->unique()->shuffle(); @endphp
											<div class="space-y-3">
												@foreach($question->options as $option)
													<div
														class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 border bg-gray-50 rounded-xl">
														<span class="text-gray-700 font-bold">{{ $option->option_text }}</span>
														<select name="q[{{ $question->id }}][{{ $option->id }}]"
															@change="markAsAnswered({{ $index }}, 'matching', {{ $question->id }})"
															class="p-2 border rounded-lg text-sm bg-white focus:border-blue-500 outline-none w-full md:w-1/2">
															<option value="">-- выберите пару --</option>
															@foreach($matches as $match)
																<option value="{{ $match }}">{{ $match }}</option>
															@endforeach
														</select>
													</div>
												@endforeach
											</div>
										@endif
									</div>
								</div>
							</div>
						@endforeach

						<!-- Блок комментария -->
						@if($test->allow_comments)
							<div x-show="currentQuestion === totalQuestions - 1" class="mt-10">
								<div class="bg-indigo-50 border-2 border-indigo-100 rounded-2xl p-6">
									<label class="block text-indigo-900 font-bold mb-3 italic">💬 Ваш комментарий к
										тесту:</label>
									<textarea name="student_comment" rows="3"
										class="w-full p-4 border-none rounded-xl shadow-inner outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
								</div>
							</div>
						@endif
					</form>
				</main>

				<!-- ФУТЕР УПРАВЛЕНИЯ -->
				<footer class="bg-white border-t p-6 flex justify-between items-center sticky bottom-0 z-50">
					<button @click="prevQuestion()" :disabled="currentQuestion === 0"
						class="px-8 py-3 text-gray-500 font-black uppercase text-xs tracking-widest disabled:opacity-20 transition-opacity">
						← Назад
					</button>

					<div class="flex gap-4">
						<template x-if="currentQuestion < totalQuestions - 1">
							<button @click="nextQuestion()"
								class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all active:scale-95">
								Далее →
							</button>
						</template>
						<template x-if="currentQuestion === totalQuestions - 1">
							<button @click="finishTest()"
								class="bg-green-600 hover:bg-green-700 text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-green-200 transition-all active:scale-95">
								Завершить
							</button>
						</template>
					</div>
				</footer>
			</div>
		</template>
	</div>

	<script>
		function testApp() {
			return {
				started: false,
				currentQuestion: 0,
				totalQuestions: {{ $questions->count() }},
				timer: {{ ($test->time_limit ?: 20) * 60 }},
				progress: 0,
				answeredFlags: {}, // {index: true/false}
				selectionMap: {}, // {qId: [option_ids]}

				init() {
					// Начальная инициализация
				},

				startTest() {
					this.started = true;
					if ({{ $test->show_time || $test->limit_time ? 'true' : 'false' }}) {
						const interval = setInterval(() => {
							if (this.timer > 0) this.timer--;
							else {
								clearInterval(interval);
								this.finishTest(true);
							}
						}, 1000);
					}
				},

				markAsAnswered(index, type, qId, optId = null) {
					let isAnswered = false;

					if (type === 'choice') {
						// Используем селектор ^= чтобы найти q[ID] и q[ID][]
						const checked = document.querySelectorAll(`input[name^="q[${qId}]"]:checked`);
						isAnswered = checked.length > 0;

						// Обновляем карту выбора для визуальной подсветки
						this.selectionMap[qId] = Array.from(checked).map(i => parseInt(i.value));
					}
					else if (type === 'input') {
						const input = document.querySelector(`[name="q[${qId}]"]`);
						isAnswered = input && input.value.trim().length > 0;
					}
					else if (type === 'sequence' || type === 'matching') {
						const inputs = document.querySelectorAll(`[name^="q[${qId}]"]`);
						isAnswered = Array.from(inputs).every(i => i.value.trim() !== "");
					}

					this.answeredFlags[index] = isAnswered;
					this.updateProgress();
				},

				isAnswered(index) {
					return this.answeredFlags[index] === true;
				},

				isOptionSelected(qId, optId) {
					return this.selectionMap[qId] && this.selectionMap[qId].includes(optId);
				},

				updateProgress() {
					const count = Object.values(this.answeredFlags).filter(v => v === true).length;
					this.progress = Math.round((count / this.totalQuestions) * 100);
				},

				nextQuestion() {
					if ({{ $test->confirm_next ? 'true' : 'false' }}) {
						if (!confirm('Перейти к следующему вопросу?')) return;
					}
					if (this.currentQuestion < this.totalQuestions - 1) {
						this.currentQuestion++;
						window.scrollTo(0, 0);
					}
				},

				prevQuestion() {
					if (this.currentQuestion > 0) {
						this.currentQuestion--;
						window.scrollTo(0, 0);
					}
				},

				finishTest(force = false) {
					if (!force && {{ $test->require_all_answers ? 'true' : 'false' }}) {
						const count = Object.values(this.answeredFlags).filter(v => v === true).length;
						if (count < this.totalQuestions) {
							alert('Вы должны ответить на ВСЕ вопросы!');
							return;
						}
					}

					if (force || confirm('Завершить тестирование?')) {
						document.getElementById('testForm').submit();
					}
				},

				formatTime(seconds) {
					const m = Math.floor(seconds / 60);
					const s = seconds % 60;
					return `${m}:${s.toString().padStart(2, '0')}`;
				}
			}
		}
	</script>
</body>

</html>
