<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'RGPD Manager' ?></title>
    <link rel="icon" type="image/png" href="assets/logo.png">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body { @apply text-slate-900 antialiased; }
        }
        @layer components {
            .btn { @apply inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200; }
            .btn-primary { @apply text-white bg-primary-600 hover:bg-primary-700 focus:ring-primary-500; }
            .btn-danger { @apply text-white bg-red-600 hover:bg-red-700 focus:ring-red-500; }
            .btn-outline { @apply border-slate-300 text-slate-700 bg-white hover:bg-slate-50 focus:ring-primary-500; }
            
            .card { @apply bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden; }
            
            .form-label { @apply block text-sm font-medium text-slate-700 mb-1; }
            .form-input { @apply block w-full rounded-md border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm transition-colors; }
            
            .alert { @apply p-4 rounded-md mb-6 border; }
            .alert-success { @apply bg-green-50 text-green-800 border-green-200; }
            .alert-error { @apply bg-red-50 text-red-800 border-red-200; }
        }
    </style>
</head>

<body class="min-h-full">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-24">
                <div class="flex items-center">
                    <a href="index.php" class="flex-shrink-0 flex items-center">
                        <img src="assets/logo_texte.png" alt="RGPD Manager" class="h-20 w-auto">
                    </a>
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php
                            $currentPage = $_GET['page'] ?? 'treatment'; // Default if logged in
                            $currentAction = $_GET['action'] ?? '';
                            ?>
                            <a href="index.php?page=treatment&action=dashboard"
                                class="inline-flex items-center px-1 pt-1 border-b-2 <?= ($currentAction === 'dashboard') ? 'border-primary-500 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' ?> text-sm font-medium">
                                Tableau de bord
                            </a>
                            <a href="index.php?page=treatment&action=list"
                                class="inline-flex items-center px-1 pt-1 border-b-2 <?= ($currentPage === 'treatment' && $currentAction === 'list') ? 'border-primary-500 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' ?> text-sm font-medium">
                                Registre
                            </a>
                            <a href="index.php?page=subprocessor&action=list"
                                class="inline-flex items-center px-1 pt-1 border-b-2 <?= ($currentPage === 'subprocessor') ? 'border-primary-500 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' ?> text-sm font-medium">
                                Sous-traitants
                            </a>
                            <a href="index.php?page=rights&action=list"
                                class="inline-flex items-center px-1 pt-1 border-b-2 <?= ($currentPage === 'rights') ? 'border-primary-500 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' ?> text-sm font-medium">
                                Exercice des droits
                            </a>
                            <a href="index.php?page=breach&action=list"
                                class="inline-flex items-center px-1 pt-1 border-b-2 <?= ($currentPage === 'breach') ? 'border-primary-500 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' ?> text-sm font-medium">
                                Violations de données
                            </a>

                            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                <a href="index.php?page=user&action=list"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 <?= ($currentPage === 'user') ? 'border-primary-500 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' ?> text-sm font-medium">
                                    Utilisateurs
                                </a>

                                <a href="index.php?page=logs&action=list"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 <?= ($currentPage === 'logs') ? 'border-primary-500 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' ?> text-sm font-medium">
                                    Journaux (Audit)
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>



                    </div>
                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <!-- Mobile menu button -->
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Ouvrir le menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-slate-600">
                                Bonjour, <span
                                    class="font-semibold text-slate-900"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                                <?php if (($_SESSION['user_role'] ?? '') === 'guest'): ?>
                                    <span
                                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Consultation
                                    </span>
                                <?php endif; ?>
                            </span>

                            <a href="index.php?page=auth&action=logout" class="btn btn-outline py-1.5">
                                Déconnecter
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-3">
                            <a href="index.php?page=auth&action=login" class="btn btn-primary py-1.5 px-6 shadow-none">
                                Connexion
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div class="hidden sm:hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1 bg-white border-t border-slate-100 px-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="index.php?page=treatment&action=dashboard"
                        class="block pl-3 pr-4 py-2 border-l-4 <?= ($currentAction === 'dashboard') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800' ?> text-base font-medium">Tableau
                        de bord</a>
                    <a href="index.php?page=treatment&action=list"
                        class="block pl-3 pr-4 py-2 border-l-4 <?= ($currentPage === 'treatment' && $currentAction === 'list') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800' ?> text-base font-medium">Registre</a>
                    <a href="index.php?page=subprocessor&action=list"
                        class="block pl-3 pr-4 py-2 border-l-4 <?= ($currentPage === 'subprocessor') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800' ?> text-base font-medium">Sous-traitants</a>
                    <a href="index.php?page=rights&action=list"
                        class="block pl-3 pr-4 py-2 border-l-4 <?= ($currentPage === 'rights') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800' ?> text-base font-medium">Exercice
                        des droits</a>
                    <a href="index.php?page=breach&action=list"
                        class="block pl-3 pr-4 py-2 border-l-4 <?= ($currentPage === 'breach') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800' ?> text-base font-medium">Violations
                        de données</a>

                    <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                        <a href="index.php?page=user&action=list"
                            class="block pl-3 pr-4 py-2 border-l-4 <?= ($currentPage === 'user') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800' ?> text-base font-medium">Utilisateurs</a>
                        <a href="index.php?page=logs&action=list"
                            class="block pl-3 pr-4 py-2 border-l-4 <?= ($currentPage === 'logs') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800' ?> text-base font-medium">Journaux
                            (Audit)</a>
                    <?php endif; ?>

                    <div class="pt-4 pb-3 border-t border-slate-200 mt-4">
                        <div class="flex items-center px-4">
                            <div class="ml-3">
                                <div class="text-base font-medium text-slate-800">
                                    <?= htmlspecialchars($_SESSION['user_name']) ?>
                                </div>
                                <div class="text-sm font-medium text-slate-500">
                                    <?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <a href="index.php?page=auth&action=logout"
                                class="block px-4 py-2 text-base font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100">Déconnexion</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="index.php?page=auth&action=login"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800 text-base font-medium">Connexion</a>
                <?php endif; ?>

            </div>
        </div>
    </nav>


    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <?= htmlspecialchars($_SESSION['flash_success']) ?>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-error flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <?= htmlspecialchars($_SESSION['flash_error']) ?>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <div class="animate-fade-in">
            <?= $content ?>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col items-center gap-4">
            <img src="assets/logo.png" alt="Logo"
                class="h-8 w-auto opacity-50 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-300">
            <p class="text-center text-sm text-slate-500">
                &copy; <?= date('Y') ?> RGPD Manager - Solution de mise en conformité
            </p>
            <a href="index.php?page=credits" class="text-xs text-slate-400 hover:text-primary-600 transition-colors">
                Crédits & Mentions
            </a>

        </div>
    </footer>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
    </style>
</body>

</html>