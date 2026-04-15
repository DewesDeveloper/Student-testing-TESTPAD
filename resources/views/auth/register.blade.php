<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Регистрация - StudentTest</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<!-- Добавляем Alpine.js для работы переключателя -->
	<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center">

	<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md" x-data="{ role: 'student' }">
		<div class="text-center mb-8">
			<h1 class="text-3xl font-bold text-indigo-600">StudentTest</h1>
			<p class="text-gray-500">Создайте аккаунт, чтобы начать</p>
		</div>
		<!-- Блок общих ошибок -->
		@if ($errors->any())
			<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200">
				<div class="flex items-center gap-2 text-red-700 font-bold mb-1">
					<span>⚠️</span>
					<span>Произошла ошибка</span>
				</div>
				<ul class="list-disc list-inside text-sm text-red-600">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form action="/register" method="POST" class="space-y-4">
			@csrf

			<!-- Выбор роли -->
			<div class="flex gap-4 p-1 bg-gray-100 rounded-lg mb-6">
				<label class="flex-1 text-center cursor-pointer">
					<input type="radio" name="role" value="student" x-model="role" class="hidden">
					<div :class="role === 'student' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500'"
						class="py-2 rounded-md transition font-bold">Студент</div>
				</label>
				<label class="flex-1 text-center cursor-pointer">
					<input type="radio" name="role" value="teacher" x-model="role" class="hidden">
					<div :class="role === 'teacher' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500'"
						class="py-2 rounded-md transition font-bold">Преподаватель</div>
				</label>
			</div>

			<!-- Поле Группа (только для студента) -->
			<div x-show="role === 'student'" x-transition x-cloak>
				<label class="block text-sm font-medium text-gray-700">Группа <span
						class="text-red-500">*</span></label>
				<input type="text" name="group" :required="role === 'student'" placeholder="Например, ИСП-21"
					class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-indigo-500 outline-none transition-all">
			</div>

			<div>
				<label class="block text-sm font-medium text-gray-700">Имя и фамилия</label>
				<input type="text" name="name" required
					class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-indigo-500 outline-none">
			</div>

			<div>
				<label class="block text-sm font-medium text-gray-700">Email</label>
				<input type="email" name="email" required
					class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-indigo-500 outline-none">
			</div>

			<div>
				<label class="block text-sm font-medium text-gray-700">Пароль</label>
				<input type="password" name="password" required
					class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-indigo-500 outline-none">
			</div>

			<button type="submit"
				class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition duration-200 shadow-md">
				Зарегистрироваться
			</button>
		</form>

		<p class="text-center mt-6 text-sm text-gray-600">
			Уже есть аккаунт? <a href="/login" class="text-indigo-600 hover:underline">Войти</a>
		</p>
	</div>

</body>

</html>
