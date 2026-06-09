<x-layouts.dashboard>
    <x-slot name="title">{{ __('My Courses') }}</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">
                @if(auth()->user()->role === 'student')
                    {{ __('My Enrolled Courses') }}
                @elseif(auth()->user()->role === 'instructor')
                    {{ __('My Courses') }}
                @else
                    {{ __('All Courses') }}
                @endif
            </h1>
            <p class="text-gray-400 text-sm mt-1">
                @if(auth()->user()->role === 'student')
                    {{ __('Courses you are currently enrolled in.') }}
                @elseif(auth()->user()->role === 'instructor')
                    {{ __('Courses you teach and manage.') }}
                @else
                    {{ __('Every course on the platform.') }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('courses.create') }}" class="flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('New Course') }}
                </a>
            @endif
            <a href="{{ route('courses.catalog') }}" class="flex items-center gap-2 bg-surface-800 border border-surface-700 hover:border-brand-500/30 text-gray-300 hover:text-white text-sm font-medium px-4 py-2 rounded-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                {{ __('Browse Catalog') }}
            </a>
        </div>
    </div>

    @php $courses = $courses ?? collect(); @endphp

    @forelse($courses as $course)
        <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden transition-all hover:border-brand-500/30 hover:shadow-lg hover:shadow-brand-500/5 mb-6">
            <div class="md:flex">
                {{-- Cover --}}
                <div class="md:w-64 h-40 md:h-auto shrink-0">
                    @if($course->cover_image_url)
                        <img src="{{ $course->cover_image_url }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full gb flex items-center justify-center">
                            <span class="text-4xl font-bold text-white/40">{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                        </div>
                    @endif
                </div>

                {{-- Details --}}
                <div class="p-5 flex-1 min-w-0 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <h2 class="text-lg font-semibold text-white">
                                    <a href="{{ route('courses.show', $course) }}" class="hover:text-brand-400 transition-colors">{{ $course->title }}</a>
                                </h2>
                                <p class="text-sm text-gray-400 mt-0.5">
                                    @if($course->instructor)
                                        {{ $course->instructor->name }}
                                    @else
                                        <span class="text-gray-600">{{ __('No instructor assigned') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($course->program)
                                    <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-brand-500/10 text-brand-400">{{ $course->program }}</span>
                                @endif
                                @if($course->enrollments_count ?? 0)
                                    <span class="text-xs text-gray-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                                        {{ $course->enrollments_count }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($course->description)
                            <p class="text-sm text-gray-500 line-clamp-2 mt-2">{{ Str::limit(strip_tags($course->description), 150) }}</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-surface-700">
                        <div>
                            @if(auth()->user()->role === 'student' && isset($course->progress))
                                <div class="flex items-center gap-3">
                                    <div class="w-32 h-1.5 bg-surface-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-brand-500 to-coral-500 rounded-full" style="width: {{ $course->progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ $course->progress }}%</span>
                                </div>
                            @elseif(auth()->user()->role === 'instructor')
                                <span class="text-xs text-gray-500">{{ __('Created') }} {{ $course->created_at->format('M d, Y') }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('courses.show', $course) }}" class="text-sm text-brand-400 hover:text-brand-300 transition-colors">{{ __('View') }} &rarr;</a>
                            @if(auth()->user()->role !== 'student')
                                <a href="{{ route('courses.edit', $course) }}" class="text-sm text-gray-400 hover:text-white px-3 py-1 rounded-lg hover:bg-surface-700 transition-colors">{{ __('Edit') }}</a>
                                <a href="{{ route('courses.content.index', $course) }}" class="text-sm text-gray-400 hover:text-white px-3 py-1 rounded-lg hover:bg-surface-700 transition-colors">{{ __('Content') }}</a>
                            @endif
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
            <p class="text-gray-400 text-lg mb-1">{{ __('No courses found.') }}</p>
            @if(auth()->user()->role === 'student')
                <p class="text-gray-500 text-sm mb-4">{{ __('You are not enrolled in any courses.') }}</p>
                <a href="{{ route('courses.catalog') }}" class="inline-flex items-center gap-2 text-sm bg-brand-500 hover:bg-brand-600 text-white px-5 py-2.5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    {{ __('Browse Course Catalog') }}
                </a>
            @elseif(auth()->user()->role === 'instructor')
                <p class="text-gray-500 text-sm mb-4">{{ __('You haven\'t created any courses yet.') }}</p>
            @else
                <p class="text-gray-500 text-sm">{{ __('No courses exist on the platform yet.') }}</p>
            @endif
        </div>
    @endforelse
</x-layouts.dashboard>
