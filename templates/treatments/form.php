<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="index.php?page=treatment&action=list"
            class="text-sm font-medium text-primary-600 hover:text-primary-500 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Retour au registre
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900">
            <?= isset($treatment->id) ? 'Modifier le traitement' : 'Nouveau traitement' ?>
        </h1>
        <p class="text-slate-500 mt-1">Saisissez les informations relatives à votre activité de traitement.</p>
    </div>

    <div class="card p-8">
        <form action="index.php?page=treatment&action=<?= isset($treatment->id) ? 'update' : 'store' ?>" method="POST"
            class="space-y-6">
            <?php if (isset($treatment->id)): ?>
                <input type="hidden" name="id" value="<?= $treatment->id ?>">
            <?php endif; ?>

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div>
                <label for="name" class="form-label">Nom du traitement</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($treatment->name ?? '') ?>"
                    required class="form-input" placeholder="Ex: Gestion de la paie, CRM Clients...">
            </div>

            <div>
                <label for="purpose" class="form-label">Finalité principale</label>
                <textarea id="purpose" name="purpose" rows="3" required class="form-input"
                    placeholder="Décrivez l'objectif du traitement..."><?= htmlspecialchars($treatment->purpose ?? '') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="legal_basis" class="form-label">Base légale</label>
                    <select id="legal_basis" name="legal_basis" required class="form-input">
                        <option value="">-- Choisir une base --</option>
                        <option value="Consentement" <?= ($treatment->legalBasis ?? '') === 'Consentement' ? 'selected' : '' ?>>Consentement</option>
                        <option value="Contrat" <?= ($treatment->legalBasis ?? '') === 'Contrat' ? 'selected' : '' ?>>
                            Contrat</option>
                        <option value="Obligation légale" <?= ($treatment->legalBasis ?? '') === 'Obligation légale' ? 'selected' : '' ?>>Obligation légale</option>
                        <option value="Mission d'intérêt public" <?= ($treatment->legalBasis ?? '') === "Mission d'intérêt public" ? 'selected' : '' ?>>Mission d'intérêt public</option>
                        <option value="Intérêt légitime" <?= ($treatment->legalBasis ?? '') === 'Intérêt légitime' ? 'selected' : '' ?>>Intérêt légitime</option>
                        <option value="Sauvegarde des intérêts vitaux" <?= ($treatment->legalBasis ?? '') === 'Sauvegarde des intérêts vitaux' ? 'selected' : '' ?>>Sauvegarde des intérêts vitaux</option>
                    </select>
                </div>
                <div>
                    <label for="data_categories" class="form-label">Catégories de données</label>
                    <input type="text" id="data_categories" name="data_categories"
                        value="<?= htmlspecialchars($treatment->dataCategories ?? '') ?>" required class="form-input"
                        placeholder="Ex: État civil, Identité...">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label for="retention_period" class="form-label">Règle de conservation</label>
                    <input type="text" id="retention_period" name="retention_period"
                        value="<?= htmlspecialchars($treatment->retentionPeriod ?? '') ?>" required class="form-input"
                        placeholder="Ex: 5 ans après la rupture du contrat">
                </div>
                <div>
                    <label for="retention_years" class="form-label">Alerte (années)</label>
                    <input type="number" id="retention_years" name="retention_years" min="1" max="99"
                        value="<?= htmlspecialchars($treatment->retentionYears ?? '5') ?>" required class="form-input">
                </div>
            </div>

            <div class="bg-slate-50 -mx-8 px-8 py-6 border-y border-slate-200">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="font-bold text-slate-800 uppercase tracking-wider text-xs">Sous-traitants (Hébergeur, SaaS, etc.)</h3>
                </div>
                
                <?php if (empty($allSubprocessors)): ?>
                    <div class="p-4 bg-white border border-slate-200 rounded-lg text-sm text-slate-500 text-center">
                        Aucun sous-traitant enregistré. 
                        <a href="index.php?page=subprocessor&action=create" class="text-primary-600 font-medium hover:underline ml-1">Ajouter un sous-traitant</a>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-slate-500 mb-4">Sélectionnez les sous-traitants impliqués dans ce traitement :</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-48 overflow-y-auto p-1">
                        <?php foreach ($allSubprocessors as $sub): ?>
                            <label class="flex items-center p-3 border border-slate-200 rounded-lg hover:bg-white hover:border-primary-200 cursor-pointer transition-all">
                                <input type="checkbox" name="subprocessors[]" value="<?= $sub->id ?>" 
                                    <?= in_array($sub->id, $selectedSubprocessors) ? 'checked' : '' ?>
                                    class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-slate-700"><?= htmlspecialchars($sub->name) ?></span>
                                    <span class="block text-xs text-slate-500"><?= htmlspecialchars($sub->service) ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-slate-50 -mx-8 px-8 py-6 border-b border-slate-200">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <h3 class="font-bold text-slate-800 uppercase tracking-wider text-xs">Diagnostic AIPD</h3>
                </div>
                <p class="text-sm text-slate-500 mb-4">Cochez les critères applicables à ce traitement :</p>

                <div class="space-y-3">
                    <label class="relative flex items-start cursor-pointer group">
                        <div class="flex items-center h-5">
                            <input type="checkbox" id="has_sensitive_data" name="has_sensitive_data" value="1"
                                <?= ($treatment->hasSensitiveData ?? false) ? 'checked' : '' ?>
                                class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <span
                                class="font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Données
                                sensibles</span>
                            <p class="text-slate-500 text-xs">Santé, opinions politiques, biométrie, origine raciale...
                            </p>
                        </div>
                    </label>

                    <label class="relative flex items-start cursor-pointer group">
                        <div class="flex items-center h-5">
                            <input type="checkbox" id="is_large_scale" name="is_large_scale" value="1"
                                <?= ($treatment->isLargeScale ?? false) ? 'checked' : '' ?>
                                class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <span
                                class="font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Traitement
                                à grande échelle</span>
                            <p class="text-slate-500 text-xs">Nombre important de personnes concernées ou volume de
                                données élevé.</p>
                        </div>
                    </label>
                </div>

                <div id="aipd-warning"
                    class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg hidden animate-fade-in">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <div class="text-sm text-amber-800">
                            <strong>AIPD Fortement conseillée :</strong> Ce traitement présente un risque élevé (données
                            sensibles + grande échelle). Une Analyse d'Impact relative à la Protection des Données est
                            préconisée.
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="index.php?page=treatment&action=list"
                    class="text-sm font-medium text-slate-500 hover:text-slate-700">Retour</a>
                <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
                    <button type="submit" class="btn btn-primary px-8 py-2.5">
                        <?= isset($treatment->id) ? 'Mettre à jour' : 'Enregistrer le traitement' ?>
                    </button>
                <?php else: ?>
                    <span class="text-sm italic text-amber-600 font-medium">Lecture seule</span>
                <?php endif; ?>
            </div>

        </form>
    </div>
</div>

<script>
    const sensBox = document.getElementById('has_sensitive_data');
    const largeBox = document.getElementById('is_large_scale');
    const warning = document.getElementById('aipd-warning');

    function checkAipd() {
        if (sensBox.checked && largeBox.checked) {
            warning.classList.remove('hidden');
        } else {
            warning.classList.add('hidden');
        }
    }

    sensBox.addEventListener('change', checkAipd);
    largeBox.addEventListener('change', checkAipd);
    checkAipd();
</script>