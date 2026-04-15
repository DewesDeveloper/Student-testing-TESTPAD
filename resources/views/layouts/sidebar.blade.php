<aside class="w-64 bg-[#2d3a4b] text-gray-300 flex-shrink-0 flex flex-col fixed h-full z-20 shadow-xl">
    <div class="p-5 bg-[#25313f] text-white font-bold flex items-center gap-3 text-xl">
        <span class="bg-blue-600 p-1 rounded text-sm">✔</span> StudentTest
    </div>

    <nav class="flex-1 overflow-y-auto py-6">
        <div class="px-6 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Основное</div>
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-6 py-3 hover:bg-[#38485a] {{ request()->routeIs('dashboard') ? 'bg-[#1e2833] text-white border-l-4 border-blue-500 shadow-inner' : '' }}">
            <span>🏠</span> Дашборд
        </a>

        {{-- Показываем меню редактора только если мы в контексте теста и НЕ на главной --}}
        @if(isset($test) && $test->id && !request()->routeIs('dashboard', 'tests.create'))
            <div class="px-6 py-2 mt-6 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Редактор теста</div>

            <a href="{{ route('tests.show', $test->id) }}"
                class="flex items-center gap-3 px-6 py-3 hover:bg-[#38485a] {{ request()->routeIs('tests.show') ? 'bg-[#38485a] text-white border-l-4 border-blue-500' : '' }}">
                <span>⚙️</span> Управление
            </a>

            <a href="{{ route('tests.settings', $test->id) }}"
                class="flex items-center gap-3 px-6 py-3 hover:bg-[#38485a] {{ request()->routeIs('tests.settings') ? 'bg-[#38485a] text-white border-l-4 border-blue-500' : '' }}">
                <span>🔧</span> Настройки
            </a>

            <a href="{{ route('tests.start-page', $test->id) }}"
                class="flex items-center gap-3 px-6 py-3 hover:bg-[#38485a] {{ request()->routeIs('tests.start-page') ? 'bg-[#38485a] text-white border-l-4 border-blue-500' : '' }}">
                <span>📄</span> Начальная страница
            </a>

            <a href="{{ route('tests.manual-index', $test->id) }}"
                class="flex items-center gap-3 px-6 py-3 hover:bg-[#38485a] {{ request()->routeIs('tests.manual-index*') || request()->routeIs('results.review') ? 'bg-[#38485a] text-white border-l-4 border-blue-500' : '' }}">
                <span>👋</span> Ручная проверка
            </a>

            <a href="{{ route('tests.result-settings', $test->id) }}"
                class="flex items-center gap-3 px-6 py-3 hover:bg-[#38485a] {{ request()->routeIs('tests.result-settings') ? 'bg-[#38485a] text-white border-l-4 border-blue-500' : '' }}">
                <span>🏆</span> Результат
            </a>

            <a href="{{ route('tests.statistics', $test->id) }}"
                class="flex items-center gap-3 px-6 py-3 hover:bg-[#38485a] {{ request()->routeIs('tests.statistics') ? 'bg-[#38485a] text-white border-l-4 border-blue-500' : '' }}">
                <span>📊</span> Статистика
            </a>
        @endif

        <div class="mt-auto px-6 py-10">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-bold flex items-center gap-2">
                    <span>🚪</span> ВЫЙТИ
                </button>
            </form>
        </div>
    </nav>
</aside>
