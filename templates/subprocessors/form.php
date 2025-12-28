<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">
            <?= isset($subprocessor) ? 'Modifier le sous-traitant' : 'Nouveau sous-traitant' ?>
        </h1>
        <p class="text-slate-500 mt-1">Saisissez les informations relatives à votre sous-traitant.</p>
    </div>

    <div class="card p-8">
        <form action="index.php?page=subprocessor&action=<?= isset($subprocessor) ? 'update' : 'store' ?>" method="POST"
            class="space-y-6">
            <?php if (isset($subprocessor)): ?>
                <input type="hidden" name="id" value="<?= $subprocessor->id ?>">
            <?php endif; ?>

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div>
                <label for="name" class="form-label">Nom de l'entreprise</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($subprocessor->name ?? '') ?>"
                    required class="form-input" placeholder="Ex: AWS, Google Cloud, SendGrid...">
            </div>

            <div>
                <label for="service" class="form-label">Service fourni / Rôle</label>
                <input type="text" id="service" name="service"
                    value="<?= htmlspecialchars($subprocessor->service ?? '') ?>" required class="form-input"
                    placeholder="Ex: Hébergement, Emailing, Support technique...">
            </div>

            <div>
                <label for="location" class="form-label">Localisation des données (Pays)</label>
                <input type="text" id="location" name="location"
                    value="<?= htmlspecialchars($subprocessor->location ?? '') ?>" required class="form-input"
                    placeholder="Ex: France, USA, Irlande...">
            </div>

            <div>
                <label for="guarantees" class="form-label">Garanties (ex: Clauses Contractuelles Types,
                    Certification...)</label>
                <textarea id="guarantees" name="guarantees" rows="3" class="form-input"
                    placeholder="Décrivez les garanties de protection des données..."><?= htmlspecialchars($subprocessor->guarantees ?? '') ?></textarea>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="index.php?page=subprocessor&action=list"
                    class="text-sm font-medium text-slate-500 hover:text-slate-700">Retour</a>
                <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
                    <button type="submit" class="btn btn-primary px-8 py-2.5">
                        <?= isset($subprocessor) ? 'Mettre à jour' : 'Enregistrer' ?>
                    </button>
                <?php else: ?>
                    <span class="text-sm italic text-amber-600 font-medium">Lecture seule</span>
                <?php endif; ?>
            </div>

        </form>
    </div>
</div>