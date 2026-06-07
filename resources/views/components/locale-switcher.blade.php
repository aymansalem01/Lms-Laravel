<div class="relative" x-data="{ localeOpen: false }" @click.outside="localeOpen = false">
    <button @click="localeOpen = !localeOpen"
            class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg text-xs font-medium text-gray-400 hover:text-white hover:bg-surface-700 transition-colors uppercase">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ app()->getLocale() }}</span>
        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="localeOpen" x-cloak
         @click.away="localeOpen = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="absolute right-0 mt-2 w-28 bg-surface-800 border border-surface-700 rounded-xl shadow-xl overflow-hidden z-50">
        <form method="POST" action="{{ route('locale.switch') }}">
            @csrf
            <input type="hidden" name="locale" value="en">
            <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm {{ app()->getLocale() === 'en' ? 'text-brand-300 bg-brand-500/10' : 'text-gray-300 hover:text-white hover:bg-surface-700' }} transition-colors">
                <span class="text-base">🇬🇧</span>
                <span>English</span>
            </button>
        </form>
        <form method="POST" action="{{ route('locale.switch') }}">
            @csrf
            <input type="hidden" name="locale" value="ar">
            <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-sm {{ app()->getLocale() === 'ar' ? 'text-brand-300 bg-brand-500/10' : 'text-gray-300 hover:text-white hover:bg-surface-700' }} transition-colors">
                <span class="text-base">🇸🇦</span>
                <span>العربية</span>
            </button>
        </form>
    </div>
</div>
