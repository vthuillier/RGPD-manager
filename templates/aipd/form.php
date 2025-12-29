<?php
$isEdit = isset($aipd);
$action = $isEdit ? 'update' : 'store';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="index.php?page=aipd&action=list"
            class="text-primary-600 hover:text-primary-700 flex items-center text-sm font-medium mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Retour à la liste
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900"><?= $title ?></h1>
        <p class="text-slate-500 mt-1">L'Analyse d'Impact (AIPD) est obligatoire pour les traitements susceptibles
            d'engendrer un risque élevé pour les droits et libertés.</p>
    </div>

    <form action="index.php?page=aipd&action=<?= $action ?>" method="POST" class="space-y-8">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $aipd->id ?>">
        <?php endif; ?>

        <div class="card p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="treatment_id" class="form-label">Traitement concerné</label>
                    <?php if ($isEdit): ?>
                        <div class="p-2 bg-slate-50 border border-slate-200 rounded-md text-slate-700 font-medium">
                            <?= htmlspecialchars($aipd->treatmentName) ?>
                        </div>
                        <input type="hidden" name="treatment_id" value="<?= $aipd->treatmentId ?>">
                    <?php else: ?>
                        <select name="treatment_id" id="treatment_id" required class="form-input">
                            <option value="">Sélectionnez un traitement</option>
                            <?php foreach ($treatments as $t): ?>
                                <option value="<?= $t->id ?>"><?= htmlspecialchars($t->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="status" class="form-label">Statut de l'analyse</label>
                    <select name="status" id="status" class="form-input">
                        <option value="draft" <?= ($isEdit && $aipd->status === 'draft') ? 'selected' : '' ?>>Brouillon
                        </option>
                        <option value="completed" <?= ($isEdit && $aipd->status === 'completed') ? 'selected' : '' ?>>
                            Terminée (En attente de validation)</option>
                        <option value="validated" <?= ($isEdit && $aipd->status === 'validated') ? 'selected' : '' ?>>
                            Validée</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="dpo_id" class="form-label">DPO (Délégué à la Protection des Données)</label>
                    <select name="dpo_id" id="dpo_id" class="form-input">
                        <option value="">Sélectionnez un DPO</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u->id ?>" <?= ($isEdit && $aipd->dpoId === $u->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u->name) ?> (<?= htmlspecialchars($u->email) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="manager_id" class="form-label">Responsable de traitement</label>
                    <select name="manager_id" id="manager_id" class="form-input">
                        <option value="">Sélectionnez un responsable</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u->id ?>" <?= ($isEdit && $aipd->managerId === $u->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u->name) ?> (<?= htmlspecialchars($u->email) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_high_risk" id="is_high_risk"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 rounded" <?= (!$isEdit || $aipd->isHighRisk) ? 'checked' : '' ?>>
                <label for="is_high_risk" class="ml-2 block text-sm text-slate-900 font-medium">
                    Ce traitement présente un risque résiduel élevé
                </label>
            </div>
        </div>

        <div class="card p-6 space-y-6">
            <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">1. Description et nécessité</h2>
            <div>
                <label for="necessity_assessment" class="form-label">Évaluation de la nécessité et de la
                    proportionnalité</label>
                <p class="text-xs text-slate-500 mb-2">Décrivez pourquoi le traitement est nécessaire pour atteindre la
                    finalité prévue.</p>
                <textarea name="necessity_assessment" id="necessity_assessment" rows="5" class="form-input"
                    placeholder="Décrivez la nécessité du traitement..."><?= $isEdit ? htmlspecialchars($aipd->necessityAssessment) : '' ?></textarea>
            </div>
        </div>

        <div class="card p-6 space-y-6">
            <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">2. Gestion des risques</h2>
            <div>
                <label for="risk_assessment" class="form-label">Évaluation des risques pour les droits et
                    libertés</label>
                <p class="text-xs text-slate-500 mb-2">Identifiez les risques potentiels (accès illégitime, modification
                    non désirée, disparition de données) et leur impact.</p>
                <textarea name="risk_assessment" id="risk_assessment" rows="5" class="form-input"
                    placeholder="Détaillez les risques identifiés..."><?= $isEdit ? htmlspecialchars($aipd->riskAssessment) : '' ?></textarea>
            </div>

            <div>
                <label for="measures_planned" class="form-label">Mesures envisagées pour faire face aux risques</label>
                <p class="text-xs text-slate-500 mb-2">Mesures techniques et organisationnelles pour atténuer les
                    risques identifiés.</p>
                <textarea name="measures_planned" id="measures_planned" rows="5" class="form-input"
                    placeholder="Décrivez les mesures de sécurité..."><?= $isEdit ? htmlspecialchars($aipd->measuresPlanned) : '' ?></textarea>
            </div>
        </div>

        <div class="card p-6 space-y-6">
            <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">3. Validation et Avis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="dpo_opinion" class="form-label">Avis du DPO (Délégué à la Protection des
                        Données)</label>
                    <textarea name="dpo_opinion" id="dpo_opinion" rows="4"
                        class="form-input"><?= $isEdit ? htmlspecialchars($aipd->dpoOpinion) : '' ?></textarea>
                </div>
                <div>
                    <label for="manager_decision" class="form-label">Décision du Responsable de traitement</label>
                    <textarea name="manager_decision" id="manager_decision" rows="4"
                        class="form-input"><?= $isEdit ? htmlspecialchars($aipd->managerDecision) : '' ?></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="index.php?page=aipd&action=list" class="btn btn-outline px-8">Annuler</a>
            <button type="submit" class="btn btn-primary px-8">
                <?= $isEdit ? 'Mettre à jour l\'analyse' : 'Enregistrer l\'analyse' ?>
            </button>
        </div>
    </form>
</div>