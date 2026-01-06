<?php
/** @var \App\Entity\MaturityAssessment|null $latest */
/** @var \App\Entity\MaturityAssessment|null $previous */
/** @var \App\Entity\MaturityAssessment[] $history */
/** @var array $recommendations */
?>

<div class="flex flex-col gap-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Auto-évaluation de Maturité</h1>
            <p class="text-slate-500">Évaluez la conformité RGPD de votre organisation sur 5 piliers clés.</p>
        </div>
        <a href="index.php?page=maturity&action=assessment" class="btn btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvelle évaluation
        </a>
    </div>

    <?php if ($latest): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Graphique Radar -->
            <div class="card p-6 flex flex-col items-center justify-center">
                <div class="flex items-center justify-between w-full mb-6">
                    <h2 class="text-lg font-semibold">Profil de maturité</h2>
                    <?php if ($previous): ?>
                        <span class="text-xs font-medium text-slate-400">Comparaison avec l'évaluation précédente</span>
                    <?php endif; ?>
                </div>
                <div class="w-full max-w-md">
                    <canvas id="maturityRadarChart"></canvas>
                </div>
            </div>

            <!-- Détails et recommandations -->
            <div class="flex flex-col gap-6">
                <!-- Recommendations -->
                <?php if (!empty($recommendations)): ?>
                    <div class="card border-l-4 border-amber-500 bg-amber-50/30">
                        <div class="p-6">
                            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Priorités d'action
                            </h2>
                            <div class="space-y-4">
                                <?php foreach ($recommendations as $reco): ?>
                                    <div class="flex gap-3 bg-white p-3 rounded-lg border border-amber-100 shadow-sm">
                                        <div class="flex-shrink-0 w-8 h-8 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center text-xs font-bold">!</div>
                                        <div>
                                            <p class="text-xs font-bold text-amber-800 uppercase tracking-wider"><?= $reco['label'] ?></p>
                                            <p class="text-sm text-slate-600"><?= $reco['text'] ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-l-4 border-green-500 bg-green-50/30 p-6">
                        <h2 class="text-lg font-bold text-green-800 mb-1 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Félicitations !
                        </h2>
                        <p class="text-sm text-green-700">Votre maturité globale est excellente. Continuez vos efforts pour maintenir ce niveau de conformité.</p>
                    </div>
                <?php endif; ?>

                <div class="card p-6">
                    <h2 class="text-lg font-semibold mb-4">Scores par pilier</h2>
                    <div class="space-y-4">
                        <?php
                        $pillars = [
                            ['label' => 'Gouvernance', 'score' => $latest->governanceScore, 'color' => 'blue'],
                            ['label' => 'Registre', 'score' => $latest->registryScore, 'color' => 'indigo'],
                            ['label' => 'Droits & Processus', 'score' => $latest->rightsScore, 'color' => 'purple'],
                            ['label' => 'Sécurité', 'score' => $latest->securityScore, 'color' => 'emerald'],
                            ['label' => 'Risques & Tiers', 'score' => $latest->riskScore, 'color' => 'rose'],
                        ];
                        foreach ($pillars as $p):
                            $percent = ($p['score'] / 5) * 100;
                        ?>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium"><?= $p['label'] ?></span>
                                    <span class="text-slate-500"><?= number_format($p['score'], 1) ?> / 5</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-<?= $p['color'] ?>-500 h-2 rounded-full" style="width: <?= $percent ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commentaires -->
        <?php if ($latest->comments): ?>
            <div class="card p-6">
                <h2 class="text-lg font-semibold mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Observations du DPO
                </h2>
                <p class="text-sm text-slate-600 italic">"<?= nl2br(htmlspecialchars($latest->comments)) ?>"</p>
            </div>
        <?php endif; ?>

        <!-- Historique -->
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-base font-semibold">Historique des évaluations</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Moyenne Globale</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Commentaire</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php foreach ($history as $h): 
                            $avg = ($h->governanceScore + $h->registryScore + $h->rightsScore + $h->securityScore + $h->riskScore) / 5;    
                        ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                    <?= date('d/m/Y H:i', strtotime($h->createdAt)) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full <?= $avg >= 4 ? 'bg-green-100 text-green-700' : ($avg >= 2.5 ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700') ?>">
                                        <?= number_format($avg, 1) ?> / 5
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 truncate max-w-xs">
                                    <?= htmlspecialchars($h->comments ?? '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('maturityRadarChart').getContext('2d');
                
                const datasets = [{
                    label: 'Maturité actuelle (<?= date('d/m/Y', strtotime($latest->createdAt)) ?>)',
                    data: [
                        <?= $latest->governanceScore ?>,
                        <?= $latest->registryScore ?>,
                        <?= $latest->rightsScore ?>,
                        <?= $latest->securityScore ?>,
                        <?= $latest->riskScore ?>
                    ],
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgb(59, 130, 246)',
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(59, 130, 246)',
                    z: 2
                }];

                <?php if ($previous): ?>
                datasets.push({
                    label: 'Évaluation précédente (<?= date('d/m/Y', strtotime($previous->createdAt)) ?>)',
                    data: [
                        <?= $previous->governanceScore ?>,
                        <?= $previous->registryScore ?>,
                        <?= $previous->rightsScore ?>,
                        <?= $previous->securityScore ?>,
                        <?= $previous->riskScore ?>
                    ],
                    backgroundColor: 'rgba(203, 213, 225, 0.2)',
                    borderColor: 'rgb(148, 163, 184)',
                    borderDash: [5, 5],
                    pointBackgroundColor: 'rgb(148, 163, 184)',
                    pointBorderColor: '#fff',
                    z: 1
                });
                <?php endif; ?>

                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: ['Gouvernance', 'Registre', 'Droits', 'Sécurité', 'Risques'],
                        datasets: datasets
                    },
                    options: {
                        scales: {
                            r: {
                                beginAtZero: true,
                                min: 0,
                                max: 5,
                                ticks: {
                                    stepSize: 1,
                                    backdropColor: 'transparent'
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 20,
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                padding: 12,
                                cornerRadius: 8
                            }
                        }
                    }
                });
            });
        </script>

    <?php else: ?>
        <div class="card p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-50 text-primary-600 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold mb-2">Aucune auto-évaluation</h2>
            <p class="text-slate-500 mb-6 max-w-sm mx-auto">Lancez votre première évaluation pour mesurer le niveau de
                conformité de votre organisme.</p>
            <a href="index.php?page=maturity&action=assessment" class="btn btn-primary">Commencer maintenant</a>
        </div>
    <?php endif; ?>
</div>