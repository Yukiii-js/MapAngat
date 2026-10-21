<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>
<main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar bg-slate-100 p-5 lg:p-8">
    <div class="max-w-6xl mx-auto space-y-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-primary">Planning tools</p>
            <h2 class="text-2xl font-black text-slate-800 mt-1">Spatial Intelligence &amp; Analysis</h2>
            <p class="text-sm text-slate-500 mt-1">Run a preliminary site screening against the municipality's planning layers.</p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
            <section class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <span class="w-10 h-10 rounded-lg bg-primary-light text-primary flex items-center justify-center"><i class="fa-solid fa-location-crosshairs"></i></span>
                    <div><h3 class="font-bold text-slate-800">New spatial check</h3><p class="text-xs text-slate-500">Enter the proposed site details to begin.</p></div>
                </div>
                <form id="screening-form" action="<?= site_url('spatial-analysis/run-screening') ?>" method="post" class="space-y-4">
                    <div>
                        <label for="app-id" class="block text-xs font-bold text-slate-600 mb-1.5">Application / reference ID</label>
                        <input id="app-id" name="app_id" type="text" value="LC-2026-015" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-primary focus:outline-none">
                    </div>
                    <div>
                        <label for="project-type" class="block text-xs font-bold text-slate-600 mb-1.5">Proposed project type</label>
                        <select id="project-type" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-primary focus:outline-none">
                            <option>Residential development</option><option>Small commercial site</option><option>Roadside facility</option><option>Farm support building</option><option>Public infrastructure</option>
                        </select>
                    </div>
                    <div>
                        <label for="barangay" class="block text-xs font-bold text-slate-600 mb-1.5">Barangay</label>
                        <select id="barangay" name="barangay" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-primary focus:outline-none">
                            <option>San Andres</option><option>Poblacion</option><option>Bangkatan</option><option>Lantuyang</option>
                        </select>
                    </div>
                    <div>
                        <label for="coordinates" class="block text-xs font-bold text-slate-600 mb-1.5">Coordinates <span class="font-normal text-slate-400">(optional)</span></label>
                        <input id="coordinates" type="text" placeholder="13.3182° N, 121.2825° E" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-primary focus:outline-none">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-primary-dark">
                        <i class="fa-solid fa-wand-magic-sparkles mr-2"></i>Run preliminary screening
                    </button>
                </form>
            </section>

            <section class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div><h3 class="font-bold text-slate-800">Screening decision support</h3><p class="text-xs text-slate-500 mt-1">Results appear here after a check is run.</p></div>
                    <span id="screening-state" class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500">Awaiting check</span>
                </div>
                <div id="screening-results" class="mt-5 space-y-3">
                    <div class="rounded-lg border border-dashed border-slate-200 p-8 text-center text-sm text-slate-400">
                        <i class="fa-solid fa-layer-group text-2xl mb-3"></i><p>Submit the site details to see zoning, hazard, and buffer summaries.</p>
                    </div>
                </div>
                <p class="mt-5 rounded-lg bg-amber-50 p-3 text-xs leading-relaxed text-amber-800"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Prototype results are indicative only and must be verified by authorized MPDO personnel before a permitting decision.</p>
            </section>
        </div>

        <section class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <h3 class="font-bold text-slate-800">Layers considered</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4">
                <div class="rounded-lg bg-slate-50 p-4"><i class="fa-solid fa-map text-primary"></i><p class="font-bold text-sm mt-2">CLUP zoning</p><p class="text-xs text-slate-500 mt-1">Preliminary land-use classification match</p></div>
                <div class="rounded-lg bg-slate-50 p-4"><i class="fa-solid fa-water text-sky-600"></i><p class="font-bold text-sm mt-2">Hazard exposure</p><p class="text-xs text-slate-500 mt-1">Indicative flood-risk context</p></div>
                <div class="rounded-lg bg-slate-50 p-4"><i class="fa-solid fa-arrows-left-right-to-line text-amber-600"></i><p class="font-bold text-sm mt-2">Setback buffers</p><p class="text-xs text-slate-500 mt-1">Proximity to mapped waterways and restrictions</p></div>
            </div>
        </section>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('screening-form');
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        const state = document.getElementById('screening-state');
        const results = document.getElementById('screening-results');
        button.disabled = true;
        button.textContent = 'Running screening...';
        state.textContent = 'Checking';
        state.className = 'rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(new FormData(form))
            });
            if (!response.ok) throw new Error('Screening request failed (' + response.status + ').');
            const result = await response.json();
            results.replaceChildren();
            [
                ['Zoning review', result.zoning, 'fa-map'],
                ['Hazard review', result.hazard, 'fa-water'],
                ['Buffer review', result.buffer, 'fa-arrows-left-right-to-line']
            ].forEach(function (item) {
                const card = document.createElement('div');
                card.className = 'rounded-lg border border-emerald-100 bg-emerald-50/60 p-4';
                const title = document.createElement('p');
                title.className = 'text-xs font-bold uppercase tracking-wide text-emerald-800';
                title.innerHTML = '<i class="fa-solid ' + item[2] + ' mr-2"></i>' + item[0];
                const detail = document.createElement('p');
                detail.className = 'text-sm text-slate-700 mt-2';
                detail.textContent = item[1];
                card.append(title, detail);
                results.append(card);
            });
            state.textContent = 'Screening complete';
            state.className = 'rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700';
        } catch (error) {
            state.textContent = 'Unable to screen';
            state.className = 'rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-700';
            results.textContent = error.message;
        } finally {
            button.disabled = false;
            button.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles mr-2"></i>Run preliminary screening';
        }
    });
});
</script>
