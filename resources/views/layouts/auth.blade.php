<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - StudentTest</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Логотип или название сверху -->
        <div class="text-center mb-8">
            <a href="/" class="text-3xl font-black text-indigo-600 tracking-tighter">StudentTest</a>
        </div>

        <!-- Контент страницы (форма) -->
        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
            @yield('content')
        </div>

        <!-- Ссылка назад на главную -->
        <div class="text-center mt-6">
            <a href="/" class="text-sm text-gray-400 hover:text-gray-600 transition">← Вернуться на главную</a>
        </div>
    </div>

</body>
</html>
