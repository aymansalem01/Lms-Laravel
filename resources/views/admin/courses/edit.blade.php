<x-layouts.dashboard>
    <x-slot name="title">{{ __('Edit Course') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.courses.show', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to course') }}</a>
            <h1 class="text-2xl font-bold text-white mt-1">{{ __('Edit Course') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ __('Update course details and settings.') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data" class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Course Title') }}</label>
                <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}" required
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('title') border-red-500/50 @enderror">
                @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Description') }}</label>
                <textarea name="description" id="description" rows="5" required
                          class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('description') border-red-500/50 @enderror">{{ old('description', $course->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="course_type" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Course Type') }}</label>
                <select name="course_type" id="course_type"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('course_type') border-red-500/50 @enderror">
                    <option value="program" {{ old('course_type', $course->course_type) === 'program' ? 'selected' : '' }}>{{ __('Program') }}</option>
                    <option value="sae_core" {{ old('course_type', $course->course_type) === 'sae_core' ? 'selected' : '' }}>{{ __('Luminus Core') }}</option>
                    <option value="university" {{ old('course_type', $course->course_type) === 'university' ? 'selected' : '' }}>{{ __('University') }}</option>
                </select>
                @error('course_type') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="program" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Program') }}</label>
                <select name="program" id="program" required
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('program') border-red-500/50 @enderror">
                    <option value="">{{ __('Select a program') }}</option>
                    @foreach(['Film Production', 'Digital Media', 'Game Design', 'Audio Engineering'] as $program)
                        <option value="{{ $program }}" {{ old('program', $course->program) === $program ? 'selected' : '' }}>{{ $program }}</option>
                    @endforeach
                </select>
                @error('program') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div x-data="{ preview: null }">
                <label class="block text-sm font-medium text-gray-300 mb-2">{{ __('Cover Image') }}</label>
                <div class="relative group">
                    <label class="relative flex items-center justify-center w-full h-48 border-2 border-dashed border-surface-600 rounded-2xl cursor-pointer hover:border-brand-500/50 transition-all duration-200 bg-surface-700/30 overflow-hidden @error('cover_image') border-red-500/50 @enderror">
                        @if($course->cover_image_url)
                            <img src="{{ $course->cover_image_url }}" class="absolute inset-0 w-full h-full object-cover" x-show="!preview">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200" x-show="!preview">
                                <div class="flex flex-col items-center gap-1.5">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-xs text-white/80 font-medium">{{ __('Change Image') }}</span>
                                </div>
                            </div>
                        @endif
                        <div x-show="!preview && {{ $course->cover_image_url ? 'false' : 'true' }}" class="flex flex-col items-center gap-2 px-6 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-surface-700 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-300 font-medium">{{ __('Upload cover image') }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('PNG, JPG or WebP · 2MB max') }}</p>
                            </div>
                            <span class="text-[11px] font-medium text-brand-400 bg-brand-500/10 px-3 py-1 rounded-full">{{ __('Browse') }}</span>
                        </div>
                        <template x-if="preview">
                            <img :src="preview" class="absolute inset-0 w-full h-full object-cover">
                        </template>
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                               @change="const f = $event.target.files[0]; if (f) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(f); }"
                               class="hidden">
                    </label>
                    <div class="flex items-center gap-2 mt-2.5">
                        <template x-if="preview">
                            <button type="button" @click="preview = null; $el.closest('[x-data]').querySelector('input[type=file]').value = ''"
                                    class="text-xs text-red-400 hover:text-red-300 transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                {{ __('Remove') }}
                            </button>
                        </template>
                    </div>
                </div>
                @error('cover_image') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Status') }}</label>
                <select name="status" id="status"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors">
                    <option value="draft" {{ old('status', !$course->is_published ? 'draft' : '') === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                    <option value="published" {{ old('status', $course->is_published ? 'published' : '') === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-surface-700">
                <a href="{{ route('admin.courses.show', $course) }}" class="text-sm text-gray-400 hover:text-white px-4 py-2 transition-colors">{{ __('Cancel') }}</a>
                <button type="submit" class="flex items-center gap-2 text-sm bg-brand-500 hover:bg-brand-600 text-white font-medium px-6 py-2.5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
