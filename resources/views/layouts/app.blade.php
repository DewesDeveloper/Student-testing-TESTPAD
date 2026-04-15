<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token для безопасности запросов -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'StudentTest') - Онлайн платформа</title>

    <!-- Стили: Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Скрипты: Alpine.js (для работы переключателей и вкладок) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Скрываем элементы Alpine до загрузки, чтобы не было "мерцания" */
        [x-cloak] { display: none !important; }

        /* Плавная прокрутка */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-[#f4f7f9] text-[#4e5e6a] min-h-screen flex">

    <!-- 1. САЙДБАР (Боковая панель) -->
    <!-- Мы подключаем его один раз здесь. Он фиксирован (fixed). -->
    @include('layouts.sidebar')

    <!-- 2. ОСНОВНАЯ ОБЛАСТЬ КОНТЕНТА -->
    <!-- ml-64 нужен, чтобы контент не заезжал под сайдбар шириной 64 единицы (16rem) -->
    <div class="flex-1 flex flex-col ml-64 min-h-screen">

        <!-- 3. ХЕДЕР (Верхняя панель) -->
        @include('layouts.header')

        <!-- 4. ГЛАВНЫЙ КОНТЕНТ СТРАНИЦЫ -->
        <main class="p-8 flex-1">
            <!-- Сюда вставляется код из @section('content') дочерних файлов -->
            @yield('content')
        </main>

        <!-- 5. ФУТЕР (Подвал) -->
        @include('layouts.footer')

    </div>

    <!-- Место для дополнительных скриптов конкретных страниц -->
    @stack('scripts')

</body>
</html>
