<x-layouts.dashboard>
    <x-slot name="title">Live Sessions — Luminus LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Live Sessions</h1>
        @if(auth()->user()->role !== 'student')
            <button x-data @click="$refs.createModal.showModal()" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Schedule Session
            </button>
        @endif
    </div>

    <style>
        dialog::backdrop { background: rgba(25, 25, 35, 0.92); }
    </style>
    <dialog x-ref="createModal" class="bg-transparent p-0 rounded-2xl max-w-lg w-full mx-auto">
        <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-white">{{ __('Schedule a Session') }}</h2>
                <button @click="$refs.createModal.close()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('live.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="modal_title" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Session Title') }}</label>
                    <input id="modal_title" name="title" type="text" value="{{ old('title') }}" placeholder="e.g. Week 4 Lecture" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="modal_course_id" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Course') }}</label>
                    <select id="modal_course_id" name="course_id" class="input-dashboard">
                        <option value="">{{ __('Select a course') }}</option>
                        @foreach(auth()->user()->taughtCourses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="modal_scheduled_at" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Date & Time') }}</label>
                    <input id="modal_scheduled_at" name="scheduled_at" type="datetime-local" value="{{ old('scheduled_at') }}" class="input-dashboard [color-scheme:dark]">
                </div>
                <div class="mb-4">
                    <label for="modal_provider" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Provider') }}</label>
                    <select id="modal_provider" name="provider" class="input-dashboard">
                        <option value="whereby" {{ old('provider') === 'whereby' ? 'selected' : '' }}>Whereby</option>
                        <option value="livekit" {{ old('provider') === 'livekit' ? 'selected' : '' }}>LiveKit</option>
                        <option value="external" {{ old('provider') === 'external' ? 'selected' : '' }}>External</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="modal_room_url" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Room URL (optional)') }}</label>
                    <input id="modal_room_url" name="room_url" type="url" value="{{ old('room_url') }}" placeholder="https://whereby.com/your-room" class="input-dashboard">
                    <p class="mt-1 text-xs text-gray-500">{{ __('Leave blank to auto-generate a room.') }}</p>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" @click="$refs.createModal.close()" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">{{ __('Cancel') }}</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">{{ __('Schedule') }}</button>
                </div>
            </form>
        </div>
    </dialog>

    <div x-data="{ tab: 'upcoming' }" class="mb-6">
        <div class="flex gap-1 bg-surface-800 rounded-xl p-1 w-fit mb-6">
            <button @click="tab = 'upcoming'" :class="tab === 'upcoming' ? 'bg-brand-600 text-white' : 'text-gray-400 hover:text-white'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">Upcoming</button>
            <button @click="tab = 'past'" :class="tab === 'past' ? 'bg-brand-600 text-white' : 'text-gray-400 hover:text-white'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">Past</button>
        </div>

        <div x-show="tab === 'upcoming'" x-cloak>
            @forelse($upcoming as $session)
                <div class="bg-surface-800 border border-white/10 rounded-xl p-6 mb-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-semibold">{{ $session->title }}</h3>
                            <p class="text-sm text-gray-400 mt-1">{{ $session->course->title ?? 'General' }}</p>
                            <div class="flex items-center gap-3 mt-3 text-sm text-gray-500">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $session->scheduled_at ? $session->scheduled_at->format('M d, Y g:i A') : 'Not scheduled' }}
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">Upcoming</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if(auth()->user()->isInstructorOrAdmin())
                                <a href="{{ route('live.edit', $session) }}" class="inline-flex items-center gap-1.5 bg-surface-600 hover:bg-surface-500 text-gray-300 rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('live.destroy', $session) }}" onsubmit="return confirm('Delete this session?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            @endif
                            <a href="{{ isset($course) ? route('courses.live.show', [$course, $session]) : route('live.show', $session) }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-5 py-2 text-sm font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Join
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                    <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <p class="text-gray-400 font-medium">No upcoming sessions</p>
                    <p class="text-gray-600 text-sm mt-1">Check back later for scheduled live sessions.</p>
                </div>
            @endforelse
        </div>

        <div x-show="tab === 'past'" x-cloak>
            @forelse($past as $session)
                <div class="bg-surface-800 border border-white/10 rounded-xl p-6 mb-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-semibold">{{ $session->title }}</h3>
                            <p class="text-sm text-gray-400 mt-1">{{ $session->course->title ?? 'General' }}</p>
                            <div class="flex items-center gap-3 mt-3 text-sm text-gray-500">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $session->scheduled_at ? $session->scheduled_at->format('M d, Y g:i A') : 'Not scheduled' }}
                                </span>
                            </div>
                            <div class="mt-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">Past</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if(auth()->user()->isInstructorOrAdmin())
                                <a href="{{ route('live.edit', $session) }}" class="inline-flex items-center gap-1.5 bg-surface-600 hover:bg-surface-500 text-gray-300 rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('live.destroy', $session) }}" onsubmit="return confirm('Delete this session?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            @endif
                            @if($session->recording_url)
                                <a href="{{ $session->recording_url }}" target="_blank" class="inline-flex items-center gap-2 bg-surface-600 hover:bg-surface-700 text-white rounded-xl px-5 py-2 text-sm font-medium transition-colors shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    View Recording
                                </a>
                            @else
                                <span class="text-sm text-gray-600 shrink-0">No recording</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                    <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <p class="text-gray-400 font-medium">No past sessions</p>
                    <p class="text-gray-600 text-sm mt-1">Past session recordings will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.dashboard>
