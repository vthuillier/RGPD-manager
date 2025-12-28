<h1>Tableau de bord de conformité</h1>

<div
    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Carte Total -->
    <div class="card"
        style="text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2rem;">
        <span style="font-size: 0.875rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Total
            Traitements</span>
        <span style="font-size: 3rem; font-weight: 800; color: var(--primary);"><?= $stats['total'] ?></span>
    </div>

    <!-- Alerte AIPD -->
    <div class="card" style="border-left: 4px solid var(--error); padding: 1.5rem;">
        <h3 style="margin-top: 0; color: var(--error); font-size: 1rem;">⚠️ Alertes AIPD</h3>
        <?php
        $aipdNeeded = array_filter($stats['treatments'] ?? [], fn($t) => $t->hasSensitiveData && $t->isLargeScale);
        ?>
        <?php if (empty($aipdNeeded)): ?>
            <p style="font-size: 0.875rem; color: var(--success); margin: 1rem 0;">Aucun traitement à risque élevé détecté.
            </p>
        <?php else: ?>
            <p style="font-size: 0.875rem; color: var(--error); font-weight: 600; margin: 0.5rem 0;">
                <?= count($aipdNeeded) ?> traitement(s) nécessitant une AIPD :</p>
            <ul style="font-size: 0.8125rem; padding-left: 1.25rem; margin-top: 0;">
                <?php foreach ($aipdNeeded as $t): ?>
                    <li><?= htmlspecialchars($t->name) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Carte Quick Action -->
    <div class="card"
        style="display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2rem; background: linear-gradient(135deg, var(--primary), #4f46e5); color: white;">
        <span style="font-weight: 600; margin-bottom: 1rem;">Action Rapide</span>
        <a href="index.php?page=treatment&action=create" class="btn"
            style="background: white; color: var(--primary); font-weight: 700;">+ Nouveau Traitement</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
    <!-- Répartition par Base Légale -->
    <div class="card">
        <h3
            style="margin-top: 0; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
            Répartition par Base Légale</h3>
        <?php if (empty($stats['legal_basis'])): ?>
            <p style="color: var(--text-muted);">Aucune donnée à afficher.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($stats['legal_basis'] as $row): ?>
                    <?php
                    $percentage = ($stats['total'] > 0) ? ($row['count'] / $stats['total']) * 100 : 0;
                    ?>
                    <div>
                        <div
                            style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.25rem;">
                            <span><?= htmlspecialchars($row['legal_basis']) ?></span>
                            <span style="font-weight: 600;"><?= $row['count'] ?> (<?= round($percentage) ?>%)</span>
                        </div>
                        <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                            <div
                                style="width: <?= $percentage ?>%; height: 100%; background: var(--primary); transition: width 0.5s;">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tips DPO -->
    <div class="card" style="border-left: 4px solid var(--primary);">
        <h3
            style="margin-top: 0; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
            💡 Conseils DPO</h3>
        <ul
            style="padding-left: 1.25rem; font-size: 0.935rem; color: var(--text); display: flex; flex-direction: column; gap: 0.75rem;">
            <li><strong>Obligation de tenir un registre :</strong> L'Article 30 du RGPD impose à la plupart des
                entreprises de tenir ce registre à jour.</li>
            <li><strong>Base légale :</strong> Assurez-vous que chaque traitement a une base légale valide. Le
                "Consentement" est souvent plus difficile à gérer que le "Contrat" ou l' "Intérêt légitime".</li>
            <li><strong>Minimisation :</strong> Ne collectez que les données strictement nécessaires (Article 5.1.c).
            </li>
            <li><strong>Conservation :</strong> Vérifiez régulièrement vos délais. Une donnée ne doit pas être conservée
                "éternellement".</li>
        </ul>
    </div>
</div>