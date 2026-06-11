<x-layouts.dashboard>
    <x-slot name="title">{{ $course->title }} — Admin</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.courses.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to Courses') }}</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-surface-800 border border-white/10 rounded-xl p-6">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $course->title }}</h1>
                    <p class="text-sm text-gray-400 mt-1">{{ $course->instructor->name ?? '—' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.courses.edit', $course) }}" class="text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Edit</a>
                    <form method="POST" action="{{ route('admin.courses.toggle-publish', $course) }}">
                        @csrf
                        <button type="submit" class="text-xs px-3 py-1.5 rounded-lg font-medium {{ $course->is_published ? 'bg-amber-500/20 text-amber-400' : 'bg-emerald-500/20 text-emerald-400' }} transition-colors">
                            {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors">Delete</button>
                    </form>
                </div>
            </div>

            @if($course->description)
                <p class="text-sm text-gray-500 mb-4">{{ $course->description }}</p>
            @endif

            <div class="grid grid-cols-3 gap-4 pt-4 border-t border-white/10">
                <div class="text-center">
                    <p class="text-2xl font-bold text-white">{{ $course->enrollments_count ?? $course->enrollments->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Enrolled</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-white">{{ $totalSubmissions ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Submissions</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-white">{{ $gradedSubmissions ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Graded</p>
                </div>
            </div>
        </div>

        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Reassign Instructor</h3>
            <form method="POST" action="{{ route('admin.courses.reassign', $course) }}" class="space-y-3">
                @csrf @method('PUT')
                <select name="instructor_id" class="input-dashboard">
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ $course->instructor_id === $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors">Update Instructor</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Enrolled Students ({{ $course->enrollments->count() }})</h3>
            <div class="space-y-2">
                @forelse($course->enrollments as $enrollment)
                    <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-surface-700/50">
                        <div class="w-7 h-7 rounded-full gb flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr($enrollment->student->name ?? '?', 0, 1)) }}</div>
                        <span class="text-sm text-gray-300">{{ $enrollment->student->name ?? 'Unknown' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No enrolled students</p>
                @endforelse
            </div>
        </div>

        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Recent Submissions</h3>
            <div class="space-y-2">
                @forelse($course->assignments->flatMap->submissions->sortByDesc('created_at')->take(10) as $submission)
                    <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-surface-700/50">
                        <div class="min-w-0">
                            <p class="text-sm text-gray-300 truncate">{{ $submission->student->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->assignment->title ?? '—' }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $submission->status === 'graded' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                            {{ $submission->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No submissions yet</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.dashboard>
