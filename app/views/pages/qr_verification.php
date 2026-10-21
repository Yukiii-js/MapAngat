<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
$qr_records = array_map(function ($application) {
    return [
        'application_id' => $application['application_id'],
        'applicant_name' => $application['applicant_name'],
        'project_type' => $application['project_type'],
        'barangay' => $application['barangay'],
        'status' => $application['status'],
        'coordinates' => $application['coordinates']
    ];
}, $applications);
?>
<main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar bg-slate-100 p-5 lg:p-8">
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="text-center">
            <p class="text-xs font-bold uppercase tracking-widest text-primary">Field tools</p>
            <h2 class="text-2xl font-black text-slate-800 mt-1">QR Field Verification</h2>
            <p class="text-sm text-slate-500 mt-2">Look up a permit or project record, then attach a field inspection note.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_340px] gap-5 items-start">
            <section class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex flex-col items-center text-center border-b border-slate-100 pb-6">
                    <div class="w-20 h-20 rounded-2xl bg-primary-light text-primary flex items-center justify-center text-3xl"><i class="fa-solid fa-qrcode"></i></div>
                    <h3 class="font-bold text-slate-800 mt-4">Scan permit or project QR code</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-md">Camera scanning is represented by a demo action. You can also enter an application ID from the register.</p>
                    <button id="simulate-scan" type="button" class="mt-4 rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-dark"><i class="fa-solid fa-camera mr-2"></i>Simulate QR scan</button>
                </div>
                <form id="record-lookup" class="mt-5 flex flex-col sm:flex-row gap-2">
                    <label class="sr-only" for="record-code">Application or project code</label>
                    <input id="record-code" type="text" placeholder="e.g. LC-2026-014" autocomplete="off" required class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-primary focus:outline-none">
                    <button type="submit" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-primary hover:text-primary">Find record</button>
                </form>
                <p id="lookup-feedback" class="mt-3 text-xs text-slate-500" aria-live="polite">No record selected.</p>

                <section id="record-details" class="hidden mt-5 rounded-xl border border-blue-100 bg-blue-50/50 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div><p class="text-[11px] font-bold uppercase tracking-wide text-blue-700">Retrieved record</p><h3 id="record-title" class="font-black text-slate-800 mt-1"></h3></div>
                        <span id="record-status" class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-700"></span>
                    </div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 text-sm">
                        <div><dt class="text-xs text-slate-500">Applicant</dt><dd id="record-applicant" class="font-semibold text-slate-800 mt-1"></dd></div>
                        <div><dt class="text-xs text-slate-500">Project type</dt><dd id="record-project" class="font-semibold text-slate-800 mt-1"></dd></div>
                        <div><dt class="text-xs text-slate-500">Barangay</dt><dd id="record-barangay" class="font-semibold text-slate-800 mt-1"></dd></div>
                        <div><dt class="text-xs text-slate-500">Coordinates</dt><dd id="record-coordinates" class="font-semibold text-slate-800 mt-1"></dd></div>
                    </dl>
                </section>
            </section>

            <aside class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-800">Field inspection report</h3>
                <p class="text-xs text-slate-500 mt-1">Available after a record is retrieved.</p>
                <form id="inspection-report" class="mt-4 space-y-3">
                    <div><label for="inspection-findings" class="block text-xs font-bold text-slate-600 mb-1">Inspection findings</label><textarea id="inspection-findings" rows="4" required disabled placeholder="Add observations from the site..." class="w-full resize-y rounded-lg border border-slate-200 px-3 py-2 text-sm disabled:bg-slate-50"></textarea></div>
                    <div><label for="inspection-date" class="block text-xs font-bold text-slate-600 mb-1">Inspection date</label><input id="inspection-date" type="date" required disabled class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm disabled:bg-slate-50"></div>
                    <button id="inspection-submit" type="submit" disabled class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-dark disabled:cursor-not-allowed disabled:bg-slate-300">Submit field report</button>
                </form>
                <p id="report-feedback" class="mt-3 text-xs text-slate-500" aria-live="polite"></p>
            </aside>
        </div>

        <p class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-xs leading-relaxed text-amber-900"><i class="fa-solid fa-circle-info mr-1"></i> A QR scan only retrieves the linked record; it does not determine compliance or approval. Inspection findings remain subject to authorized review.</p>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const records = <?= json_encode($qr_records, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const codeInput = document.getElementById('record-code');
    const details = document.getElementById('record-details');
    const feedback = document.getElementById('lookup-feedback');
    const reportFields = document.querySelectorAll('#inspection-findings, #inspection-date, #inspection-submit');
    let selectedRecord = null;

    function lookup(code) {
        const normalized = code.trim().toLowerCase();
        selectedRecord = records.find(function (record) {
            return record.application_id.toLowerCase() === normalized;
        }) || null;
        if (!selectedRecord) {
            details.classList.add('hidden');
            reportFields.forEach(function (field) { field.disabled = true; });
            feedback.textContent = 'No matching clearance record was found.';
            return;
        }
        document.getElementById('record-title').textContent = selectedRecord.application_id;
        document.getElementById('record-status').textContent = selectedRecord.status;
        document.getElementById('record-applicant').textContent = selectedRecord.applicant_name;
        document.getElementById('record-project').textContent = selectedRecord.project_type;
        document.getElementById('record-barangay').textContent = selectedRecord.barangay;
        document.getElementById('record-coordinates').textContent = selectedRecord.coordinates || 'Not recorded';
        details.classList.remove('hidden');
        reportFields.forEach(function (field) { field.disabled = false; });
        feedback.textContent = 'Record found. Verify the site details before submitting a field report.';
    }

    document.getElementById('record-lookup').addEventListener('submit', function (event) {
        event.preventDefault();
        lookup(codeInput.value);
    });
    document.getElementById('simulate-scan').addEventListener('click', function () {
        if (records.length === 0) {
            feedback.textContent = 'There are no clearance records available to scan.';
            return;
        }
        codeInput.value = records[0].application_id;
        lookup(codeInput.value);
    });
    document.getElementById('inspection-report').addEventListener('submit', function (event) {
        event.preventDefault();
        if (!selectedRecord) return;
        document.getElementById('report-feedback').textContent = 'Prototype field report captured for ' + selectedRecord.application_id + '. It has not been submitted to a review queue.';
        this.reset();
    });
});
</script>
