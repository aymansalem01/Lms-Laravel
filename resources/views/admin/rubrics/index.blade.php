<x-layouts.dashboard>
    <x-slot name="title">Rubric Management — Luminus LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Rubrics</h1>
        <button @click="$dispatch('open-create-rubric')" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Rubric
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Title</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Course</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Instructor</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Assignments</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Created</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($rubrics as $rubric)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.rubrics.show', $rubric) }}" class="text-white font-medium hover:text-brand-300 transition-colors">{{ $rubric->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $rubric->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $rubric->course->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $rubric->assignments_count ?? 0 }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $rubric->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.rubrics.show', $rubric) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                                    <form method="POST" action="{{ route('admin.rubrics.destroy', $rubric) }}" onsubmit="return confirm('Delete this rubric permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">No rubrics found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $rubrics->links() }}

    {{-- Create Rubric Modal --}}
    <div x-data="{ open: false }" @open-create-rubric.window="open = true" x-show="open" x-cloak
         x-effect="open && $nextTick(() => $refs.rubricCourse.focus())"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Create Rubric</h3>
            <form method="POST" action="{{ route('admin.rubrics.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="rubric_course_id" class="block text-sm font-medium text-gray-300 mb-1.5">Course</label>
                    <select id="rubric_course_id" x-ref="rubricCourse" name="course_id" class="input-dashboard">
                        <option value="">Select course...</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="rubric_title" class="block text-sm font-medium text-gray-300 mb-1.5">Title</label>
                    <input id="rubric_title" name="title" type="text" placeholder="e.g. Essay Rubric" class="input-dashboard">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
