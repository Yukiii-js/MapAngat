<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MapAngat - MPDO Baco' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#0038DE', 'primary-dark': '#002bc2', 'primary-light': '#e8edff' }
                }
            }
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        #map { width: 100%; height: 100%; z-index: 1; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased h-screen overflow-hidden">
<div class="flex h-screen overflow-hidden">