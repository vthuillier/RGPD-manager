<div class="max-w-lg mx-auto">
    <div class="text-center mb-8">
        <img src="assets/logo.png" alt="Logo" class="h-20 w-auto mx-auto mb-4">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Inscription</h1>
        <p class="text-slate-600">Créez votre compte pour commencer à gérer vos registres RGPD</p>
    </div>

    <div class="card p-8">
        <form action="index.php?page=auth&action=register_process" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div>
                <label for="name" class="form-label">Nom complet</label>
                <input type="text" id="name" name="name" required class="form-input" placeholder="Jean Dupont">
            </div>

            <div>
                <label for="organization_name" class="form-label">Nom de votre organisation / entreprise</label>
                <input type="text" id="organization_name" name="organization_name" required class="form-input"
                    placeholder="Ma Société SAS">
            </div>


            <div>
                <label for="email" class="form-label">Adresse Email</label>
                <input type="email" id="email" name="email" required class="form-input" placeholder="jean@exemple.fr">
            </div>

            <div>
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" required minlength="8" class="form-input"
                    placeholder="••••••••">
                <p class="mt-1 text-xs text-slate-500 italic">8 caractères minimum préconisés</p>
            </div>

            <div class="pt-2">
                <button type="submit" class="btn btn-primary w-full py-2.5">
                    Créer mon compte
                </button>
            </div>

            <p class="text-center text-sm text-slate-600 mt-6">
                Déjà inscrit ?
                <a href="index.php?page=auth&action=login"
                    class="font-medium text-primary-600 hover:text-primary-500 transition-colors">
                    Se connecter
                </a>
            </p>
        </form>
    </div>
</div>