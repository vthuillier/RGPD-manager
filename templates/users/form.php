<?php
$isEdit = isset($user);
$actionUrl = $isEdit ? 'index.php?page=user&action=update' : 'index.php?page=user&action=store';
?>
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="index.php?page=user&action=list"
            class="text-slate-500 hover:text-slate-700 flex items-center mb-4 transition-colors">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Retour à la liste
        </a>
        <h1 class="text-2xl font-bold text-slate-800"><?= $isEdit ? 'Modifier un utilisateur' : 'Ajouter un utilisateur' ?></h1>
        <p class="text-slate-600"><?= $isEdit ? 'Mettez à jour les informations et les accès de l\'utilisateur.' : 'L\'utilisateur pourra se connecter avec son email et le mot de passe défini ci-dessous.' ?>
        </p>
    </div>

    <div class="card p-8">
        <form action="<?= $actionUrl ?>" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $user->id ?>">
            <?php endif; ?>

            <div>
                <label for="name" class="form-label">Nom complet</label>
                <input type="text" id="name" name="name" required class="form-input" placeholder="Ex: Jean Dupont" value="<?= $isEdit ? htmlspecialchars($user->name) : '' ?>">
            </div>

            <div>
                <label for="email" class="form-label">Adresse Email</label>
                <input type="email" id="email" name="email" required class="form-input" placeholder="jean@exemple.fr" value="<?= $isEdit ? htmlspecialchars($user->email) : '' ?>">
            </div>

            <div>
                <label for="role" class="form-label">Rôle</label>
                <select id="role" name="role" class="form-input">
                    <option value="user" <?= ($isEdit && $user->role === 'user') ? 'selected' : '' ?>>Utilisateur (Lambda)</option>
                    <option value="org_admin" <?= ($isEdit && $user->role === 'org_admin') ? 'selected' : '' ?>>Administrateur d'organisme</option>
                    <?php if (($_SESSION['user_role'] ?? 'user') === 'super_admin'): ?>
                        <option value="super_admin" <?= ($isEdit && $user->role === 'super_admin') ? 'selected' : '' ?>>Administrateur logiciel</option>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Accès aux organismes</label>
                <div class="space-y-2 mt-2 max-h-48 overflow-y-auto p-4 border border-slate-200 rounded-lg">
                    <?php foreach ($organizations as $org): ?>
                        <div class="flex items-center">
                            <input type="checkbox" id="org_<?= $org->id ?>" name="organizations[]" value="<?= $org->id ?>" 
                                class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500"
                                <?= ($isEdit ? in_array($org->id, $userOrgIds) : $org->id === $this->organizationId) ? 'checked' : '' ?>>
                            <label for="org_<?= $org->id ?>" class="ml-2 text-sm text-slate-700">
                                <?= htmlspecialchars($org->name) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="mt-1 text-xs text-slate-500 italic">L'utilisateur aura accès aux données des organismes cochés.</p>
            </div>

            <div>
                <label for="password" class="form-label"><?= $isEdit ? 'Changer le mot de passe (laisser vide pour conserver)' : 'Mot de passe temporaire' ?></label>
                <input type="password" id="password" name="password" <?= $isEdit ? '' : 'required' ?> minlength="8" class="form-input"
                    placeholder="••••••••">
                <p class="mt-1 text-xs text-slate-500 italic">8 caractères minimum préconisés. L'utilisateur pourra le
                    modifier ultérieurement.</p>
            </div>

            <div class="pt-4">
                <button type="submit" class="btn btn-primary w-full py-2.5">
                    <?= $isEdit ? 'Mettre à jour' : 'Créer l\'utilisateur' ?>
                </button>
            </div>
        </form>
    </div>
</div>