<x-layouts.dashboard>
    <x-slot name="title">{{ __('Course Catalog') }}</x-slot>

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">{{ __('Course Catalog') }}</h1>
        <p class="text-gray-400 text-sm mt-1">{{ __('Browse all available courses and enroll.') }}</p>
    </div>

    {{-- Filters --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('courses.catalog') }}" class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search courses...') }}"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors">
            </div>

            {{-- Program filter --}}
            <select name="program"
                    class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors">
                <option value="">{{ __('All Programs') }}</option>
                @foreach(['Film Production', 'Digital Media', 'Game Design', 'Audio Engineering'] as $program)
                    <option value="{{ $program }}" {{ request('program') === $program ? 'selected' : '' }}>{{ $program }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>

            @if(request('search') || request('program'))
                <a href="{{ route('courses.catalog') }}" class="text-sm text-gray-400 hover:text-white px-3 py-2 transition-colors">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    {{-- Results --}}
    @php $courses = $courses ?? collect(); @endphp

    @if($courses->isEmpty() && (request('search') || request('program')))
        <div class="bg-surface-800 border border-white/10 rounded-xl p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-surface-700 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <p class="text-gray-400 text-lg mb-1">{{ __('No courses match your filters.') }}</p>
            <a href="{{ route('courses.catalog') }}" class="text-brand-400 hover:text-brand-300 text-sm transition-colors">{{ __('Clear all filters') }}</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden transition-all hover:border-brand-500/30 hover:shadow-lg hover:shadow-brand-500/5 flex flex-col">
                    {{-- Cover --}}
                    @if($course->cover_image)
                        <img src="{{ $course->cover_image }}" alt="{{ $course->title }}" class="w-full h-40 object-cover">
                    @else
                        <div class="w-full h-40 gb flex items-center justify-center">
                            <span class="text-4xl font-bold text-white/40">{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                        </div>
                    @endif

                    <div class="p-5 flex flex-col flex-1">
                        {{-- Program badge --}}
                        @if($course->program)
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-400 self-start mb-2">{{ $course->program }}</span>
                        @endif

                        <h3 class="font-semibold text-white mb-1">
                            <a href="{{ route('courses.show', $course) }}" class="hover:text-brand-400 transition-colors">{{ $course->title }}</a>
                        </h3>

                        <p class="text-xs text-gray-500 mb-2">
                            {{ $course->instructor->name ?? __('Unknown Instructor') }}
                            &middot;
                            <span>{{ $course->enrollments_count ?? 0 }} {{ __('enrolled') }}</span>
                        </p>

                        @if($course->description)
                            <p class="text-sm text-gray-500 leading-relaxed flex-1">{{ Str::limit(strip_tags($course->description), 120) }}</p>
                        @endif

                        <div class="mt-4 pt-4 border-t border-surface-700">
                            @if(auth()->user()->role === 'student' && ($enrolledIds ?? collect())->contains($course->id))
                                <a href="{{ route('courses.show', $course) }}" class="block w-full text-center text-sm font-medium bg-brand-500/20 text-brand-400 hover:bg-brand-500/30 px-4 py-2 rounded-lg transition-colors">
                                    {{ __('View Course') }}
                                </a>
                            @elseif(auth()->user()->role === 'student')
                                <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                    @csrf
                                    <button type="submit" class="w-full text-center text-sm font-medium bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                                        {{ __('Enroll Now') }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('courses.show', $course) }}" class="block w-full text-center text-sm font-medium bg-surface-700 hover:bg-surface-600 text-gray-300 px-4 py-2 rounded-lg transition-colors">
                                    {{ __('View Details') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-surface-800 border border-white/10 rounded-xl p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-surface-700 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <p class="text-gray-400 text-lg">{{ __('No courses available yet.') }}</p>
                </div>
            @endforelse
        </div>
    @endif
</x-layouts.dashboard>
