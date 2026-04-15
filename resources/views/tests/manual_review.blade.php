@extends('layouts.app')

@section('title', 'Ручная проверка')
@section('header_title', 'Проверка результата: ' . $result->student->name)

@section('content')
	<div class="max-w-5xl mx-auto" x-data="{
					scores: {},
					init() {
						@foreach($result->test->questions as $q)
							this.scores[{{ $q->id }}] = {{ $result->answers[$q->id]['score'] ?? 0 }};
						@endforeach
					},
					get total() {
						return Object.values(this.scores).reduce((a, b) => parseFloat(a) + parseFloat(b), 0).toFixed(2);
					}
				}">

		<!-- Шапка -->
		<div class="flex justify-between items-center mb-6">
			<h1 class="text-2xl font-bold text-gray-700">Проверка теста: <span
					class="text-blue-600">{{ $result->test->title }}</span></h1>
			<div class="flex items-center gap-4">
				<span
					class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $result->is_reviewed ? 'bg-green-500 text-white' : 'bg-orange-400 text-white' }}">
					{{ $result->is_reviewed ? 'Статус: Проверено' : 'Статус: Ожидает проверки' }}
				</span>
				<a href="{{ route('tests.manual-index', $result->test_id) }}"
					class="text-gray-400 hover:text-gray-600 text-sm font-bold">✖ Закрыть</a>
			</div>
		</div>

		@if($result->student_comment)
			<div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-xl mb-8">
				<h3 class="text-blue-800 font-bold uppercase text-xs mb-1">Комментарий студента:</h3>
				<p class="text-blue-900 italic">"{{ $result->student_comment }}"</p>
			</div>
		@endif

		<form action="{{ route('results.update-score', $result->id) }}" method="POST">
			@csrf
			<div class="space-y-6 mb-24">
				@foreach($result->test->questions as $index => $question)
					@php
						$data = $result->answers[$question->id] ?? null;
						$studentAns = $data['answer'] ?? null;
					@endphp

					<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
						<div class="p-6">
							<div class="flex gap-4 mb-6">
								<span
									class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center font-bold text-gray-500">{{ $index + 1 }}</span>
								<h3 class="text-lg font-bold text-gray-800">{{ $question->question_text }}</h3>
							</div>

							<!-- ВИЗУАЛИЗАЦИЯ ОТВЕТОВ -->
							<div class="space-y-3 mb-6">

								@if(in_array($question->type, ['single_choice', 'multi_choice', 'single', 'multi']))
									@foreach($question->options as $option)
										@php
											$picked = is_array($studentAns) ? in_array($option->id, $studentAns) : ($studentAns == $option->id);
											$classes = $option->is_correct ? 'border-green-500 bg-green-50' : ($picked ? 'border-red-500 bg-red-50' : 'border-gray-100');
										@endphp
										<div class="p-3 border-2 rounded-xl flex justify-between items-center {{ $classes }}">
											<span>{{ $option->option_text }}</span>
											@if($picked) <span class="text-[9px] font-bold uppercase">Выбор студента</span> @endif
										</div>
									@endforeach

								@elseif($question->type === 'matching')
									<div class="grid gap-2">
										@foreach($question->options as $option)
											@php $studentChoice = $studentAns[$option->id] ?? 'не выбрано'; @endphp
											<div
												class="flex items-center justify-between p-3 border rounded-xl {{ $studentChoice === $option->match_text ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
												<span class="font-bold">{{ $option->option_text }}</span>
												<span class="text-gray-400">→</span>
												<span>Студент: <b class="text-blue-600">{{ $studentChoice }}</b> | Правильно:
													<b>{{ $option->match_text }}</b></span>
											</div>
										@endforeach
									</div>

								@elseif($question->type === 'file')
									<div class="p-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 text-center">
										@if(isset($studentAns['path']))
											<p class="text-sm mb-3">Загружен файл: <b>{{ $studentAns['name'] }}</b></p>
											<a href="{{ asset('storage/' . $studentAns['path']) }}" target="_blank"
												class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition inline-block">📎
												Скачать файл</a>
										@else
											<p class="text-gray-400">Файл не загружен</p>
										@endif
									</div>

								@else
									<div class="p-4 bg-gray-50 rounded-xl border">
										<p class="text-xs text-gray-400 uppercase font-bold mb-1">Ответ студента:</p>
										<p class="text-lg font-bold text-blue-900">
											{{ is_array($studentAns) ? implode(', ', $studentAns) : ($studentAns ?: 'Нет ответа') }}
										</p>
									</div>
								@endif
							</div>

							<!-- ПОЛЕ БАЛЛОВ -->
							<div class="bg-gray-50 p-4 rounded-xl flex items-center justify-between border border-gray-200">
								<span class="text-xs text-gray-500 uppercase font-bold italic">Корректировка баллов за
									вопрос:</span>
								<div class="flex items-center gap-3">
									<input type="number" step="0.1" name="q_scores[{{ $question->id }}]"
										x-model="scores[{{ $question->id }}]" max="{{ $question->points }}"
										class="w-24 p-2 bg-white border-2 border-blue-100 rounded-lg text-center text-xl font-black text-blue-600 outline-none">
									<span class="text-gray-400 font-bold">/ {{ $question->points }}</span>
								</div>
							</div>
						</div>
						<div class="p-4 bg-gray-50 flex justify-end border-t border-gray-100">
							<a href="{{ route('results.question-pdf', ['result' => $result->id, 'question' => $question->id]) }}"
								class="flex items-center gap-2 text-[10px] font-bold text-blue-500 hover:text-blue-700 uppercase tracking-widest">
								<span>📄</span> Скачать отчет по этому вопросу
							</a>
						</div>
					</div>
				@endforeach
				<a href="{{ route('results.pdf', $result->id) }}"
					class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 p-2 px-4 rounded-lg text-gray-600 transition-colors text-xs font-bold"
					title="Скачать протокол в PDF">
					📄 Скачать PDF-отчет
				</a>
			</div>

			<!-- ПОДВАЛ -->
			<div class="fixed bottom-0 left-64 right-0 bg-indigo-900 p-6 shadow-2xl flex justify-between items-center z-50">
				<div class="text-white">
					<span class="text-indigo-300 text-xs uppercase font-bold">Итоговый пересчитанный результат:</span>
					<div class="flex items-baseline gap-2">
						<span class="text-4xl font-black" x-text="total"></span>
						<span class="text-indigo-400 text-xl font-bold">/ {{ $result->total_points }} баллов</span>
					</div>
				</div>
				<div class="flex gap-4">
					<button type="submit"
						class="bg-green-500 hover:bg-green-400 text-white px-10 py-3 rounded-xl font-black shadow-lg transition-all uppercase tracking-wider">Подтвердить
						и Сохранить</button>
				</div>
			</div>
		</form>
	</div>
@endsection
