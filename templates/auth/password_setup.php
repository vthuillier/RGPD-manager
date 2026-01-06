<div class="max-w-md mx-auto my-12">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">RGPD Manager</h1>
        <p class="mt-2 text-slate-600">Définissez votre mot de passe pour accéder à votre compte.</p>
    </div>

    <div class="card p-8 shadow-xl border-slate-200">
        <form action="index.php?page=password&action=update" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div>
                <label for="password" class="form-label text-slate-700">Nouveau mot de passe</label>
                <div class="mt-1">
                    <input type="password" id="password" name="password" required minlength="8"
                        class="form-input focus:ring-primary-500 focus:border-primary-500" placeholder="••••••••">
                </div>
            </div>

            <div>
                <label for="password_confirm" class="form-label text-slate-700">Confirmer le mot de passe</label>
                <div class="mt-1">
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="8"
                        class="form-input focus:ring-primary-500 focus:border-primary-500" placeholder="••••••••">
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg">
                <p class="text-xs text-slate-500 flex gap-2">
                    <svg class="w-4 h-4 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.
                </p>
            </div>

            <div>
                <button type="submit"
                    class="btn btn-primary w-full py-3 text-base shadow-sm hover:translate-y-[-1px] transition-all">
                    Activer mon compte
                </button>
            </div>
        </form>
    </div>
</div>