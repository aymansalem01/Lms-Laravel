<x-layouts.dashboard>
    <x-slot name="title">Grading — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Grading</h1>
        <p class="text-sm text-gray-500">Select a course to view assignments and grade submissions</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($courses as $course)
            <a href="{{ route('admin.grading.assignments', $course) }}"
               class="group bg-surface-800 border border-white/10 rounded-xl p-5 hover:border-brand-500/50 hover:bg-surface-700/80 transition-all block">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-white font-semibold group-hover:text-brand-300 transition-colors">{{ $course->title }}</h3>
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-brand-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span>{{ $course->assignments_count }} assignments</span>
                    <span>{{ $course->total_submissions_count }} submissions</span>
                    <span class="text-emerald-400">{{ $course->graded_submissions_count }} graded</span>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <p class="text-lg">No courses found.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>
</x-layouts.dashboard>
