<x-layouts.landing title="Luminus Digital Creative Technology — School of Creative Media">

    @php
    // ── DATA ────────────────────────────────────────────────
    $roles = [
        ['key' => 'student',    'title' => 'Student',    'desc' => 'Access your courses, submit assignments, track your progress, and collaborate with peers.', 'cta' => 'Sign in as student', 'badge' => 'Learner',   'icon' => 'book'],
        ['key' => 'instructor', 'title' => 'Instructor', 'desc' => 'Build courses, manage assignments, grade submissions, and engage with your class.',    'cta' => 'Sign in as instructor', 'badge' => 'Faculty',   'icon' => 'clipboard'],
    ];

    $stats = [
        ['label' => 'Active Students',   'value' => '4,128', 'trend' => '18% from last year', 'icon' => 'users',      'tint' => 'coral'],
        ['label' => 'Programs Offered',  'value' => '5',     'trend' => '2 new this year',    'icon' => 'award',      'tint' => 'brand'],
        ['label' => 'Industry Partners', 'value' => '84',    'trend' => '12 added in 2026',   'icon' => 'briefcase',  'tint' => 'emerald'],
        ['label' => 'Employment Rate',   'value' => '91%',   'trend' => '+4% vs 2024',        'icon' => 'trending',   'tint' => 'amber'],
    ];

    $programs = [
        ['name' => 'Film & Television',  'desc' => 'From script to screen — master narrative craft, cinematography, directing, and post-production in our industry-standard studios.', 'tags' => ['Film', 'TV', 'Cinematography', 'Editing']],
        ['name' => 'Audio Engineering',  'desc' => 'Produce, mix, and master. Train on SSL consoles and Pro Tools in our world-class recording studios.', 'tags' => ['Music', 'Sound Design', 'Mixing']],
        ['name' => 'Digital Media',      'desc' => 'Design experiences that shape the web. UI/UX, motion graphics, front-end development, and creative direction.', 'tags' => ['UI/UX', 'Motion', 'Front-End']],
        ['name' => 'Game Design',        'desc' => 'Build worlds that captivate. Game mechanics, 3D modeling, level design, and interactive storytelling.', 'tags' => ['Unity', 'Unreal', '3D Modeling']],
        ['name' => 'Esports',            'desc' => 'Compete, cast, and produce. Strategic gaming, live event production, and digital content creation for the esports industry.', 'tags' => ['Competition', 'Production', 'Streaming']],
    ];

    $features = [
        ['icon' => 'video',     'title' => 'Media-Safe Streaming', 'desc' => 'Upload, stream, and critique 4K video and high-res audio natively in the browser — no plugins needed.'],
        ['icon' => 'grade',     'title' => 'Rubric-Based Grading', 'desc' => 'Grade assignments against custom rubrics with inline feedback, annotations, and time-stamped comments.'],
        ['icon' => 'briefcase', 'title' => 'Portfolio Builder',    'desc' => 'Students curate a living portfolio of their best work to share with faculty, peers, and future employers.'],
        ['icon' => 'dashboard', 'title' => 'Progress Analytics',   'desc' => 'Track engagement, completion, and performance at a glance with actionable instructor dashboards.'],
        ['icon' => 'lang',      'title' => 'Bilingual Interface',  'desc' => 'Full Arabic and English support with RTL layouts, Arabic fonts, and locale-aware content.'],
        ['icon' => 'shield',    'title' => 'Enterprise Security',  'desc' => 'SSO, role-based access, audit logs, and GDPR/COPPA compliance built in from day one.'],
    ];

    $replaces = ['Google Classroom', 'Canvas', 'Blackboard', 'Moodle', 'Slack', 'YouTube Studio'];

    $cohorts = [
        ['date' => 'Sep 15', 'year' => '2026', 'program' => 'Film & Television',     'loc' => 'Amman',  'seats' => '18/24', 'seatsNum' => 18, 'seatsMax' => 24, 'status' => 'open',    'prog' => 'film'],
        ['date' => 'Sep 20', 'year' => '2026', 'program' => 'Audio Engineering',     'loc' => 'Amman',  'seats' => '21/24', 'seatsNum' => 21, 'seatsMax' => 24, 'status' => 'closing', 'prog' => 'audio'],
        ['date' => 'Oct 1',  'year' => '2026', 'program' => 'Digital Media',         'loc' => 'Online', 'seats' => '42/48', 'seatsNum' => 42, 'seatsMax' => 48, 'status' => 'open',    'prog' => 'dm'],
        ['date' => 'Oct 15', 'year' => '2026', 'program' => 'Game Design',           'loc' => 'Amman',  'seats' => '16/20', 'seatsNum' => 16, 'seatsMax' => 20, 'status' => 'few',     'prog' => 'game'],
        ['date' => 'Nov 1',  'year' => '2026', 'program' => 'Esports Management',    'loc' => 'Online', 'seats' => '10/20', 'seatsNum' => 10, 'seatsMax' => 20, 'status' => 'waitlist','prog' => 'esports'],
    ];

    $faculty = [
        ['name' => 'Layla Hassan',   'role' => 'Head of Film',     'bio' => 'Award-winning documentary filmmaker with 15+ years in broadcast production across the MENA region.', 'credits' => ['Al Jazeera', 'BBC', 'Netflix']],
        ['name' => 'Omar Farouk',    'role' => 'Audio Director',   'bio' => 'Grammy-nominated sound engineer who has worked with top Arabic and international artists on major label releases.', 'credits' => ['Universal', 'Rotana', 'Spotify']],
        ['name' => 'Dana Khalil',    'role' => 'Digital Lead',     'bio' => 'UX strategist and creative technologist who has shipped products used by millions across the Middle East.', 'credits' => ['Google', 'Meta', 'Careem']],
        ['name' => 'Samir Nasri',    'role' => 'Game Design Chair','bio' => 'Founder of a regional game studio behind titles with 10M+ downloads and multiple Apple Feature awards.', 'credits' => ['Apple', 'Unity', 'Steam']],
    ];

    $works = [
        ['t' => 'The Last Frame',     'by' => 'Lara Othman',    'prog' => 'Film',      'kind' => 'Short Film'],
        ['t' => 'Echo Chamber',       'by' => 'Zaid Mansour',   'prog' => 'Audio',     'kind' => 'Album'],
        ['t' => 'Flow State',         'by' => 'Rana Haddad',    'prog' => 'Digital',   'kind' => 'Web App'],
        ['t' => 'Descent',            'by' => 'Karim Nasser',    'prog' => 'Game',      'kind' => 'Demo'],
        ['t' => 'Pulse',              'by' => 'Yara Shamma',    'prog' => 'Audio',     'kind' => 'Track'],
        ['t' => 'Arena Rising',       'by' => 'Tamer Saleh',    'prog' => 'Esports',   'kind' => 'Production'],
    ];

    $worksVisual = [
        ['dot' => 'bg-rose-500',    'span' => 'col-span-12 lg:col-span-8 lg:row-span-2', 'ratio' => '16/9'],
        ['dot' => 'bg-amber-500',   'span' => 'col-span-12 sm:col-span-6 lg:col-span-4', 'ratio' => '4/3'],
        ['dot' => 'bg-blue-500',    'span' => 'col-span-12 sm:col-span-6 lg:col-span-4', 'ratio' => '4/3'],
        ['dot' => 'bg-emerald-500', 'span' => 'col-span-12 sm:col-span-6 lg:col-span-4', 'ratio' => '4/3'],
        ['dot' => 'bg-rose-500',    'span' => 'col-span-12 sm:col-span-6 lg:col-span-4', 'ratio' => '4/3'],
        ['dot' => 'bg-fuchsia-500', 'span' => 'col-span-12 sm:col-span-6 lg:col-span-4', 'ratio' => '4/3'],
    ];

    $faq = [
        ['q' => 'What programs does Luminus Jordan offer?',               'a' => 'We offer bachelor\'s degrees in Film & Television, Audio Engineering, Digital Media, and Game Design, plus diplomas in Game Art and Esports Management. All programs are accredited by the Ministry of Higher Education.'],
        ['q' => 'Is the platform available in Arabic?',                'a' => 'Yes — the entire LMS is bilingual in English and Arabic with full RTL support, Arabic typography, and locale-aware content. You can switch at any time.'],
        ['q' => 'How do I apply for admissions?',                      'a' => 'Applications are open year-round. You can apply online through our portal, submit your portfolio or audition, and receive a decision within two weeks.'],
        ['q' => 'Can I try the platform before enrolling?',            'a' => 'Absolutely — you can explore the student experience by signing up for a demo account. Talk to our admissions team to schedule a guided tour.'],
        ['q' => 'What equipment and software do I need?',              'a' => 'We provide industry-standard labs with cameras, SSL consoles, Pro Tools, Unity, Unreal, and Adobe Creative Cloud. A modern laptop is recommended for Digital Media and Game Design students.'],
        ['q' => 'Is financial aid or scholarships available?',         'a' => 'Yes — we offer merit-based scholarships, need-based aid, and flexible payment plans. Contact our financial aid office for a personalized consultation.'],
    ];

    $tintMap = [
        'emerald' => ['accent' => 'text-emerald-400', 'tint' => 'bg-emerald-500/10'],
        'brand'   => ['accent' => 'text-brand-400',   'tint' => 'bg-brand-500/10'],
        'coral'   => ['accent' => 'text-coral-400',   'tint' => 'bg-coral-500/10'],
        'amber'   => ['accent' => 'text-amber-400',   'tint' => 'bg-amber-500/10'],
    ];

    $progDot = ['film' => 'bg-rose-500', 'audio' => 'bg-amber-500', 'dm' => 'bg-blue-500', 'game' => 'bg-emerald-500', 'esports' => 'bg-fuchsia-500'];
    $statusCls = [
        'open'     => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
        'closing'  => 'bg-coral-500/20 text-coral-300 border-coral-500/30',
        'few'      => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
        'waitlist' => 'bg-surface-500/40 text-gray-400 border-white/10',
    ];
    $statusLabel = ['open' => __('messages.cohort_open'), 'closing' => __('messages.cohort_closing'), 'few' => __('messages.cohort_few'), 'waitlist' => __('messages.cohort_waitlist')];

    $featureTints = [
        ['accent' => 'text-blue-400',    'tint' => 'bg-blue-500/10'],
        ['accent' => 'text-coral-400',   'tint' => 'bg-coral-500/10'],
        ['accent' => 'text-emerald-400', 'tint' => 'bg-emerald-500/10'],
        ['accent' => 'text-brand-400',   'tint' => 'bg-brand-500/10'],
        ['accent' => 'text-amber-400',   'tint' => 'bg-amber-500/10'],
        ['accent' => 'text-fuchsia-400', 'tint' => 'bg-fuchsia-500/10'],
    ];

    $facultyDot = ['bg-amber-500', 'bg-rose-500', 'bg-emerald-500', 'bg-blue-500'];
    @endphp

    {{-- ═══════════════ NAV ═══════════════ --}}
    <header class="sticky top-0 z-40 backdrop-blur-md bg-surface-900/75 border-b border-white/5">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10 h-16 flex items-center gap-8">
            <a href="/" class="flex items-center gap-2.5 flex-shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-brand flex items-center justify-center shadow-lg shadow-brand-500/30">
                    <svg class="w-[18px] h-[18px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div class="leading-tight">
                    <p class="font-bold text-sm text-white">Luminus</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">Creative Media</p>
                </div>
            </a>
            <nav class="hidden lg:flex items-center gap-1 ms-4">
                @php $navLabels = [__('messages.nav_programs'), __('messages.nav_features'), __('messages.nav_admissions'), __('messages.nav_faculty'), __('messages.nav_faq')] @endphp
                @foreach($navLabels as $i => $l)
                <a href="#{{ ['programs','platform','admissions','faculty','faq'][$i] }}" class="px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-surface-700 rounded-lg transition-colors">{{ $l }}</a>
                @endforeach
            </nav>
            <div class="ms-auto flex items-center gap-2">
                {{-- Locale toggle --}}
                <form method="POST" action="{{ route('locale.switch') }}" class="flex">
                    @csrf
                    <input type="hidden" name="locale" value="{{ app()->getLocale() === 'en' ? 'ar' : 'en' }}">
                    <button type="submit" class="hidden md:flex items-center gap-1.5 px-3 py-2 text-xs font-mono uppercase tracking-wider text-gray-400 hover:text-white hover:bg-surface-700 rounded-lg transition-colors border border-white/5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="latin">{{ app()->getLocale() === 'en' ? 'AR' : 'EN' }}</span>
                    </button>
                </form>
                {{-- Theme toggle --}}
                <button onclick="
                    let next = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
                    document.documentElement.dataset.theme = next;
                    fetch('{{ route('theme.switch') }}', { method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' }, body:JSON.stringify({ theme: next }) });
                " class="flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 hover:text-white hover:bg-surface-700 transition-colors border border-white/5">
                    <svg class="w-3.5 h-3.5 text-amber-400 theme-icon-sun" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="w-3.5 h-3.5 text-brand-400 theme-icon-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-surface-700 rounded-lg transition-colors">{{ __('messages.sign_in') }}</a>
                <a href="{{ route('signup') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-gradient-brand rounded-lg shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all">
                    {{ __('messages.apply_now') }}
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </header>

    {{-- ═══════════════ MARQUEE ═══════════════ --}}
    <div class="bg-gradient-to-r from-brand-700 via-brand-600 to-coral-600 text-white relative overflow-hidden">
        <div class="marquee-mask flex">
            <div class="flex gap-12 px-6 py-2.5 animate-marquee whitespace-nowrap">
                @foreach(array_merge(
                    [['icon'=>'sparkles','text'=>'Applications Open for Fall 2026'],
                     ['icon'=>'film','text'=>'New Esports Management Program'],
                     ['icon'=>'mic','text'=>'Studio Time — Book Now'],
                     ['icon'=>'award','text'=>'92% Graduate Employability'],
                     ['icon'=>'video','text'=>'Showcase Your Work'],
                     ['icon'=>'sparkles','text'=>'Faculty Hiring — Apply Within']],
                    [['icon'=>'sparkles','text'=>'Applications Open for Fall 2026'],
                     ['icon'=>'film','text'=>'New Esports Management Program'],
                     ['icon'=>'mic','text'=>'Studio Time — Book Now'],
                     ['icon'=>'award','text'=>'92% Graduate Employability'],
                     ['icon'=>'video','text'=>'Showcase Your Work'],
                     ['icon'=>'sparkles','text'=>'Faculty Hiring — Apply Within']]
                ) as $m)
                <span class="inline-flex items-center gap-2.5 text-xs font-mono uppercase tracking-wider text-white/80">
                    <svg class="w-3 h-3 text-coral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>
                    {{ $m['text'] }}
                    <span class="text-white/30 ms-8">&#9670;</span>
                </span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════════════ HERO ═══════════════ --}}
    <section class="relative overflow-hidden">
        <div aria-hidden class="absolute -top-32 -start-32 w-[600px] h-[600px] rounded-full bg-brand-500/20 blur-3xl pointer-events-none animate-spin-slow"></div>
        <div aria-hidden class="absolute top-40 -end-32 w-[500px] h-[500px] rounded-full bg-coral-500/15 blur-3xl pointer-events-none"></div>
        <div aria-hidden class="absolute inset-0 hero-grid-bg pointer-events-none"></div>
        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-10 pt-16 lg:pt-24 pb-20">
            <div class="grid lg:grid-cols-12 gap-10 items-end">
                {{-- Left: hero content --}}
                <div class="lg:col-span-7 space-y-7 animate-fade-in">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-800/80 border border-white/10 text-xs">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-coral-500 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-coral-500"></span>
                        </span>
                        <span class="font-medium text-gray-300">{{ __('messages.hero_subtitle') }}</span>
                        <span class="text-gray-500">&middot;</span>
                        <span class="font-mono text-gray-400">{{ __('messages.hero_days_left') }}</span>
                    </div>
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl xl:text-[88px] font-black tracking-tight text-white leading-[.95]">
                        {{ __('messages.hero_heading_1') }}<br>
                        <span class="gradient-text">{{ __('messages.hero_heading_2') }}</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-gray-400 max-w-xl leading-relaxed">
                        {{ __('messages.hero_description') }}
                        <span class="text-white">{{ __('messages.hero_filmmakers') }}</span>,
                        <span class="text-white">{{ __('messages.hero_audio_engineers') }}</span>,
                        <span class="text-white">{{ __('messages.hero_game_designers') }}</span>,
                        <span class="text-white">{{ __('messages.hero_digital_creators') }}</span>
                        {{ __('messages.hero_and_educators') }}
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('signup') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-brand text-white font-medium rounded-xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all">
                            {{ __('messages.apply_now') }}
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="#" class="inline-flex items-center gap-2 px-5 py-3 bg-surface-700 hover:bg-surface-600 text-gray-200 font-medium rounded-xl border border-white/10 transition-all">
                            <svg class="w-3.5 h-3.5 text-coral-400" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            {{ __('messages.take_a_tour') }}
                        </a>
                    </div>
                    <div class="flex items-center gap-6 pt-4">
                        <div class="flex -space-x-2">
                            @foreach(['from-brand-500 to-brand-700','from-coral-400 to-coral-600','from-blue-400 to-blue-600','from-emerald-400 to-emerald-600'] as $g)
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $g }} border-2 border-surface-900"></div>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-400">
                            <span class="text-white font-semibold">4,128+</span> {{ __('messages.hero_students_learning') }}
                        </p>
                    </div>
                </div>
                {{-- Right: role cards --}}
                <div class="lg:col-span-5 space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-1 flex items-center gap-2">
                        <span class="h-px w-8 bg-gradient-to-r from-brand-500 to-coral-500"></span>
                        {{ __('messages.hero_sign_in_to') }}
                    </p>
                    @foreach($roles as $r)
                    @php
                        $rk = $r['key'];
                        $v = $rk === 'student' ? ['accent'=>'from-brand-500/30 to-brand-700/10','text'=>'text-brand-400','border'=>'border-brand-500/40'] : ['accent'=>'from-coral-500/20 to-coral-700/5','text'=>'text-coral-400','border'=>'border-white/5'];
                        $roleTitle = $rk === 'student' ? __('messages.role_student_title') : __('messages.role_instructor_title');
                        $roleDesc = $rk === 'student' ? __('messages.role_student_desc') : __('messages.role_instructor_desc');
                        $roleCta = $rk === 'student' ? __('messages.role_student_cta') : __('messages.role_instructor_cta');
                        $roleBadge = $rk === 'student' ? __('messages.role_student_badge') : __('messages.role_instructor_badge');
                    @endphp
                    <a href="{{ route('login', ['role' => $r['key']]) }}" class="group block relative overflow-hidden rounded-xl border {{ $r['key'] === 'student' ? $v['border'] : 'border-white/5' }} bg-surface-800 card-hover p-5">
                        <div class="absolute inset-0 bg-gradient-to-br {{ $v['accent'] }} opacity-60 pointer-events-none"></div>
                        <div class="relative flex flex-col gap-3 h-full">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-lg bg-surface-700/80 border border-white/10 flex items-center justify-center {{ $v['text'] }}">
                                    @if($r['icon'] === 'book')
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    @else
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    @endif
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider {{ $v['text'] }} bg-surface-700/60 border {{ $r['key'] === 'student' ? 'border-brand-500/30' : 'border-coral-500/30' }}">{{ $roleBadge }}</span>
                            </div>
                            <div class="space-y-1.5">
                                <h3 class="font-bold text-xl text-white">{{ $roleTitle }}</h3>
                                <p class="text-sm text-gray-400 leading-relaxed">{{ $roleDesc }}</p>
                            </div>
                            <div class="mt-auto pt-3 flex items-center justify-between border-t border-white/5">
                                <span class="text-sm font-medium {{ $v['text'] }}">{{ $roleCta }}</span>
                                <svg class="w-4 h-4 {{ $v['text'] }} group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════ STATS ═══════════════ --}}
    <section class="border-t border-white/5 bg-surface-900/50">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php $statLabels = [__('messages.stat_active_students'), __('messages.stat_programs_offered'), __('messages.stat_industry_partners'), __('messages.stat_employment_rate')] @endphp
                @php $statTrends = [__('messages.stat_trend_students'), __('messages.stat_trend_programs'), __('messages.stat_trend_partners'), __('messages.stat_trend_employment')] @endphp
                @foreach($stats as $i => $s)
                @php $v = $tintMap[$s['tint']] ?? $tintMap['brand'] @endphp
                <div class="bg-surface-800 border border-white/5 rounded-xl p-5 card-hover">
                    <div class="flex items-start justify-between mb-3">
                        <p class="text-sm text-gray-400">{{ $statLabels[$i] ?? $s['label'] }}</p>
                        <div class="w-9 h-9 rounded-lg {{ $v['tint'] }} flex items-center justify-center {{ $v['accent'] }}">
                            @if($s['icon'] === 'users')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                            @elseif($s['icon'] === 'award')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
                            @elseif($s['icon'] === 'briefcase')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                            @endif
                        </div>
                    </div>
                    <p class="text-3xl lg:text-4xl font-bold text-white tracking-tight">{{ $s['value'] }}</p>
                    <p class="text-xs mt-1.5 {{ $v['accent'] }}">&uarr; {{ $statTrends[$i] ?? $s['trend'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════ PROGRAMS GRID ═══════════════ --}}
    <section id="programs" class="relative">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-8">
                    <p class="text-xs font-mono uppercase tracking-widest text-brand-400 mb-4 flex items-center gap-2">
                        <span class="h-px w-8 bg-brand-500"></span>
                        {{ __('messages.section_our_programs') }}
                    </p>
                    <h2 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white tracking-tight leading-[1.05]">
                        {{ __('messages.section_programs_heading') }}
                    </h2>
                </div>
                <p class="lg:col-span-4 text-base text-gray-400 leading-relaxed lg:text-end">
                    {{ __('messages.section_programs_desc') }}
                </p>
            </div>
            <div class="mt-12 grid grid-cols-12 gap-4">
                    @php $programVisuals = [
                        ['emoji'=>'🎬','duration'=>'2 yr &middot; BA','studentsN'=>924,'tag'=> __('messages.most_popular'),'pillCls'=>'bg-rose-500/20 text-rose-300 border-rose-500/30','gradient'=>'from-rose-500/25 via-rose-700/10 to-transparent','reel'=>true],
                        ['emoji'=>'🎧','duration'=>'2 yr &middot; BA','studentsN'=>612,'tag'=>null,'pillCls'=>'bg-amber-500/20 text-amber-300 border-amber-500/30','gradient'=>'from-amber-500/25 via-amber-700/10 to-transparent','reel'=>false],
                        ['emoji'=>'🖥️','duration'=>'18 mo &middot; Diploma','studentsN'=>1218,'tag'=>null,'pillCls'=>'bg-blue-500/20 text-blue-300 border-blue-500/30','gradient'=>'from-blue-500/25 via-blue-700/10 to-transparent','reel'=>false],
                        ['emoji'=>'🎮','duration'=>'2 yr &middot; BA','studentsN'=>840,'tag'=> __('messages.new_cohort'),'pillCls'=>'bg-emerald-500/20 text-emerald-300 border-emerald-500/30','gradient'=>'from-emerald-500/25 via-emerald-700/10 to-transparent','reel'=>false],
                        ['emoji'=>'🕹️','duration'=>'1 yr &middot; Diploma','studentsN'=>534,'tag'=> __('messages.new_badge'),'pillCls'=>'bg-fuchsia-500/20 text-fuchsia-300 border-fuchsia-500/30','gradient'=>'from-fuchsia-500/25 via-fuchsia-700/10 to-transparent','reel'=>false],
                    ] @endphp
                @foreach($programs as $i => $p)
                @php $v = $programVisuals[$i]; $isTall = $i === 0; $span = $i === 0 ? 'col-span-12 lg:col-span-8 lg:row-span-2' : ($i <= 2 ? 'col-span-12 sm:col-span-6 lg:col-span-4' : 'col-span-12 sm:col-span-6 lg:col-span-6') @endphp
                <a href="#" class="{{ $span }} group relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 card-hover">
                    <div aria-hidden class="absolute inset-0 bg-gradient-to-br {{ $v['gradient'] }} opacity-80 pointer-events-none"></div>
                    <div class="relative {{ $isTall ? 'p-7 min-h-[440px]' : 'p-6 min-h-[260px]' }} flex flex-col">
                        <div class="flex items-start justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-surface-700/70 border border-white/10 flex items-center justify-center text-2xl">{{ $v['emoji'] }}</div>
                                <div>
                                    <p class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider border {{ $v['pillCls'] }}">{!! $v['duration'] !!}</p>
                                    <p class="text-[11px] text-gray-500 mt-1 font-mono">{{ number_format($v['studentsN']) }} {{ __('messages.students_count') }}</p>
                                </div>
                            </div>
                            @if($v['tag'])
                            <span class="px-2 py-0.5 text-[10px] font-mono uppercase tracking-wider rounded bg-white/10 text-white border border-white/20">{{ $v['tag'] }}</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-white tracking-tight {{ $isTall ? 'text-5xl lg:text-6xl' : 'text-3xl' }}">{{ $p['name'] }}</h3>
                        @if($isTall)
                        <div class="ph rounded-xl mt-6 mb-6" style="aspect-ratio:16/8"><span class="ph-lbl">Showreel</span></div>
                        @endif
                        <p class="text-gray-400 mt-4 {{ $isTall ? 'text-base max-w-2xl' : 'text-sm' }} leading-relaxed">{{ $p['desc'] }}</p>
                        <div class="mt-5 flex flex-wrap gap-1.5">
                            @foreach($p['tags'] as $tag)
                            <span class="text-[10px] font-mono uppercase tracking-wider px-2 py-1 rounded bg-surface-700/60 border border-white/5 text-gray-400">{{ $tag }}</span>
                            @endforeach
                        </div>
                        <div class="mt-auto pt-5 flex items-center justify-between border-t border-white/5">
                            <span class="text-sm font-medium text-white inline-flex items-center gap-1.5 group-hover:gap-2.5 transition-all">
                                {{ __('messages.explore_program') }}
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════ PLATFORM FEATURES ═══════════════ --}}
    <section id="platform" class="border-t border-white/5 relative overflow-hidden">
        <div aria-hidden class="absolute -start-40 top-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full bg-brand-500/10 blur-3xl"></div>
        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-8">
                    <p class="text-xs font-mono uppercase tracking-widest text-brand-400 mb-4 flex items-center gap-2">
                        <span class="h-px w-8 bg-brand-500"></span>
                        {{ __('messages.section_features_label') }}
                    </p>
                    <h2 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white tracking-tight leading-[1.05]">
                        {{ __('messages.section_features_heading') }}
                    </h2>
                </div>
                <p class="lg:col-span-4 text-base text-gray-400 leading-relaxed lg:text-end">
                    {{ __('messages.section_features_desc') }}
                </p>
            </div>
            <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $featureTitleKeys = [__('messages.feature_video_title'), __('messages.feature_grade_title'), __('messages.feature_portfolio_title'), __('messages.feature_analytics_title'), __('messages.feature_lang_title'), __('messages.feature_shield_title')];
                    $featureDescKeys = [__('messages.feature_video_desc'), __('messages.feature_grade_desc'), __('messages.feature_portfolio_desc'), __('messages.feature_analytics_desc'), __('messages.feature_lang_desc'), __('messages.feature_shield_desc')];
                @endphp
                @foreach($features as $i => $f)
                @php $v = $featureTints[$i] @endphp
                <div class="group relative bg-surface-800 border border-white/5 rounded-xl p-6 card-hover">
                    <div class="w-11 h-11 rounded-xl {{ $v['tint'] }} {{ $v['accent'] }} flex items-center justify-center mb-4 border border-white/5">
                        @if($f['icon'] === 'video')
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        @elseif($f['icon'] === 'grade')
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        @elseif($f['icon'] === 'briefcase')
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        @elseif($f['icon'] === 'dashboard')
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        @elseif($f['icon'] === 'lang')
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @endif
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2 tracking-tight">{{ $featureTitleKeys[$i] }}</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">{{ $featureDescKeys[$i] }}</p>
                    <svg class="w-3.5 h-3.5 absolute top-6 end-6 text-gray-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
                @endforeach
            </div>
            <div class="mt-10 grid lg:grid-cols-12 gap-4">
                <div class="lg:col-span-7 bg-surface-800 border border-white/5 rounded-xl p-6 lg:p-7">
                    <p class="text-xs font-mono uppercase tracking-widest text-gray-500 mb-3">{{ __('messages.replaces_label') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($replaces as $item)
                        <span class="latin px-3 py-1.5 text-sm bg-surface-700 border border-white/5 rounded-lg text-gray-300 line-through opacity-60">{{ $item }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="lg:col-span-5 bg-gradient-to-br from-brand-600/20 to-coral-500/10 border border-brand-500/30 rounded-xl p-6 lg:p-7">
                    <p class="text-xs font-mono uppercase tracking-widest text-brand-300 mb-3">{{ __('messages.with_one_platform') }}</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-brand flex items-center justify-center shadow-lg shadow-brand-500/30">
                            <svg class="w-[22px] h-[22px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-white">Luminus Digital Creative Technology</p>
                            <p class="text-xs text-gray-400">{{ __('messages.built_for_creative') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════ COHORTS TABLE ═══════════════ --}}
    <section id="admissions" class="border-t border-white/5 bg-surface-900/40">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-8">
                    <p class="text-xs font-mono uppercase tracking-widest text-brand-400 mb-4 flex items-center gap-2">
                        <span class="h-px w-8 bg-brand-500"></span>
                        {{ __('messages.section_cohorts_label') }}
                    </p>
                    <h2 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white tracking-tight leading-[1.05]">
                        {{ __('messages.section_cohorts_heading') }}
                    </h2>
                </div>
                <p class="lg:col-span-4 text-base text-gray-400 leading-relaxed lg:text-end">
                    {{ __('messages.section_cohorts_desc') }}
                </p>
            </div>
            <div class="mt-12 bg-surface-800 border border-white/5 rounded-xl overflow-hidden">
                <div class="grid grid-cols-12 gap-4 px-6 py-3.5 border-b border-white/5 bg-surface-700/40 text-[11px] font-mono uppercase tracking-widest text-gray-500">
                    <div class="col-span-2 flex items-center gap-2"><svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>{{ __('messages.cohort_start') }}</div>
                    <div class="col-span-4">{{ __('messages.cohort_program') }}</div>
                    <div class="col-span-3">{{ __('messages.cohort_location') }}</div>
                    <div class="col-span-1 text-end">{{ __('messages.cohort_seats') }}</div>
                    <div class="col-span-2 text-end">{{ __('messages.cohort_status') }}</div>
                </div>
                @foreach($cohorts as $c)
                @php $ratio = $c['seatsNum'] / $c['seatsMax'] @endphp
                <a href="#" class="grid grid-cols-12 gap-4 px-6 py-5 border-b border-white/5 last:border-b-0 hover:bg-surface-700/30 transition-colors items-center group">
                    <div class="col-span-2 flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full {{ $progDot[$c['prog']] ?? 'bg-brand-500' }}"></div>
                        <span class="font-mono text-sm text-white">{{ $c['date'] }}</span>
                        <span class="font-mono text-xs text-gray-500">{{ $c['year'] }}</span>
                    </div>
                    <div class="col-span-4 font-medium text-white text-[15px]">{{ $c['program'] }}</div>
                    <div class="col-span-3 text-sm text-gray-400 flex items-center gap-2">
                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $c['loc'] }}
                    </div>
                    <div class="col-span-1 text-end">
                        <span class="font-mono text-sm text-gray-300">{{ $c['seats'] }}</span>
                        <div class="mt-1 ms-auto w-14 h-1 bg-surface-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-brand" style="width:{{ $ratio * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="col-span-2 flex items-center justify-end gap-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider border {{ $statusCls[$c['status']] }}">{{ $statusLabel[$c['status']] }}</span>
                        <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-gray-400 inline-flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-mono text-gray-500">{{ __('messages.cohort_apps_close') }}</span>
                </p>
                <a href="{{ route('signup') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-surface-700 hover:bg-surface-600 border border-white/10 text-white text-sm font-medium rounded-lg transition-all">
                    {{ __('messages.start_application') }}
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ═══════════════ FACULTY GRID ═══════════════ --}}
    <section id="faculty" class="border-t border-white/5">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-8">
                    <p class="text-xs font-mono uppercase tracking-widest text-brand-400 mb-4 flex items-center gap-2">
                        <span class="h-px w-8 bg-brand-500"></span>
                        {{ __('messages.section_faculty_label') }}
                    </p>
                    <h2 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white tracking-tight leading-[1.05]">
                        {{ __('messages.section_faculty_heading') }}
                    </h2>
                </div>
                <p class="lg:col-span-4 text-base text-gray-400 leading-relaxed lg:text-end">
                    {{ __('messages.section_faculty_desc') }}
                </p>
            </div>
            <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($faculty as $i => $f)
                <a href="#" class="group bg-surface-800 border border-white/5 rounded-xl overflow-hidden card-hover flex flex-col">
                    <div class="ph" style="aspect-ratio:4/5"><span class="ph-lbl">Portrait &mdash; {{ explode(' ', $f['name'])[0] }}</span></div>
                    <div class="p-5 space-y-3 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="text-lg font-bold text-white leading-tight">{{ $f['name'] }}</h3>
                                <p class="text-xs text-coral-400 mt-1 font-medium">{{ $f['role'] }}</p>
                            </div>
                            <span class="w-2 h-2 rounded-full {{ $facultyDot[$i] }} mt-2 flex-shrink-0"></span>
                        </div>
                        <p class="text-sm text-gray-400 leading-relaxed flex-1">{{ $f['bio'] }}</p>
                        <div class="flex flex-wrap gap-1.5 pt-3 border-t border-white/5">
                            @foreach($f['credits'] as $c)
                            <span class="text-[10px] font-mono uppercase tracking-wider px-2 py-1 rounded bg-surface-700 border border-white/5 text-gray-400">{{ $c }}</span>
                            @endforeach
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════ WORK SHOWCASE ═══════════════ --}}
    <section id="student-work" class="border-t border-white/5 bg-black/40 relative overflow-hidden">
        <div aria-hidden class="absolute end-0 top-20 w-[600px] h-[600px] rounded-full bg-coral-500/10 blur-3xl"></div>
        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-8">
                    <p class="text-xs font-mono uppercase tracking-widest text-brand-400 mb-4 flex items-center gap-2">
                        <span class="h-px w-8 bg-brand-500"></span>
                        {{ __('messages.section_work_label') }}
                    </p>
                    <h2 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white tracking-tight leading-[1.05]">
                        {{ __('messages.section_work_heading') }}
                    </h2>
                </div>
                <p class="lg:col-span-4 text-base text-gray-400 leading-relaxed lg:text-end">
                    {{ __('messages.section_work_desc') }}
                </p>
            </div>
            <div class="mt-12 grid grid-cols-12 gap-4">
                @foreach($works as $i => $w)
                @php $v = $worksVisual[$i] @endphp
                <a href="#" class="{{ $v['span'] }} group rounded-xl overflow-hidden border border-white/5 bg-surface-800 card-hover">
                    <div class="ph relative" style="aspect-ratio:{{ $v['ratio'] }}">
                        <span class="ph-lbl">{{ $w['kind'] }}</span>
                        <div class="absolute inset-0 flex items-end p-5">
                            <div class="bg-gradient-to-t from-black/80 via-black/40 to-transparent absolute inset-0 pointer-events-none"></div>
                            <div class="relative space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $v['dot'] }}"></span>
                                    <span class="text-[10px] font-mono uppercase tracking-widest text-white/70">{{ $w['prog'] }}</span>
                                </div>
                                <h3 class="font-bold text-white tracking-tight {{ $i === 0 ? 'text-3xl lg:text-4xl' : 'text-xl' }}">{{ $w['t'] }}</h3>
                                <p class="text-sm text-white/70">{{ __('messages.work_by') }} {{ $w['by'] }}</p>
                            </div>
                            <div class="absolute top-4 end-4 w-9 h-9 rounded-full bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center group-hover:bg-white group-hover:text-black transition-all">
                                <svg class="w-3.5 h-3.5 text-white group-hover:text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
                <p class="text-xs font-mono uppercase tracking-widest text-gray-500">{{ __('messages.work_showing') }}</p>
                <a href="#" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-brand text-white text-sm font-medium rounded-lg shadow-lg shadow-brand-500/30">
                    {{ __('messages.work_enter') }}
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ═══════════════ ALUMNI QUOTE ═══════════════ --}}
    <section class="border-t border-white/5">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-12 gap-10 items-start">
                <div class="lg:col-span-4 space-y-4">
                    <p class="text-xs font-mono uppercase tracking-widest text-coral-400 flex items-center gap-2">
                        <svg class="w-3 h-3 text-coral-400" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151C7.546 6.068 5.983 8.789 5.983 11H10v10H0z"/></svg>
                        {{ __('messages.section_alumni_label') }}
                    </p>
                    <div class="ph rounded-xl" style="aspect-ratio:1/1;max-width:280px"><span class="ph-lbl">Portrait</span></div>
                    <div>
                        <p class="text-xl font-bold text-white">Nadia Al-Fayez</p>
                        <p class="text-sm text-gray-400 mt-1">{{ __('messages.alumni_class_of') }}</p>
                        <p class="text-xs font-mono uppercase tracking-wider text-coral-400 mt-2">{{ __('messages.alumni_now') }}</p>
                    </div>
                </div>
                <blockquote class="lg:col-span-8 text-2xl lg:text-3xl xl:text-4xl text-white font-light leading-tight tracking-tight">
                    <span class="text-coral-400 text-5xl leading-none">&ldquo;</span>
                    {{ __('messages.alumni_quote') }}
                    <span class="text-coral-400 text-5xl leading-none">&rdquo;</span>
                    <footer class="mt-6 flex items-center gap-4 text-sm font-mono text-gray-500 uppercase tracking-widest not-italic">
                        <span class="h-px w-12 bg-gray-700"></span>
                        {{ __('messages.alumni_featured') }}
                    </footer>
                </blockquote>
            </div>
        </div>
    </section>

    {{-- ═══════════════ FAQ ═══════════════ --}}
    <section id="faq" class="border-t border-white/5">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-8">
                    <p class="text-xs font-mono uppercase tracking-widest text-brand-400 mb-4 flex items-center gap-2">
                        <span class="h-px w-8 bg-brand-500"></span>
                        {{ __('messages.section_faq_label') }}
                    </p>
                    <h2 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white tracking-tight leading-[1.05]">
                        {{ __('messages.section_faq_heading') }}
                    </h2>
                </div>
            </div>
            <div class="mt-12 max-w-4xl mx-auto bg-surface-800 border border-white/5 rounded-xl overflow-hidden" x-data="{ open: 0 }">
                @foreach($faq as $i => $f)
                <div class="border-b border-white/5 last:border-b-0">
                    <button @click="open = (open === {{ $i }} ? -1 : {{ $i }})" class="w-full text-start px-6 py-5 hover:bg-surface-700/30 transition-colors">
                        <div class="flex items-start justify-between gap-6">
                            <div class="flex items-start gap-4 flex-1">
                                <span class="font-mono text-xs text-brand-400 pt-1">{{ sprintf('%02d', $i + 1) }}</span>
                                <span class="text-lg text-white font-medium leading-snug">{{ $f['q'] }}</span>
                            </div>
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-all" :class="open === {{ $i }} ? 'bg-gradient-brand' : 'bg-surface-700'">
                                <svg x-show="open !== {{ $i }}" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <svg x-show="open === {{ $i }}" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </div>
                        </div>
                    </button>
                    <div x-show="open === {{ $i }}" x-transition class="ps-10 pe-12 pb-5 text-sm text-gray-400 leading-relaxed max-w-2xl">
                        {{ $f['a'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════ CTA ═══════════════ --}}
    <section class="relative border-t border-white/5 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-brand"></div>
        <div aria-hidden class="absolute inset-0 hero-grid-bg opacity-30 pointer-events-none"></div>
        <div aria-hidden class="absolute -top-32 -end-20 w-[500px] h-[500px] rounded-full bg-white/10 blur-3xl"></div>
        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-10 py-20 lg:py-28">
            <div class="grid lg:grid-cols-12 gap-10 items-end">
                <div class="lg:col-span-8 space-y-5">
                    <p class="text-xs font-mono uppercase tracking-widest text-white/80 flex items-center gap-2">
                        <span class="h-px w-8 bg-white/60"></span>
                        {{ __('messages.section_cta_label') }}
                    </p>
                    <h2 class="text-5xl lg:text-7xl xl:text-8xl font-black text-white tracking-tight leading-[.95]">
                        {{ __('messages.section_cta_heading_1') }}<br>
                        <span class="text-white/80">{{ __('messages.section_cta_heading_2') }}</span>
                    </h2>
                    <p class="text-lg text-white/80 max-w-xl">{{ __('messages.section_cta_desc') }}</p>
                </div>
                <div class="lg:col-span-4 flex flex-col gap-3 lg:items-end">
                    <a href="{{ route('signup') }}" class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-white text-brand-700 text-base font-bold rounded-xl shadow-2xl shadow-black/30 hover:scale-[1.02] transition-transform">
                        {{ __('messages.apply_now') }}
                        <svg class="w-[18px] h-[18px] text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="#" class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-white/10 backdrop-blur text-white text-base font-medium rounded-xl border border-white/30 hover:bg-white/20 transition-colors">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('messages.schedule_tour') }}
                    </a>
                    <p class="text-xs text-white/60 mt-2 lg:text-end font-mono uppercase tracking-wider">{{ __('messages.cta_no_fee') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════ FOOTER ═══════════════ --}}
    <footer class="border-t border-white/5 bg-surface-900">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10 pt-16 pb-10">
            <div class="grid lg:grid-cols-12 gap-10">
                <div class="lg:col-span-4 space-y-5">
                    <a href="/" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-brand flex items-center justify-center shadow-lg shadow-brand-500/30">
                            <svg class="w-[18px] h-[18px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-white">Luminus</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">Creative Media</p>
                        </div>
                    </a>
                    <address class="not-italic text-sm text-gray-400 leading-relaxed">
                        {{ __('messages.footer_address_line1') }}<br>
                        {{ __('messages.footer_address_line2') }}<br>
                        {{ __('messages.footer_address_line3') }}
                    </address>
                    <div class="space-y-1.5 text-sm font-mono text-gray-500">
                        <p class="latin">+962 6 580 0000</p>
                        <p class="latin">info@luminus.jo</p>
                    </div>
                    <div class="flex gap-2 pt-2">
                        @foreach([['instagram','M','bg-gradient-to-br from-purple-500 to-pink-500'],['youtube','▶','bg-red-600'],['linkedin','in','bg-blue-600'],['github','G','bg-gray-600']] as [$s,$l,$bg])
                        <a href="#" class="w-9 h-9 rounded-lg {{ $bg }} flex items-center justify-center text-white text-xs font-bold hover:opacity-80 transition-all">{{ $l }}</a>
                        @endforeach
                    </div>
                </div>
                @php
                    $footerCols = [
                        [__('messages.footer_col_programs'), [__('messages.footer_link_film'), __('messages.footer_link_audio'), __('messages.footer_link_digital'), __('messages.footer_link_game'), __('messages.footer_link_esports')]],
                        [__('messages.footer_col_admissions'), [__('messages.footer_link_how_to_apply'), __('messages.footer_link_requirements'), __('messages.footer_link_tuition'), __('messages.footer_link_dates'), __('messages.footer_link_contact')]],
                        [__('messages.footer_col_campus'), [__('messages.footer_link_facilities'), __('messages.footer_link_student_life'), __('messages.footer_link_careers'), __('messages.footer_link_alumni'), __('messages.footer_link_support')]],
                        [__('messages.footer_col_platform'), [__('messages.footer_link_features'), __('messages.footer_link_for_instructors'), __('messages.footer_link_for_students'), __('messages.footer_link_pricing'), __('messages.footer_link_api')]],
                    ];
                @endphp
                @foreach($footerCols as [$colTitle, $colLinks])
                <div class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-4">{{ $colTitle }}</p>
                    <ul class="space-y-2.5">
                        @foreach($colLinks as $l)
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">{!! $l !!}</a></li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
            <div class="mt-14 pt-6 border-t border-white/5 flex flex-wrap items-center justify-between gap-4 text-xs font-mono uppercase tracking-widest text-gray-500">
                <p>{{ __('messages.footer_copyright', ['year' => date('Y')]) }}</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_privacy') }}</a>
                    <a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_terms') }}</a>
                    <a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_cookies') }}</a>
                    <a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_accessibility') }}</a>
                </div>
                <p class="latin">{{ __('messages.footer_version') }}</p>
            </div>
        </div>
    </footer>

    {{-- Alpine.js for FAQ accordion --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</x-layouts.landing>
