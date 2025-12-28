<div class="max-w-md mx-auto">
    <div class="text-center mb-8">
        <img src="assets/logo.png" alt="Logo" class="h-20 w-auto mx-auto mb-4">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Connexion</h1>
        <p class="text-slate-600">Accédez à votre compte RGPD Manager</p>
    </div>

    <div class="card p-8">
        <form action="index.php?page=auth&action=login_process" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" required class="form-input" placeholder="votre@email.com">
            </div>

            <div>
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" required class="form-input" placeholder="••••••••">
            </div>

            <div class="pt-2">
                <button type="submit" class="btn btn-primary w-full py-2.5">
                    Se connecter
                </button>
            </div>

            <?php if ($allowGuest ?? false): ?>
                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-slate-500 uppercase tracking-wider text-xs">Ou</span>
                    </div>
                </div>

                <a href="index.php?page=auth&action=login_guest"
                    class="btn btn-outline w-full py-2.5 flex items-center gap-2 border-primary-100 text-primary-600 hover:bg-primary-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Consulter en tant qu'invité
                </a>
            <?php endif; ?>


            <p class="text-center text-sm text-slate-600 mt-6">
                Pas encore de compte ?
                <a href="index.php?page=auth&action=register"
                    class="font-medium text-primary-600 hover:text-primary-500 transition-colors">
                    S'inscrire
                </a>
            </p>
        </form>
    </div>
</div>