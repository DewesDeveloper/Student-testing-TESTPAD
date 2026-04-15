<label class="relative inline-flex items-center cursor-pointer">
    <input type="checkbox" name="{{ $name }}" id="{{ $name }}" class="hidden peer" {{ $value ? 'checked' : '' }}>
    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-500 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
</label>
