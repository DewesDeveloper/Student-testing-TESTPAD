<header class="h-16 bg-white border-b flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm">
    <div>
        @yield('header_title')
    </div>
    <div class="flex items-center gap-4 text-sm">
        <div class="flex flex-col text-right">
            <span class="font-bold text-gray-700">{{ Auth::user()->name }}</span>
            <span class="text-[10px] uppercase text-indigo-500 font-bold tracking-widest">{{ Auth::user()->role }}</span>
        </div>
        <div class="w-10 h-10 bg-indigo-100 border border-indigo-200 rounded-full flex items-center justify-center text-indigo-600 font-bold">
            {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</header>
