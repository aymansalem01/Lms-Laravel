<x-layouts.dashboard>
    <x-slot name="title">{{ $course->title }} — {{ __('Quiz Grading') }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('grading.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to courses
        </a>
        <div class="flex items-center justify-between mt-1">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $course->title }}</h1>
                <p class="text-sm text-gray-400 mt-1">Select a quiz to review attempts</p>
            </div>
        </div>
    </div>

    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Quiz</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Attempts</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Graded</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Pending</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($quizzes as $quiz)
                        @php $pending = $quiz->attempts_count - $quiz->graded_count; @endphp
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-white font-medium">{{ $quiz->title }}</p>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $quiz->attempts_count }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-emerald-400">{{ $quiz->graded_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="{{ $pending > 0 ? 'text-amber-400' : 'text-gray-500' }}">{{ $pending }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('courses.quizzes.review', [$course, $quiz]) }}"
                                       class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-600 transition-colors">
                                        Review
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">No quizzes found for this course.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.dashboard>
