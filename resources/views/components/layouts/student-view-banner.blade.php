@if($studentView)
<div class="fixed top-0 left-0 right-0 z-[9999] bg-amber-500/90 dark:bg-amber-600/90 text-white px-4 py-2 flex items-center justify-between text-sm shadow-lg">
    <span class="flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        {{ __('You are viewing as a student') }}
    </span>
    <form method="POST" action="{{ route('view.exit') }}">
        @csrf
        <button type="submit" class="text-xs font-semibold uppercase tracking-wider bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg transition-colors">
            {{ __('Exit View') }}
        </button>
    </form>
</div>
@endif
