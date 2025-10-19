<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Alice Studio — Step-by-Step Creator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #0a0a0f;
            color: #fff;
        }

        .glass {
            background: rgba(255, 255, 255, .03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, .08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
            box-shadow: 0 4px 20px rgba(99, 102, 241, .4), 0 8px 40px rgba(236, 72, 153, .3);
            transition: all .3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 30px rgba(99, 102, 241, .5), 0 12px 60px rgba(236, 72, 153, .4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            transition: all .3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, .1);
            border-color: rgba(99, 102, 241, .3);
        }

        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%
            }
            50% {
                background-position: 100% 50%
            }
        }

        .chip {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            transition: all .3s ease;
            cursor: pointer;
        }

        .chip:hover {
            background: rgba(99, 102, 241, .2);
            border-color: rgba(99, 102, 241, .4);
            transform: translateY(-2px);
        }

        .chip-active {
            background: linear-gradient(135deg, rgba(99, 102, 241, .3), rgba(139, 92, 246, .3));
            border: 1px solid #6366f1;
            box-shadow: 0 0 20px rgba(99, 102, 241, .4);
        }

        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            background: rgba(255, 255, 255, .1);
            height: 6px;
            border-radius: 3px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            cursor: pointer;
            box-shadow: 0 0 10px rgba(99, 102, 241, .6);
        }

        input[type="range"]::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            cursor: pointer;
            box-shadow: 0 0 10px rgba(99, 102, 241, .6);
            border: none;
        }

        .input-field {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            color: #fff;
            transition: all .3s ease;
        }

        .input-field:focus {
            background: rgba(255, 255, 255, .08);
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 20px rgba(99, 102, 241, .3);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% {
                transform: translateY(0)
            }
            50% {
                transform: translateY(-10px)
            }
        }

        select option {
            background: #1a1a24;
            color: #fff;
        }

        .gradient-text {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .step-indicator {
            position: relative;
        }

        .step-indicator::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 100%;
            height: 2px;
            background: rgba(255, 255, 255, .1);
        }

        .step-indicator:last-child::after {
            display: none;
        }

        .step-complete {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-color: #6366f1;
            box-shadow: 0 0 20px rgba(99, 102, 241, .5);
        }

        .step-complete::after {
            background: linear-gradient(90deg, #6366f1, rgba(99, 102, 241, .3));
        }

        .step-active {
            background: linear-gradient(135deg, rgba(99, 102, 241, .3), rgba(139, 92, 246, .3));
            border: 2px solid #6366f1;
            box-shadow: 0 0 30px rgba(99, 102, 241, .6);
        }

        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
            animation: fadeIn .3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px)
            }
            to {
                opacity: 1;
                transform: translateY(0)
            }
        }
    </style>
</head>
<body class="min-h-screen">
<!-- Animated background -->
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl floating"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl floating"
         style="animation-delay:1s;"></div>
    <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl floating"
         style="animation-delay:2s;"></div>
</div>

<main class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-3">
            <span class="inline-block w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            <span class="text-sm font-semibold">Guided Creator</span>
        </div>
        <h1 class="text-5xl font-bold gradient-text mb-2">Create Your Perfect Image</h1>
        <p class="text-gray-400">Follow the steps to build your masterpiece</p>
    </div>

    <!-- Step Progress -->
    <div class="glass rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex-1 flex items-center gap-4" id="stepIndicators"></div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-400" id="stepLabels"></div>
    </div>

    <!-- Main Content Card -->
    <div class="glass rounded-3xl overflow-hidden">
        <div class="p-8">
            <div id="stepContainer"><!-- Steps injected here --></div>

            <!-- Preview -->
            <div class="glass rounded-2xl p-4 mb-6">
                <div class="text-sm font-medium text-gray-300 mb-3">Preview</div>
                <div class="rounded-xl overflow-hidden border border-white/10">
                    <div class="aspect-video w-full bg-gradient-to-br from-gray-900 to-gray-800">
                        <img src="https://images.unsplash.com/photo-1612198182706-f09f36c5a3a3?q=80&w=1200&auto=format&fit=crop"
                             alt="preview" class="w-full h-full object-cover"/>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between gap-4">
                <button id="prevBtn" class="btn-secondary h-12 px-8 rounded-xl font-semibold">← Back</button>
                <div class="text-sm text-gray-400" id="stepCounter">Step 1 of 1</div>
                <button id="nextBtn" class="btn-primary h-12 px-8 rounded-xl text-white font-semibold">Next →</button>
            </div>
        </div>
    </div>

    <!-- Quick Tips -->
    <div class="mt-6 glass rounded-2xl p-4" id="tipContainer"></div>
</main>

<script>
    // ===== Config =====
    const studioCode = 'model';        // from your DB (studios.code)
    let userGender = 'both';           // 'male' | 'female' | 'both'

    // ===== State =====
    let currentStepIndex = 0;
    let steps = []; // from API
    const selections = {}; // { sectionCode: { type, values/tokens/files... } }

    // ===== API =====
    async function fetchStudioTree(code, gender = 'both') {
        const res = await fetch(`/api/v1/studios/${code}?gender=${encodeURIComponent(gender)}`);
        if (!res.ok) throw new Error('Failed to load studio');
        const json = await res.json();
        return json.data || json; // Support resource or plain JSON
    }

    // ===== Rendering utilities =====
    function setTip(text) {
        document.getElementById('tipContainer').innerHTML = `
        <div class="flex items-start gap-3">
          <div class="text-2xl">💡</div>
          <div>
            <div class="font-semibold text-sm mb-1">Pro Tip</div>
            <div class="text-sm text-gray-400">${text}</div>
          </div>
        </div>`;
    }

    function renderIndicators() {
        const stepNames = steps.map(s => s.title);
        const current = currentStepIndex + 1;
        const total = steps.length;

        const indicatorsHTML = stepNames.map((_, i) => {
            const num = i + 1;
            let cls = 'step-indicator w-12 h-12 rounded-full flex items-center justify-center font-bold border-2';
            if (num < current) cls += ' step-complete', label = '✓';
            else if (num === current) cls += ' step-active', label = num;
            else cls += ' glass border', label = num;
            var label = (num < current) ? '✓' : num;
            return `<div class="${cls}">${label}</div>`;
        }).join('');
        document.getElementById('stepIndicators').innerHTML = indicatorsHTML;

        const labelsHTML = stepNames.map((name, i) => {
            const isActive = i === currentStepIndex;
            return `<span class="${isActive ? 'text-indigo-400 font-semibold' : ''}">${name}</span>`;
        }).join('');
        document.getElementById('stepLabels').innerHTML = labelsHTML;

        document.getElementById('stepCounter').textContent = `Step ${current} of ${total}`;
    }

    function chip(label, active, slug, iconUrl) {
        const base = active ? 'chip-active' : 'chip';
        return `
        <button class="${base} rounded-xl p-3 text-center text-sm" data-choice="${slug}">
          ${iconUrl ? `<img src="${iconUrl}" alt="" class="w-5 h-5 inline-block mr-1 align-[-2px]"/>` : ''}
          ${label}
        </button>`;
    }

    function renderSection(sec) {
        const secAttr = `data-section="${sec.code}" data-mode="${sec.selection_mode}" data-max="${sec.max_select || 1}" data-min="${sec.min_select || 0}" data-group="${sec.group?.code || ''}" ${sec.group?.exclusive_sections ? 'data-exclusive="1"' : ''}`;
        const title = `<div class="text-sm font-medium text-gray-300 mb-2">${sec.name}${sec.is_required ? ' <span class="text-pink-400">*</span>' : ''}</div>`;

        if (sec.input_type === 'chips') {
            const gridCols = (sec.max_select && sec.max_select > 4) ? 'grid-cols-4' : 'grid-cols-3';
            const cards = (sec.choices || []).map(c => chip(c.label, !!c.is_default, c.slug, c.icon_url)).join('');
            return `<div class="mb-6" ${secAttr}>
          ${title}
          <div class="grid ${gridCols} gap-3">${cards}</div>
        </div>`;
        }

        if (sec.input_type === 'text') {
            return `<div class="mb-6" ${secAttr}>
          ${title}
          <input class="w-full rounded-xl input-field p-3 text-sm" placeholder="Type here"/>
        </div>`;
        }

        if (sec.input_type === 'textarea') {
            return `<div class="mb-6" ${secAttr}>
          ${title}
          <textarea rows="4" class="w-full rounded-xl input-field p-4 text-base resize-none" placeholder="Write here..."></textarea>
        </div>`;
        }

        if (sec.input_type === 'upload') {
            return `<div class="mb-6" ${secAttr}>
          ${title}
          <input type="file" accept="image/*" class="w-full rounded-xl input-field p-3 text-sm"/>
        </div>`;
        }

        return '';
    }

    function renderCurrentStep() {
        const step = steps[currentStepIndex];
        const sectionsHTML = (step.sections || []).map(renderSection).join('');
        document.getElementById('stepContainer').innerHTML = `
        <div class="mb-8 step-content active">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center font-bold">${currentStepIndex + 1}</div>
            <div>
              <h2 class="text-2xl font-bold gradient-text">${step.title}</h2>
              ${step.subtitle ? `<p class="text-gray-400 text-sm">${step.subtitle}</p>` : ''}
            </div>
          </div>
          ${sectionsHTML}
        </div>`;

        bindInteractionsForStep();
        setTip('Use the options to configure your scene. All data is coming from Admin.');
    }

    // ===== Selection logic (exclusivity & min/max) =====
    function enforceExclusivity(container, clickedBtn) {
        const groupCode = container.dataset.group;
        const isExclusive = container.dataset.exclusive === '1';
        if (!groupCode || !isExclusive) return;

        // Deactivate all chips in same exclusive group (across sections in the step)
        const root = document.getElementById('stepContainer');
        root.querySelectorAll(`[data-group="${groupCode}"] .chip-active`).forEach(el => {
            if (el !== clickedBtn) {
                el.classList.remove('chip-active');
                el.classList.add('chip');
            }
        });
    }

    function bindInteractionsForStep() {
        // chips
        document.querySelectorAll('#stepContainer [data-section] .chip, #stepContainer [data-section] .chip-active')
            .forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const container = e.currentTarget.closest('[data-section]');
                    const mode = container.dataset.mode;
                    const max = parseInt(container.dataset.max || '1', 10);

                    if (mode === 'single') {
                        container.querySelectorAll('.chip-active').forEach(x => {
                            x.classList.remove('chip-active');
                            x.classList.add('chip');
                        });
                        e.currentTarget.classList.remove('chip');
                        e.currentTarget.classList.add('chip-active');
                    } else {
                        const isActive = e.currentTarget.classList.contains('chip-active');
                        if (isActive) {
                            e.currentTarget.classList.remove('chip-active');
                            e.currentTarget.classList.add('chip');
                        } else {
                            const activeCount = container.querySelectorAll('.chip-active').length;
                            if (activeCount < max) {
                                e.currentTarget.classList.remove('chip');
                                e.currentTarget.classList.add('chip-active');
                            }
                        }
                    }
                    enforceExclusivity(container, e.currentTarget);
                    saveSectionState(container);
                    updateSummaryPreview();
                });
            });

        // text / textarea
        document.querySelectorAll('#stepContainer [data-section] input[type="text"], #stepContainer [data-section] textarea')
            .forEach(input => {
                input.addEventListener('input', (e) => {
                    const container = e.currentTarget.closest('[data-section]');
                    saveSectionState(container);
                    updateSummaryPreview();
                });
            });

        // file
        document.querySelectorAll('#stepContainer [data-section] input[type="file"]')
            .forEach(input => {
                input.addEventListener('change', (e) => {
                    const container = e.currentTarget.closest('[data-section]');
                    saveSectionState(container);
                    updateSummaryPreview();
                });
            });
    }

    function saveSectionState(container) {
        const sectionCode = container.dataset.section;
        const mode = container.dataset.mode;

        if (!selections[sectionCode]) selections[sectionCode] = {type: container.getAttribute('data-type') || 'chips'};

        // chips
        const activeChips = Array.from(container.querySelectorAll('.chip-active')).map(b => b.getAttribute('data-choice'));
        if (activeChips.length) {
            selections[sectionCode] = {type: 'chips', values: activeChips};
            return;
        }
        // text
        const textInput = container.querySelector('input[type="text"]');
        if (textInput) {
            selections[sectionCode] = {type: 'text', value: textInput.value || ''};
            return;
        }
        // textarea
        const textarea = container.querySelector('textarea');
        if (textarea) {
            selections[sectionCode] = {type: 'textarea', value: textarea.value || ''};
            return;
        }
        // file
        const fileInput = container.querySelector('input[type="file"]');
        if (fileInput) {
            selections[sectionCode] = {type: 'upload', file: (fileInput.files && fileInput.files[0]) || null};
            return;
        }

        // fallback
        selections[sectionCode] = {type: 'unknown'};
    }

    function updateSummaryPreview() {
        // You can build a human-readable summary here or assemble prompt tokens
        // Example (console): console.log('Current selections', JSON.parse(JSON.stringify(selections)));
    }

    // ===== Navigation =====
    function syncNavButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        prevBtn.disabled = currentStepIndex === 0;
        prevBtn.style.opacity = currentStepIndex === 0 ? '0.5' : '1';
        nextBtn.innerHTML = (currentStepIndex === steps.length - 1) ? '✨ Generate Image' : 'Next →';

        prevBtn.onclick = () => {
            if (currentStepIndex > 0) {
                currentStepIndex--;
                renderIndicators();
                renderCurrentStep();
            }
        };

        nextBtn.onclick = () => {
            if (currentStepIndex < steps.length - 1) {
                currentStepIndex++;
                renderIndicators();
                renderCurrentStep();
            } else {
                // Final generate action (assemble prompt/tokens & call your image API)
                alert('🎉 Generating your image with admin-driven settings...');
                // TODO: assemblePromptFromSelections(selections)
            }
        };
    }

    // ===== Boot =====
    (async function init() {
        try {
            const studio = await fetchStudioTree(studioCode, userGender);
            steps = (studio.steps || []).sort((a, b) => a.order - b.order);

            // Basic fallback if empty
            if (!steps.length) {
                document.getElementById('stepContainer').innerHTML = '<div class="text-gray-400">No steps configured.</div>';
                return;
            }

            renderIndicators();
            renderCurrentStep();
            syncNavButtons();
        } catch (e) {
            console.error(e);
            document.getElementById('stepContainer').innerHTML = '<div class="text-red-400">Failed to load from Admin. Check API.</div>';
        }
    })();
</script>
</body>
</html>
