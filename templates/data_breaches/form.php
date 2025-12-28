<?php $isEdit = isset($breach); ?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="index.php?page=breach&action=list"
            class="inline-flex items-center text-sm text-slate-500 hover:text-primary-600 mb-4 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Retour au registre des violations
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900">
            <?= $isEdit ? 'Modifier le dossier d\'incident' : 'Déclarer une violation de données' ?>
        </h1>
        <p class="text-slate-500 mt-1">Saisie des informations requises pour l'audit et la conformité</p>
    </div>

    <form action="index.php?page=breach&action=<?= $isEdit ? 'update' : 'store' ?>" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $breach->id ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne Gauche : Infos Générales -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Nature de l'incident
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="discovery_date" class="form-label">Date et heure de la découverte</label>
                            <input type="datetime-local" id="discovery_date" name="discovery_date"
                                value="<?= $breach ? date('Y-m-d\TH:i', strtotime($breach->discoveryDate)) : date('Y-m-d\TH:i') ?>"
                                required class="form-input">
                        </div>
                        <div>
                            <label for="nature" class="form-label">Nature de la violation</label>
                            <textarea id="nature" name="nature" rows="3" required class="form-input"
                                placeholder="Ex: Accès non autorisé suite à un phishing, perte d'une clé USB non chiffrée..."><?= htmlspecialchars($breach->nature ?? '') ?></textarea>
                        </div>
                        <div>
                            <label for="consequences" class="form-label">Conséquences probables</label>
                            <textarea id="consequences" name="consequences" rows="2" class="form-input"
                                placeholder="Risques pour les personnes : usurpation d'identité, perte de confidentialité..."><?= htmlspecialchars($breach->consequences ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>

                        Mesures prises
                    </h3>
                    <textarea id="measures_taken" name="measures_taken" rows="4" class="form-input"
                        placeholder="Actions immédiates pour stopper la fuite, mesures pour éviter que cela ne se reproduise..."><?= htmlspecialchars($breach->measuresTaken ?? '') ?></textarea>
                </div>
            </div>

            <!-- Colonne Droite : Données & Stats -->
            <div class="space-y-6">
                <div class="card p-6">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Données concernées</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="data_categories" class="form-label">Catégories de données</label>
                            <input type="text" id="data_categories" name="data_categories"
                                value="<?= htmlspecialchars($breach->dataCategories ?? '') ?>" required
                                class="form-input" placeholder="Ex: Identité, données bancaires, mot de passe...">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="subjects_count" class="form-label">Nb personnes</label>
                                <input type="number" id="subjects_count" name="subjects_count"
                                    value="<?= $breach->subjectsCount ?? '' ?>" class="form-input" placeholder="N/A">
                            </div>
                            <div>
                                <label for="records_count" class="form-label">Nb fichiers</label>
                                <input type="number" id="records_count" name="records_count"
                                    value="<?= $breach->recordsCount ?? '' ?>" class="form-input" placeholder="N/A">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-6 bg-slate-50 border-slate-200">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Obligations légales</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="is_notified_authority" name="is_notified_authority"
                                <?= ($breach->isNotifiedAuthority ?? false) ? 'checked' : '' ?>
                                class="mt-1 h-4 w-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                            <label for="is_notified_authority" class="text-sm text-slate-700">Notification à l'autorité
                                (CNIL) effectuée ?</label>
                        </div>
                        <div>
                            <label for="notification_authority_date" class="form-label text-xs">Si oui, à quelle date
                                ?</label>
                            <input type="datetime-local" id="notification_authority_date"
                                name="notification_authority_date"
                                value="<?= ($breach->notificationAuthorityDate ?? false) ? date('Y-m-d\TH:i', strtotime($breach->notificationAuthorityDate)) : '' ?>"
                                class="form-input text-sm">
                        </div>
                        <hr class="border-slate-200">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="is_notified_individuals" name="is_notified_individuals"
                                <?= ($breach->isNotifiedIndividuals ?? false) ? 'checked' : '' ?>
                                class="mt-1 h-4 w-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                            <label for="is_notified_individuals" class="text-sm text-slate-700">Notification aux
                                personnes concernées ?</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="index.php?page=breach&action=list"
                class="btn btn-outline border-none text-slate-500 hover:text-slate-700">Retour</a>
            <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
                <button type="submit"
                    class="btn btn-primary bg-red-600 hover:bg-red-700 px-8 border-none shadow-lg shadow-red-100">
                    <?= $isEdit ? 'Mettre à jour le dossier' : 'Enregistrer le signalement' ?>
                </button>
            <?php else: ?>
                <span class="text-sm italic text-amber-600 font-medium">Lecture seule</span>
            <?php endif; ?>
        </div>

    </form>
</div>