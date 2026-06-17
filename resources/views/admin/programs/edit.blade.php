<x-layouts.dashboard>
    <x-slot name="title">Edit Program — Luminus LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.programs.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to programs
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Edit Details --}}
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-6">Program Details</h2>
            <form method="POST" action="{{ route('admin.programs.update', $program) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $program->name) }}" class="w-full bg-surface-700 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors">
                    @error('name')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full bg-surface-700 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors resize-none">{{ old('description', $program->description) }}</textarea>
                    @error('description')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Save Changes</button>
                </div>
            </form>
        </div>

        {{-- Manage Courses --}}
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-6">Assigned Courses</h2>

            <form method="POST" action="{{ route('admin.programs.courses.assign', $program) }}" class="mb-6">
                @csrf
                <div class="flex gap-2">
                    <select name="course_id" class="flex-1 bg-surface-700 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors">
                        <option value="">Select a course to assign...</option>
                        @foreach($availableCourses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-4 py-2.5 text-sm font-medium transition-colors shrink-0">Assign</button>
                </div>
            </form>

            @forelse($program->courses as $course)
                <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-500/20 flex items-center justify-center text-brand-300 text-xs font-bold">{{ strtoupper(substr($course->title, 0, 1)) }}</div>
                        <div>
                            <p class="text-sm text-white font-medium">{{ $course->title }}</p>
                            <p class="text-xs text-gray-500">{{ $course->instructor->name ?? 'No instructor' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.programs.courses.unassign', [$program, $course]) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors">Remove</button>
                    </form>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No courses assigned yet.</p>
            @endforelse
        </div>
    </div>
</x-layouts.dashboard>
