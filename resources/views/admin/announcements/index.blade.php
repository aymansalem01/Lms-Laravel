<x-layouts.dashboard>
    <x-slot name="title">Announcement Management — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Announcements</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.announcements.export') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <a href="{{ route('admin.announcements.export-example') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Example CSV
            </a>
            <button @click="$dispatch('open-create-announcement')" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Announcement
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6">
        <select class="input-dashboard">
            <option value="">All Courses</option>
            @foreach($courses ?? [] as $course)
                <option value="{{ $course->id }}">{{ $course->title }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Title</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Course</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Author</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Priority</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Dismissals</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Date</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($announcements as $announcement)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.announcements.show', $announcement) }}" class="text-white font-medium hover:text-brand-300 transition-colors">{{ $announcement->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $announcement->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $announcement->author->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($announcement->priority === 'urgent') bg-red-500/20 text-red-400
                                    @elseif($announcement->priority === 'high') bg-amber-500/20 text-amber-400
                                    @elseif($announcement->priority === 'low') bg-blue-500/20 text-blue-400
                                    @else bg-purple-500/20 text-purple-400
                                    @endif">
                                    {{ ucfirst($announcement->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $announcement->dismissals_count ?? 0 }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $announcement->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.announcements.show', $announcement) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                                    <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">No announcements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $announcements->links() }}

    {{-- Create Announcement Modal --}}
    <div x-data="{ open: false }" @open-create-announcement.window="open = true" x-show="open" x-cloak
         x-effect="open && $nextTick(() => $refs.announcementTitle.focus())"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Create Announcement</h3>
            <form method="POST" action="{{ route('admin.announcements.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="announcement_title" class="block text-sm font-medium text-gray-300 mb-1.5">Title</label>
                    <input id="announcement_title" x-ref="announcementTitle" name="title" type="text" placeholder="Announcement title" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="announcement_content" class="block text-sm font-medium text-gray-300 mb-1.5">Content</label>
                    <textarea id="announcement_content" name="content" rows="4" placeholder="Announcement content..." class="input-dashboard resize-none"></textarea>
                </div>
                <div class="mb-4">
                    <label for="announcement_course_id" class="block text-sm font-medium text-gray-300 mb-1.5">Course (optional)</label>
                    <select id="announcement_course_id" name="course_id" class="input-dashboard">
                        <option value="">All Courses</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="announcement_priority" class="block text-sm font-medium text-gray-300 mb-1.5">Priority</label>
                    <select id="announcement_priority" name="priority" class="input-dashboard">
                        <option value="low">Low</option>
                        <option value="normal" selected>Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
