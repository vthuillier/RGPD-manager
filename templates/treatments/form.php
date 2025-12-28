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

        <div
            style="margin-top: 1.5rem; background: #f8fafc; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid var(--border);">
            <h3 style="margin-top: 0; font-size: 1rem; color: var(--text);">Diagnostic AIPD</h3>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem;">Cochez les cases si le
                traitement répond à l'un de ces critères :</p>

            <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 0.5rem;">
                <input type="checkbox" id="has_sensitive_data" name="has_sensitive_data" value="1"
                    <?= ($treatment->hasSensitiveData ?? false) ? 'checked' : '' ?> style="width: auto;">
                <label for="has_sensitive_data" style="margin-bottom: 0;">Données sensibles (santé, opinions,
                    biométrie...)</label>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center;">
                <input type="checkbox" id="is_large_scale" name="is_large_scale" value="1" <?= ($treatment->isLargeScale ?? false) ? 'checked' : '' ?> style="width: auto;">
                <label for="is_large_scale" style="margin-bottom: 0;">Traitement à grande échelle</label>
            </div>

            <div id="aipd-warning" class="alert alert-error"
                style="margin-top: 1.5rem; display: none; font-size: 0.875rem;">
                ⚠️ <strong>AIPD Fortement conseillée :</strong> Ce traitement présente au moins deux critères de risque
                (données sensibles + grande échelle). Une Analyse d'Impact relative à la Protection des Données devrait
                être réalisée.
            </div>
        </div>

        <script>
            const sensBox = document.getElementById('has_sensitive_data');
            const largeBox = document.getElementById('is_large_scale');
            const warning = document.getElementById('aipd-warning');

            function checkAipd() {
                if (sensBox.checked && largeBox.checked) {
                    warning.style.display = 'block';
                } else {
                    warning.style.display = 'none';
                }
            }

            sensBox.addEventListener('change', checkAipd);
            largeBox.addEventListener('change', checkAipd);
            checkAipd(); // Run on load
        </script>

        <div style="margin-top: 2rem;">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="index.php?page=treatment&action=list"
                style="margin-left: 1rem; color: var(--text-muted);">Annuler</a>
        </div>
    </form>
</div>