<x-layouts.dashboard>
    <x-slot name="title">Edit Live Session — Luminus LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('live.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to live sessions
        </a>
    </div>

    <div class="bg-surface-800 border border-white/10 rounded-xl p-6 max-w-2xl">
        <h1 class="text-xl font-bold text-white mb-6">Edit Session</h1>

        <form method="POST" action="{{ route('live.update', $session) }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">Session Title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $session->title) }}" placeholder="e.g. Week 4 Lecture" class="input-dashboard">
                @error('title')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="course_id" class="block text-sm font-medium text-gray-300 mb-1.5">Course</label>
                <select id="course_id" name="course_id" class="input-dashboard">
                    <option value="">Select a course</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ (old('course_id', $session->course_id) == $c->id) ? 'selected' : '' }}>{{ $c->title }}</option>
                    @endforeach
                </select>
                @error('course_id')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="scheduled_at" class="block text-sm font-medium text-gray-300 mb-1.5">Date & Time</label>
                <input id="scheduled_at" name="scheduled_at" type="datetime-local" value="{{ old('scheduled_at', $session->scheduled_at ? $session->scheduled_at->format('Y-m-d\TH:i') : '') }}" class="input-dashboard [color-scheme:dark]">
                @error('scheduled_at')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="provider" class="block text-sm font-medium text-gray-300 mb-1.5">Provider</label>
                <select id="provider" name="provider" class="input-dashboard">
                    <option value="whereby" {{ old('provider', $session->provider) === 'whereby' ? 'selected' : '' }}>Whereby</option>
                    <option value="livekit" {{ old('provider', $session->provider) === 'livekit' ? 'selected' : '' }}>LiveKit</option>
                    <option value="external" {{ old('provider', $session->provider) === 'external' ? 'selected' : '' }}>External</option>
                </select>
                @error('provider')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="duration" class="block text-sm font-medium text-gray-300 mb-1.5">Duration (minutes)</label>
                <input id="duration" name="duration" type="number" value="{{ old('duration', $session->duration) }}" placeholder="e.g. 60" min="1" max="1440" class="input-dashboard">
                @error('duration')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="room_url" class="block text-sm font-medium text-gray-300 mb-1.5">Room URL</label>
                <input id="room_url" name="room_url" type="url" value="{{ old('room_url', $session->room_url) }}" placeholder="https://whereby.com/your-room" class="input-dashboard">
                @error('room_url')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="recording_url" class="block text-sm font-medium text-gray-300 mb-1.5">Recording URL</label>
                <input id="recording_url" name="recording_url" type="url" value="{{ old('recording_url', $session->recording_url) }}" placeholder="https://..." class="input-dashboard">
                @error('recording_url')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('live.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</a>
                <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Update Session</button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>