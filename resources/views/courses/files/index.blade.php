<x-layouts.dashboard>
    <x-slot name="title">{{ __('Course Files') }} — {{ $course->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to course') }}
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ __('Course Files') }}</h1>
                <p class="text-sm text-gray-400 mt-1">{{ $course->name }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-500/10 border border-green-500/20 rounded-xl px-4 py-3">
            <p class="text-sm text-green-400 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10">
                    <h2 class="text-lg font-semibold text-white">{{ __('All Files') }} ({{ $files->count() }})</h2>
                </div>
                @if($files->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-10 h-10 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <p class="text-gray-400">{{ __('No files yet') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Add files using the form on the right.') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-white/10">
                        @foreach($files as $file)
                            <div class="flex items-center justify-between px-6 py-4 hover:bg-surface-700/50 transition-colors">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-10 h-10 rounded-xl bg-surface-700 flex items-center justify-center shrink-0">
                                        @php
                                            $icon = match($file->mime_type) {
                                                'pdf' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                                                'video', 'mp4', 'webm' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                                                'image', 'jpg', 'jpeg', 'png' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                                                default => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                                            };
                                        @endphp
                                        <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-white truncate">{{ $file->filename }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $file->mime_type ?? __('Unknown type') }} · {{ $file->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-3">
                                    <a href="{{ $file->file_path }}" target="_blank"
                                       class="text-sm text-brand-400 hover:text-brand-300 transition-colors flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        {{ __('Open') }}
                                    </a>
                                    <form method="POST" action="{{ route('courses.files.destroy', [$course, $file]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this file?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-sm text-red-400 hover:text-red-300 transition-colors flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">{{ __('Add File') }}</h2>
                <form method="POST" action="{{ route('courses.files.store', $course) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Name') }}</label>
                        <input type="text" name="filename" value="{{ old('filename') }}" required
                               placeholder="e.g. Syllabus PDF"
                               class="input-dashboard">
                        @error('filename')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('File URL') }}</label>
                        <input type="url" name="file_path" value="{{ old('file_path') }}" required
                               placeholder="https://example.com/file.pdf"
                               class="input-dashboard">
                        @error('file_path')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Type') }}</label>
                        <select name="mime_type"
                                class="input-dashboard">
                            <option value="document" {{ old('mime_type') === 'document' ? 'selected' : '' }}>{{ __('Document') }}</option>
                            <option value="pdf" {{ old('mime_type') === 'pdf' ? 'selected' : '' }}>{{ __('PDF') }}</option>
                            <option value="video" {{ old('mime_type') === 'video' ? 'selected' : '' }}>{{ __('Video') }}</option>
                            <option value="image" {{ old('mime_type') === 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                            <option value="audio" {{ old('mime_type') === 'audio' ? 'selected' : '' }}>{{ __('Audio') }}</option>
                            <option value="link" {{ old('mime_type') === 'link' ? 'selected' : '' }}>{{ __('Link') }}</option>
                            <option value="other" {{ old('mime_type') === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                        </select>
                        @error('mime_type')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit"
                            class="w-full bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                        {{ __('Add File') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
