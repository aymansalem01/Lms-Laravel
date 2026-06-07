<x-layouts.dashboard>
    <x-slot name="title">{{ __('Edit Assignment') }} — {{ $course->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.assignments.index', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to assignments') }}
        </a>
        <h1 class="text-2xl font-bold text-white">{{ __('Edit Assignment') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $course->name }} — {{ $assignment->title }}</p>
    </div>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('courses.assignments.update', [$course, $assignment]) }}" class="bg-surface-800 border border-white/10 rounded-2xl p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="module_id" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Module') }} <span class="text-gray-500">({{ __('optional') }})</span></label>
                <select name="module_id" id="module_id" class="input-dashboard">
                    <option value="">{{ __('No module') }}</option>
                    @foreach($modules as $module)
                        <option value="{{ $module->id }}" {{ old('module_id', $assignment->module_id) == $module->id ? 'selected' : '' }}>{{ $module->title }}</option>
                    @endforeach
                </select>
                @error('module_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Title') }}</label>
                <input type="text" name="title" id="title" value="{{ old('title', $assignment->title) }}" required
                       class="input-dashboard {{ $errors->has('title') ? 'border-red-500' : '' }}">
                @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Description') }}</label>
                <textarea name="description" id="description" rows="6" required
                          class="input-dashboard resize-none {{ $errors->has('description') ? 'border-red-500' : '' }}">{{ old('description', $assignment->description) }}</textarea>
                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Due Date') }}</label>
                    <input type="datetime-local" name="due_date" id="due_date"
                           value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}"
                           class="input-dashboard {{ $errors->has('due_date') ? 'border-red-500' : '' }}">
                    @error('due_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="max_score" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Max Score') }}</label>
                    <input type="number" name="max_score" id="max_score" value="{{ old('max_score', $assignment->max_score) }}" min="1" required
                           class="input-dashboard {{ $errors->has('max_score') ? 'border-red-500' : '' }}">
                    @error('max_score') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="rubric_id" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Rubric') }} <span class="text-gray-500">({{ __('optional') }})</span></label>
                <select name="rubric_id" id="rubric_id"
                        class="input-dashboard">
                    <option value="">{{ __('No rubric') }}</option>
                    @foreach($rubrics as $rubric)
                        <option value="{{ $rubric->id }}" {{ old('rubric_id', $assignment->rubric_id) == $rubric->id ? 'selected' : '' }}>{{ $rubric->title }}</option>
                    @endforeach
                </select>
                @error('rubric_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                    {{ __('Update Assignment') }}
                </button>
                <a href="{{ route('courses.assignments.index', $course) }}"
                   class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
