<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="index.php?page=user&action=list"
            class="text-slate-500 hover:text-slate-700 flex items-center mb-4 transition-colors">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Retour à la liste
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Ajouter un utilisateur</h1>
        <p class="text-slate-600">L'utilisateur pourra se connecter avec son email et le mot de passe défini ci-dessous.
        </p>
    </div>

    <div class="card p-8">
        <form action="index.php?page=user&action=store" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div>
                <label for="name" class="form-label">Nom complet</label>
                <input type="text" id="name" name="name" required class="form-input" placeholder="Ex: Jean Dupont">
            </div>

            <div>
                <label for="email" class="form-label">Adresse Email</label>
                <input type="email" id="email" name="email" required class="form-input" placeholder="jean@exemple.fr">
            </div>

            <div>
                <label for="role" class="form-label">Rôle</label>
                <select id="role" name="role" class="form-input">
                    <option value="user">Utilisateur (Lecture/Écriture)</option>
                    <option value="admin">Administrateur (Gestion des utilisateurs)</option>
                </select>
            </div>

            <div>
                <label for="password" class="form-label">Mot de passe temporaire</label>
                <input type="password" id="password" name="password" required minlength="8" class="form-input"
                    placeholder="••••••••">
                <p class="mt-1 text-xs text-slate-500 italic">8 caractères minimum préconisés. L'utilisateur pourra le
                    modifier ultérieurement.</p>
            </div>

            <div class="pt-4">
                <button type="submit" class="btn btn-primary w-full py-2.5">
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>