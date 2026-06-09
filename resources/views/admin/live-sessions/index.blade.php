<x-layouts.dashboard>
    <x-slot name="title">Live Sessions — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Live Sessions</h1>
        <button @click="$dispatch('open-create-session')" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Session
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] ?? $sessions->total() }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Upcoming</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['upcoming'] ?? 0 }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Past</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['past'] ?? 0 }}</p>
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
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Instructor</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Scheduled</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Provider</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Status</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.live-sessions.show', $session) }}" class="text-white font-medium hover:text-brand-300 transition-colors">{{ $session->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $session->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $session->course->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at)->format('M d, Y H:i') : '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-500/20 text-brand-300">{{ $session->provider ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php $isPast = $session->scheduled_at && \Carbon\Carbon::parse($session->scheduled_at)->isPast(); @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isPast ? 'bg-gray-500/20 text-gray-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                                    {{ $isPast ? 'Past' : 'Upcoming' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.live-sessions.show', $session) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                                    <form method="POST" action="{{ route('admin.live-sessions.destroy', $session) }}" onsubmit="return confirm('Delete this session?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">No sessions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $sessions->links() }}

    {{-- Create Session Modal --}}
    <div x-data="{ open: false }" @open-create-session.window="open = true" x-show="open" x-cloak
         x-effect="open && $nextTick(() => $refs.sessionCourse.focus())"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Create Session</h3>
            <form method="POST" action="{{ route('admin.live-sessions.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="session_course_id" class="block text-sm font-medium text-gray-300 mb-1.5">Course</label>
                    <select id="session_course_id" x-ref="sessionCourse" name="course_id" class="input-dashboard">
                        <option value="">Select course...</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="session_title" class="block text-sm font-medium text-gray-300 mb-1.5">Title</label>
                    <input id="session_title" name="title" type="text" placeholder="Session title" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="session_scheduled_at" class="block text-sm font-medium text-gray-300 mb-1.5">Scheduled At</label>
                    <input id="session_scheduled_at" name="scheduled_at" type="datetime-local" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="session_room_url" class="block text-sm font-medium text-gray-300 mb-1.5">Room URL</label>
                    <input id="session_room_url" name="room_url" type="url" placeholder="https://meet.example.com/room" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="session_provider" class="block text-sm font-medium text-gray-300 mb-1.5">Provider</label>
                    <select id="session_provider" name="provider" class="input-dashboard">
                        <option value="zoom">Zoom</option>
                        <option value="google-meet">Google Meet</option>
                        <option value="teams">Microsoft Teams</option>
                        <option value="other">Other</option>
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
