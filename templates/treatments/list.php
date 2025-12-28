<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Registre des traitements</h1>
        <p class="text-slate-500 mt-1">Gérez vos activités de traitement de données personnelles</p>
    </div>
    <div class="flex gap-3">
        <a href="index.php?page=treatment&action=create" class="btn btn-primary px-5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouveau traitement
        </a>
        <div class="flex border border-slate-200 rounded-md shadow-sm overflow-hidden bg-white">
            <a href="index.php?page=treatment&action=export_csv" class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 border-r border-slate-200" title="Exporter en CSV">
                CSV
            </a>
            <a href="index.php?page=treatment&action=export_pdf" target="_blank" class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" title="Imprimer / PDF">
                PDF
            </a>
        </div>
    </div>
</div>

<div class="card p-6 mb-8">
    <form action="index.php" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <input type="hidden" name="page" value="treatment">
        <input type="hidden" name="action" value="list">

        <div class="md:col-span-2">
            <label for="search" class="form-label text-slate-500">Rechercher</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="search" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                    placeholder="Nom, finalité..." class="form-input pl-10">
            </div>
        </div>

        <div>
            <label for="legal_basis" class="form-label text-slate-500">Base légale</label>
            <select id="legal_basis" name="legal_basis" class="form-input">
                <option value="">-- Toutes --</option>
                <option value="Consentement" <?= ($filters['legal_basis'] ?? '') === 'Consentement' ? 'selected' : '' ?>>Consentement</option>
                <option value="Contrat" <?= ($filters['legal_basis'] ?? '') === 'Contrat' ? 'selected' : '' ?>>Contrat</option>
                <option value="Obligation légale" <?= ($filters['legal_basis'] ?? '') === 'Obligation légale' ? 'selected' : '' ?>>Obligation légale</option>
                <option value="Mission d'intérêt public" <?= ($filters['legal_basis'] ?? '') === "Mission d'intérêt public" ? 'selected' : '' ?>>Mission d'intérêt public</option>
                <option value="Intérêt légitime" <?= ($filters['legal_basis'] ?? '') === 'Intérêt légitime' ? 'selected' : '' ?>>Intérêt légitime</option>
                <option value="Sauvegarde des intérêts vitaux" <?= ($filters['legal_basis'] ?? '') === 'Sauvegarde des intérêts vitaux' ? 'selected' : '' ?>>Sauvegarde des intérêts vitaux</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary flex-1">Filtrer</button>
            <?php if (!empty($filters['search']) || !empty($filters['legal_basis'])): ?>
                <a href="index.php?page=treatment&action=list" class="btn btn-outline" title="Réinitialiser">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <?php if (empty($treatments)): ?>
        <div class="p-12 text-center">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="text-lg font-medium text-slate-900">Aucun traitement</h3>
            <p class="text-slate-500 mt-1">Commencez par ajouter une nouvelle activité de traitement.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nom du traitement</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Finalité principale</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Base Légale</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php foreach ($treatments as $treatment): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-900"><?= htmlspecialchars($treatment->name) ?></span>
                                    <?php if ($treatment->hasSensitiveData && $treatment->isLargeScale): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200" title="AIPD fortement conseillée">
                                            AIPD
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600 line-clamp-1"><?= htmlspecialchars($treatment->purpose) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700 border border-primary-100">
                                    <?= htmlspecialchars($treatment->legalBasis) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="index.php?page=treatment&action=edit&id=<?= $treatment->id ?>" class="text-primary-600 hover:text-primary-900 bg-primary-50 px-3 py-1 rounded-md transition-colors">
                                        Modifier
                                    </a>
                                    <form action="index.php?page=treatment&action=delete" method="POST" class="inline-block"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce traitement ?');">
                                        <input type="hidden" name="id" value="<?= $treatment->id ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md transition-colors">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
