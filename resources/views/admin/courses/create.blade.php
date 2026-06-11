<x-layouts.dashboard>
    <x-slot name="title">{{ __('Create Course') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.courses.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to courses') }}</a>
            <h1 class="text-2xl font-bold text-white mt-1">{{ __('Create New Course') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ __('Set up a new course on the platform.') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Course Title') }}</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('title') border-red-500/50 @enderror"
                       placeholder="{{ __('e.g. Introduction to Film Directing') }}">
                @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Description') }}</label>
                <textarea name="description" id="description" rows="5" required
                          class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('description') border-red-500/50 @enderror"
                          placeholder="{{ __('Describe what students will learn...') }}">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="course_type" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Course Type') }}</label>
                <select name="course_type" id="course_type"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('course_type') border-red-500/50 @enderror">
                    <option value="program" {{ old('course_type') === 'program' ? 'selected' : '' }}>{{ __('Program') }}</option>
                    <option value="sae_core" {{ old('course_type') === 'sae_core' ? 'selected' : '' }}>{{ __('SAE Core') }}</option>
                    <option value="university" {{ old('course_type') === 'university' ? 'selected' : '' }}>{{ __('University') }}</option>
                </select>
                @error('course_type') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="instructor_id" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Assign Instructor') }}</label>
                <select name="instructor_id" id="instructor_id" required
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('instructor_id') border-red-500/50 @enderror">
                    <option value="">{{ __('Select an instructor') }}</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }} ({{ $instructor->email }})</option>
                    @endforeach
                </select>
                @error('instructor_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="program" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Program') }}</label>
                <select name="program" id="program" required
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('program') border-red-500/50 @enderror">
                    <option value="">{{ __('Select a program') }}</option>
                    @foreach(['Film Production', 'Digital Media', 'Game Design', 'Audio Engineering'] as $program)
                        <option value="{{ $program }}" {{ old('program') === $program ? 'selected' : '' }}>{{ $program }}</option>
                    @endforeach
                </select>
                @error('program') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div x-data="{ preview: null }">
                <label class="block text-sm font-medium text-gray-300 mb-2">{{ __('Cover Image') }}</label>
                <label class="relative flex items-center justify-center w-full h-48 border-2 border-dashed border-surface-600 rounded-2xl cursor-pointer hover:border-brand-500/50 transition-all duration-200 bg-surface-700/30 overflow-hidden @error('cover_image') border-red-500/50 @enderror">
                    <div x-show="!preview" class="flex flex-col items-center gap-2 px-6 text-center">
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
                @error('cover_image') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-surface-700">
                <a href="{{ route('admin.courses.index') }}" class="text-sm text-gray-400 hover:text-white px-4 py-2 transition-colors">{{ __('Cancel') }}</a>
                <button type="submit" class="flex items-center gap-2 text-sm bg-brand-500 hover:bg-brand-600 text-white font-medium px-6 py-2.5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Create Course') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
