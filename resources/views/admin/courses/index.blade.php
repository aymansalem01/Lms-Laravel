<x-layouts.dashboard>
    <x-slot name="title">Course Management — Luminus LMS</x-slot>

    <div x-data="{ bulkOpen: false }" class="contents">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Courses</h1>
        <div class="flex items-center gap-2">
            <button @click="bulkOpen = true" class="inline-flex items-center gap-2 bg-surface-600 hover:bg-surface-500 text-gray-300 rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Bulk Create
            </button>
            <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Course
            </a>
        </div>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('admin.courses.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6">
        <div class="relative flex-1 max-w-md">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" placeholder="Search courses..." value="{{ request('search') }}" class="w-full bg-surface-700 border border-white/20 text-white placeholder-gray-500 rounded-xl py-3 pl-10 pr-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors">
        </div>
        <select name="program" onchange="this.form.submit()" class="bg-surface-700 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors" style="color-scheme:dark">
            <option value="">All Programs</option>
            @foreach($programs ?? [] as $program)
                <option value="{{ $program }}" {{ request('program') === $program ? 'selected' : '' }}>{{ $program }}</option>
            @endforeach
        </select>
    </form>

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
                                <a href="{{ route('admin.courses.show', $course) }}" class="text-white font-medium hover:text-brand-400 transition-colors">{{ $course->title }}</a>
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
                                    <a href="{{ route('admin.courses.edit', $course) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Edit</a>
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

    {{-- Bulk Create Modal --}}
    <div x-show="bulkOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="bulkOpen = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl" @click.away="bulkOpen = false">
            <h3 class="text-lg font-semibold text-white mb-4">Bulk Create Courses</h3>
            <p class="text-sm text-gray-400 mb-4">Upload a CSV file with course data. <a href="{{ route('admin.courses.bulk-create-example') }}" class="text-brand-400 hover:text-brand-300">Download example</a></p>
            <form method="POST" action="{{ route('admin.courses.bulk-create') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">CSV File</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" required
                           class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-500/20 file:text-brand-400 hover:file:bg-brand-500/30">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="bulkOpen = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Upload &amp; Create</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</x-layouts.dashboard>
