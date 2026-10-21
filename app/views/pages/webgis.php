<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>
<main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar bg-slate-100 p-5 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-primary">Municipal GIS</p>
                <h2 class="text-2xl font-black text-slate-800 mt-1">Web-GIS Map</h2>
                <p class="text-sm text-slate-500 mt-1">Explore clearance sites and municipal project locations.</p>
            </div>
            <form id="map-search-form" class="flex gap-2">
                <label class="sr-only" for="map-search">Search locations</label>
                <input id="map-search" type="search" placeholder="Search a barangay or site"
                       class="w-64 max-w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none">
                <button class="rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white hover:bg-primary-dark" type="submit">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Search
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_300px] gap-5">
            <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <div>
                        <h3 class="font-bold text-slate-800">Baco, Oriental Mindoro</h3>
                        <p class="text-xs text-slate-500">Interactive municipal map · select a marker for details</p>
                    </div>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Map ready
                    </span>
                </div>
                <div id="prototype-map" class="h-[540px] w-full bg-slate-100"></div>
            </section>

            <aside class="space-y-4">
                <section class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                    <h3 class="font-bold text-slate-800">Map layers</h3>
                    <p class="text-xs text-slate-500 mt-1">Toggle the visible location groups.</p>
                    <div class="mt-4 space-y-3 text-sm">
                        <label class="flex items-center gap-3"><input class="map-layer rounded text-primary" type="checkbox" data-layer="clearances" checked> Location clearances <span class="ml-auto text-xs text-slate-400"><?= count($clearances) ?></span></label>
                        <label class="flex items-center gap-3"><input class="map-layer rounded text-primary" type="checkbox" data-layer="projects" checked> Municipal projects <span class="ml-auto text-xs text-slate-400"><?= count($projects) ?></span></label>
                        <label class="flex items-center gap-3"><input class="map-layer rounded text-primary" type="checkbox" data-layer="risk" checked> Flood risk overlay</label>
                    </div>
                    <div class="border-t border-slate-100 mt-4 pt-4">
                        <h4 class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-3">Legend</h4>
                        <div class="space-y-2 text-xs text-slate-600">
                            <p class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-600"></span> Clearance application</p>
                            <p class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Municipal project</p>
                            <p class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-amber-400"></span> Flood-risk area (illustrative)</p>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                    <h3 class="font-bold text-slate-800">Visible records</h3>
                    <div class="mt-3 space-y-3">
                        <?php foreach (array_slice($clearances, 0, 3) as $row): ?>
                            <div class="border-l-2 border-primary pl-3">
                                <p class="text-xs font-bold text-slate-800"><?= html_escape($row['application_id']) ?></p>
                                <p class="text-xs text-slate-500"><?= html_escape($row['barangay']) ?> · <?= html_escape($row['status']) ?></p>
                            </div>
                        <?php endforeach; ?>
                        <?php foreach (array_slice($projects, 0, 2) as $row): ?>
                            <div class="border-l-2 border-emerald-500 pl-3">
                                <p class="text-xs font-bold text-slate-800"><?= html_escape($row['project_name']) ?></p>
                                <p class="text-xs text-slate-500"><?= html_escape($row['barangay']) ?> · <?= html_escape($row['progress']) ?>% complete</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="mt-4 border-t border-slate-100 pt-3 text-[11px] leading-relaxed text-slate-400">Prototype marker positions and risk overlay are illustrative, not surveyed site or hazard data.</p>
                </section>
            </aside>
        </div>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('prototype-map').setView([13.3182, 121.2825], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const groups = {
        clearances: L.layerGroup().addTo(map),
        projects: L.layerGroup().addTo(map),
        risk: L.layerGroup().addTo(map)
    };
    L.marker([13.3182, 121.2825]).bindPopup('<strong>Municipal Hall</strong><br>Baco, Oriental Mindoro').addTo(map);
    L.circle([13.327, 121.271], {radius: 650, color: '#f59e0b', fillColor: '#fbbf24', fillOpacity: 0.18})
        .bindPopup('Illustrative flood-risk area').addTo(groups.risk);
    [
        {point: [13.329, 121.270], label: 'San Andres · LC-2026-014'},
        {point: [13.318, 121.284], label: 'Poblacion · LC-2026-013'},
        {point: [13.309, 121.294], label: 'Bangkatan · LC-2026-012'}
    ].forEach(function (item) {
        L.circleMarker(item.point, {radius: 8, color: '#1d4ed8', fillColor: '#2563eb', fillOpacity: 0.9})
            .bindPopup(item.label).addTo(groups.clearances);
    });
    [
        {point: [13.336, 121.276], label: 'Barangay Road Repair'},
        {point: [13.313, 121.265], label: 'Drainage Improvement'},
        {point: [13.302, 121.281], label: 'Municipal Health Facility'}
    ].forEach(function (item) {
        L.circleMarker(item.point, {radius: 8, color: '#059669', fillColor: '#10b981', fillOpacity: 0.9})
            .bindPopup(item.label).addTo(groups.projects);
    });

    document.querySelectorAll('.map-layer').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const layer = groups[checkbox.dataset.layer];
            if (checkbox.checked) map.addLayer(layer);
            else map.removeLayer(layer);
        });
    });
    document.getElementById('map-search-form').addEventListener('submit', function (event) {
        event.preventDefault();
        const query = document.getElementById('map-search').value.trim().toLowerCase();
        if (!query) {
            window.alert('Enter a barangay or site to search.');
            return;
        }
        const locations = {
            'san andres': [13.329, 121.270],
            'poblacion': [13.318, 121.284],
            'bangkatan': [13.309, 121.294],
            'lantuyang': [13.336, 121.276],
            'baco': [13.3182, 121.2825]
        };
        const match = Object.keys(locations).find(function (name) { return name.includes(query) || query.includes(name); });
        if (match) map.setView(locations[match], 15);
        else window.alert('No prototype map location matched that search.');
    });
});
</script>
