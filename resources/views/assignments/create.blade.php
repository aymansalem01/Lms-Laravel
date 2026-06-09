<x-layouts.dashboard>
    <x-slot name="title">{{ __('Create Assignment') }} — {{ $course->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.assignments.index', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to assignments') }}
        </a>
        <h1 class="text-2xl font-bold text-white">{{ __('Create Assignment') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $course->name }}</p>
    </div>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('courses.assignments.store', $course) }}" enctype="multipart/form-data" class="bg-surface-800 border border-white/10 rounded-2xl p-6 space-y-6">
            @csrf
            <div>
                <label for="module_id" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Module') }} <span class="text-gray-500">({{ __('optional') }})</span></label>
                <select name="module_id" id="module_id" class="input-dashboard">
                    <option value="">{{ __('No module') }}</option>
                    @foreach($modules as $module)
                        <option value="{{ $module->id }}" {{ old('module_id', request('module_id')) == $module->id ? 'selected' : '' }}>{{ $module->title }}</option>
                    @endforeach
                </select>
                @error('module_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Title') }}</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="input-dashboard {{ $errors->has('title') ? 'border-red-500' : '' }}"
                       placeholder="{{ __('e.g. Week 4: CSS Layout Project') }}">
                @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Description') }}</label>
                <textarea name="description" id="description" rows="6" required
                          class="input-dashboard resize-none {{ $errors->has('description') ? 'border-red-500' : '' }}"
                          placeholder="{{ __('Describe the assignment requirements, instructions, and any resources...') }}">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Attachment') }} <span class="text-gray-500">({{ __('optional') }})</span></label>
                <div class="flex items-center gap-3">
                    <label class="relative flex items-center gap-3 bg-surface-700 border border-dashed border-surface-600 rounded-xl px-4 py-3 cursor-pointer hover:border-brand-500/50 transition-colors w-full">
                        <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <span class="text-sm text-gray-400">{{ __('Upload a file (PDF, DOC, ZIP, etc.)') }}</span>
                        <input type="file" name="attachment" accept=".pdf,.doc,.docx,.zip,.rar,.7z,.png,.jpg,.jpeg,.ppt,.pptx,.xls,.xlsx,.txt"
                               @change="const f = $event.target.files[0]; if (f) { $el.closest('label').querySelector('span').textContent = f.name; }"
                               class="hidden">
                    </label>
                </div>
                @error('attachment') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Due Date') }}</label>
                    <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}"
                           class="input-dashboard {{ $errors->has('due_date') ? 'border-red-500' : '' }}">
                    @error('due_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="max_score" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Max Score') }}</label>
                    <input type="number" name="max_score" id="max_score" value="{{ old('max_score', 100) }}" min="1" required
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
                        <option value="{{ $rubric->id }}" {{ old('rubric_id') == $rubric->id ? 'selected' : '' }}>{{ $rubric->title }}</option>
                    @endforeach
                </select>
                @error('rubric_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                    {{ __('Create Assignment') }}
                </button>
                <a href="{{ route('courses.assignments.index', $course) }}"
                   class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
