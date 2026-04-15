<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title') - StudentTest</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center p-4">
	<div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
		<h2 class="text-2xl font-bold mb-4 text-gray-800 text-center">Сброс пароля</h2>
		<p class="text-gray-500 text-sm mb-6 text-center">Введите ваш Email, и мы отправим ссылку для восстановления.
		</p>

		@if (session('status'))
			<div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border border-green-200">
				{{ session('status') }}
			</div>
		@endif

		<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
			@csrf
			<div>
				<label class="block text-sm font-medium text-gray-700">Email адрес</label>
				<input type="email" name="email" required
					class="w-full mt-1 px-4 py-2 border rounded-xl focus:ring-blue-500 outline-none">
			</div>
			<button type="submit"
				class="w-full bg-blue-600 text-white py-2 rounded-xl font-bold hover:bg-blue-700 transition">
				Отправить ссылку
			</button>
		</form>
		<a href="/" class="text-sm text-gray-400 hover:text-gray-600 transition">← Вернуться на главную</a>
	</div>
</body>
</html>
