<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="index.php?page=security_measure&action=list"
            class="text-sm font-medium text-primary-600 hover:text-primary-500 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Retour à la bibliothèque
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900">
            <?= $title ?>
        </h1>
        <p class="text-slate-500 mt-1">Définissez une mesure de sécurité technique ou organisationnelle.</p>
    </div>

    <div class="card p-8">
        <form action="index.php?page=security_measure&action=<?= $measure?->id ? 'update' : 'store' ?>" method="POST"
            class="space-y-6">
            <?php if ($measure?->id): ?>
                <input type="hidden" name="id" value="<?= $measure->id ?>">
            <?php endif; ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div>
                <label for="category" class="form-label">Catégorie</label>
                <select id="category" name="category" required class="form-input">
                    <option value="Technique" <?= ($measure?->category === 'Technique') ? 'selected' : '' ?>>Technique
                    </option>
                    <option value="Organisationnel" <?= ($measure?->category === 'Organisationnel') ? 'selected' : '' ?>>Organisationnel</option>
                    <option value="Physique" <?= ($measure?->category === 'Physique') ? 'selected' : '' ?>>Physique
                    </option>
                    <option value="Contrôle d'accès" <?= ($measure?->category === "Contrôle d'accès") ? 'selected' : '' ?>>Contrôle d'accès</option>
                    <option value="Confidentialité" <?= ($measure?->category === 'Confidentialité') ? 'selected' : '' ?>>Confidentialité</option>
                    <option value="Disponibilité" <?= ($measure?->category === 'Disponibilité') ? 'selected' : '' ?>>Disponibilité</option>
                    <option value="Traçabilité" <?= ($measure?->category === 'Traçabilité') ? 'selected' : '' ?>>Traçabilité</option>
                </select>
            </div>

            <div>
                <label for="name" class="form-label">Nom de la mesure</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($measure?->name ?? '') ?>" required
                    class="form-input" placeholder="Ex: Chiffrement de bout en bout">
            </div>

            <div>
                <label for="description" class="form-label">Description (optionnel)</label>
                <textarea id="description" name="description" rows="3" class="form-input"
                    placeholder="Précisez les modalités d'application..."><?= htmlspecialchars($measure?->description ?? '') ?></textarea>
            </div>

            <div>
                <label for="weight" class="form-label">Poids (Impact sur le score de sécurité)</label>
                <div class="flex items-center gap-4">
                    <input type="range" id="weight" name="weight" min="1" max="5" step="1"
                        value="<?= $measure?->weight ?? 1 ?>" class="flex-1 accent-primary-600">
                    <span id="weight-display"
                        class="w-12 text-center font-bold text-primary-600 bg-primary-50 py-1 rounded-md border border-primary-100">
                        <?= $measure?->weight ?? 1 ?>
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-2">1 = Faible impact, 5 = Mesure critique (MFA, Chiffrement, etc.)
                </p>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="index.php?page=security_measure&action=list"
                    class="text-sm font-medium text-slate-500 hover:text-slate-700">Annuler</a>
                <button type="submit" class="btn btn-primary px-8">
                    <?= $measure?->id ? 'Mettre à jour' : 'Enregistrer la mesure' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const range = document.getElementById('weight');
    const display = document.getElementById('weight-display');
    range.addEventListener('input', () => {
        display.textContent = range.value;
    });
</script>