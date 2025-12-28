<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Registre des Violations de Données</h1>
        <p class="text-slate-500 mt-1">Documentation obligatoire des incidents de sécurité (Art. 33 et 34 du RGPD)</p>
    </div>
    <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
        <a href="index.php?page=breach&action=create"
            class="btn btn-primary bg-red-600 hover:bg-red-700 flex items-center justify-center gap-2 shadow-lg shadow-red-100 border-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
            </svg>
            Déclarer un incident
        </a>
    <?php endif; ?>

</div>

<div class="card overflow-hidden">
    <?php if (empty($breaches)): ?>
        <div class="p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 text-slate-400 rounded-full mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-slate-900 mb-1">Aucune violation enregistrée</h3>
            <p class="text-slate-500 max-w-sm mx-auto">Heureusement, aucun incident de sécurité n'a été répertorié pour le
                moment.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date
                            découverte</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nature
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Notification Autorité</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Notification Personnes</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php foreach ($breaches as $b): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= date('d/m/Y H:i', strtotime($b->discoveryDate)) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900 truncate max-w-xs">
                                    <?= htmlspecialchars($b->nature) ?>
                                </div>
                                <div class="text-xs text-slate-500"><?= htmlspecialchars($b->dataCategories) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($b->isNotifiedAuthority): ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Fait (<?= date('d/m/Y', strtotime($b->notificationAuthorityDate)) ?>)
                                    </span>
                                <?php else: ?>
                                    <?php
                                    $hoursSince = (time() - strtotime($b->discoveryDate)) / 3600;
                                    $isLate = $hoursSince > 72;
                                    ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $isLate ? 'bg-red-100 text-red-800 animate-pulse' : 'bg-amber-100 text-amber-800' ?>">
                                        <?= $isLate ? 'RETARD (>72h)' : 'À faire' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $b->isNotifiedIndividuals ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800' ?>">
                                    <?= $b->isNotifiedIndividuals ? 'Informées' : 'Non requise / À faire' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="index.php?page=breach&action=edit&id=<?= $b->id ?>"
                                        class="text-primary-600 hover:text-primary-900 p-1 rounded-md hover:bg-primary-50 transition-colors"
                                        title="<?= (($_SESSION['user_role'] ?? '') === 'guest') ? 'Voir les détails' : 'Modifier' ?>">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                    <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
                                        <form action="index.php?page=breach&action=delete" method="POST"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier ?');"
                                            class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="id" value="<?= $b->id ?>">
                                            <button type="submit"
                                                class="text-red-400 hover:text-red-600 p-1 rounded-md hover:bg-red-50 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
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