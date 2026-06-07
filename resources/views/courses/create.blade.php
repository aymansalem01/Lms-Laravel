<x-layouts.dashboard>
    <x-slot name="title">{{ __('Create Course') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">{{ __('Create New Course') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ __('Set up a new course for your students.') }}</p>
        </div>

        <form method="POST" action="{{ route('courses.store') }}" class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">
            @csrf

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Course Title') }}</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('title') border-red-500/50 @enderror"
                       placeholder="{{ __('e.g. Introduction to Film Directing') }}">
                @error('title')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Description') }}</label>
                <textarea name="description" id="description" rows="5" required
                          class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('description') border-red-500/50 @enderror"
                          placeholder="{{ __('Describe what students will learn in this course...') }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Course Type --}}
            <div>
                <label for="course_type" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Course Type') }}</label>
                <select name="course_type" id="course_type"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('course_type') border-red-500/50 @enderror">
                    <option value="program" {{ old('course_type') === 'program' ? 'selected' : '' }}>{{ __('Program') }}</option>
                    <option value="sae_core" {{ old('course_type') === 'sae_core' ? 'selected' : '' }}>{{ __('SAE Core') }}</option>
                    <option value="university" {{ old('course_type') === 'university' ? 'selected' : '' }}>{{ __('University') }}</option>
                </select>
                @error('course_type')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Assign Instructor --}}
            <div>
                <label for="instructor_id" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Assign Instructor') }}</label>
                <select name="instructor_id" id="instructor_id" required
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('instructor_id') border-red-500/50 @enderror">
                    <option value="">{{ __('Select an instructor') }}</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }} ({{ $instructor->email }})</option>
                    @endforeach
                </select>
                @error('instructor_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Program --}}
            <div>
                <label for="program" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Program') }}</label>
                <select name="program" id="program" required
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('program') border-red-500/50 @enderror">
                    <option value="">{{ __('Select a program') }}</option>
                    @foreach(['Film Production', 'Digital Media', 'Game Design', 'Audio Engineering'] as $program)
                        <option value="{{ $program }}" {{ old('program') === $program ? 'selected' : '' }}>{{ $program }}</option>
                    @endforeach
                </select>
                @error('program')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cover image URL --}}
            <div>
                <label for="cover_image_url" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Cover Image URL') }}</label>
                <input type="url" name="cover_image_url" id="cover_image_url" value="{{ old('cover_image_url') }}"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('cover_image_url') border-red-500/50 @enderror"
                       placeholder="{{ __('https://example.com/image.jpg') }}">
                <p class="mt-1 text-xs text-gray-500">{{ __('Optional. A gradient will be used if none provided.') }}</p>
                @error('cover_image_url')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-surface-700">
                <a href="{{ route('courses.index') }}" class="text-sm text-gray-400 hover:text-white px-4 py-2 transition-colors">{{ __('Cancel') }}</a>
                <button type="submit" class="flex items-center gap-2 text-sm bg-brand-500 hover:bg-brand-600 text-white font-medium px-6 py-2.5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Create Course') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
