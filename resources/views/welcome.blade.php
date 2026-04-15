<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentTest - Онлайн тестирование</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">

    <!-- Навигация -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="text-2xl font-bold text-indigo-600">StudentTest</div>
                <div class="hidden md:flex space-x-8">
                    <a href="#" class="text-gray-700 hover:text-indigo-600">Тесты</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600">Результаты</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600">О нас</a>
                </div>
                <div>
                    <a href="/login" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">Войти</a>
					  <a href="/register" class="bg-white text-indigo-600 px-5 py-2 rounded-lg hover:bg-indigo-100 transition">Регистрация</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero секция -->
    <header class="py-16 bg-indigo-700 text-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4">Проверь свои знания онлайн</h1>
            <p class="text-xl text-indigo-100 mb-8">Сотни тестов по разным дисциплинам для студентов всех курсов.</p>
            <div class="flex justify-center gap-4">
                <a href="/login" class="bg-white text-indigo-700 font-bold px-8 py-3 rounded-full shadow-lg hover:bg-gray-100 transition">Начать тест</a>
                <a href="/login" class="border-2 border-white text-white font-bold px-8 py-3 rounded-full hover:bg-white hover:text-indigo-700 transition">Создать тест</a>
            </div>
        </div>
    </header>

    <!-- Секция с тестами -->
    <main class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Популярные тесты</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($featuredTests as $test)
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $test['title'] }}</h3>
                <p class="text-gray-600 mb-4">Количество вопросов: {{ $test['questions'] }}</p>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400">⏱ {{ $test['time'] }}</span>
                    <a href="#" class="text-indigo-600 font-semibold hover:underline">Пройти →</a>
                </div>
            </div>
            @endforeach
        </div>
    </main>

    <!-- Футер -->
    <footer class="bg-gray-800 text-gray-300 py-10 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} StudentTest Platform. Все права защищены.</p>
        </div>
    </footer>

</body>
</html>
