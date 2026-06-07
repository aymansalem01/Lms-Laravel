<x-layouts.dashboard>
    <x-slot name="title">{{ __('Edit Course') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">{{ __('Edit Course') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ __('Update course details and settings.') }}</p>
        </div>

        <form method="POST" action="{{ route('courses.update', $course) }}" class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Course Title') }}</label>
                <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}" required
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('title') border-red-500/50 @enderror">
                @error('title')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Description') }}</label>
                <textarea name="description" id="description" rows="5" required
                          class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('description') border-red-500/50 @enderror">{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Course Type --}}
            <div>
                <label for="course_type" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Course Type') }}</label>
                <select name="course_type" id="course_type"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('course_type') border-red-500/50 @enderror">
                    <option value="program" {{ old('course_type', $course->course_type) === 'program' ? 'selected' : '' }}>{{ __('Program') }}</option>
                    <option value="sae_core" {{ old('course_type', $course->course_type) === 'sae_core' ? 'selected' : '' }}>{{ __('SAE Core') }}</option>
                    <option value="university" {{ old('course_type', $course->course_type) === 'university' ? 'selected' : '' }}>{{ __('University') }}</option>
                </select>
                @error('course_type')
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
                        <option value="{{ $program }}" {{ old('program', $course->program) === $program ? 'selected' : '' }}>{{ $program }}</option>
                    @endforeach
                </select>
                @error('program')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cover image URL --}}
            <div>
                <label for="cover_image" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Cover Image URL') }}</label>
                <input type="url" name="cover_image" id="cover_image" value="{{ old('cover_image', $course->cover_image) }}"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors @error('cover_image') border-red-500/50 @enderror"
                       placeholder="{{ __('https://example.com/image.jpg') }}">
                <p class="mt-1 text-xs text-gray-500">{{ __('Leave empty to use a gradient placeholder.') }}</p>
                @error('cover_image')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Status') }}</label>
                <select name="status" id="status"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors">
                    <option value="draft" {{ old('status', $course->status) === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                    <option value="published" {{ old('status', $course->status) === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-surface-700">
                {{-- Delete --}}
                <button type="button" x-data @click="if(confirm('{{ __('Are you sure you want to delete this course? This action cannot be undone.') }}')) { $el.closest('form').querySelector('#delete-form').submit() }"
                        class="flex items-center gap-1.5 text-sm text-red-400 hover:text-red-300 px-3 py-2 rounded-lg hover:bg-red-500/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    {{ __('Delete Course') }}
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-400 hover:text-white px-4 py-2 transition-colors">{{ __('Cancel') }}</a>
                    <button type="submit" class="flex items-center gap-2 text-sm bg-brand-500 hover:bg-brand-600 text-white font-medium px-6 py-2.5 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </form>

        {{-- Hidden delete form --}}
        <form id="delete-form" method="POST" action="{{ route('courses.destroy', $course) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</x-layouts.dashboard>
