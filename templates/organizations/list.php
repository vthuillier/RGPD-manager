<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Gestion des organismes</h1>
    <a href="index.php?page=organization&action=create" class="btn btn-primary flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Nouvel organisme
    </a>
</div>

<div class="card overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nom de
                    l'organisme</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            <?php foreach ($organizations as $org): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        #<?= $org->id ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                        <?= htmlspecialchars($org->name) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="index.php?page=organization&action=backup&id=<?= $org->id ?>"
                            class="text-green-600 hover:text-green-900 mr-4" title="Télécharger un backup JSON">Backup</a>
                        <a href="index.php?page=organization&action=edit&id=<?= $org->id ?>"
                            class="text-primary-600 hover:text-primary-900 mr-4">Modifier</a>

                        <?php if ($org->id !== (int) $_SESSION['organization_id']): ?>
                            <div class="inline-flex items-center">
                                <button type="button"
                                    onclick="this.nextElementSibling.classList.remove('hidden'); this.classList.add('hidden')"
                                    class="text-red-600 hover:text-red-900">
                                    Supprimer
                                </button>
                                <form action="index.php?page=organization&action=delete" method="POST"
                                    class="hidden inline-flex items-center space-x-2">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="id" value="<?= $org->id ?>">
                                    <button type="submit"
                                        class="bg-red-600 text-white px-3 py-1 rounded-md text-xs font-bold hover:bg-red-700 transition-colors shadow-sm">
                                        Sûr ?
                                    </button>
                                    <button type="button"
                                        onclick="this.parentElement.classList.add('hidden'); this.parentElement.previousElementSibling.classList.remove('hidden')"
                                        class="text-slate-400 hover:text-slate-600 text-xs">
                                        Annuler
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <span class="text-slate-400 italic text-xs">Actif</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>