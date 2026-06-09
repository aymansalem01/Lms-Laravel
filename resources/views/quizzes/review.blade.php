<x-layouts.dashboard>
    <x-slot name="title">{{ __('Quiz Review') }} — {{ $quiz->title }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to quiz') }}
        </a>
        <h1 class="text-2xl font-bold text-white">{{ $quiz->title }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $course->name }} — {{ __('Quiz Review') }}</p>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Total Submissions') }}</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $totalSubmissions }} / {{ $students->count() }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Graded') }}</p>
            <p class="text-2xl font-bold text-green-400 mt-1">{{ $gradedCount }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Questions') }}</p>
            <p class="text-2xl font-bold text-brand-400 mt-1">{{ $quiz->questions_count }}</p>
        </div>
    </div>

    <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 text-xs uppercase tracking-wider border-b border-white/10">
                        <th class="px-5 py-3">{{ __('Student') }}</th>
                        <th class="px-5 py-3">{{ __('Status') }}</th>
                        <th class="px-5 py-3">{{ __('Score') }}</th>
                        <th class="px-5 py-3">{{ __('Attempts') }}</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($students as $student)
                        @php
                            $attempts = $student->quizAttempts;
                            $bestAttempt = $attempts->sortByDesc('score')->first();
                            $released = $attempts->firstWhere('released_at', '!==', null);
                            $status = match(true) {
                                $attempts->isEmpty() => 'not_submitted',
                                (bool) $released => 'graded',
                                default => 'submitted',
                            };
                        @endphp
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full gb flex items-center justify-center text-white text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-white">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($status === 'not_submitted')
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-gray-500/10 text-gray-400">{{ __('Not Submitted') }}</span>
                                @elseif($status === 'graded')
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-green-500/10 text-green-400">{{ __('Graded') }}</span>
                                @else
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-400">{{ __('Submitted') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($bestAttempt)
                                    <span class="text-gray-300 font-medium">{{ number_format($bestAttempt->score, 1) }} / {{ number_format($bestAttempt->max_score, 1) }}</span>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($attempts->isNotEmpty())
                                    <span class="text-gray-400">{{ $attempts->count() }}</span>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if($bestAttempt)
                                    <a href="{{ route('courses.quizzes.results', [$course, $quiz, $bestAttempt]) }}"
                                       class="text-sm bg-brand-600 hover:bg-brand-500 text-white font-medium rounded-xl px-4 py-1.5 transition-colors duration-200 inline-block">
                                        {{ __('View') }}
                                    </a>
                                @else
                                    <span class="text-xs text-gray-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="text-gray-400 text-sm">{{ __('No students enrolled.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.dashboard>
