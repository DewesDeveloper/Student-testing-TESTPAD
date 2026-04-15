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
		<h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Придумайте новый пароль</h2>

		<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
			@csrf
			<input type="hidden" name="token" value="{{ $token }}">

			<div>
				<label class="block text-sm font-medium text-gray-700">Ваш Email</label>
				<input type="email" name="email" value="{{ request('email') }}" required
					class="w-full mt-1 px-4 py-2 border rounded-xl bg-gray-50">
			</div>

			<div>
				<label class="block text-sm font-medium text-gray-700">Новый пароль</label>
				<input type="password" name="password" required
					class="w-full mt-1 px-4 py-2 border rounded-xl focus:ring-blue-500 outline-none">
			</div>

			<div>
				<label class="block text-sm font-medium text-gray-700">Подтвердите пароль</label>
				<input type="password" name="password_confirmation" required
					class="w-full mt-1 px-4 py-2 border rounded-xl focus:ring-blue-500 outline-none">
			</div>

			<button type="submit"
				class="w-full bg-blue-600 text-white py-2 rounded-xl font-bold hover:bg-blue-700 transition">
				Обновить пароль
			</button>
		</form>
	</div>
</body>
</body>

</html>
