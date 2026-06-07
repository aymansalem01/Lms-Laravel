<x-layouts.dashboard>
    <x-slot name="title">{{ __('Edit Topic') }} - {{ $topic->title }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.content.lesson.show', [$course, $lesson]) }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to Lesson') }}</a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 max-w-2xl">
        <h1 class="text-xl font-bold text-white mb-6">{{ __('Edit Topic') }}</h1>

        <form method="POST" action="{{ route('courses.content.topics.update', [$course, $lesson, $topic]) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Topic Title') }}</label>
                <input type="text" name="title" required value="{{ $topic->title }}"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Type') }}</label>
                <select name="type" id="topicTypeEdit" required
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors"
                        x-data x-init="showFields('{{ $topic->type }}')"
                        @change="document.querySelectorAll('.topic-field').forEach(el => el.classList.add('hidden')); document.getElementById('field-' + $event.target.value)?.classList.remove('hidden')">
                    <option value="file" {{ $topic->type === 'file' ? 'selected' : '' }}>{{ __('File') }}</option>
                    <option value="link" {{ $topic->type === 'link' ? 'selected' : '' }}>{{ __('External Link') }}</option>
                    <option value="html" {{ $topic->type === 'html' ? 'selected' : '' }}>{{ __('Write Content') }}</option>
                    <option value="video" {{ $topic->type === 'video' ? 'selected' : '' }}>{{ __('Video Link') }}</option>
                    <option value="audio" {{ $topic->type === 'audio' ? 'selected' : '' }}>{{ __('Audio Link') }}</option>
                </select>
            </div>

            <div id="field-file" class="topic-field {{ $topic->type === 'file' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('File URL') }}</label>
                <input type="url" name="file_url" value="{{ $topic->file_url }}" placeholder="https://example.com/file.pdf"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div id="field-link" class="topic-field {{ $topic->type === 'link' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('External URL') }}</label>
                <input type="url" name="external_url" value="{{ $topic->external_url }}" placeholder="https://example.com"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div id="field-html" class="topic-field {{ $topic->type === 'html' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Content') }}</label>
                <textarea name="content" rows="10"
                          class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors font-mono">{{ $topic->content }}</textarea>
            </div>

            <div id="field-video" class="topic-field {{ $topic->type === 'video' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Video URL') }}</label>
                <input type="url" name="video_url" value="{{ $topic->video_url }}" placeholder="https://www.youtube.com/embed/..."
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div id="field-audio" class="topic-field {{ $topic->type === 'audio' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Audio URL') }}</label>
                <input type="url" name="audio_url" value="{{ $topic->audio_url }}" placeholder="https://example.com/audio.mp3"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>

            <div class="flex items-center gap-3 justify-end pt-2">
                <a href="{{ route('courses.content.lesson.show', [$course, $lesson]) }}" class="text-sm text-gray-400 hover:text-white px-4 py-2 transition-colors">{{ __('Cancel') }}</a>
                <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white rounded-lg px-6 py-2 text-sm font-medium transition-colors">{{ __('Update Topic') }}</button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
