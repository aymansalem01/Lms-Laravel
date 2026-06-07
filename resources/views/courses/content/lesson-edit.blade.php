<x-layouts.dashboard>
    <x-slot name="title">{{ __('Edit Lesson') }} - {{ $course->title }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.content.index', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to Content') }}</a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 max-w-2xl">
        <h1 class="text-xl font-bold text-white mb-6">{{ __('Edit Lesson') }}</h1>
        <form method="POST" action="{{ route('courses.content.lesson.update', [$course, $lesson]) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Lesson Title') }}</label>
                <input type="text" name="title" value="{{ old('title', $lesson->title) }}" required
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Content') }}</label>
                <textarea name="content" rows="8"
                          class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">{{ old('content', $lesson->content) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Prerequisite Lesson') }} <span class="text-gray-500 font-normal">({{ __('optional') }})</span></label>
                <select name="prerequisite_lesson_id"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                    <option value="">{{ __('None') }}</option>
                    @foreach($course->modules as $m)
                        @foreach($m->lessons as $l)
                            @if($l->id !== $lesson->id)
                                <option value="{{ $l->id }}" @if(old('prerequisite_lesson_id', $lesson->prerequisite_lesson_id) == $l->id) selected @endif>{{ $m->title }} — {{ $l->title }}</option>
                            @endif
                        @endforeach
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">{{ __('Students must complete the prerequisite lesson before accessing this one.') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Video URL') }}</label>
                    <input type="url" name="video_url" value="{{ old('video_url', $lesson->video_url) }}"
                           class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Audio URL') }}</label>
                    <input type="url" name="audio_url" value="{{ old('audio_url', $lesson->audio_url) }}"
                           class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('File URL') }}</label>
                    <input type="url" name="file_url" value="{{ old('file_url', $lesson->file_url) }}"
                           class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Order Index') }}</label>
                    <input type="number" name="order_index" min="0" value="{{ old('order_index', $lesson->order_index) }}"
                           class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                </div>
            </div>

            <div class="flex items-center gap-3 justify-end pt-2">
                <a href="{{ route('courses.content.index', $course) }}" class="text-sm text-gray-400 hover:text-white px-4 py-2 transition-colors">{{ __('Cancel') }}</a>
                <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white rounded-lg px-6 py-2 text-sm font-medium transition-colors">{{ __('Update Lesson') }}</button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
