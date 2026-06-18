<x-layouts.dashboard>
    <x-slot name="title">{{ $lesson->title }} - {{ $course->title }}</x-slot>

    @php
        $prevLesson = $prevLesson ?? null;
        $nextLesson = $nextLesson ?? null;
        $isCompleted = $isCompleted ?? false;
        $isLocked = $isLocked ?? false;
    @endphp

    @if($isLocked)
        <div class="max-w-4xl mx-auto">
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-10 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-surface-700 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">{{ __('Lesson Locked') }}</h2>
                <p class="text-gray-400 text-sm mb-4">
                    {{ __('You must complete') }}
                    <a href="{{ route('courses.content.lesson.show', [$course, $prerequisiteLesson]) }}" class="text-brand-400 hover:text-brand-300 underline">{{ $prerequisiteLesson->title }}</a>
                    {{ __('before accessing this lesson.') }}
                </p>
                <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center gap-1.5 text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ __('Back to course') }}
                </a>
            </div>
        </div>
    @else
    <div class="max-w-4xl mx-auto">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('courses.show', $course) }}" class="hover:text-gray-300 transition-colors">{{ $course->title }}</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @if($lesson->module)
                <span>{{ $lesson->module->title }}</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @endif
            <span class="text-gray-300">{{ $lesson->title }}</span>
        </div>

        {{-- Lesson header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">{{ $lesson->title }}</h1>
            @if($lesson->module)
                <p class="text-sm text-gray-500 mt-1">{{ $lesson->module->title }}</p>
            @endif
        </div>

        {{-- Video embed --}}
        @if($lesson->video_path)
            <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden mb-6">
                <video controls class="w-full aspect-video" preload="metadata">
                    <source src="{{ Storage::url($lesson->video_path) }}" type="video/mp4">
                    {{ __('Your browser does not support the video element.') }}
                </video>
            </div>
        @elseif($lesson->video_url)
            <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden mb-6 aspect-video">
                <iframe src="{{ $lesson->video_url }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        @endif

        {{-- Audio player --}}
        @if($lesson->audio_url)
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-5 mb-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-coral-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-coral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                    </div>
                    <span class="text-sm font-medium text-white">{{ __('Audio Recording') }}</span>
                </div>
                <audio controls class="w-full">
                    <source src="{{ $lesson->audio_url }}" type="audio/mpeg">
                    {{ __('Your browser does not support the audio element.') }}
                </audio>
            </div>
        @endif

        {{-- Content --}}
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 mb-6">
            <div class="prose prose-invert prose-sm max-w-none text-gray-300 leading-relaxed">
                {!! $lesson->content ?? '<p class="text-gray-500">' . __('No content for this lesson yet.') . '</p>' !!}
            </div>
        </div>

        {{-- File download --}}
        @if($lesson->file_url)
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-5 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-brand-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">{{ __('Attached File') }}</p>
                            <p class="text-xs text-gray-500">{{ pathinfo($lesson->file_url, PATHINFO_BASENAME) }}</p>
                        </div>
                    </div>
                    <a href="{{ $lesson->file_url }}" target="_blank" download
                       class="flex items-center gap-1.5 text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        {{ __('Download') }}
                    </a>
                </div>
            </div>
        @endif

        {{-- Topics --}}
        @if($lesson->topics->count())
            <div class="space-y-4 mb-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">{{ __('Topics') }}</h2>
                    @if(in_array(auth()->user()->role, ['instructor', 'admin']))
                        <a href="{{ route('courses.content.topics.create', [$course, $lesson]) }}"
                           class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Add Topic') }}
                        </a>
                    @endif
                </div>

                @foreach($lesson->topics as $topic)
                    <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                {{-- Type icon --}}
                                <div class="w-10 h-10 rounded-lg shrink-0 flex items-center justify-center
                                    {{ $topic->type === 'file' ? 'bg-brand-500/10' : '' }}
                                    {{ $topic->type === 'link' ? 'bg-blue-500/10' : '' }}
                                    {{ $topic->type === 'html' ? 'bg-purple-500/10' : '' }}
                                    {{ $topic->type === 'video' ? 'bg-red-500/10' : '' }}
                                    {{ $topic->type === 'audio' ? 'bg-green-500/10' : '' }}">
                                    @if($topic->type === 'file')
                                        <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @elseif($topic->type === 'link')
                                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    @elseif($topic->type === 'html')
                                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @elseif($topic->type === 'video')
                                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    @elseif($topic->type === 'audio')
                                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-white">{{ $topic->title }}</h3>

                                    @if($topic->type === 'file')
                                        <a href="{{ $topic->file_url }}" target="_blank" download
                                           class="inline-flex items-center gap-1.5 text-sm text-brand-400 hover:text-brand-300 mt-1 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            {{ pathinfo($topic->file_url, PATHINFO_BASENAME) }}
                                        </a>
                                    @elseif($topic->type === 'link')
                                        <a href="{{ $topic->external_url }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 text-sm text-blue-400 hover:text-blue-300 mt-1 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            {{ $topic->external_url }}
                                        </a>
                                    @elseif($topic->type === 'html')
                                        <div class="prose prose-invert prose-sm max-w-none text-gray-300 mt-2 leading-relaxed">
                                            {!! $topic->content !!}
                                        </div>
                                    @elseif($topic->type === 'video')
                                        <div class="mt-2 aspect-video bg-surface-900 rounded-lg overflow-hidden">
                                            <iframe src="{{ $topic->video_url }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        </div>
                                    @elseif($topic->type === 'audio')
                                        <div class="mt-2">
                                            <audio controls class="w-full">
                                                <source src="{{ $topic->audio_url }}" type="audio/mpeg">
                                                {{ __('Your browser does not support the audio element.') }}
                                            </audio>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if(in_array(auth()->user()->role, ['instructor', 'admin']))
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('courses.content.topics.edit', [$course, $lesson, $topic]) }}"
                                       class="flex items-center gap-1.5 text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 px-3 py-1.5 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('courses.content.topics.destroy', [$course, $lesson, $topic]) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="flex items-center gap-1.5 text-sm bg-red-500/10 hover:bg-red-500/20 text-red-400 px-3 py-1.5 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif(in_array(auth()->user()->role, ['instructor', 'admin']))
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 mb-6 text-center">
                <p class="text-gray-500 text-sm mb-3">{{ __('No topics yet.') }}</p>
                <a href="{{ route('courses.content.topics.create', [$course, $lesson]) }}"
                   class="inline-flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Add First Topic') }}
                </a>
            </div>
        @endif

        {{-- Mark as complete + Prev/Next --}}
        <div class="flex items-center justify-between bg-surface-800 border border-surface-700 rounded-xl p-5">
            @if($prevLesson)
                <a href="{{ route('courses.content.lesson.show', [$course, $prevLesson]) }}"
                   class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ $prevLesson->title }}
                </a>
            @else
                <div></div>
            @endif

            <div class="flex items-center gap-3">
                @if(auth()->user()->role === 'student')
                    <form method="POST" action="{{ route('courses.content.lesson.complete', [$course, $lesson]) }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-1.5 text-sm font-medium px-5 py-2.5 rounded-lg transition-all
                                       {{ $isCompleted ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-brand-500 hover:bg-brand-600 text-white' }}">
                            @if($isCompleted)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('Marked Complete') }}
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ __('Mark as Complete') }}
                            @endif
                        </button>
                    </form>
                @endif
            </div>

            @if($nextLesson)
                <a href="{{ route('courses.content.lesson.show', [$course, $nextLesson]) }}"
                   class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-white transition-colors">
                    {{ $nextLesson->title }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <div></div>
            @endif
        </div>
    </div>
    @endif
</x-layouts.dashboard>
