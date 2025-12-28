<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Journaux d'Audit</h1>
        <p class="text-slate-500 mt-1">Traçabilité des actions effectuées sur la plateforme (Conformité RGPD)</p>
    </div>
</div>

<div class="card overflow-hidden">
    <?php if (empty($logs)): ?>
        <div class="p-12 text-center text-slate-500">
            Aucun journal d'audit disponible pour le moment.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date &
                            Heure</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Utilisateur</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Action
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Entité
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Détails</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">IP
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= date('d/m/Y H:i:s', strtotime($log->createdAt)) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm font-medium text-slate-900"><?= htmlspecialchars($log->userName ?? 'Système') ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $badgeClass = 'bg-slate-100 text-slate-800';
                                if (strpos($log->action, 'CREATE') !== false)
                                    $badgeClass = 'bg-green-100 text-green-800';
                                if (strpos($log->action, 'UPDATE') !== false)
                                    $badgeClass = 'bg-blue-100 text-blue-800';
                                if (strpos($log->action, 'DELETE') !== false)
                                    $badgeClass = 'bg-red-100 text-red-800';
                                if (strpos($log->action, 'LOGIN') !== false)
                                    $badgeClass = 'bg-indigo-100 text-indigo-800';
                                ?>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badgeClass ?>">
                                    <?= htmlspecialchars($log->action) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= htmlspecialchars(ucfirst($log->entityType ?? '-')) ?>
                                <?php if ($log->entityId): ?>
                                    <span class="text-xs text-slate-400">(#<?= $log->entityId ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">
                                <?= htmlspecialchars($log->details ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                                <?= htmlspecialchars($log->ipAddress ?? '-') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>