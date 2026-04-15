<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Вход - StudentTest</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center">

	<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
		<div class="text-center mb-8">
			<h1 class="text-3xl font-bold text-indigo-600">StudentTest</h1>
			<p class="text-gray-500">С возвращением!</p>
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
		<form action="/login" method="POST" class="space-y-4">
			@csrf

			<div>
				<label class="block text-sm font-medium text-gray-700">Email</label>
				<input type="email" name="email" required
					class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500 outline-none">
			</div>

			<div>
				<label class="block text-sm font-medium text-gray-700">Пароль</label>
				<input type="password" name="password" required
					class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500 outline-none">
			</div>

			<div class="flex items-center justify-between text-sm">
				<label class="flex items-center text-gray-600">
					<input type="checkbox" class="mr-2"> Запомнить меня
				</label>
				<a href="{{ route('password.request') }}" class="text-indigo-600 hover:underline">Забыли пароль?</a>
			</div>

			<button type="submit"
				class="w-full bg-indigo-600 text-white py-2 rounded-lg font-bold hover:bg-indigo-700 transition duration-200">
				Войти
			</button>
		</form>

		<p class="text-center mt-6 text-sm text-gray-600">
			Нет аккаунта? <a href="/register" class="text-indigo-600 hover:underline">Регистрация</a>
		</p>
	</div>

</body>

</html>
