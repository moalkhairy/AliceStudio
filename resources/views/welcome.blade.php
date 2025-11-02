{{-- resources/views/creator/index.blade.php --}}
        <!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Alice Studio — Step-by-Step Creator</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #0a0a0f;
            color: #fff
        }

        .glass {
            background: rgba(255, 255, 255, .03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, .08)
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
            box-shadow: 0 4px 20px rgba(99, 102, 241, .4), 0 8px 40px rgba(236, 72, 153, .3);
            transition: .3s
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 30px rgba(99, 102, 241, .5), 0 12px 60px rgba(236, 72, 153, .4)
        }

        .btn-secondary {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            transition: .3s
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, .1);
            border-color: rgba(99, 102, 241, .3)
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
            transition: .2s;
            cursor: pointer
        }

        .chip:hover {
            background: rgba(99, 102, 241, .2);
            border-color: rgba(99, 102, 241, .4);
            transform: translateY(-2px)
        }

        .chip-active {
            background: linear-gradient(135deg, rgba(99, 102, 241, .3), rgba(139, 92, 246, .3));
            border: 1px solid #6366f1;
            box-shadow: 0 0 20px rgba(99, 102, 241, .4)
        }

        .input-field {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            color: #fff;
            transition: .3s
        }

        .input-field:focus {
            background: rgba(255, 255, 255, .08);
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 20px rgba(99, 102, 241, .3)
        }

        .gradient-text {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text
        }

        .step-indicator {
            position: relative
        }

        .step-indicator::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 100%;
            height: 2px;
            background: rgba(255, 255, 255, .1)
        }

        .step-indicator:last-child::after {
            display: none
        }

        .step-complete {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-color: #6366f1;
            box-shadow: 0 0 20px rgba(99, 102, 241, .5)
        }

        .step-active {
            background: linear-gradient(135deg, rgba(99, 102, 241, .3), rgba(139, 92, 246, .3));
            border: 2px solid #6366f1;
            box-shadow: 0 0 30px rgba(99, 102, 241, .6)
        }
    </style>
</head>
<body class="min-h-screen">
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl"
         style="animation-delay:1s;"></div>
    <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl"
         style="animation-delay:2s;"></div>
</div>

<main class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-3">
            <span class="inline-block w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            <span class="text-sm font-semibold">Guided Creator</span>
        </div>
        <h1 class="text-5xl font-bold gradient-text mb-2">Create Your Perfect Image</h1>
        <p class="text-gray-400">Follow the steps to build your masterpiece</p>
    </div>

    {{-- Progress --}}
    <div class="glass rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex-1 flex items-center gap-4" id="stepIndicators"></div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-400" id="stepLabels"></div>
    </div>

    <div class="glass rounded-3xl overflow-hidden">
        <div class="p-8">
            <div id="alerts"></div>
            <div id="stepContainer"></div>

            <div id="resultContainer" class="hidden mt-6">
                <div class="glass rounded-2xl p-4">
                    <div class="text-sm font-medium text-gray-300 mb-3">Result</div>
                    <div class="rounded-xl overflow-hidden border border-white/10">
                        <img id="resultImage" src="" alt="result" class="w-full h-auto object-cover"/>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between gap-4 mt-6">
                <button id="prevBtn" class="btn-secondary h-12 px-8 rounded-xl font-semibold">← Back</button>
                <div class="text-sm text-gray-400" id="stepCounter">Step 1</div>
                <button id="nextBtn" class="btn-primary h-12 px-8 rounded-xl text-white font-semibold">Next →</button>
            </div>
        </div>
    </div>

    <div class="mt-6 glass rounded-2xl p-4" id="tipContainer"></div>
</main>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const WIZARD = @json($wizard);

    let gender = null;
    let currentStep = 0;
    const totalSteps = Array.isArray(WIZARD?.steps) && WIZARD.steps.length ? WIZARD.steps.length : 1; // still show Step 1 UI
    const selections = {}; // { [sectionKey]: value|string[]|boolean }
    let step1LockedForUploads = true; // uploads must be done before selecting any first-step section

    // Shortcuts
    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
    const stepContainer = $('#stepContainer');
    const stepIndicators = $('#stepIndicators');
    const stepLabels = $('#stepLabels');
    const stepCounter = $('#stepCounter');
    const prevBtn = $('#prevBtn');
    const nextBtn = $('#nextBtn');
    const resultContainer = $('#resultContainer');
    const resultImage = $('#resultImage');
    const tipContainer = $('#tipContainer');
    const alerts = $('#alerts');

    /** Only filter admin sections by gender; uploads+gender are NOT from WIZARD */
    function visibleGroups(step) {
        const g = gender;
        const groups = (step?.groups || []);
        return groups.map(gr => {
            const filtered = (gr.sections || []).filter(sec => {
                const sGender = sec.gender || 'both';
                return !g || sGender === 'both' || sGender === g;
            });
            return {...gr, sections: filtered};
        }).filter(gr => gr.sections.length);
    }

    function renderIndicators() {
        stepIndicators.innerHTML = Array.from({length: totalSteps}).map((_, i) => {
            const done = i < currentStep;
            const active = i === currentStep;
            const base = 'step-indicator w-12 h-12 rounded-full flex items-center justify-center font-bold border-2';
            const cls = done ? base + ' step-complete' : active ? base + ' step-active' : base + ' glass';
            const label = done ? '✓' : (i + 1);
            return `<div class="${cls}">${label}</div>`;
        }).join('');
        // Titles from WIZARD if available
        stepLabels.innerHTML = (WIZARD?.steps || []).length
            ? WIZARD.steps.map((s, i) => `<span class="${i === currentStep ? 'text-indigo-400 font-semibold' : ''}">${s.title}</span>`).join('')
            : `<span class="text-indigo-400 font-semibold">Step 1</span>`;
        stepCounter.textContent = `Step ${currentStep + 1} of ${totalSteps}`;
    }

    function inputRow(label, inner) {
        return `<div class="mb-4">
          ${label ? `<label class="text-sm font-medium text-gray-300 mb-2 block">${label}</label>` : ''}
          ${inner}
        </div>`;
    }

    function sectionHTML(sec, groupExclusive) {
        const key = sec.key;
        const val = selections[key];

        // Disabled state if group is exclusive and another section already chosen
        let disableSection = false;
        if (groupExclusive) {
            const chosen = exclusiveChosenKeyInGroup(sec);
            if (chosen && chosen !== key) disableSection = true;
        }
        const disabled = disableSection ? 'disabled' : '';
        const disabledCls = disableSection ? 'opacity-50 pointer-events-none' : '';

        if (sec.type === 'textarea') {
            return `<div class="${disabledCls}">
              ${inputRow(sec.label, `<textarea data-sec="${key}" class="w-full rounded-xl input-field p-4 text-base resize-none" rows="4" ${disabled}>${val ?? ''}</textarea>`)}
            </div>`;
        }

        if (sec.type === 'text') {
            return `<div class="${disabledCls}">
              ${inputRow(sec.label, `<input data-sec="${key}" class="w-full rounded-xl input-field p-3 text-sm" value="${val ?? ''}" ${disabled}/>`)}
            </div>`;
        }

        if (sec.type === 'select') {
            const opts = (sec.options || []).map(o => {
                const selected = (val ?? sec.options?.[0]?.key) === o.key ? 'selected' : '';
                return `<option value="${o.key}" ${selected}>${o.label}</option>`;
            }).join('');
            return `<div class="${disabledCls}">
              ${inputRow(sec.label, `<select data-sec="${key}" class="w-full rounded-xl input-field p-3 text-sm" ${disabled}>${opts}</select>`)}
            </div>`;
        }

        if (sec.type === 'chips') {
            const mode = sec.selection || 'single';
            const cur = val ?? (mode === 'multiple' ? [] : null);
            const chips = (sec.options || []).map(o => {
                const active = mode === 'multiple' ? (cur || []).includes(o.key) : cur === o.key;
                return `<button type="button" class="px-4 py-2 rounded-full chip ${active ? 'chip-active' : ''} text-sm"
                        data-chip data-sec="${key}" data-mode="${mode}" data-val="${o.key}" ${disabled}>
                        ${o.icon ? o.icon + ' ' : ''}${o.label}</button>`;
            }).join('');
            return `<div class="${disabledCls}">
              ${inputRow(sec.label, `<div class="flex flex-wrap gap-2">${chips}</div>`)}
            </div>`;
        }

        if (sec.type === 'checkbox' || sec.type === 'boolean') {
            const checked = val ? 'checked' : '';
            return `<label class="rounded-xl glass p-3 flex items-center justify-between cursor-pointer hover:bg-white/10 ${disabledCls}">
              <span class="text-sm">${sec.label}</span>
              <input type="checkbox" data-sec="${key}" ${checked} ${disabled}/>
            </label>`;
        }

        return '';
    }

    function exclusiveChosenKeyInGroup(sampleSection) {
        const step = (WIZARD?.steps || [])[currentStep];
        if (!step) return null;
        for (const g of (step.groups || [])) {
            if (!g.exclusive) continue;
            const keys = (g.sections || []).map(s => s.key);
            if (!keys.includes(sampleSection.key)) continue;
            for (const k of keys) {
                const v = selections[k];
                const chosenNow = !(v === undefined || v === null || v === '' || (Array.isArray(v) && v.length === 0) || (typeof v === 'boolean' && v === false));
                if (chosenNow) return k;
            }
        }
        return null;
    }

    /** --- HARD-PINNED blocks (NOT from WIZARD) --- */

    // 1) IMAGE UPLOAD — first in Step 1
    function pinnedUploads() {
        return `
          <div class="mb-6">
            <label class="text-sm font-medium text-gray-300 mb-3 block">Upload Reference Images (up to 3)</label>
            <input id="imagesInput" type="file" accept="image/*" multiple class="w-full rounded-xl input-field p-3 text-sm"/>
            <div class="text-xs text-gray-400 mt-2">Add up to 3 images <b>before</b> selecting any option in this step.</div>
            <div id="imagesPreview" class="mt-3 grid grid-cols-3 gap-3"></div>
          </div>
        `;
    }

    // 2) GENDER — second in Step 1
    function pinnedGender() {
        const maleActive = gender === 'male' ? 'chip-active' : '';
        const femaleActive = gender === 'female' ? 'chip-active' : '';
        return `
          <div class="mb-6">
            <div class="text-sm font-medium text-gray-300 mb-2">Select Gender</div>
            <div class="flex gap-2">
              <button type="button" class="px-4 py-2 rounded-full chip ${maleActive}" data-gender-btn="male">Male</button>
              <button type="button" class="px-4 py-2 rounded-full chip ${femaleActive}" data-gender-btn="female">Female</button>
            </div>
          </div>
        `;
    }

    function renderStep() {
        const stepFromAdmin = (WIZARD?.steps || [])[currentStep] || null;
        const groups = stepFromAdmin ? visibleGroups(stepFromAdmin) : [];

        // Header (from admin if exists)
        const header = stepFromAdmin ? `
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center font-bold">${currentStep + 1}</div>
              <div>
                <h2 class="text-2xl font-bold gradient-text">${stepFromAdmin.title}</h2>
                <p class="text-gray-400 text-sm">${stepFromAdmin.subtitle || ''}</p>
              </div>
            </div>` : `
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center font-bold">${currentStep + 1}</div>
              <div>
                <h2 class="text-2xl font-bold gradient-text">Step 1</h2>
                <p class="text-gray-400 text-sm">Start by adding images and choosing gender</p>
              </div>
            </div>`;

        // Render (pinned uploads + gender FIRST), then admin groups
        stepContainer.innerHTML = `
          <div class="mb-8">
            ${header}

            ${currentStep === 0 ? pinnedUploads() : ''}
            ${currentStep === 0 ? pinnedGender() : ''}

            ${groups.length ? groups.map(g => `
              <div class="space-y-4 mb-6" data-group data-exclusive="${g.exclusive ? '1' : '0'}">
                ${(g.sections || []).map(sec => `
                  <div class="group-section" data-section="${sec.key}">
                    ${sectionHTML(sec, g.exclusive)}
                  </div>`).join('')}
              </div>`).join('') : (stepFromAdmin
            ? ''
            : `<div class="text-sm text-gray-400">No admin sections configured yet.</div>`)}
          </div>
        `;

        // Bind per-step elements
        bindInputsAndChips();
        if (currentStep === 0) {
            bindUploads();
            bindGender();
        }

        tipContainer.innerHTML = `
          <div class="flex items-start gap-3">
            <div class="text-2xl">💡</div>
            <div>
              <div class="font-semibold text-sm mb-1">Pro Tip</div>
              <div class="text-sm text-gray-400">${stepFromAdmin?.tip || 'Add at least one image and pick gender to tailor the next options.'}</div>
            </div>
          </div>
        `;
    }

    function bindUploads() {
        const input = $('#imagesInput');
        const preview = $('#imagesPreview');
        if (!input) return;

        input.addEventListener('change', (e) => {
            const files = Array.from(e.target.files || []);
            if (files.length > 3) {
                show('You can upload up to 3 images.', 'error');
                input.value = '';
                preview.innerHTML = '';
                return;
            }
            preview.innerHTML = '';
            files.forEach(f => {
                const r = new FileReader();
                r.onload = () => {
                    const img = document.createElement('img');
                    img.src = r.result;
                    img.className = 'w-full h-28 object-cover rounded-xl';
                    preview.appendChild(img);
                };
                r.readAsDataURL(f);
            });
            // Only sections are locked; gender is always allowed
            step1LockedForUploads = false;
        });
    }

    function bindGender() {
        $$('[data-gender-btn]').forEach(btn => {
            btn.addEventListener('click', () => {
                gender = btn.getAttribute('data-gender-btn');
                $$('[data-gender-btn]').forEach(b => b.classList.remove('chip-active'));
                btn.classList.add('chip-active');
                // re-render to apply gender filtering on admin sections
                render();
            });
        });
    }

    function bindInputsAndChips() {
        // Lock admin sections on step 1 until uploads selected (gender is allowed anytime)
        if (currentStep === 0 && step1LockedForUploads) {
            stepContainer.addEventListener('click', (e) => {
                const target = e.target.closest('[data-sec],[data-chip]');
                if (target) {
                    const imagesInput = $('#imagesInput');
                    if (imagesInput && (!imagesInput.files || imagesInput.files.length === 0)) {
                        e.preventDefault();
                        show('Upload up to 3 images first (or skip uploads by choosing Next).', 'error');
                    }
                }
            }, {once: true});
        }

        // Inputs
        $$('[data-sec]').forEach(el => {
            const key = el.getAttribute('data-sec');
            const handler = () => {
                const type = el.getAttribute('type');
                let v;
                if (type === 'checkbox') v = el.checked; else v = el.value;
                selections[key] = v;
                enforceExclusivity(key);
            };
            el.addEventListener('input', handler);
            el.addEventListener('change', handler);
        });

        // Chips
        $$('[data-chip]').forEach(btn => {
            btn.addEventListener('click', () => {
                const key = btn.getAttribute('data-sec');
                const mode = btn.getAttribute('data-mode') || 'single';
                let curr = selections[key];

                if (mode === 'single') {
                    selections[key] = btn.getAttribute('data-val');
                    $$(`[data-chip][data-sec="${key}"]`).forEach(b => b.classList.remove('chip-active'));
                    btn.classList.add('chip-active');
                } else {
                    curr = Array.isArray(curr) ? curr : [];
                    const val = btn.getAttribute('data-val');
                    const i = curr.indexOf(val);
                    if (i >= 0) curr.splice(i, 1); else curr.push(val);
                    selections[key] = curr;
                    btn.classList.toggle('chip-active');
                }
                enforceExclusivity(key);
            });
        });
    }

    function enforceExclusivity(changedKey) {
        const step = (WIZARD?.steps || [])[currentStep];
        if (!step) return;
        for (const g of (step.groups || [])) {
            if (!g.exclusive) continue;
            const keys = (g.sections || []).map(s => s.key);
            if (!keys.includes(changedKey)) continue;

            let chosen = null;
            for (const k of keys) {
                const v = selections[k];
                const chosenNow = !(v === undefined || v === null || v === '' || (Array.isArray(v) && v.length === 0) || (typeof v === 'boolean' && v === false));
                if (chosenNow) {
                    chosen = k;
                    break;
                }
            }

            keys.forEach(k => {
                if (k === chosen) return;
                const el = stepContainer.querySelector(`[data-section="${k}"]`);
                if (!el) return;
                if (chosen) el.classList.add('opacity-50', 'pointer-events-none');
                else el.classList.remove('opacity-50', 'pointer-events-none');
            });
        }
    }

    function render() {
        renderIndicators();
        renderStep();
        prevBtn.disabled = currentStep === 0;
        nextBtn.textContent = currentStep === totalSteps - 1 ? '✨ Generate Image' : 'Next →';
        resultContainer.classList.add('hidden');
    }

    prevBtn.addEventListener('click', () => {
        if (currentStep > 0) {
            currentStep--;
            render();
        }
    });

    nextBtn.addEventListener('click', async () => {
        if (!gender) {
            show('Please select gender first.', 'error');
            return;
        }

        if (currentStep < totalSteps - 1) {
            currentStep++;
            render();
            return;
        }
        await generate();
    });

    async function generate() {
        try {
            disableNav(true, 'Generating…');

            const fd = new FormData();
            fd.append('gender', gender);
            fd.append('selections', JSON.stringify(selections));
            const imagesInput = $('#imagesInput');
            if (imagesInput && imagesInput.files) {
                Array.from(imagesInput.files).slice(0, 3).forEach((f, i) => fd.append(`images[${i}]`, f));
            }

            const res = await fetch(`{{ route('creator.generate') }}`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': CSRF},
                body: fd
            });

            if (!res.ok) {
                const t = await res.text();
                show('Generation failed: ' + t, 'error');
                return;
            }

            const data = await res.json();
            if (data.status === 'ok' && data.image_url) {
                resultImage.src = data.image_url;
                resultContainer.classList.remove('hidden');
                resultContainer.scrollIntoView({behavior: 'smooth', block: 'center'});
            } else {
                show('Generation failed: invalid response.', 'error');
            }
        } catch {
            show('Network error. Please try again.', 'error');
        } finally {
            disableNav(false);
        }
    }

    function disableNav(state, text) {
        prevBtn.disabled = state || currentStep === 0;
        nextBtn.disabled = state;
        nextBtn.textContent = state && text ? text : (currentStep === totalSteps - 1 ? '✨ Generate Image' : 'Next →');
    }

    function show(msg, type = 'info') {
        alerts.innerHTML = `<div class="mb-4 rounded-xl p-3 ${type === 'error' ? 'bg-red-500/10 border border-red-400/40' : 'glass'} text-sm">${msg}</div>`;
        setTimeout(() => alerts.innerHTML = '', 3500);
    }

    // init
    render();
</script>
</body>
</html>
