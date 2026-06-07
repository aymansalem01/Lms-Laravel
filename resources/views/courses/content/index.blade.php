<x-layouts.dashboard>
    <x-slot name="title">{{ __('Course Content') }} - {{ $course->title }}</x-slot>

    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $course->title }}</h1>
                <p class="text-gray-400 text-sm mt-1">{{ __('Manage course modules and lessons.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-400 hover:text-white px-3 py-2 transition-colors">{{ __('View Course') }}</a>
                <a href="{{ route('courses.edit', $course) }}" class="text-sm text-gray-400 hover:text-white px-3 py-2 transition-colors">{{ __('Edit') }}</a>
            </div>
        </div>
    </div>

    {{-- Add Module button --}}
    <div class="mb-6">
        <button x-data @click="$refs.addModuleForm.classList.toggle('hidden')"
                class="flex items-center gap-2 text-sm bg-brand-500 hover:bg-brand-600 text-white font-medium px-4 py-2.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add Module') }}
        </button>
    </div>

    {{-- Add Module form (inline) --}}
    <div x-ref="addModuleForm" class="hidden bg-surface-800 border border-surface-700 rounded-xl p-5 mb-6 transition-all">
        <h3 class="text-sm font-semibold text-white mb-4">{{ __('New Module') }}</h3>
             <form method="POST" action="{{ route('courses.content.store', $course) }}" class="space-y-4">
            @csrf
            <div>
                <label for="module_title" class="block text-xs font-medium text-gray-400 mb-1">{{ __('Module Title') }}</label>
                <input type="text" name="title" id="module_title" required placeholder="{{ __('e.g. Week 1: Fundamentals') }}"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors">
            </div>
            <div>
                <label for="module_description" class="block text-xs font-medium text-gray-400 mb-1">{{ __('Description') }} <span class="text-gray-600">{{ __('(optional)') }}</span></label>
                <input type="text" name="description" id="module_description" placeholder="{{ __('Brief description of this module...') }}"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors">
            </div>
            <div class="flex items-center gap-2 justify-end">
                <button type="button" @click="$refs.addModuleForm.classList.add('hidden')" class="text-sm text-gray-400 hover:text-white px-3 py-1.5 transition-colors">{{ __('Cancel') }}</button>
                <button type="submit" class="text-sm bg-brand-500 hover:bg-brand-600 text-white font-medium px-4 py-1.5 rounded-lg transition-colors">
                    {{ __('Add Module') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Modules list --}}
    <div id="modules-container" class="space-y-4">
        @forelse(($modules ?? collect()) as $module)
            <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden" data-module-id="{{ $module->id }}">
                {{-- Module header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-700">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="cursor-move text-gray-600 hover:text-gray-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-white">{{ $module->title }}</h3>
                            @if($module->description)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $module->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('courses.content.edit', [$course, $module]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-700 transition-colors">
                            {{ __('Edit') }}
                        </a>
                        <form method="POST" action="{{ route('courses.content.destroy', [$course, $module]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this module and all its lessons?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Lessons --}}
                <div class="px-5 py-3 space-y-1">
                    @forelse(($module->lessons ?? collect()) as $lesson)
                        <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-surface-700/50 transition-colors group">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <a href="{{ route('courses.content.lesson.show', [$course, $lesson]) }}" class="text-sm text-gray-300 hover:text-white transition-colors truncate">{{ $lesson->title }}</a>
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('courses.content.lesson.edit', [$course, $lesson]) }}" class="text-xs text-gray-500 hover:text-white px-2 py-0.5 rounded transition-colors">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('courses.content.lesson.destroy', [$course, $lesson]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this lesson?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-0.5 rounded transition-colors">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600 px-3 py-2">{{ __('No lessons in this module.') }}</p>
                    @endforelse

                    {{-- Add lesson button --}}
                    <button x-data @click="$refs.addLessonForm{{ $module->id }}.classList.toggle('hidden')"
                            class="flex items-center gap-1.5 text-xs text-brand-400 hover:text-brand-300 px-3 py-2 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Add Lesson') }}
                    </button>

                    {{-- Inline add lesson form --}}
                    <div x-ref="addLessonForm{{ $module->id }}" class="hidden bg-surface-700/50 rounded-lg p-4 mt-2 transition-all">
                        <form method="POST" action="{{ route('courses.content.lesson.store', $course) }}">
                            @csrf
                            <input type="hidden" name="module_id" value="{{ $module->id }}">
                            <div class="space-y-3">
                                <input type="text" name="title" required placeholder="{{ __('Lesson title...') }}"
                                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-1.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                                <textarea name="content" rows="3" placeholder="{{ __('Lesson content (optional)...') }}"
                                          class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-1.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors"></textarea>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <input type="url" name="video_url" placeholder="{{ __('Video URL (optional)') }}"
                                           class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-1.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                                    <input type="url" name="audio_url" placeholder="{{ __('Audio URL (optional)') }}"
                                           class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-1.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                                    <input type="url" name="file_url" placeholder="{{ __('File URL (optional)') }}"
                                           class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-1.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                                    <input type="number" name="order_index" min="0" placeholder="{{ __('Order (optional)') }}"
                                           class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-1.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                                </div>
                                <div class="flex items-center gap-2 justify-end">
                                    <button type="button" @click="$refs.addLessonForm{{ $module->id }}.classList.add('hidden')" class="text-xs text-gray-400 hover:text-white px-2 py-1 transition-colors">{{ __('Cancel') }}</button>
                                    <button type="submit" class="text-xs bg-brand-500 hover:bg-brand-600 text-white font-medium px-3 py-1 rounded-lg transition-colors">{{ __('Add') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Module Resources (Quizzes, Live Sessions, Assignments) --}}
                <div x-data="{ showResources: false }" class="border-t border-surface-700">
                    <button @click="showResources = !showResources" class="w-full flex items-center justify-between px-5 py-2.5 text-xs text-gray-400 hover:text-gray-300 transition-colors">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            <span>{{ __('Module Resources') }}</span>
                            @php $resourceCount = $module->quizzes->count() + $module->liveSessions->count() + $module->assignments->count(); @endphp
                            @if($resourceCount > 0)
                                <span class="text-[10px] bg-brand-500/20 text-brand-400 px-1.5 py-0.5 rounded-full">{{ $resourceCount }}</span>
                            @endif
                        </div>
                        <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': showResources }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="showResources" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="px-5 pb-4 space-y-3">
                            {{-- Quizzes --}}
                            <div class="bg-surface-700 border border-surface-700 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-gray-300">{{ __('Quizzes') }}</span>
                                    <a href="{{ route('courses.quizzes.create', ['course' => $course, 'module_id' => $module->id]) }}"
                                       class="flex items-center gap-1 text-xs text-brand-400 hover:text-brand-300 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        {{ __('Add') }}
                                    </a>
                                </div>
                                @forelse($module->quizzes as $resource)
                                    <div class="flex items-center justify-between px-2 py-1.5 rounded hover:bg-surface-700/50 transition-colors group/resource">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <svg class="w-3.5 h-3.5 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <a href="{{ route('courses.quizzes.show', [$course, $resource]) }}" class="text-xs text-gray-400 hover:text-white truncate">{{ $resource->title }}</a>
                                        </div>
                                        <div class="flex items-center gap-1 opacity-0 group-hover/resource:opacity-100 transition-opacity">
                                            <a href="{{ route('courses.quizzes.edit', [$course, $resource]) }}" class="text-[10px] text-gray-600 hover:text-gray-300 px-1.5 py-0.5 rounded transition-colors">{{ __('Edit') }}</a>
                                            <form method="POST" action="{{ route('courses.quizzes.destroy', [$course, $resource]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this quiz?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-[10px] text-red-500 hover:text-red-400 px-1.5 py-0.5 rounded transition-colors">{{ __('Delete') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-600 px-2">{{ __('No quizzes assigned.') }}</p>
                                @endforelse
                            </div>

                            {{-- Live Sessions --}}
                            <div class="bg-surface-700 border border-surface-700 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-gray-300">{{ __('Live Sessions') }}</span>
                                    <a href="{{ route('courses.live.create', ['course' => $course, 'module_id' => $module->id]) }}"
                                       class="flex items-center gap-1 text-xs text-brand-400 hover:text-brand-300 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        {{ __('Add') }}
                                    </a>
                                </div>
                                @forelse($module->liveSessions as $resource)
                                    <div class="flex items-center justify-between px-2 py-1.5 rounded hover:bg-surface-700/50 transition-colors group/resource">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <svg class="w-3.5 h-3.5 text-coral-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            <a href="{{ route('courses.live.show', [$course, $resource]) }}" class="text-xs text-gray-400 hover:text-white truncate">{{ $resource->title }}</a>
                                        </div>
                                        <div class="flex items-center gap-1 opacity-0 group-hover/resource:opacity-100 transition-opacity">
                                            <a href="{{ route('courses.live.create', ['course' => $course]) }}?module_id={{ $module->id }}" class="text-[10px] text-gray-600 hover:text-gray-300 px-1.5 py-0.5 rounded transition-colors">{{ __('Edit') }}</a>
                                            <form method="POST" action="" class="inline" onsubmit="return false;">
                                                @csrf @method('DELETE')
                                                <button type="submit" disabled class="text-[10px] text-red-500/50 px-1.5 py-0.5 rounded">{{ __('Delete') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-600 px-2">{{ __('No live sessions assigned.') }}</p>
                                @endforelse
                            </div>

                            {{-- Assignments --}}
                            <div class="bg-surface-700 border border-surface-700 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-gray-300">{{ __('Assignments') }}</span>
                                    <a href="{{ route('courses.assignments.create', ['course' => $course, 'module_id' => $module->id]) }}"
                                       class="flex items-center gap-1 text-xs text-brand-400 hover:text-brand-300 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        {{ __('Add') }}
                                    </a>
                                </div>
                                @forelse($module->assignments as $resource)
                                    <div class="flex items-center justify-between px-2 py-1.5 rounded hover:bg-surface-700/50 transition-colors group/resource">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <a href="{{ route('courses.assignments.show', [$course, $resource]) }}" class="text-xs text-gray-400 hover:text-white truncate">{{ $resource->title }}</a>
                                        </div>
                                        <div class="flex items-center gap-1 opacity-0 group-hover/resource:opacity-100 transition-opacity">
                                            <a href="{{ route('courses.assignments.edit', [$course, $resource]) }}" class="text-[10px] text-gray-600 hover:text-gray-300 px-1.5 py-0.5 rounded transition-colors">{{ __('Edit') }}</a>
                                            <form method="POST" action="{{ route('courses.assignments.destroy', [$course, $resource]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this assignment?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-[10px] text-red-500 hover:text-red-400 px-1.5 py-0.5 rounded transition-colors">{{ __('Delete') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-600 px-2">{{ __('No assignments assigned.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-surface-800 border border-white/10 rounded-xl p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-surface-700 flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <p class="text-gray-400 text-lg mb-1">{{ __('No modules yet.') }}</p>
                <p class="text-gray-500 text-sm">{{ __('Start by adding your first module with the button above.') }}</p>
            </div>
        @endforelse
    </div>
</x-layouts.dashboard>
