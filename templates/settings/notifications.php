<div class="max-w-4xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate">
                Paramètres des Notifications
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Configurez les alertes automatiques pour rester conforme et proactif.
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 gap-3">
            <a href="index.php?page=settings&action=test_smtp<?= ($isSystem ?? false) ? '&system=1' : (isset($targetOrgId) && $targetOrgId != ($_SESSION['organization_id'] ?? 0) ? '&org_id=' . $targetOrgId : '') ?>"
                class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Envoyer un mail de test
            </a>
            <a href="index.php?page=settings&action=trigger_process<?= ($isSystem ?? false) ? '&system=1' : (isset($targetOrgId) && $targetOrgId != ($_SESSION['organization_id'] ?? 0) ? '&org_id=' . $targetOrgId : '') ?>"
                class="btn btn-outline gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Simuler les rappels
            </a>
        </div>
    </div>

    <div class="card p-6">
        <form action="index.php?page=settings&action=update_notifications" method="POST" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="is_system" value="<?= ($isSystem ?? false) ? '1' : '0' ?>">
            <input type="hidden" name="target_org_id" value="<?= $targetOrgId ?? '' ?>">

            <!-- Configuration SMTP -->
            <div class="border-b border-slate-200 pb-6">
                <h3 class="text-lg font-medium text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Configuration SMTP
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="smtp_host" class="form-label">Serveur SMTP</label>
                        <input type="text" name="smtp_host" id="smtp_host"
                            value="<?= htmlspecialchars($settings->smtpHost ?? '') ?>" class="form-input"
                            placeholder="smtp.exemple.com">
                    </div>
                    <div>
                        <label for="smtp_port" class="form-label">Port SMTP</label>
                        <input type="number" name="smtp_port" id="smtp_port" value="<?= $settings->smtpPort ?>"
                            class="form-input" placeholder="587">
                    </div>
                    <div>
                        <label for="smtp_user" class="form-label">Utilisateur SMTP</label>
                        <input type="text" name="smtp_user" id="smtp_user"
                            value="<?= htmlspecialchars($settings->smtpUser ?? '') ?>" class="form-input"
                            placeholder="user@exemple.com">
                    </div>
                    <div>
                        <label for="smtp_pass" class="form-label">Mot de passe SMTP</label>
                        <input type="password" name="smtp_pass" id="smtp_pass"
                            value="<?= htmlspecialchars($settings->smtpPass ?? '') ?>" class="form-input"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label for="smtp_encryption" class="form-label">Chiffrement</label>
                        <select name="smtp_encryption" id="smtp_encryption" class="form-input">
                            <option value="tls" <?= ($settings->smtpEncryption === 'tls') ? 'selected' : '' ?>>TLS
                                (Recommandé)</option>
                            <option value="ssl" <?= ($settings->smtpEncryption === 'ssl') ? 'selected' : '' ?>>SSL</option>
                            <option value="none" <?= ($settings->smtpEncryption === 'none') ? 'selected' : '' ?>>Aucun
                            </option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="from_email" class="form-label">Email Expéditeur</label>
                            <input type="email" name="from_email" id="from_email"
                                value="<?= htmlspecialchars($settings->fromEmail ?? '') ?>" class="form-input"
                                placeholder="ne-pas-repondre@exemple.com">
                        </div>
                        <div>
                            <label for="from_name" class="form-label">Nom Expéditeur</label>
                            <input type="text" name="from_name" id="from_name"
                                value="<?= htmlspecialchars($settings->fromName ?? '') ?>" class="form-input"
                                placeholder="RGPD Manager - Alerte">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emails de notification -->
            <div class="border-b border-slate-200 pb-6">
                <h3 class="text-lg font-medium text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Destinataires des rapports
                </h3>
                <div class="max-w-md">
                    <label for="notification_emails" class="form-label">Adresses email (séparées par des
                        virgules)</label>
                    <input type="text" name="notification_emails" id="notification_emails"
                        value="<?= htmlspecialchars($settings->notificationEmails ?? '') ?>" class="form-input"
                        placeholder="admin@exemple.com, dpo@exemple.com">
                    <p class="mt-2 text-xs text-slate-500">Si vide, les notifications seront envoyées à l'administrateur
                        de l'organisation par défaut.</p>
                </div>
            </div>

            <!-- Exercice des droits -->
            <div class="border-b border-slate-200 pb-6">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="enable_rights_reminders" name="enable_rights_reminders" type="checkbox"
                            <?= $settings->enableRightsReminders ? 'checked' : '' ?>
                            class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-slate-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="enable_rights_reminders" class="font-medium text-slate-900">Rappels pour l'exercice
                            des droits</label>
                        <p class="text-slate-500">Alerter lorsque le délai légal de 30 jours approche.</p>
                    </div>
                </div>
                <?php if ($settings->enableRightsReminders): ?>
                    <div class="mt-4 ml-7 max-w-xs">
                        <label for="rights_reminder_days" class="form-label text-xs">Alerter combien de jours avant
                            l'échéance ?</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="rights_reminder_days" id="rights_reminder_days"
                                value="<?= $settings->rightsReminderDays ?>" min="1" max="25" class="form-input w-20">
                            <span class="text-sm text-slate-500">jours</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Revues de traitements -->
            <div class="border-b border-slate-200 pb-6">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="enable_treatment_review_reminders" name="enable_treatment_review_reminders"
                            type="checkbox" <?= $settings->enableTreatmentReviewReminders ? 'checked' : '' ?>
                            class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-slate-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="enable_treatment_review_reminders" class="font-medium text-slate-900">Revues
                            périodiques des traitements</label>
                        <p class="text-slate-500">S'assurer que les fiches de registre sont régulièrement vérifiées.</p>
                    </div>
                </div>
                <?php if ($settings->enableTreatmentReviewReminders): ?>
                    <div class="mt-4 ml-7 max-w-xs">
                        <label for="treatment_review_interval_years" class="form-label text-xs">Fréquence des revues</label>
                        <div class="flex items-center gap-2">
                            <select name="treatment_review_interval_years" id="treatment_review_interval_years"
                                class="form-input w-32">
                                <option value="1" <?= $settings->treatmentReviewIntervalYears == 1 ? 'selected' : '' ?>>Tout
                                    les ans</option>
                                <option value="2" <?= $settings->treatmentReviewIntervalYears == 2 ? 'selected' : '' ?>>Tout
                                    les 2 ans</option>
                                <option value="3" <?= $settings->treatmentReviewIntervalYears == 3 ? 'selected' : '' ?>>Tout
                                    les 3 ans</option>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- AIPD en brouillon -->
            <div class="pb-6">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="enable_aipd_draft_reminders" name="enable_aipd_draft_reminders" type="checkbox"
                            <?= $settings->enableAipdDraftReminders ? 'checked' : '' ?>
                            class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-slate-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="enable_aipd_draft_reminders" class="font-medium text-slate-900">Rappels pour les
                            AIPD en brouillon</label>
                        <p class="text-slate-500">Éviter d'oublier des analyses d'impact commencées.</p>
                    </div>
                </div>
                <?php if ($settings->enableAipdDraftReminders): ?>
                    <div class="mt-4 ml-7 max-w-xs">
                        <label for="aipd_draft_reminder_days" class="form-label text-xs">Alerter après combien de jours
                            d'inactivité ?</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="aipd_draft_reminder_days" id="aipd_draft_reminder_days"
                                value="<?= $settings->aipdDraftReminderDays ?>" min="1" max="90" class="form-input w-20">
                            <span class="text-sm text-slate-500">jours</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="pt-5 border-t border-slate-200 flex justify-end">
                <button type="submit" class="btn btn-primary px-8">
                    Enregistrer les paramètres
                </button>
            </div>
        </form>
    </div>
</div>