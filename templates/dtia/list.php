<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Transferts Hors-UE (DTIA)</h1>
        <p class="text-slate-500 mt-1">Analyse d'impact des transferts de données internationaux (Schrems II).</p>
    </div>
    <a href="index.php?page=dtia&action=create" class="btn btn-primary flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Nouvelle DTIA
    </a>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pays /
                        Mécanisme</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Acteurs (Exp/Imp)</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Risque
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Statut
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php if (empty($dtias)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 italic">
                            Aucun transfert international documenté pour le moment.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($dtias as $dtia): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-900">
                                    <?= htmlspecialchars($dtia->countryName) ?>
                                </div>
                                <div class="text-xs text-slate-500">
                                    <?php
                                    $mechs = [
                                        'adequacy' => 'Décision d\'adéquation',
                                        'scc' => 'Clauses Contractuelles Types (SCC)',
                                        'bcr' => 'Règles d\'entreprise contraignantes (BCR)',
                                        'derogation' => 'Dérogation (Art. 49)'
                                    ];
                                    echo $mechs[$dtia->transferMechanism] ?? $dtia->transferMechanism;
                                    ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-slate-600"><span class="font-medium">De:</span>
                                    <?= htmlspecialchars($dtia->dataExporter) ?>
                                </div>
                                <div class="text-xs text-slate-600"><span class="font-medium">À:</span>
                                    <?= htmlspecialchars($dtia->dataImporter) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $riskColors = [
                                    'low' => 'bg-green-100 text-green-700',
                                    'medium' => 'bg-blue-100 text-blue-700',
                                    'high' => 'bg-orange-100 text-orange-700',
                                    'critical' => 'bg-red-100 text-red-700'
                                ];
                                $riskLabels = [
                                    'low' => 'Faible',
                                    'medium' => 'Modéré',
                                    'high' => 'Élevé',
                                    'critical' => 'Critique'
                                ];
                                ?>
                                <span
                                    class="px-2 py-1 text-[10px] font-bold rounded-full <?= $riskColors[$dtia->riskLevel] ?? 'bg-slate-100 text-slate-700' ?>">
                                    <?= $riskLabels[$dtia->riskLevel] ?? $dtia->riskLevel ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                <?= date('d/m/Y', strtotime($dtia->assessmentDate)) ?>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs font-medium <?= $dtia->status === 'completed' ? 'text-green-600' : 'text-slate-400 italic' ?>">
                                    <?= $dtia->status === 'completed' ? 'Validé' : 'Brouillon' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <a href="index.php?page=dtia&action=edit&id=<?= $dtia->id ?>"
                                        class="text-primary-600 hover:text-primary-900">Modifier</a>
                                    <a href="index.php?page=dtia&action=delete&id=<?= $dtia->id ?>"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Supprimer cette DTIA ?')">Supprimer</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>