<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Мои тесты</title>
</head>
<body class="bg-[#f4f7f9] p-8">

    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl text-gray-600">Все тесты</h1>
            <a href="/tests/create" class="bg-white border px-4 py-2 rounded shadow-sm text-gray-600 text-sm hover:bg-gray-50 flex items-center gap-2">
                <span class="text-blue-500 font-bold text-lg">+</span> Добавить
            </a>
        </div>

        <!-- Фильтры по дисциплинам -->
        <div class="flex gap-4 mb-8 overflow-x-auto pb-2">
            <a href="{{ route('tests.catalog') }}" class="px-4 py-2 bg-white rounded shadow-sm border text-sm {{ !request('discipline_id') ? 'border-blue-500 text-blue-500' : 'text-gray-600' }}">
                Все дисциплины
            </a>
            @foreach($disciplines as $d)
                <a href="?discipline_id={{ $d->id }}" class="px-4 py-2 bg-white rounded shadow-sm border text-sm {{ request('discipline_id') == $d->id ? 'border-blue-500 text-blue-500' : 'text-gray-600' }}">
                    {{ $d->name }} ({{ $d->tests_count }})
                </a>
            @endforeach
        </div>

        <!-- Сетка тестов -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($tests as $test)
            <div class="bg-white rounded shadow-sm overflow-hidden flex flex-col group border hover:border-blue-300 transition-all">
                <!-- Верхняя часть (серая заливка) -->
                <div class="bg-[#6c757d] h-40 p-4 relative">
                    <h3 class="text-white text-lg font-medium leading-tight">{{ $test->title }}</h3>
                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                         <span class="text-white cursor-pointer">⚙️</span>
                    </div>
                </div>

                <!-- Футер карточки (иконки и данные) -->
                <div class="p-3 bg-white flex flex-col gap-3">
                    <div class="flex justify-between items-center text-[13px] text-gray-500">
                        <div class="flex items-center gap-3">
                            <!-- Статус: замок -->
                            <span title="{{ $test->is_active ? 'Открыт' : 'Закрыт' }}">
                                {!! $test->is_active ? '<span class="text-green-500">🔓</span>' : '<span class="text-red-500">🔒</span>' !!}
                            </span>
                            <!-- Кол-во людей -->
                            <div class="flex items-center gap-1">
                                <span class="text-xs">👥</span>
                                <span>{{ $test->results->count() }}</span>
                            </div>
                        </div>
                        <!-- Дата -->
                        <span>{{ $test->created_at->format('d.m.Y') }}</span>
                    </div>

                    <!-- Нижние иконки -->
                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <div class="flex gap-4">
                            <span class="text-gray-400 cursor-pointer hover:text-blue-500" title="Папка">📂</span>
                        </div>
                        <div class="flex gap-4">
                             <a href="{{ route('test.take', $test->id) }}" title="Информация" class="text-gray-400 hover:text-blue-500">ℹ️</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</body>
</html>
