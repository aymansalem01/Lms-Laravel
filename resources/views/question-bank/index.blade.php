<x-layouts.dashboard>
    <x-slot name="title">{{ __('Question Bank') }} — {{ $course->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to course') }}
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ __('Question Bank') }}</h1>
                <p class="text-sm text-gray-400 mt-1">{{ $course->name }} — {{ $items->total() }} {{ __('questions') }}</p>
            </div>
            <a href="{{ route('courses.question-bank.create', $course) }}"
               class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-4 py-2 text-sm transition-colors duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add Question') }}
            </a>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($items as $item)
        <div class="bg-surface-800 border border-white/10 rounded-2xl p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider
                            @switch($item->type)
                                @case('multiple_choice') bg-blue-500/20 text-blue-300 border border-blue-500/30 @break
                                @case('true_false') bg-purple-500/20 text-purple-300 border border-purple-500/30 @break
                                @case('short_answer') bg-amber-500/20 text-amber-300 border border-amber-500/30 @break
                                @case('long_answer') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 @break
                            @endswitch">
                            {{ __(ucwords(str_replace('_', ' ', $item->type))) }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $item->points }} pts</span>
                        <span class="text-[10px] text-gray-600 font-mono">{{ $item->usages->count() }} {{ __('uses') }}</span>
                    </div>
                    <p class="text-sm text-white leading-relaxed">{{ $item->question }}</p>
                    @if($item->type === 'multiple_choice' && $item->options)
                    <div class="mt-2 space-y-1">
                        @foreach($item->options as $optIndex => $option)
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <span class="w-4 text-center text-gray-600">{{ chr(65 + $optIndex) }}</span>
                            <span>{{ $option }}</span>
                            @if((string)$item->correct_answer === (string)$optIndex)
                            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('courses.question-bank.edit', [$course, $item]) }}"
                       class="text-gray-500 hover:text-brand-400 transition-colors p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('courses.question-bank.destroy', [$course, $item]) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                        @csrf @method('DELETE')
                        <button class="text-gray-500 hover:text-red-400 transition-colors p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="rounded-2xl border border-dashed border-white/10 p-12 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-surface-700 flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <p class="text-gray-400 text-sm mb-1">{{ __('No questions in the bank yet.') }}</p>
            <a href="{{ route('courses.question-bank.create', $course) }}" class="text-brand-400 hover:text-brand-300 text-sm transition-colors">{{ __('Add your first question') }}</a>
        </div>
        @endforelse
    </div>

    @if($items->hasPages())
    <div class="mt-6">
        {{ $items->links() }}
    </div>
    @endif
</x-layouts.dashboard>
