<x-layouts.dashboard>
    <x-slot name="title">{{ __('Question Banks') }} — {{ $course->name }}</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                {{ __('Back to course') }}
            </a>
            <h1 class="text-2xl font-bold text-white">{{ __('Question Banks') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ $course->name }}</p>
        </div>
        <a href="{{ route('courses.question-bank.create', $course) }}" class="flex items-center gap-1.5 text-sm bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-4 py-2.5 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('New Bank') }}
        </a>
    </div>

    @forelse($banks as $bank)
        <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ $bank->name }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $bank->items->count() }} {{ __('questions') }}</p>
                </div>
                @if($bank->is_visible_to_all)
                    <span class="text-[11px] font-medium text-brand-400 bg-brand-500/10 px-2 py-0.5 rounded-full">{{ __('Global') }}</span>
                @endif
            </div>
            @if($bank->items->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-white/5">
                                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Type') }}</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Question') }}</th>
                                <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Points') }}</th>
                                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($bank->items as $item)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <span class="text-[11px] font-medium px-2 py-0.5 rounded-full
                                            {{ $item->type === 'multiple_choice' ? 'bg-brand-500/10 text-brand-400' : '' }}
                                            {{ $item->type === 'true_false' ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                                            {{ $item->type === 'short_answer' ? 'bg-blue-500/10 text-blue-400' : '' }}
                                            {{ $item->type === 'long_answer' ? 'bg-purple-500/10 text-purple-400' : '' }}">
                                            {{ str_replace('_', ' ', ucfirst($item->type)) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-gray-300 max-w-md truncate">{{ $item->question }}</td>
                                    <td class="px-5 py-3.5 text-center text-sm text-gray-400">{{ $item->points }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('courses.question-bank.edit', [$course, $item]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">{{ __('Edit') }}</a>
                                            <form method="POST" action="{{ route('courses.question-bank.destroy', [$course, $item]) }}" class="inline" onsubmit="return confirm('Delete this question?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">{{ __('Delete') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-8 text-center">
                    <p class="text-gray-500 text-sm">{{ __('No questions in this bank.') }}</p>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-surface-800 border border-white/10 rounded-2xl px-5 py-12 text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-surface-700 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-gray-400 text-sm">{{ __('No question banks yet.') }}</p>
            <p class="text-gray-500 text-xs mt-1">{{ __('Create a bank to add reusable questions for quizzes.') }}</p>
            <a href="{{ route('courses.question-bank.create', $course) }}" class="text-brand-400 hover:text-brand-300 text-sm mt-3 inline-block">{{ __('Create Bank') }}</a>
        </div>
    @endforelse
</x-layouts.dashboard>