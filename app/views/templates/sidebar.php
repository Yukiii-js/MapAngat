<aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between z-20 shrink-0 shadow-sm">
    <div>
        <div class="p-5 border-b border-slate-100 bg-primary/5 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-primary tracking-tight">MapAngat</h1>
                <p class="text-[11px] text-slate-600 font-semibold mt-0.5 leading-tight">MPDO • Baco, Oriental Mindoro</p>
            </div>
        </div>

        <nav class="p-3 space-y-1 text-sm font-medium">
            <a href="<?= site_url('dashboard') ?>" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg <?= segment(1) == 'dashboard' ? 'text-primary bg-primary-light font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                <i class="fa-solid fa-gauge-high w-5 text-center"></i> Overview
            </a>
            <a href="<?= site_url('web-gis') ?>" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg <?= segment(1) == 'web-gis' ? 'text-primary bg-primary-light font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                <i class="fa-solid fa-map w-5 text-center"></i> Web-GIS Map
            </a>
            <a href="<?= site_url('spatial-analysis') ?>" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg <?= segment(1) == 'spatial-analysis' ? 'text-primary bg-primary-light font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                <i class="fa-solid fa-layer-group w-5 text-center"></i> Spatial Analysis
            </a>
            <a href="<?= site_url('location-clearance') ?>" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg <?= segment(1) == 'location-clearance' ? 'text-primary bg-primary-light font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                <i class="fa-solid fa-file-shield w-5 text-center"></i> Location Clearance
            </a>
            <a href="<?= site_url('program-monitoring') ?>" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg <?= segment(1) == 'program-monitoring' ? 'text-primary bg-primary-light font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                <i class="fa-solid fa-bars-progress w-5 text-center"></i> Program Monitoring
            </a>
            <a href="<?= site_url('qr-verification') ?>" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg <?= segment(1) == 'qr-verification' ? 'text-primary bg-primary-light font-bold' : 'text-slate-600 hover:bg-slate-100' ?>">
                <i class="fa-solid fa-qrcode w-5 text-center"></i> QR Field Compliance
            </a>
        </nav>
    </div>

    <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                <?= html_escape($username ?? 'Admin') ?>
            </div>
            <div class="overflow-hidden">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Signed in as</p>
                <p class="text-xs font-bold text-slate-700 truncate"><?= html_escape($username ?? 'Admin') ?></p>
            </div>
        </div>
        <a href="<?= site_url('logout') ?>" class="text-slate-400 hover:text-rose-600 p-2 rounded-lg transition-colors"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
    </div>
</aside>