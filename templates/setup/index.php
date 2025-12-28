<div class="max-w-2xl mx-auto py-12">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Bienvenue sur RGPD Manager</h1>
        <p class="text-slate-600">Commençons par configurer votre espace de travail et votre compte administrateur.</p>
    </div>

    <div class="card p-8">
        <form action="index.php?page=setup&action=process" method="POST" class="space-y-8">
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center border-b pb-2">
                    <svg class="w-6 h-6 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    Votre Organisation
                </h2>
                <div>
                    <label for="org_name"
                        class="form-label text-sm uppercase tracking-wider font-semibold text-slate-500">Nom de
                        l'organisation</label>
                    <input type="text" id="org_name" name="org_name" required class="form-input"
                        placeholder="Ex: Ma Société SAS">
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center border-b pb-2">
                    <svg class="w-6 h-6 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Compte Administrateur
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="admin_name"
                            class="form-label text-sm uppercase tracking-wider font-semibold text-slate-500">Nom
                            complet</label>
                        <input type="text" id="admin_name" name="admin_name" required class="form-input"
                            placeholder="Jean Dupont">
                    </div>
                    <div>
                        <label for="admin_email"
                            class="form-label text-sm uppercase tracking-wider font-semibold text-slate-500">Email
                            professionnel</label>
                        <input type="email" id="admin_email" name="admin_email" required class="form-input"
                            placeholder="admin@ma-societe.fr">
                    </div>
                </div>
                <div>
                    <label for="admin_password"
                        class="form-label text-sm uppercase tracking-wider font-semibold text-slate-500">Mot de
                        passe</label>
                    <input type="password" id="admin_password" name="admin_password" required minlength="8"
                        class="form-input" placeholder="••••••••">
                    <p class="mt-1 text-xs text-slate-500 italic">8 caractères minimum préconisés</p>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="btn btn-primary w-full py-3 text-lg font-bold shadow-lg shadow-primary-200">
                    Finaliser l'installation
                </button>
            </div>
        </form>
    </div>
</div>