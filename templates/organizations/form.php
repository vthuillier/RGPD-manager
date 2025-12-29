<?php
$isEdit = isset($organization);
$actionUrl = $isEdit ? 'index.php?page=organization&action=update' : 'index.php?page=organization&action=store';
?>
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="index.php?page=organization&action=list"
            class="text-slate-500 hover:text-slate-700 flex items-center mb-4 transition-colors">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Retour à la liste
        </a>
        <h1 class="text-2xl font-bold text-slate-800"><?= $isEdit ? 'Modifier l\'organisme' : 'Ajouter un organisme' ?>
        </h1>
        <p class="text-slate-600">Définissez le nom de l'entité juridique à gérer.</p>
    </div>

    <div class="card p-8">
        <form action="<?= $actionUrl ?>" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $organization->id ?>">
            <?php endif; ?>

            <div>
                <label for="name" class="form-label text-sm uppercase tracking-wider font-semibold text-slate-500">Nom
                    de l'organisme</label>
                <input type="text" id="name" name="name" required class="form-input" placeholder="Ex: Ma Société SAS"
                    value="<?= $isEdit ? htmlspecialchars($organization->name) : '' ?>">
            </div>

            <div class="pt-4">
                <button type="submit" class="btn btn-primary w-full py-2.5">
                    <?= $isEdit ? 'Mettre à jour' : 'Créer l\'organisme' ?>
                </button>
            </div>
        </form>
    </div>
</div>