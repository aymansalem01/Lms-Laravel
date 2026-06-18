<x-layouts.dashboard>
    <x-slot name="title">{{ __('Grading') }}</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">{{ __('Grading') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ __('Select a course to view assignments and grade submissions') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($courses as $course)
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5 hover:border-brand-500/50 hover:bg-surface-700/80 transition-all group">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-white font-semibold group-hover:text-brand-300 transition-colors">{{ $course->title }}</h3>
                </div>
                <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                    <span>{{ $course->assignments_count }} assignments</span>
                    <span>{{ $course->total_submissions_count }} submissions</span>
                    <span class="text-emerald-400">{{ $course->graded_submissions_count }} graded</span>
                </div>
                @if($course->quizzes_count > 0)
                    <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                        <span class="text-brand-400">{{ $course->quizzes_count }} quizzes</span>
                        <span>{{ $course->quiz_attempts_count }} attempts</span>
                    </div>
                @endif
                <div class="flex items-center gap-2 pt-2 border-t border-white/5">
                    <a href="{{ route('grading.assignments', $course) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Assignments</a>
                    @if($course->quizzes_count > 0)
                        <a href="{{ route('grading.quizzes', $course) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Quizzes</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <p class="text-lg">No courses found.</p>
            </div>
        @endforelse
    </div>
</x-layouts.dashboard>
