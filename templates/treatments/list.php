<h1>Registre des traitements</h1>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <a href="index.php?page=treatment&action=create" class="btn">Nouveau traitement</a>
    <div>
        <a href="index.php?page=treatment&action=export_csv" class="btn btn-outline"
            style="margin-right: 0.5rem;">Exporter en CSV</a>
        <a href="index.php?page=treatment&action=export_pdf" target="_blank" class="btn btn-outline">Imprimer / PDF</a>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
    <form action="index.php" method="GET"
        style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
        <input type="hidden" name="page" value="treatment">
        <input type="hidden" name="action" value="list">

        <div>
            <label for="search" style="font-size: 0.8125rem; color: var(--text-muted);">Rechercher (Nom,
                finalité...)</label>
            <input type="text" id="search" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                placeholder="Ex: RH, Clients...">
        </div>

        <div>
            <label for="legal_basis" style="font-size: 0.8125rem; color: var(--text-muted);">Base légale</label>
            <select id="legal_basis" name="legal_basis">
                <option value="">-- Toutes les bases --</option>
                <option value="Consentement" <?= ($filters['legal_basis'] ?? '') === 'Consentement' ? 'selected' : '' ?>>
                    Consentement</option>
                <option value="Contrat" <?= ($filters['legal_basis'] ?? '') === 'Contrat' ? 'selected' : '' ?>>Contrat
                </option>
                <option value="Obligation légale" <?= ($filters['legal_basis'] ?? '') === 'Obligation légale' ? 'selected' : '' ?>>Obligation légale</option>
                <option value="Mission d'intérêt public" <?= ($filters['legal_basis'] ?? '') === "Mission d'intérêt public" ? 'selected' : '' ?>>Mission d'intérêt public</option>
                <option value="Intérêt légitime" <?= ($filters['legal_basis'] ?? '') === 'Intérêt légitime' ? 'selected' : '' ?>>Intérêt légitime</option>
                <option value="Sauvegarde des intérêts vitaux" <?= ($filters['legal_basis'] ?? '') === 'Sauvegarde des intérêts vitaux' ? 'selected' : '' ?>>Sauvegarde des intérêts vitaux</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn">Filtrer</button>
            <?php if (!empty($filters['search']) || !empty($filters['legal_basis'])): ?>
                <a href="index.php?page=treatment&action=list" class="btn btn-outline">Réinitialiser</a>
            <?php endif; ?>
        </div>
    </form>
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
                        <td>
                            <?= htmlspecialchars($treatment->name) ?>
                            <?php if ($treatment->hasSensitiveData && $treatment->isLargeScale): ?>
                                <span
                                    style="background: var(--error); color: white; border-radius: 4px; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; margin-left: 5px;"
                                    title="AIPD fortement conseillée">AIPD</span>
                            <?php endif; ?>
                        </td>
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