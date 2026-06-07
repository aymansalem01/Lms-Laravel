<x-layouts.dashboard>
    <x-slot name="title">{{ $lesson->title }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.lessons.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to lessons
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-start justify-between mb-4">
                    <h2 class="text-xl font-bold text-white">{{ $lesson->title }}</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Edit</a>
                        <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('Delete this lesson?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Delete</button>
                        </form>
                    </div>
                </div>

                @if($lesson->content)
                    <div class="prose prose-invert max-w-none text-gray-300 text-sm leading-relaxed mb-4">
                        {!! $lesson->content !!}
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($lesson->video_url)
                        <div class="bg-surface-700/50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Video URL</p>
                            <a href="{{ $lesson->video_url }}" target="_blank" class="text-brand-400 hover:text-brand-300 text-sm break-all">{{ $lesson->video_url }}</a>
                        </div>
                    @endif
                    @if($lesson->audio_url)
                        <div class="bg-surface-700/50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Audio URL</p>
                            <a href="{{ $lesson->audio_url }}" target="_blank" class="text-brand-400 hover:text-brand-300 text-sm break-all">{{ $lesson->audio_url }}</a>
                        </div>
                    @endif
                    @if($lesson->file_url)
                        <div class="bg-surface-700/50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">File URL</p>
                            <a href="{{ $lesson->file_url }}" target="_blank" class="text-brand-400 hover:text-brand-300 text-sm break-all">{{ $lesson->file_url }}</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Progress Stats --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Progress</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-500/20 flex items-center justify-center text-brand-300 font-bold">{{ $completionCount ?? 0 }}</div>
                    <div>
                        <p class="text-white font-medium">{{ $completionCount ?? 0 }} completed</p>
                        <p class="text-gray-500 text-xs">Out of {{ $lesson->progress->count() }} total enrollments</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Module</h4>
                <p class="text-white text-sm">{{ $lesson->module->title ?? '—' }}</p>
            </div>
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Course</h4>
                <p class="text-white text-sm">{{ $lesson->module->course->title ?? '—' }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $lesson->module->course->instructor->name ?? '—' }}</p>
            </div>
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Order Index</h4>
                <p class="text-white text-sm">{{ $lesson->order_index ?? '—' }}</p>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
