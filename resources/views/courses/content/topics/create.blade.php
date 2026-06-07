<x-layouts.dashboard>
    <x-slot name="title">{{ __('Create Topic') }} - {{ $lesson->title }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.content.lesson.show', [$course, $lesson]) }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to Lesson') }}</a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 max-w-2xl">
        <h1 class="text-xl font-bold text-white mb-6">{{ __('Create Topic') }}</h1>
        <p class="text-sm text-gray-400 mb-6">{{ __('Lesson') }}: <span class="text-white">{{ $lesson->title }}</span></p>

        <form method="POST" action="{{ route('courses.content.topics.store', [$course, $lesson]) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Topic Title') }}</label>
                <input type="text" name="title" required placeholder="{{ __('e.g. Introduction Video') }}"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Type') }}</label>
                <select name="type" id="topicType" required
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors"
                        x-data @change="document.querySelectorAll('.topic-field').forEach(el => el.classList.add('hidden')); document.getElementById('field-' + $event.target.value)?.classList.remove('hidden')">
                    <option value="file">{{ __('File') }}</option>
                    <option value="link">{{ __('External Link') }}</option>
                    <option value="html">{{ __('Write Content') }}</option>
                    <option value="video">{{ __('Video Link') }}</option>
                    <option value="audio">{{ __('Audio Link') }}</option>
                </select>
            </div>

            <div id="field-file" class="topic-field hidden">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('File URL') }}</label>
                <input type="url" name="file_url" placeholder="https://example.com/file.pdf"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div id="field-link" class="topic-field hidden">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('External URL') }}</label>
                <input type="url" name="external_url" placeholder="https://example.com"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div id="field-html" class="topic-field hidden">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Content') }}</label>
                <textarea name="content" rows="10"
                          class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors font-mono"
                          placeholder="{{ __('Write your content here...') }}"></textarea>
            </div>

            <div id="field-video" class="topic-field hidden">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Video URL') }}</label>
                <input type="url" name="video_url" placeholder="https://www.youtube.com/embed/..."
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div id="field-audio" class="topic-field hidden">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Audio URL') }}</label>
                <input type="url" name="audio_url" placeholder="https://example.com/audio.mp3"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div class="flex items-center gap-3 justify-end pt-2">
                <a href="{{ route('courses.content.lesson.show', [$course, $lesson]) }}" class="text-sm text-gray-400 hover:text-white px-4 py-2 transition-colors">{{ __('Cancel') }}</a>
                <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white rounded-lg px-6 py-2 text-sm font-medium transition-colors">{{ __('Create Topic') }}</button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
