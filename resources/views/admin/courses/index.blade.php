<x-layouts.dashboard>
    <x-slot name="title">Course Management — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Courses</h1>
    </div>

    {{-- Search & Filter --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6">
        <div class="relative flex-1 max-w-md">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" placeholder="Search courses..." class="w-full bg-surface-700 border border-white/20 text-white placeholder-gray-500 rounded-xl py-3 pl-10 pr-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors">
        </div>
        <select class="bg-surface-700 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors" style="color-scheme:dark">
            <option value="">All Programs</option>
            @foreach($programs ?? [] as $program)
                <option value="{{ $program }}">{{ $program }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Course</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Instructor</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Program</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Enrolled</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Published</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($courses as $course)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-white font-medium">{{ $course->title }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $course->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $course->program ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $course->enrollments_count ?? $course->enrollments()->count() }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($course->is_published)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">Published</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Toggle Publish --}}
                                    <form method="POST" action="{{ route('admin.courses.toggle-publish', $course) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">
                                            {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                    </form>
                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">No courses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $courses->links() }}
</x-layouts.dashboard>
