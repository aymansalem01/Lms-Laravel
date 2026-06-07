<x-layouts.dashboard>
    <x-slot name="title">{{ __('Quizzes') }} — {{ $course->name }}</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $course->name }} — {{ __('Quizzes') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ count($quizzes) }} {{ __('quizzes') }}</p>
        </div>
        @if(auth()->user()->isInstructorOrAdmin())
            <div class="flex items-center gap-3">
                <a href="{{ route('courses.question-bank.index', $course) }}"
                   class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    {{ __('Question Bank') }}
                </a>
                <a href="{{ route('courses.quizzes.create', $course) }}"
                   class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Create Quiz') }}
                </a>
            </div>
        @endif
    </div>

    @if(count($quizzes) === 0)
        <div class="bg-surface-800 border border-white/10 rounded-2xl p-12 text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-gray-400 text-lg mb-2">{{ __('No quizzes yet') }}</p>
            <p class="text-gray-500 text-sm">{{ __('Quizzes will appear here once created.') }}</p>
            @if(auth()->user()->role === 'instructor')
                <a href="{{ route('courses.quizzes.create', $course) }}" class="inline-block mt-4 bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                    {{ __('Create the first quiz') }}
                </a>
            @endif
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($quizzes as $quiz)
                @php
                    $isInstructor = auth()->user()->role === 'instructor';
                    $bestAttempt = !$isInstructor ? $quiz->attempts->where('user_id', auth()->id())->sortByDesc('score')->first() : null;
                    $questionTypes = $quiz->questions->pluck('type')->unique();
                    $typeBadge = match(true) {
                        $questionTypes->count() === 1 && $questionTypes->first() === 'multiple_choice' => ['MC', 'brand'],
                        $questionTypes->count() === 1 && in_array($questionTypes->first(), ['short_answer', 'long_answer']) => ['Essay', 'coral'],
                        $questionTypes->isEmpty() => ['No Q', 'gray'],
                        default => ['Mixed', 'purple'],
                    };
                @endphp
                <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}"
                   class="bg-surface-800 border border-white/10 rounded-xl p-5 hover:border-brand-500/50 transition-all duration-200 group">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <h3 class="text-white font-semibold group-hover:text-brand-300 transition-colors">{{ $quiz->title }}</h3>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-{{ $typeBadge[1] }}-500/10 text-{{ $typeBadge[1] }}-400">{{ $typeBadge[0] }}</span>
                                @if($quiz->is_published)
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-green-500/10 text-green-400">{{ __('Published') }}</span>
                            @else
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-yellow-500/10 text-yellow-400">{{ __('Draft') }}</span>
                            @endif
                    </div>
                    <p class="text-sm text-gray-400 line-clamp-2 mb-3">{{ $quiz->description }}</p>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ count($quiz->questions) }} {{ __('questions') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            {{ $quiz->max_attempts }} {{ __('attempts') }}
                        </span>
                        @if(!$isInstructor && $bestAttempt)
                            <span class="flex items-center gap-1.5 text-brand-400 font-medium">
                                {{ __('Best') }}: {{ number_format($bestAttempt->score, 1) }}
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.dashboard>
