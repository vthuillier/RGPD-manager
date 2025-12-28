<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Tableau de bord de conformité</h1>
        <p class="text-slate-500 mt-1">Vue d'ensemble de votre état de conformité RGPD</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="index.php?page=report&action=annual" class="btn btn-outline flex items-center justify-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                </path>
            </svg>
            Rapport Annuel (PDF)
        </a>
    </div>
</div>


<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Carte Total -->
    <div class="card p-6 flex flex-col items-center justify-center text-center">
        <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                </path>
            </svg>
        </div>
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Traitements</span>
        <span class="text-4xl font-black text-primary-600 mt-1"><?= $stats['total'] ?></span>
    </div>

    <!-- Carte Droits Total -->
    <div class="card p-6 flex flex-col items-center justify-center text-center">
        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Exercices de droits</span>
        <span class="text-4xl font-black text-indigo-600 mt-1"><?= $stats['rights']['total'] ?></span>
    </div>

    <!-- Alertes Droits Urgents -->
    <div
        class="card p-6 border-l-4 <?= $stats['rights']['urgent'] > 0 ? 'border-red-500 bg-red-50' : 'border-indigo-500' ?>">
        <div class="flex items-center gap-2 mb-3">
            <div
                class="p-2 <?= $stats['rights']['urgent'] > 0 ? 'bg-red-100 text-red-600' : 'bg-indigo-100 text-indigo-600' ?> rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800">Urgences Droits</h3>
        </div>
        <?php if ($stats['rights']['urgent'] > 0): ?>
            <p class="text-2xl font-black text-red-600"><?= $stats['rights']['urgent'] ?></p>
            <p class="text-xs text-red-700 font-medium">Réponse(s) en retard !</p>
        <?php else: ?>
            <p class="text-2xl font-black text-slate-400">0</p>
            <p class="text-xs text-slate-500">Délais respectés.</p>
        <?php endif; ?>
    </div>

    <!-- Alertes Violations (72h) -->
    <div
        class="card p-6 border-l-4 <?= $stats['breaches']['urgent'] > 0 ? 'border-red-600 bg-red-50' : 'border-slate-300' ?>">
        <div class="flex items-center gap-2 mb-3">
            <div
                class="p-2 <?= $stats['breaches']['urgent'] > 0 ? 'bg-red-600 text-white' : 'bg-slate-200 text-slate-600' ?> rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800">Violations (-72h)</h3>
        </div>
        <?php if ($stats['breaches']['urgent'] > 0): ?>
            <p class="text-2xl font-black text-red-600 animate-pulse"><?= $stats['breaches']['urgent'] ?></p>
            <p class="text-xs text-red-700 font-bold">ALERTE : Notification CNIL hors délai !</p>
        <?php else: ?>
            <p class="text-2xl font-black text-slate-400"><?= $stats['breaches']['total'] ?></p>
            <p class="text-xs text-slate-500">Incident(s) répertorié(s).</p>
        <?php endif; ?>
    </div>


    <!-- Alerte AIPD -->

    <div class="card p-6 border-l-4 border-amber-500">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 bg-amber-100 text-amber-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800">Alertes AIPD</h3>
        </div>
        <?php
        $aipdNeeded = array_filter($stats['treatments'] ?? [], fn($t) => $t->hasSensitiveData && $t->isLargeScale);
        ?>
        <?php if (empty($aipdNeeded)): ?>
            <p class="text-sm text-green-600 font-medium">Aucun risque élevé détecté.</p>
        <?php else: ?>
            <p class="text-sm text-amber-700 font-bold mb-2">
                <?= count($aipdNeeded) ?> action(s) requise(s) :
            </p>
            <ul class="text-xs space-y-1 text-slate-600">
                <?php foreach (array_slice($aipdNeeded, 0, 3) as $t): ?>
                    <li class="flex items-center gap-1">
                        <span class="w-1 h-1 bg-amber-400 rounded-full"></span>
                        <?= htmlspecialchars($t->name) ?>
                    </li>
                <?php endforeach; ?>
                <?php if (count($aipdNeeded) > 3): ?>
                    <li class="text-slate-400 italic">+ <?= count($aipdNeeded) - 3 ?> autres...</li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Alerte Rétention -->
    <div class="card p-6 border-l-4 border-rose-500">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 bg-rose-100 text-rose-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800">Purge & Rétention</h3>
        </div>
        <?php
        $today = new DateTime();
        $expiringSoon = array_filter($stats['treatments'] ?? [], function ($t) use ($today) {
            $createdAt = new DateTime($t->createdAt);
            $expiryDate = $createdAt->modify('+' . $t->retentionYears . ' years');
            $diff = $today->diff($expiryDate);
            return $expiryDate <= $today || ($diff->invert === 0 && $diff->days < 60);
        });
        ?>
        <?php if (empty($expiringSoon)): ?>
            <p class="text-sm text-green-600 font-medium">Aucune purge à prévoir.</p>
        <?php else: ?>
            <p class="text-sm text-rose-700 font-bold mb-2"><?= count($expiringSoon) ?> à réviser :</p>
            <ul class="text-xs space-y-1 text-slate-600">
                <?php foreach (array_slice($expiringSoon, 0, 3) as $t): ?>
                    <?php
                    $expiry = (new DateTime($t->createdAt))->modify('+' . $t->retentionYears . ' years');
                    $isExpired = $expiry <= $today;
                    ?>
                    <li class="flex items-center justify-between gap-2">
                        <span class="truncate"><?= htmlspecialchars($t->name) ?></span>
                        <span class="<?= $isExpired ? 'text-rose-600 font-bold' : 'text-amber-600' ?>">
                            <?= $isExpired ? 'EXP' : 'J-' . $today->diff($expiry)->days ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Carte Quick Action -->
    <div
        class="card p-6 bg-gradient-to-br from-primary-600 to-indigo-700 text-white flex flex-col justify-between border-none">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-primary-100">Action Rapide</span>
            <h3 class="text-lg font-bold mt-1">Nouveau registre</h3>
        </div>
        <a href="index.php?page=treatment&action=create"
            class="mt-4 bg-white text-primary-700 hover:bg-primary-50 px-4 py-2 rounded-lg text-sm font-bold transition-all text-center">
            + Ajouter un traitement
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Répartition par Base Légale -->
    <div class="card p-8">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Bases Légales</h3>
            <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-2 py-1 rounded">Répartition</span>
        </div>

        <?php if (empty($stats['legal_basis'])): ?>
            <div class="py-12 text-center text-slate-400 italic">Aucune donnée disponible.</div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($stats['legal_basis'] as $row): ?>
                    <?php
                    $percentage = ($stats['total'] > 0) ? ($row['count'] / $stats['total']) * 100 : 0;
                    ?>
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($row['legal_basis']) ?></span>
                            <span class="text-sm font-bold text-primary-600"><?= $row['count'] ?> <span
                                    class="text-slate-400 font-normal ml-1">(<?= round($percentage) ?>%)</span></span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-primary-500 rounded-full transition-all duration-1000"
                                style="width: <?= $percentage ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tips DPO -->
    <div class="card p-8 border-l-4 border-primary-500">
        <h3 class="text-lg font-bold text-slate-800 mb-6 pb-4 border-b border-slate-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Conseils DPO
        </h3>
        <ul class="space-y-4">
            <li class="flex gap-3">
                <div
                    class="flex-shrink-0 w-5 h-5 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center text-xs font-bold">
                    1</div>
                <p class="text-sm text-slate-600"><strong>Obligation Article 30 :</strong> Le registre doit être tenu à
                    jour et disponible en cas de contrôle de la CNIL.</p>
            </li>
            <li class="flex gap-3">
                <div
                    class="flex-shrink-0 w-5 h-5 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center text-xs font-bold">
                    2</div>
                <p class="text-sm text-slate-600"><strong>Base légale :</strong> Vérifiez que "Consentement" est
                    réellement libre. Souvent, "Intérêt légitime" ou "Contrat" sont plus adaptés.</p>
            </li>
            <li class="flex gap-3">
                <div
                    class="flex-shrink-0 w-5 h-5 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center text-xs font-bold">
                    3</div>
                <p class="text-sm text-slate-600"><strong>Durée de conservation :</strong> Ne conservez pas les données
                    "au cas où". Fixez des règles claires de purge (Article 5.1.e).</p>
            </li>
            <li class="flex gap-3">
                <div
                    class="flex-shrink-0 w-5 h-5 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center text-xs font-bold">
                    4</div>
                <p class="text-sm text-slate-600"><strong>AIPD :</strong> Si un traitement est susceptible d'engendrer
                    un risque élevé (données sensibles à grande échelle), l'AIPD est obligatoire.</p>
            </li>
        </ul>
    </div>
</div>