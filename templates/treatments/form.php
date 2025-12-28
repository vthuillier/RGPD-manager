<h1><?= isset($treatment->id) ? 'Modifier le traitement' : 'Nouveau traitement' ?></h1>

<div class="card">
    <form action="index.php?page=treatment&action=<?= isset($treatment->id) ? 'update' : 'store' ?>" method="POST">
        <?php if (isset($treatment->id)): ?>
            <input type="hidden" name="id" value="<?= $treatment->id ?>">
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div>
            <label for="name">Nom du traitement</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($treatment->name ?? '') ?>" required>
        </div>

        <div>
            <label for="purpose">Finalité</label>
            <textarea id="purpose" name="purpose" rows="3"
                required><?= htmlspecialchars($treatment->purpose ?? '') ?></textarea>
        </div>

        <div>
            <label for="legal_basis">Base légale</label>
            <select id="legal_basis" name="legal_basis" required>
                <option value="">-- Choisir une base --</option>
                <option value="Consentement" <?= ($treatment->legalBasis ?? '') === 'Consentement' ? 'selected' : '' ?>>
                    Consentement</option>
                <option value="Contrat" <?= ($treatment->legalBasis ?? '') === 'Contrat' ? 'selected' : '' ?>>Contrat
                </option>
                <option value="Obligation légale" <?= ($treatment->legalBasis ?? '') === 'Obligation légale' ? 'selected' : '' ?>>Obligation légale</option>
                <option value="Mission d'intérêt public" <?= ($treatment->legalBasis ?? '') === "Mission d'intérêt public" ? 'selected' : '' ?>>Mission d'intérêt public</option>
                <option value="Intérêt légitime" <?= ($treatment->legalBasis ?? '') === 'Intérêt légitime' ? 'selected' : '' ?>>Intérêt légitime</option>
                <option value="Sauvegarde des intérêts vitaux" <?= ($treatment->legalBasis ?? '') === 'Sauvegarde des intérêts vitaux' ? 'selected' : '' ?>>Sauvegarde des intérêts vitaux</option>
            </select>
        </div>

        <div>
            <label for="data_categories">Catégories de données</label>
            <input type="text" id="data_categories" name="data_categories"
                placeholder="Ex: État civil, Identité, Données de connexion..."
                value="<?= htmlspecialchars($treatment->dataCategories ?? '') ?>" required>
        </div>

        <div>
            <label for="retention_period">Durée de conservation</label>
            <input type="text" id="retention_period" name="retention_period"
                placeholder="Ex: 5 ans après la fin de la relation..."
                value="<?= htmlspecialchars($treatment->retentionPeriod ?? '') ?>" required>
        </div>

        <div style="margin-top: 2rem;">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="index.php?page=treatment&action=list"
                style="margin-left: 1rem; color: var(--text-muted);">Annuler</a>
        </div>
    </form>
</div>