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