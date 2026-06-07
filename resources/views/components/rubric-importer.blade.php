<div x-data="{ preview: null, criteria: [], levels: [], cells: [] }" class="bg-surface-800 border border-white/10 rounded-xl p-6">
    <h3 class="text-lg font-semibold text-white mb-4">{{ __('Import Rubric from Brightspace') }}</h3>

    <form @submit.prevent="
        const file = $refs.fileInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            const parser = new DOMParser();
            const xml = parser.parseFromString(e.target.result, 'text/xml');
            const parseError = xml.querySelector('parsererror');
            if (parseError) { alert('Invalid XML file'); return; }

            const items = [];
            const levelSet = new Set();

            xml.querySelectorAll('criterion').forEach((c, ci) => {
                const name = c.querySelector('name')?.textContent || 'Criterion ' + (ci + 1);
                items.push({ name });
                c.querySelectorAll('level').forEach((l) => {
                    const lname = l.querySelector('name')?.textContent || '';
                    if (lname && !levelSet.has(lname)) levelSet.add(lname);
                });
            });

            const lvls = Array.from(levelSet).map(n => ({ name: n }));
            const clls = [];
            xml.querySelectorAll('criterion').forEach((c, ci) => {
                c.querySelectorAll('level').forEach((l) => {
                    const lname = l.querySelector('name')?.textContent || '';
                    const li = lvls.findIndex(x => x.name === lname);
                    clls.push({
                        criterion: ci,
                        level: li,
                        score: parseFloat(l.querySelector('score')?.textContent || 0),
                        description: l.querySelector('description')?.textContent || '',
                    });
                });
            });

            preview = true;
            criteria = items;
            levels = lvls;
            cells = clls;
        };
        reader.readAsText(file);
    ">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Select Brightspace XML file') }}</label>
            <input type="file" x-ref="fileInput" accept=".xml" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-brand-600 file:text-white hover:file:bg-brand-500 file:cursor-pointer cursor-pointer">
        </div>

        <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
            {{ __('Preview') }}
        </button>
    </form>

    <template x-if="preview">
        <div class="mt-6">
            <div class="border border-white/10 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-700">
                            <th class="p-3 text-left text-gray-300 font-medium">{{ __('Criteria') }}</th>
                            <template x-for="level in levels" :key="level.name">
                                <th class="p-3 text-center text-gray-300 font-medium" x-text="level.name"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(criterion, ci) in criteria" :key="ci">
                            <tr class="border-t border-white/10">
                                <td class="p-3 text-white font-medium" x-text="criterion.name"></td>
                                <template x-for="(level, li) in levels" :key="li">
                                    <td class="p-3 text-center text-gray-400">
                                        <template x-for="cell in cells.filter(c => c.criterion === ci && c.level === li)" :key="cell.criterion + '-' + cell.level">
                                            <div>
                                                <span class="text-white font-semibold" x-text="cell.score"></span>
                                                <p class="text-xs text-gray-500 mt-1" x-text="cell.description"></p>
                                            </div>
                                        </template>
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <form method="POST" action="{{ route('courses.rubrics.import', $course) }}" class="mt-4">
                @csrf
                <input type="hidden" name="criteria" x-bind:value="JSON.stringify(criteria)">
                <input type="hidden" name="levels" x-bind:value="JSON.stringify(levels)">
                <input type="hidden" name="cells" x-bind:value="JSON.stringify(cells)">
                <div class="mb-4">
                    <label for="import_title" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Rubric Title') }}</label>
                    <input id="import_title" name="title" type="text" required class="input-dashboard" placeholder="{{ __('Enter rubric title') }}">
                </div>
                <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
                    {{ __('Import Rubric') }}
                </button>
            </form>
        </div>
    </template>
</div>
