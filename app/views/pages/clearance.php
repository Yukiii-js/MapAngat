<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>
<main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar bg-slate-100 p-5 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-primary">Permitting workflow</p>
                <h2 class="text-2xl font-black text-slate-800 mt-1">Location Clearance</h2>
                <p class="text-sm text-slate-500 mt-1">Register and track development-location clearance applications.</p>
            </div>
            <a href="#new-application" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-dark">
                <i class="fa-solid fa-plus mr-2"></i>New application
            </a>
        </div>

        <?php if (!empty($clearance_error)): ?>
            <div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= html_escape($clearance_error) ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <?php
            $status_cards = [
                ['label' => 'Pending', 'value' => $counts['pending'], 'icon_class' => 'bg-amber-50 text-amber-600', 'icon' => 'fa-hourglass-half'],
                ['label' => 'For review', 'value' => $counts['review'], 'icon_class' => 'bg-blue-50 text-blue-600', 'icon' => 'fa-magnifying-glass'],
                ['label' => 'Approved', 'value' => $counts['approved'], 'icon_class' => 'bg-emerald-50 text-emerald-600', 'icon' => 'fa-circle-check']
            ];
            foreach ($status_cards as $card):
            ?>
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
                    <div><p class="text-xs font-bold uppercase tracking-wide text-slate-500"><?= $card['label'] ?></p><p class="text-2xl font-black text-slate-800 mt-1"><?= (int) $card['value'] ?></p></div>
                    <span class="w-11 h-11 rounded-xl <?= $card['icon_class'] ?> flex items-center justify-center"><i class="fa-solid <?= $card['icon'] ?>"></i></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_340px] gap-5 items-start">
            <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div><h3 class="font-bold text-slate-800">Application register</h3><p class="text-xs text-slate-500 mt-1"><?= count($applications) ?> application(s)</p></div>
                    <div class="flex gap-2">
                        <label class="sr-only" for="clearance-search">Search applications</label>
                        <input id="clearance-search" type="search" placeholder="Search ID or barangay" class="rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-primary focus:outline-none">
                        <label class="sr-only" for="clearance-status">Filter status</label>
                        <select id="clearance-status" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs">
                            <option value="">All statuses</option><option>Pending</option><option>For review</option><option>Needs info</option><option>Approved</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                            <tr><th class="px-5 py-3">Application ID</th><th class="px-5 py-3">Applicant / project</th><th class="px-5 py-3">Barangay</th><th class="px-5 py-3">Submitted</th><th class="px-5 py-3">Status</th></tr>
                        </thead>
                        <tbody id="clearance-rows" class="divide-y divide-slate-100">
                            <?php foreach ($applications as $row): ?>
                                <?php
                                $status_class = 'bg-slate-100 text-slate-700';
                                if ($row['status'] === 'Pending') $status_class = 'bg-amber-100 text-amber-800';
                                elseif ($row['status'] === 'For review') $status_class = 'bg-blue-100 text-blue-800';
                                elseif ($row['status'] === 'Needs info') $status_class = 'bg-rose-100 text-rose-800';
                                elseif ($row['status'] === 'Approved') $status_class = 'bg-emerald-100 text-emerald-800';
                                ?>
                                <tr class="clearance-row hover:bg-slate-50/70" data-search="<?= html_escape(strtolower($row['application_id'] . ' ' . $row['applicant_name'] . ' ' . $row['project_type'] . ' ' . $row['barangay'])) ?>" data-status="<?= html_escape($row['status']) ?>">
                                    <td class="px-5 py-4 font-bold text-primary"><?= html_escape($row['application_id']) ?></td>
                                    <td class="px-5 py-4"><p class="font-semibold text-slate-800"><?= html_escape($row['applicant_name']) ?></p><p class="text-xs text-slate-500"><?= html_escape($row['project_type']) ?></p></td>
                                    <td class="px-5 py-4 text-slate-600"><?= html_escape($row['barangay']) ?></td>
                                    <td class="px-5 py-4 text-slate-600"><?= html_escape(date('M d, Y', strtotime($row['created_at']))) ?></td>
                                    <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold <?= $status_class ?>"><?= html_escape($row['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($applications)): ?>
                                <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">No applications have been submitted yet.</td></tr>
                            <?php endif; ?>
                            <tr id="clearance-empty-filter" class="hidden"><td colspan="5" class="px-5 py-8 text-center text-sm text-slate-400">No applications match your filters.</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="new-application" class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-800">New application</h3>
                <p class="text-xs text-slate-500 mt-1">Create a record for initial review.</p>
                <form action="<?= site_url('location-clearance/store') ?>" method="post" class="mt-4 space-y-3">
                    <div><label for="applicant-name" class="block text-xs font-bold text-slate-600 mb-1">Applicant name</label><input id="applicant-name" name="applicant_name" maxlength="100" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-primary focus:outline-none"></div>
                    <div><label for="project-type" class="block text-xs font-bold text-slate-600 mb-1">Project / site type</label><input id="project-type" name="project_type" maxlength="100" required placeholder="e.g. Residential development" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-primary focus:outline-none"></div>
                    <div><label for="application-barangay" class="block text-xs font-bold text-slate-600 mb-1">Barangay</label>
                        <select id="application-barangay" name="barangay" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none">
                            <option value="">Select a barangay</option><option>San Andres</option><option>Poblacion</option><option>Bangkatan</option><option>Lantuyang</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-dark">Submit for review</button>
                </form>
                <p class="mt-3 text-[11px] leading-relaxed text-slate-400">New records are saved with Pending status. Spatial assessment and official approval remain separate review steps.</p>
            </section>
        </div>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('clearance-search');
    const status = document.getElementById('clearance-status');
    const rows = Array.from(document.querySelectorAll('.clearance-row'));
    const empty = document.getElementById('clearance-empty-filter');
    function filterRows() {
        let visible = 0;
        rows.forEach(function (row) {
            const matches = row.dataset.search.includes(search.value.trim().toLowerCase())
                && (!status.value || row.dataset.status === status.value);
            row.classList.toggle('hidden', !matches);
            if (matches) visible++;
        });
        empty.classList.toggle('hidden', visible > 0 || rows.length === 0);
    }
    search.addEventListener('input', filterRows);
    status.addEventListener('change', filterRows);
});
</script>
