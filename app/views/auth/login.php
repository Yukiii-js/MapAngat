<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MapAngat Login' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0038DE',
                        'primary-dark': '#002bc2',
                        'primary-light': '#e8edff',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-900 text-slate-800 font-sans antialiased h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-200">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-primary-light text-primary rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                <i class="fa-solid fa-map-location-dot text-2xl"></i>
            </div>
            <h1 class="text-3xl font-black text-primary tracking-tight">MapAngat</h1>
            <p class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-wider">Web-GIS & Project Monitoring System</p>
            <p class="text-xs text-slate-400 mt-0.5">MPDO • Baco, Oriental Mindoro</p>
        </div>

        <?php
            $login_error = $_SESSION['login_error'] ?? '';
            if ($login_error !== '') {
                unset($_SESSION['login_error']);
            }
        ?>
        <?php if ($login_error !== ''): ?>
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                <?= html_escape($login_error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('auth/process_login') ?>" method="POST" class="space-y-4 mt-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Username</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                    <input type="text" name="username" value="Clapis" required
                        class="w-full pl-10 pr-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                    <input type="password" name="password" value="Clapis0224" required
                        class="w-full pl-10 pr-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" checked class="rounded text-primary focus:ring-primary">
                    Remember me
                </label>
                <a href="#" onclick="alert('Please contact the MPDO System Administrator to reset your access credentials.')" class="font-bold text-primary hover:underline">Forgot password?</a>
            </div>

            <button type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-xl text-sm transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In to MapAngat
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 text-center">
            <p class="text-[11px] text-slate-400 font-medium">Municipal Planning & Development Office — LGU Baco</p>
        </div>
    </div>

</body>
</html>