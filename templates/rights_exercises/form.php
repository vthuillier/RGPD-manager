<?php $isEdit = isset($exercise); ?>

<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="index.php?page=rights&action=list"
            class="inline-flex items-center text-sm text-slate-500 hover:text-primary-600 mb-4 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Retour au registre
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900">
            <?= $isEdit ? 'Modifier le dossier' : 'Nouvelle demande d\'exercice de droits' ?>
        </h1>
        <p class="text-slate-500 mt-1">Conformément aux Chapitre III du RGPD</p>
    </div>

    <form action="index.php?page=rights&action=<?= $isEdit ? 'update' : 'store' ?>" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $exercise->id ?>">
        <?php endif; ?>

        <div class="card p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1">
                    <label for="applicant_name" class="form-label">Nom du demandeur</label>
                    <input type="text" id="applicant_name" name="applicant_name"
                        value="<?= htmlspecialchars($exercise->applicantName ?? '') ?>" required class="form-input"
                        placeholder="Ex: Jean Dupont">
                </div>

                <div class="col-span-1">
                    <label for="request_date" class="form-label">Date de réception</label>
                    <input type="date" id="request_date" name="request_date"
                        value="<?= $exercise->requestDate ?? date('Y-m-d') ?>" required class="form-input">
                </div>

                <div class="col-span-1">
                    <label for="request_type" class="form-label">Type de droit exercé</label>
                    <select id="request_type" name="request_type" required class="form-input">
                        <option value="">-- Sélectionner --</option>
                        <option value="Droit d'accès" <?= ($exercise->requestType ?? '') === "Droit d'accès" ? 'selected' : '' ?>>Droit d'accès</option>
                        <option value="Droit de rectification" <?= ($exercise->requestType ?? '') === "Droit de rectification" ? 'selected' : '' ?>>Droit de rectification</option>
                        <option value="Droit à l'effacement" <?= ($exercise->requestType ?? '') === "Droit à l'effacement" ? 'selected' : '' ?>>Droit à l'effacement (oubli)</option>
                        <option value="Droit à la limitation" <?= ($exercise->requestType ?? '') === "Droit à la limitation" ? 'selected' : '' ?>>Droit à la limitation du traitement</option>
                        <option value="Droit à la portabilité" <?= ($exercise->requestType ?? '') === "Droit à la portabilité" ? 'selected' : '' ?>>Droit à la portabilité</option>
                        <option value="Droit d'opposition" <?= ($exercise->requestType ?? '') === "Droit d'opposition" ? 'selected' : '' ?>>Droit d'opposition</option>
                    </select>
                </div>

                <div class="col-span-1">
                    <label for="status" class="form-label">Statut de la demande</label>
                    <select id="status" name="status" required class="form-input">
                        <option value="En attente" <?= ($exercise->status ?? 'En attente') === 'En attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="Terminé" <?= ($exercise->status ?? '') === 'Terminé' ? 'selected' : '' ?>>Terminé /
                            Répondu</option>
                        <option value="Rejeté" <?= ($exercise->status ?? '') === 'Rejeté' ? 'selected' : '' ?>>Rejeté
                        </option>
                    </select>
                </div>

                <div class="col-span-1">
                    <label for="completion_date" class="form-label">Date de clôture (si fini)</label>
                    <input type="date" id="completion_date" name="completion_date"
                        value="<?= $exercise->completionDate ?? '' ?>" class="form-input">
                </div>
            </div>

            <div class="mt-6">
                <label for="details" class="form-label">Notes et précisions</label>
                <textarea id="details" name="details" rows="4" class="form-input"
                    placeholder="Détails de la demande, justificatifs d'identité reçus, réponse envoyée..."><?= htmlspecialchars($exercise->details ?? '') ?></textarea>
                <p class="mt-1 text-xs text-slate-400 italic">Rappel : Le délai de réponse est de 1 mois maximum
                    (éventuellement prolongeable de 2 mois en cas de complexité).</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="index.php?page=rights&action=list" class="btn btn-outline">Retour</a>
            <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
                <button type="submit" class="btn btn-primary px-8 shadow-lg shadow-primary-200">
                    <?= $isEdit ? 'Mettre à jour' : 'Enregistrer la demande' ?>
                </button>
            <?php else: ?>
                <span class="text-sm italic text-amber-600 font-medium">Lecture seule</span>
            <?php endif; ?>
        </div>

    </form>
</div>