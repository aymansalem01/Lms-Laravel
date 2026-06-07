<x-layouts.dashboard>
    <x-slot name="title">New Topic — {{ $course->title }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.discussions.index', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to discussions
        </a>
    </div>

    <div class="bg-surface-800 border border-white/10 rounded-xl p-6 max-w-2xl">
        <h1 class="text-xl font-bold text-white mb-6">Create New Topic</h1>

        <form method="POST" action="{{ route('courses.discussions.store', $course) }}">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" placeholder="What's on your mind?" class="input-dashboard">
                @error('title')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-300 mb-1.5">Content</label>
                <textarea id="content" name="content" rows="6" placeholder="Write your topic content..." class="input-dashboard resize-none">{{ old('content') }}</textarea>
                @error('content')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('courses.discussions.index', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</a>
                <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Create Topic</button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
