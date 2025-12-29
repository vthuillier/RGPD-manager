<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Analyses d'Impact (AIPD)</h1>
        <p class="text-slate-500 mt-1">Gérez vos analyses d'impact relatives à la protection des données</p>
    </div>
    <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
        <div class="flex gap-3">
            <a href="index.php?page=aipd&action=create" class="btn btn-primary px-5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle AIPD
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="card overflow-hidden">
    <?php if (empty($aipds)): ?>
        <div class="p-12 text-center">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <h3 class="text-lg font-medium text-slate-900">Aucune analyse d'impact</h3>
            <p class="text-slate-500 mt-1">Commencez par réaliser une AIPD pour vos traitements à haut risque.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Traitement</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Statut
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Haut
                            Risque</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php foreach ($aipds as $aipd): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-slate-900"><?= htmlspecialchars($aipd->treatmentName) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($aipd->isHighRisk): ?>
                                    <span class="text-red-600 flex items-center text-sm font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Oui
                                    </span>
                                <?php else: ?>
                                    <span class="text-green-600 flex items-center text-sm font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Non
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                <?= date('d/m/Y', strtotime($aipd->createdAt)) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="index.php?page=report&action=aipd&id=<?= $aipd->id ?>"
                                        class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md transition-colors"
                                        title="Exporter en PDF">
                                        PDF
                                    </a>
                                    <a href="index.php?page=aipd&action=view&id=<?= $aipd->id ?>"
                                        class="text-slate-600 hover:text-slate-900 bg-slate-100 px-3 py-1 rounded-md transition-colors">
                                        Détails
                                    </a>
                                    <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
                                        <a href="index.php?page=aipd&action=edit&id=<?= $aipd->id ?>"
                                            class="text-primary-600 hover:text-primary-900 bg-primary-50 px-3 py-1 rounded-md transition-colors">
                                            Modifier
                                        </a>
                                        <form action="index.php?page=aipd&action=delete" method="POST" class="inline-block"
                                            onsubmit="return confirm('Supprimer cette analyse d\'impact ?');">
                                            <input type="hidden" name="id" value="<?= $aipd->id ?>">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md transition-colors">
                                                Supprimer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>