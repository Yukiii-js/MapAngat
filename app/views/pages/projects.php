<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>
<main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar bg-slate-100 p-5 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-primary">Municipal portfolio</p>
                <h2 class="text-2xl font-black text-slate-800 mt-1">Program &amp; Project Monitoring</h2>
                <p class="text-sm text-slate-500 mt-1">Track implementation progress and upcoming field inspections.</p>
            </div>
            <button id="project-update-toggle" type="button" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-dark"><i class="fa-solid fa-pen-to-square mr-2"></i>Record an update</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <?php
            $summary_cards = [
                ['label' => 'Total projects', 'value' => $counts['total'], 'icon' => 'fa-folder-open', 'icon_class' => 'bg-blue-50 text-blue-600'],
                ['label' => 'On track', 'value' => $counts['on_track'], 'icon' => 'fa-circle-check', 'icon_class' => 'bg-emerald-50 text-emerald-600'],
                ['label' => 'At risk', 'value' => $counts['at_risk'], 'icon' => 'fa-triangle-exclamation', 'icon_class' => 'bg-amber-50 text-amber-600'],
                ['label' => 'Delayed', 'value' => $counts['delayed'], 'icon' => 'fa-circle-exclamation', 'icon_class' => 'bg-rose-50 text-rose-600']
            ];
            foreach ($summary_cards as $card):
            ?>
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
                    <div><p class="text-xs font-bold uppercase tracking-wide text-slate-500"><?= $card['label'] ?></p><p class="text-2xl font-black text-slate-800 mt-1"><?= (int) $card['value'] ?></p></div>
                    <span class="w-11 h-11 rounded-xl <?= $card['icon_class'] ?> flex items-center justify-center"><i class="fa-solid <?= $card['icon'] ?>"></i></span>
                </div>
            <?php endforeach; ?>
        </div>

        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div><h3 class="font-bold text-slate-800">Active municipal projects</h3><p class="text-xs text-slate-500 mt-1">Progress and inspection schedule</p></div>
                <label class="sr-only" for="project-status-filter">Filter projects by status</label>
                <select id="project-status-filter" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs"><option value="">All statuses</option><option>On track</option><option>At risk</option><option>Delayed</option></select>
            </div>
            <div class="divide-y divide-slate-100">
                <?php foreach ($projects as $project): ?>
                    <?php
                    $status_class = 'bg-slate-100 text-slate-700';
                    if ($project['status'] === 'On track') $status_class = 'bg-emerald-100 text-emerald-800';
                    elseif ($project['status'] === 'At risk') $status_class = 'bg-amber-100 text-amber-800';
                    elseif ($project['status'] === 'Delayed') $status_class = 'bg-rose-100 text-rose-800';
                    $progress = max(0, min(100, (int) $project['progress']));
                    ?>
                    <article class="project-row p-5 grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_180px_170px] gap-4 items-center" data-status="<?= html_escape($project['status']) ?>">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2"><h4 class="font-bold text-slate-800"><?= html_escape($project['project_name']) ?></h4><span class="rounded-full px-2.5 py-1 text-[11px] font-bold <?= $status_class ?>"><?= html_escape($project['status']) ?></span></div>
                            <p class="text-xs text-slate-500 mt-1"><?= html_escape($project['barangay']) ?> · Municipal project</p>
                            <div class="flex items-center gap-3 mt-4">
                                <div class="h-2 flex-1 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-primary" style="width: <?= $progress ?>%"></div></div>
                                <span class="w-10 text-right text-xs font-bold text-slate-700"><?= $progress ?>%</span>
                            </div>
                        </div>
                        <div class="text-sm"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Next inspection</p><p class="font-semibold text-slate-700 mt-1"><?= !empty($project['inspection_date']) ? html_escape(date('M d, Y', strtotime($project['inspection_date']))) : 'Not scheduled' ?></p></div>
                        <button type="button" class="record-project-update rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 hover:border-primary hover:text-primary" data-project="<?= html_escape($project['project_name']) ?>"><i class="fa-solid fa-clipboard-check mr-1"></i>Record update</button>
                    </article>
                <?php endforeach; ?>
                <?php if (empty($projects)): ?>
                    <p class="p-10 text-center text-sm text-slate-400">No projects are available yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <section id="project-update-panel" class="hidden bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3"><div><h3 class="font-bold text-slate-800">Project update form</h3><p class="text-xs text-slate-500 mt-1">Capture a prototype field update for review.</p></div><button id="project-update-close" type="button" class="text-slate-400 hover:text-slate-700" aria-label="Close update form"><i class="fa-solid fa-xmark"></i></button></div>
            <form id="project-update-form" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div><label for="update-project" class="block text-xs font-bold text-slate-600 mb-1">Project</label><select id="update-project" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"><?php foreach ($projects as $project): ?><option><?= html_escape($project['project_name']) ?></option><?php endforeach; ?></select></div>
                <div><label for="update-progress" class="block text-xs font-bold text-slate-600 mb-1">Progress (%)</label><input id="update-progress" type="number" min="0" max="100" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></div>
                <div><label for="update-status" class="block text-xs font-bold text-slate-600 mb-1">Status</label><select id="update-status" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"><option>On track</option><option>At risk</option><option>Delayed</option></select></div>
                <div><label for="update-notes" class="block text-xs font-bold text-slate-600 mb-1">Field notes</label><input id="update-notes" type="text" maxlength="250" placeholder="Brief update from the site" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></div>
                <div class="md:col-span-2 flex items-center justify-between"><p id="update-feedback" class="text-xs text-slate-500" aria-live="polite">Updates submitted here are a prototype and are not saved to the project register.</p><button type="submit" class="rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white hover:bg-primary-dark">Submit update</button></div>
            </form>
        </section>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const panel = document.getElementById('project-update-panel');
    const projectSelect = document.getElementById('update-project');
    function openUpdate(project) {
        panel.classList.remove('hidden');
        if (project) projectSelect.value = project;
        panel.scrollIntoView({behavior: 'smooth', block: 'start'});
    }
    document.getElementById('project-update-toggle').addEventListener('click', function () { openUpdate(''); });
    document.getElementById('project-update-close').addEventListener('click', function () { panel.classList.add('hidden'); });
    document.querySelectorAll('.record-project-update').forEach(function (button) {
        button.addEventListener('click', function () { openUpdate(button.dataset.project); });
    });
    document.getElementById('project-status-filter').addEventListener('change', function (event) {
        document.querySelectorAll('.project-row').forEach(function (row) {
            row.classList.toggle('hidden', Boolean(event.target.value) && row.dataset.status !== event.target.value);
        });
    });
    document.getElementById('project-update-form').addEventListener('submit', function (event) {
        event.preventDefault();
        document.getElementById('update-feedback').textContent = 'Prototype update captured for ' + projectSelect.value + '. It has not been saved to the project register.';
        this.reset();
    });
});
</script>
