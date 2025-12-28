<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #334155;
            line-height: 1.5;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }

        h1 {
            color: #1e3a8a;
            font-size: 24px;
            margin-bottom: 5px;
        }

        h2 {
            color: #1e40af;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-top: 30px;
        }

        .stats-grid {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .stats-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            text-align: center;
        }

        .stats-val {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }

        .stats-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #f1f5f9;
            text-align: left;
            padding: 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }

        td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }

        .page-break {
            page-break-after: always;
        }

        .alert {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <?php if (!empty($logoBase64)): ?>
            <img src="<?= $logoBase64 ?>" style="height: 50px; margin-bottom: 10px;">
        <?php endif; ?>
        <h1>Rapport Annuel de Conformité RGPD</h1>
        <p>Année <?= $year ?> | Généré le <?= $date ?></p>
    </div>


    <h2>1. Synthèse de l'activité</h2>
    <table class="stats-grid">
        <tr>
            <td class="stats-box">
                <div class="stats-val"><?= $treatments['total'] ?></div>
                <div class="stats-label">Traitements</div>
            </td>
            <td class="stats-box">
                <div class="stats-val"><?= $rights['total'] ?></div>
                <div class="stats-label">Exercices de droits</div>
            </td>
            <td class="stats-box">
                <div class="stats-val"><?= $breaches['total'] ?></div>
                <div class="stats-label">Violations de données</div>
            </td>
        </tr>
    </table>

    <h2>2. État du Registre des Traitements</h2>
    <p>Répartition par base légale :</p>
    <ul>
        <?php foreach ($treatments['by_legal_basis'] as $basis => $count): ?>
            <li><strong><?= $basis ?></strong> : <?= $count ?> traitement(s)</li>
        <?php endforeach; ?>
    </ul>

    <table>
        <thead>
            <tr>
                <th>Nom du traitement</th>
                <th>Finalité</th>
                <th>Données sensibles</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($treatments['list'] as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t->name) ?></td>
                    <td><?= htmlspecialchars($t->purpose) ?></td>
                    <td><?= $t->hasSensitiveData ? 'OUI' : 'NON' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>3. Exercice des Droits</h2>
    <p>Bilan des demandes reçues des personnes concernées :</p>
    <ul>
        <li>Demandes traitées (Terminées) : <?= $rights['completed'] ?></li>
        <li>Demandes en cours (En attente) : <?= $rights['pending'] ?></li>
        <?php if ($rights['urgent'] > 0): ?>
            <li class="alert">ALERTE : <?= $rights['urgent'] ?> demande(s) présentent un retard critique.</li>
        <?php endif; ?>
    </ul>

    <h2>4. Violations de Données & Incidents</h2>
    <?php if (empty($recent_breaches)): ?>
        <p>Aucune violation de données n'a été signalée durant cette période.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date découverte</th>
                    <th>Nature de l'incident</th>
                    <th>Notification CNIL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_breaches as $b): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($b->discoveryDate)) ?></td>
                        <td><?= htmlspecialchars($b->nature) ?></td>
                        <td><?= $b->isNotifiedAuthority ? 'Faite' : 'NON FAITE' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer">
        Document généré par RGPD Manager - <?= $date ?> - Confidentiel
    </div>
</body>

</html>