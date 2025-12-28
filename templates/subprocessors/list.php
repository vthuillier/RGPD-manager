<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Registre des Sous-traitants</h1>
        <p class="text-slate-500 mt-1">Gérez vos sous-traitants et partenaires manipulant des données</p>
    </div>
    <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
        <div class="flex gap-3">
            <a href="index.php?page=subprocessor&action=create" class="btn btn-primary px-5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouveau sous-traitant
            </a>
        </div>
    <?php endif; ?>

</div>

<div class="card overflow-hidden">
    <?php if (empty($subprocessors)): ?>
        <div class="p-12 text-center">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
            <h3 class="text-lg font-medium text-slate-900">Aucun sous-traitant</h3>
            <p class="text-slate-500 mt-1">Commencez par ajouter un nouveau sous-traitant (hébergeur, SaaS, etc.)</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nom
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Service / Rôle</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Localisation</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php foreach ($subprocessors as $sub): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-slate-900"><?= htmlspecialchars($sub->name) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= htmlspecialchars($sub->service) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= htmlspecialchars($sub->location) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="index.php?page=subprocessor&action=edit&id=<?= $sub->id ?>"
                                        class="text-primary-600 hover:text-primary-900 bg-primary-50 px-3 py-1 rounded-md transition-colors">
                                        <?= (($_SESSION['user_role'] ?? '') === 'guest') ? 'Voir' : 'Modifier' ?>
                                    </a>
                                    <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
                                        <form action="index.php?page=subprocessor&action=delete" method="POST" class="inline-block"
                                            onsubmit="return confirm('Supprimer ce sous-traitant ?');">
                                            <input type="hidden" name="id" value="<?= $sub->id ?>">
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