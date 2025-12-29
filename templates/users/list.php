<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Gestion des utilisateurs</h1>
    <a href="index.php?page=user&action=create" class="btn btn-primary flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
        </svg>
        Nouvel utilisateur
    </a>
</div>

<div class="card overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Organismes
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Rôle</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            <?php foreach ($users as $user): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                        <?= htmlspecialchars($user->name) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?= htmlspecialchars($user->email) ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        <?php
                        $orgRepo = new \App\Repository\OrganizationRepository();
                        $userOrgs = $orgRepo->findAllByUserId((int) $user->id);
                        $orgNames = array_map(fn($o) => $o->name, $userOrgs);
                        echo !empty($orgNames) ? implode(', ', array_map('htmlspecialchars', $orgNames)) : '<span class="italic text-slate-400">Aucun</span>';
                        ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-pill status-pill-<?= $user->role === 'admin' ? 'high' : 'info' ?>">
                            <?php
                            $rolesMap = [
                                'super_admin' => 'Administrateur logiciel',
                                'org_admin' => 'Administrateur organisme',
                                'user' => 'Utilisateur lambda',
                                'guest' => 'Invité'
                            ];
                            echo $rolesMap[$user->role] ?? $user->role;
                            ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="index.php?page=user&action=edit&id=<?= $user->id ?>"
                            class="text-primary-600 hover:text-primary-900 mr-3">Modifier</a>
                        <?php if ($user->id !== (int) $_SESSION['user_id']): ?>
                            <form action="index.php?page=user&action=delete" method="POST" class="inline"
                                onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="id" value="<?= $user->id ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>