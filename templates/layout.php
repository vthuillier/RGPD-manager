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

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

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
    <nav class="bg-white /80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center gap-8">
                    <a href="index.php" class="flex-shrink-0 flex items-center transition-transform hover:scale-105">
                        <img src="assets/logo_texte.png" alt="RGPD Manager" class="h-16 w-auto">
                    </a>

                    <div class="hidden lg:flex items-center space-x-1">
                        <?php
                        $currentPage = $_GET['page'] ?? 'treatment';
                        if (isset($_SESSION['user_id']) && !in_array($currentPage, ['setup', 'upgrade'])):
                            $currentAction = $_GET['action'] ?? '';
                            $userRole = $_SESSION['user_role'] ?? 'user';

                            $isActive = function ($page, $action = null) use ($currentPage, $currentAction) {
                                if ($action === null)
                                    return $currentPage === $page;
                                return $currentPage === $page && $currentAction === $action;
                            };

                            $navItemClass = "inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 gap-2 ";
                            $activeClass = "bg-primary-50 text-primary-700 shadow-sm ring-1 ring-primary-100";
                            $inactiveClass = "text-slate-600 hover:bg-slate-50 hover:text-slate-900";
                            ?>

                            <a href="index.php?page=treatment&action=dashboard"
                                class="<?= $navItemClass ?> <?= ($currentAction === 'dashboard') ? $activeClass : $inactiveClass ?>">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                Tableau de bord
                            </a>

                            <!-- Dropdown Registry -->
                            <div class="relative group">
                                <button
                                    class="<?= $navItemClass ?> <?= in_array($currentPage, ['treatment', 'subprocessor', 'aipd', 'dtia']) && $currentAction !== 'dashboard' ? $activeClass : $inactiveClass ?>">
                                    <i data-lucide="folder-kanban" class="w-4 h-4"></i>
                                    Registre & Conformité
                                    <i data-lucide="chevron-down"
                                        class="w-3 h-3 transition-transform group-hover:rotate-180"></i>
                                </button>
                                <div class="absolute top-full left-0 w-56 pt-2 hidden group-hover:block z-50">
                                    <div
                                        class="bg-white border border-slate-200 shadow-xl rounded-xl py-2 overflow-hidden ring-1 ring-black ring-opacity-5">
                                        <a href="index.php?page=treatment&action=list"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('treatment', 'list') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                            <i data-lucide="list-todo" class="w-4 h-4"></i> Registre des traitements
                                        </a>
                                        <a href="index.php?page=subprocessor&action=list"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('subprocessor') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                            <i data-lucide="users-2" class="w-4 h-4"></i> Sous-traitants
                                        </a>
                                        <a href="index.php?page=aipd&action=list"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('aipd') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                            <i data-lucide="shield-alert" class="w-4 h-4"></i> AIPD / DPIA
                                        </a>
                                        <a href="index.php?page=dtia&action=list"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('dtia') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                            <i data-lucide="globe" class="w-4 h-4"></i> Transferts (DTIA)
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Dropdown Operations -->
                            <div class="relative group">
                                <button
                                    class="<?= $navItemClass ?> <?= in_array($currentPage, ['rights', 'breach', 'maturity']) ? $activeClass : $inactiveClass ?>">
                                    <i data-lucide="activity" class="w-4 h-4"></i>
                                    Opérations
                                    <i data-lucide="chevron-down"
                                        class="w-3 h-3 transition-transform group-hover:rotate-180"></i>
                                </button>
                                <div class="absolute top-full left-0 w-56 pt-2 hidden group-hover:block z-50">
                                    <div
                                        class="bg-white border border-slate-200 shadow-xl rounded-xl py-2 overflow-hidden ring-1 ring-black ring-opacity-5">
                                        <a href="index.php?page=rights&action=list"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('rights') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                            <i data-lucide="user-plus" class="w-4 h-4"></i> Exercice des droits
                                        </a>
                                        <a href="index.php?page=breach&action=list"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('breach') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                            <i data-lucide="skull" class="w-4 h-4"></i> Violations de données
                                        </a>
                                        <a href="index.php?page=maturity&action=index"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('maturity') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                            <i data-lucide="line-chart" class="w-4 h-4"></i> Indice de maturité
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Dropdown -->
                            <?php if ($userRole === 'org_admin' || $userRole === 'super_admin'): ?>
                                <div class="relative group">
                                    <button
                                        class="<?= $navItemClass ?> <?= in_array($currentPage, ['user', 'security_measure', 'settings', 'organization', 'logs']) ? $activeClass : $inactiveClass ?>">
                                        <i data-lucide="settings" class="w-4 h-4"></i>
                                        Paramètres
                                        <i data-lucide="chevron-down"
                                            class="w-3 h-3 transition-transform group-hover:rotate-180"></i>
                                    </button>
                                    <div class="absolute top-full left-0 w-64 pt-2 hidden group-hover:block z-50">
                                        <div
                                            class="bg-white border border-slate-200 shadow-xl rounded-xl py-2 overflow-hidden ring-1 ring-black ring-opacity-5">
                                            <div
                                                class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                                Organisation</div>
                                            <a href="index.php?page=user&action=list"
                                                class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('user') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                                <i data-lucide="users" class="w-4 h-4"></i> Utilisateurs
                                            </a>
                                            <a href="index.php?page=security_measure&action=list"
                                                class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('security_measure') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                                <i data-lucide="shield" class="w-4 h-4"></i> Mesures de sécurité
                                            </a>
                                            <a href="index.php?page=settings&action=notifications"
                                                class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('settings') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                                <i data-lucide="bell" class="w-4 h-4"></i> Notifications
                                            </a>

                                            <?php if ($userRole === 'super_admin'): ?>
                                                <div class="my-2 border-t border-slate-100"></div>
                                                <div
                                                    class="px-4 py-1.5 text-[10px] font-bold text-red-400 uppercase tracking-widest">
                                                    Système</div>
                                                <a href="index.php?page=organization&action=list"
                                                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('organization') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                                    <i data-lucide="building-2" class="w-4 h-4 text-red-500"></i> Organismes
                                                </a>
                                                <a href="index.php?page=logs&action=list"
                                                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $isActive('logs') ? 'bg-primary-50 text-primary-700 font-semibold' : '' ?>">
                                                    <i data-lucide="newspaper" class="w-4 h-4 text-red-500"></i> Logs d'audit
                                                </a>
                                                <a href="index.php?page=settings&action=notifications&system=1"
                                                    class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                    <i data-lucide="mail-warning" class="w-4 h-4"></i> Config Mail
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <?php if (isset($_SESSION['user_id']) && !in_array($currentPage, ['setup', 'upgrade'])): ?>
                        <div class="hidden md:flex items-center space-x-4">
                            <!-- Context Switcher -->
                            <?php
                            $orgRepo = new \App\Repository\OrganizationRepository();
                            if ($userRole === 'super_admin') {
                                $userOrgs = $orgRepo->findAll();
                            } else {
                                $userOrgs = $orgRepo->findAllByUserId((int) $_SESSION['user_id']);
                            }
                            ?>
                            <div class="relative group">
                                <div
                                    class="flex items-center gap-2 bg-slate-100/50 px-3 py-1.5 rounded-lg border border-slate-200 cursor-pointer hover:bg-white transition-all duration-200">
                                    <i data-lucide="building" class="w-4 h-4 text-slate-500"></i>
                                    <span class="text-xs font-semibold text-slate-700 max-w-[150px] truncate">
                                        <?php
                                        $currentOrg = array_filter($userOrgs, fn($o) => $o->id === ($_SESSION['organization_id'] ?? 0));
                                        echo !empty($currentOrg) ? htmlspecialchars(reset($currentOrg)->name) : 'Sélectionner';
                                        ?>
                                    </span>
                                    <i data-lucide="chevrons-up-down" class="w-3 h-3 text-slate-400"></i>
                                </div>
                                <div class="absolute top-full right-0 w-64 pt-2 hidden group-hover:block z-50">
                                    <div
                                        class="bg-white border border-slate-200 shadow-xl rounded-xl py-2 overflow-hidden overflow-y-auto max-h-80 ring-1 ring-black ring-opacity-5">
                                        <div
                                            class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                            Changer d'organisme</div>
                                        <?php if (empty($userOrgs)): ?>
                                            <div class="px-4 py-2 text-xs text-slate-500 italic">Aucun organisme</div>
                                        <?php endif; ?>
                                        <?php foreach ($userOrgs as $org): ?>
                                            <a href="index.php?page=auth&action=switch_org&org_id=<?= $org->id ?>"
                                                class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors <?= $org->id === ($_SESSION['organization_id'] ?? 0) ? 'bg-primary-50 text-primary-700 font-bold' : '' ?>">
                                                <div
                                                    class="w-5 h-5 flex items-center justify-center bg-slate-100 rounded text-[10px]">
                                                    <?= strtoupper(substr($org->name, 0, 1)) ?>
                                                </div>
                                                <span class="truncate"><?= htmlspecialchars($org->name) ?></span>
                                                <?php if ($org->id === ($_SESSION['organization_id'] ?? 0)): ?>
                                                    <i data-lucide="check" class="ml-auto w-3 h-3"></i>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- User Profile -->
                            <div class="relative group">
                                <button
                                    class="flex items-center gap-3 pl-3 pr-1 py-1 rounded-full border border-slate-200 bg-white hover:shadow-md transition-all duration-200">
                                    <div class="flex flex-col items-end">
                                        <span
                                            class="text-xs font-bold text-slate-900 leading-tight"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                                        <span
                                            class="text-[10px] text-slate-500 uppercase tracking-tighter font-medium leading-tight"><?= $userRole === 'super_admin' ? 'Super Admin' : ($userRole === 'org_admin' ? 'Admin' : 'Utilisateur') ?></span>
                                    </div>
                                    <div
                                        class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-white">
                                        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                                    </div>
                                </button>
                                <div class="absolute top-full right-0 w-48 pt-2 hidden group-hover:block z-50">
                                    <div
                                        class="bg-white border border-slate-200 shadow-xl rounded-xl py-2 overflow-hidden ring-1 ring-black ring-opacity-5">
                                        <a href="index.php?page=auth&action=logout"
                                            class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i data-lucide="log-out" class="w-4 h-4"></i> Déconnexion
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile toggle -->
                        <div class="lg:hidden flex items-center">
                            <button type="button"
                                onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                                class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-slate-500 hover:bg-slate-100 transition-colors">
                                <i data-lucide="menu" class="h-6 w-6"></i>
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-3">
                            <a href="index.php?page=auth&action=login"
                                class="text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">Se
                                connecter</a>
                            <a href="index.php?page=setup"
                                class="btn btn-primary bg-primary-600 px-5 py-2 rounded-lg">Commencer</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="hidden lg:hidden border-t border-slate-100 bg-white/95 backdrop-blur-md" id="mobile-menu">
            <div class="px-4 pt-2 pb-6 space-y-1">
                <?php if (isset($_SESSION['user_id']) && !in_array($currentPage, ['setup', 'upgrade'])): ?>
                    <div class="flex items-center gap-4 py-4 border-b border-slate-100 mb-4">
                        <div
                            class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center text-white font-bold">
                            <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900"><?= htmlspecialchars($_SESSION['user_name']) ?>
                            </div>
                            <div class="text-[10px] text-slate-500 uppercase tracking-widest"><?= $userRole ?></div>
                        </div>
                    </div>

                    <div class="py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Navigation</div>
                    <a href="index.php?page=treatment&action=dashboard"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium transition-colors <?= $isActive('treatment', 'dashboard') ? 'bg-primary-50 text-primary-700' : 'text-slate-600' ?>">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                    </a>
                    <a href="index.php?page=treatment&action=list"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium transition-colors <?= $isActive('treatment', 'list') ? 'bg-primary-50 text-primary-700' : 'text-slate-600' ?>">
                        <i data-lucide="list-todo" class="w-5 h-5"></i> Registre
                    </a>
                    <a href="index.php?page=subprocessor&action=list"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium transition-colors <?= $isActive('subprocessor') ? 'bg-primary-50 text-primary-700' : 'text-slate-600' ?>">
                        <i data-lucide="users-2" class="w-5 h-5"></i> Sous-traitants
                    </a>
                    <a href="index.php?page=aipd&action=list"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium transition-colors <?= $isActive('aipd') ? 'bg-primary-50 text-primary-700' : 'text-slate-600' ?>">
                        <i data-lucide="shield-alert" class="w-5 h-5"></i> AIPD
                    </a>

                    <div class="py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Opérations</div>
                    <a href="index.php?page=rights&action=list"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium transition-colors <?= $isActive('rights') ? 'bg-primary-50 text-primary-700' : 'text-slate-600' ?>">
                        <i data-lucide="user-plus" class="w-5 h-5"></i> Droits
                    </a>
                    <a href="index.php?page=breach&action=list"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium transition-colors <?= $isActive('breach') ? 'bg-primary-50 text-primary-700' : 'text-slate-600' ?>">
                        <i data-lucide="skull" class="w-5 h-5"></i> Violations
                    </a>
                    <a href="index.php?page=maturity&action=index"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium transition-colors <?= $isActive('maturity') ? 'bg-primary-50 text-primary-700' : 'text-slate-600' ?>">
                        <i data-lucide="line-chart" class="w-5 h-5"></i> Maturité
                    </a>

                    <div class="pt-6 mt-6 border-t border-slate-100">
                        <a href="index.php?page=auth&action=logout"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium text-red-600 hover:bg-red-50 transition-colors">
                            <i data-lucide="log-out" class="w-5 h-5"></i> Déconnexion
                        </a>
                    </div>
                <?php else: ?>
                    <a href="index.php?page=auth&action=login"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium text-primary-600 hover:bg-primary-50">
                        <i data-lucide="log-in" class="w-5 h-5"></i> Connexion
                    </a>
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
                &copy; <?= date('Y') ?> RGPD Manager - Solution de mise en conformité -
                v<?= defined('APP_VERSION') ? APP_VERSION : '?.?.?' ?>
            </p>
            <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-xs text-slate-400">
                <a href="index.php?page=legal&action=mentions" class="hover:text-primary-600 transition-colors">Mentions
                    Légales</a>
                <a href="index.php?page=legal&action=policy" class="hover:text-primary-600 transition-colors">Politique
                    de Confidentialité</a>
                <a href="index.php?page=legal&action=cgu" class="hover:text-primary-600 transition-colors">CGU</a>
                <a href="index.php?page=credits" class="hover:text-primary-600 transition-colors">Crédits</a>
            </div>

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
    <script>
        lucide.createIcons();
    </script>
</body>

</html>