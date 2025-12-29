<div class="max-w-4xl mx-auto">
    <div class="mb-8 flex justify-between items-start">
        <div>
            <a href="index.php?page=aipd&action=list"
                class="text-primary-600 hover:text-primary-700 flex items-center text-sm font-medium mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900"><?= $title ?></h1>
            <p class="text-slate-500 mt-1">Analyse d'impact relative au traitement : <span
                    class="font-semibold text-slate-900"><?= htmlspecialchars($aipd->treatmentName) ?></span></p>
        </div>
        <div class="flex gap-3">
            <a href="index.php?page=report&action=aipd&id=<?= $aipd->id ?>"
                class="btn btn-outline flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                    </path>
                </svg>
                Exporter (PDF)
            </a>
            <a href="index.php?page=aipd&action=edit&id=<?= $aipd->id ?>" class="btn btn-outline">
                Modifier
            </a>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Header Info -->
        <div class="card p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Statut</span>
                    <?php
                    $statusClasses = match ($aipd->status) {
                        'draft' => 'bg-slate-100 text-slate-800',
                        'completed' => 'bg-blue-100 text-blue-800',
                        'validated' => 'bg-green-100 text-green-800',
                        default => 'bg-slate-100 text-slate-800'
                    };
                    $statusLabels = [
                        'draft' => 'Brouillon',
                        'completed' => 'Terminée',
                        'validated' => 'Validée'
                    ];
                    ?>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClasses ?>">
                        <?= $statusLabels[$aipd->status] ?? $aipd->status ?>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Risque
                        Résiduel</span>
                    <?php if ($aipd->isHighRisk): ?>
                        <span class="text-red-600 flex items-center text-sm font-bold">Élevé</span>
                    <?php else: ?>
                        <span class="text-green-600 flex items-center text-sm font-bold">Acceptable</span>
                    <?php endif; ?>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Dernière mise
                        à jour</span>
                    <span
                        class="text-sm text-slate-900 font-medium"><?= date('d/m/Y H:i', strtotime($aipd->updatedAt)) ?></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-slate-100">
                <div>
                    <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">DPO
                        Assigné</span>
                    <span
                        class="text-sm text-slate-900 font-bold"><?= $aipd->dpoName ? htmlspecialchars($aipd->dpoName) : '<span class="text-slate-400 font-normal">Non assigné</span>' ?></span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Responsable
                        de traitement</span>
                    <span
                        class="text-sm text-slate-900 font-bold"><?= $aipd->managerName ? htmlspecialchars($aipd->managerName) : '<span class="text-slate-400 font-normal">Non assigné</span>' ?></span>
                </div>
            </div>
        </div>

        <!-- Section 1 -->
        <div class="card p-6">
            <h2 class="text-xl font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">1. Description et nécessité
            </h2>
            <div class="prose prose-slate max-w-none">
                <h3 class="text-sm font-semibold text-slate-600 mb-2">Évaluation de la nécessité et de la
                    proportionnalité</h3>
                <p class="text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-100 italic">
                    <?= !empty($aipd->necessityAssessment) ? nl2br(htmlspecialchars($aipd->necessityAssessment)) : '<span class="text-slate-400">Non renseigné</span>' ?>
                </p>
            </div>
        </div>

        <!-- Section 2 -->
        <div class="card p-6 space-y-6">
            <h2 class="text-xl font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">2. Gestion des risques</h2>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-2">Évaluation des risques identifiés</h3>
                <div class="text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <?= !empty($aipd->riskAssessment) ? nl2br(htmlspecialchars($aipd->riskAssessment)) : '<span class="text-slate-400">Non renseigné</span>' ?>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-2">Mesures d'atténuation</h3>
                <div class="text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <?= !empty($aipd->measuresPlanned) ? nl2br(htmlspecialchars($aipd->measuresPlanned)) : '<span class="text-slate-400">Non renseigné</span>' ?>
                </div>
            </div>
        </div>

        <!-- Section 3 -->
        <div class="card p-6 space-y-6">
            <h2 class="text-xl font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">3. Avis et Décision</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-slate-600 mb-2 uppercase tracking-tighter">Avis du DPO</h3>
                    <div class="text-slate-700 bg-amber-50 p-4 rounded-lg border border-amber-100">
                        <?= !empty($aipd->dpoOpinion) ? nl2br(htmlspecialchars($aipd->dpoOpinion)) : '<span class="text-slate-400">En attente d\'avis...</span>' ?>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-600 mb-2 uppercase tracking-tighter">Décision du
                        Responsable</h3>
                    <div class="text-slate-700 bg-primary-50 p-4 rounded-lg border border-primary-100">
                        <?= !empty($aipd->managerDecision) ? nl2br(htmlspecialchars($aipd->managerDecision)) : '<span class="text-slate-400">Décision en attente...</span>' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>