<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Registre des traitements RGPD</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 30px;
            line-height: 1.4;
            color: #333;
        }

        h1 {
            color: #2563eb;
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }

        .meta {
            margin-bottom: 30px;
            text-align: right;
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            word-wrap: break-word;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
            font-size: 0.9em;
        }

        td {
            font-size: 0.85em;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }

            @page {
                margin: 1cm;
            }
        }

        .print-btn-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            border: none;
        }
    </style>
</head>

<body>
    <div class="no-print print-btn-container">
        <button class="btn" onclick="window.print()">Imprimer cette page / Enregistrer en PDF</button>
        <p><small>(Note: Utilisez la fonction "Enregistrer au format PDF" de votre navigateur pour générer le
                fichier)</small></p>
    </div>

    <h1>Registre des activités de traitement</h1>

    <div class="meta">
        Généré le <?= date('d/m/Y') ?> par <?= htmlspecialchars($_SESSION['user_name']) ?><br>
        RGPD Manager
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Nom</th>
                <th style="width: 25%;">Finalité</th>
                <th style="width: 15%;">Base Légale</th>
                <th style="width: 25%;">Catégories de données</th>
                <th style="width: 20%;">Conservation</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($treatments as $treatment): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($treatment->name) ?></strong></td>
                    <td><?= nl2br(htmlspecialchars($treatment->purpose)) ?></td>
                    <td><?= htmlspecialchars($treatment->legalBasis) ?></td>
                    <td><?= htmlspecialchars($treatment->dataCategories) ?></td>
                    <td><?= htmlspecialchars($treatment->retentionPeriod) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <footer style="margin-top: 50px; font-size: 0.8em; color: #666; text-align: center;">
        Document généré conformément aux exigences de l'Article 30 du RGPD.
    </footer>
</body>

</html>