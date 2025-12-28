<h1>Registre des traitements</h1>

<div style="margin-bottom: 1.5rem;">
    <a href="index.php?page=treatment&action=create" class="btn">Nouveau traitement</a>
</div>

<div class="card">
    <?php if (empty($treatments)): ?>
        <p>Aucun traitement enregistré pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Finalité</th>
                    <th>Base Légale</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($treatments as $treatment): ?>
                    <tr>
                        <td><?= htmlspecialchars($treatment->name) ?></td>
                        <td><?= htmlspecialchars($treatment->purpose) ?></td>
                        <td><?= htmlspecialchars($treatment->legalBasis) ?></td>
                        <td>
                            <a href="index.php?page=treatment&action=edit&id=<?= $treatment->id ?>" class="btn">Modifier</a>
                            <form action="index.php?page=treatment&action=delete" method="POST" style="display:inline-block;"
                                onsubmit="return confirm('Confirmer la suppression ?');">
                                <input type="hidden" name="id" value="<?= $treatment->id ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>