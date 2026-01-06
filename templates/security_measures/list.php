<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Mesures de Sécurité Personnalisées</h1>
        <p class="text-slate-500 mt-1">Gérez vos propres mesures techniques et organisationnelles (TOMs) et leurs poids.
        </p>
    </div>
    <div class="flex gap-3">
        <a href="index.php?page=security_measure&action=create" class="btn btn-primary px-5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouvelle mesure
        </a>
    </div>
</div>

<div class="card overflow-hidden">
    <?php if (empty($measures)): ?>
        <div class="p-12 text-center text-slate-500">
            <p>Vous n'avez pas encore ajouté de mesures de sécurité personnalisées.</p>
            <p class="text-sm mt-2">Les mesures par défaut du système resteront disponibles dans vos traitements.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Catégorie</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nom
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Poids</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php foreach ($measures as $measure): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                <?= htmlspecialchars($measure->category) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900">
                                    <?= htmlspecialchars($measure->name) ?>
                                </div>
                                <?php if ($measure->description): ?>
                                    <div class="text-xs text-slate-500">
                                        <?= htmlspecialchars($measure->description) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    Impact:
                                    <?= $measure->weight ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="index.php?page=security_measure&action=edit&id=<?= $measure->id ?>"
                                        class="text-primary-600 hover:text-primary-900 bg-primary-50 px-3 py-1 rounded-md transition-colors">
                                        Modifier
                                    </a>
                                    <form action="index.php?page=security_measure&action=delete" method="POST"
                                        class="inline-block" onsubmit="return confirm('Supprimer cette mesure ?');">
                                        <input type="hidden" name="id" value="<?= $measure->id ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md transition-colors">
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