<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #334155;
            line-height: 1.5;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }

        h1 {
            color: #1e3a8a;
            font-size: 20px;
            margin-bottom: 5px;
        }

        h2 {
            color: #1e40af;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-top: 20px;
            font-size: 14px;
        }

        .section {
            margin-bottom: 20px;
        }

        .field {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
            display: block;
        }

        .content {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 4px;
            margin-top: 4px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-draft {
            background: #f1f5f9;
            color: #475569;
        }

        .badge-completed {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-validated {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-risk {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-safe {
            background: #dcfce7;
            color: #15803d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="header">
        <?php if (!empty($logoBase64)): ?>
            <img src="<?= $logoBase64 ?>" style="height: 40px; margin-bottom: 5px;">
        <?php endif; ?>
        <h1>Analyse d'Impact relative à la Protection des Données (AIPD)</h1>
        <p>Traitement : <strong><?= htmlspecialchars($aipd->treatmentName) ?></strong></p>
    </div>

    <div class="section">
        <table>
            <tr>
                <td>
                    <span class="label">Statut</span>
                    <?php
                    $statusLabels = ['draft' => 'Brouillon', 'completed' => 'Terminée', 'validated' => 'Validée'];
                    ?>
                    <span class="badge badge-<?= $aipd->status ?>">
                        <?= $statusLabels[$aipd->status] ?? $aipd->status ?>
                    </span>
                </td>
                <td>
                    <span class="label">Risque Résiduel</span>
                    <?php if ($aipd->isHighRisk): ?>
                        <span class="badge badge-risk">ÉLEVÉ</span>
                    <?php else: ?>
                        <span class="badge badge-safe">ACCEPTABLE</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="label">Dernière mise à jour</span>
                    <?= date('d/m/Y', strtotime($aipd->updatedAt)) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">DPO Assigné</span>
                    <?= $aipd->dpoName ? htmlspecialchars($aipd->dpoName) : 'Non assigné' ?>
                </td>
                <td colspan="2">
                    <span class="label">Responsable de traitement</span>
                    <?= $aipd->managerName ? htmlspecialchars($aipd->managerName) : 'Non assigné' ?>
                </td>
            </tr>
        </table>
    </div>

    <h2>1. Description et nécessité</h2>
    <div class="section">
        <span class="label">Évaluation de la nécessité et de la proportionnalité</span>
        <div class="content">
            <?= nl2br(htmlspecialchars($aipd->necessityAssessment ?? 'Non renseigné')) ?>
        </div>
    </div>

    <h2>2. Gestion des risques</h2>
    <div class="section">
        <span class="label">Évaluation des risques pour les droits et libertés</span>
        <div class="content">
            <?= nl2br(htmlspecialchars($aipd->riskAssessment ?? 'Non renseigné')) ?>
        </div>
    </div>
    <div class="section">
        <span class="label">Mesures envisagées (techniques et organisationnelles)</span>
        <div class="content">
            <?= nl2br(htmlspecialchars($aipd->measuresPlanned ?? 'Non renseigné')) ?>
        </div>
    </div>

    <h2>3. Avis et Décision</h2>
    <div class="section">
        <table>
            <tr>
                <td style="width: 50%;">
                    <span class="label">Avis du DPO</span>
                    <div style="font-style: italic; margin-top: 5px;">
                        <?= nl2br(htmlspecialchars($aipd->dpoOpinion ?? 'En attente...')) ?>
                    </div>
                </td>
                <td style="width: 50%;">
                    <span class="label">Décision du Responsable</span>
                    <div style="font-style: italic; margin-top: 5px;">
                        <?= nl2br(htmlspecialchars($aipd->managerDecision ?? 'En attente...')) ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Document généré le <?= $date ?> - RGPD Manager - Confidentiel
    </div>
</body>

</html>