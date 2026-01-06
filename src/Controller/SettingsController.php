<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Entity\NotificationSetting;
use App\Service\NotificationService;
use Exception;

class SettingsController extends BaseController
{
    private NotificationRepository $repository;
    public function __construct()
    {
        $this->ensureRole(['org_admin', 'super_admin']);
        $this->repository = new NotificationRepository();
    }

    public function notifications(): void
    {
        $orgId = (int) ($_SESSION['organization_id'] ?? 0);
        // Si super_admin veut configurer le système ou une organisation spécifique
        if ($_SESSION['user_role'] === 'super_admin') {
            if (isset($_GET['system'])) {
                $orgId = -1;
            } elseif (isset($_GET['org_id'])) {
                $orgId = (int) $_GET['org_id'];
            }
        }

        $settings = $this->repository->getSettingsByOrganizationId($orgId);
        $this->render('settings/notifications', [
            'settings' => $settings,
            'title' => ($orgId === -1) ? 'Configuration Système (Mail)' : 'Paramètres des notifications',
            'isSystem' => ($orgId === -1),
            'targetOrgId' => $orgId
        ]);
    }

    public function updateNotifications(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        try {
            $orgId = (int) ($_SESSION['organization_id'] ?? 0);
            if ($_SESSION['user_role'] === 'super_admin') {
                if (isset($_POST['is_system']) && $_POST['is_system'] == '1') {
                    $orgId = -1;
                } elseif (isset($_POST['target_org_id'])) {
                    $orgId = (int) $_POST['target_org_id'];
                }
            }

            $settings = new NotificationSetting(
                null,
                $orgId,
                isset($_POST['enable_rights_reminders']),
                (int) ($_POST['rights_reminder_days'] ?? 7),
                isset($_POST['enable_treatment_review_reminders']),
                (int) ($_POST['treatment_review_interval_years'] ?? 1),
                isset($_POST['enable_aipd_draft_reminders']),
                (int) ($_POST['aipd_draft_reminder_days'] ?? 15),
                $_POST['notification_emails'] ?? null,
                $_POST['smtp_host'] ?? null,
                (int) ($_POST['smtp_port'] ?? 587),
                $_POST['smtp_user'] ?? null,
                $_POST['smtp_pass'] ?? null,
                $_POST['smtp_encryption'] ?? 'tls',
                $_POST['from_email'] ?? null,
                $_POST['from_name'] ?? null
            );
            $this->repository->saveSettings($settings);
            $this->auditLog('SETTINGS_UPDATE', 'notification_settings', $orgId);
            $urlParams = '';
            if ($orgId === -1) {
                $urlParams = '&system=1';
            } elseif ($orgId !== (int) ($_SESSION['organization_id'] ?? 0)) {
                $urlParams = '&org_id=' . $orgId;
            }

            $_SESSION['flash_success'] = "Paramètres mis à jour avec succès.";
            $this->redirect('index.php?page=settings&action=notifications' . $urlParams);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Erreur : " . $e->getMessage();
            $urlParams = '';
            // $orgId might not be initialized if exception occurs before line 47 (unlikely here but possible in general)
            // But here it is line 47.
            $actualOrgId = $orgId;
            if ($actualOrgId === -1) {
                $urlParams = '&system=1';
            } elseif ($actualOrgId !== (int) ($_SESSION['organization_id'] ?? 0)) {
                $urlParams = '&org_id=' . $actualOrgId;
            }
            $this->redirect('index.php?page=settings&action=notifications' . $urlParams);
        }
    }

    /**
     * Permet de forcer le traitement des notifications pour test (Dry run)
     */
    public function triggerProcess(): void
    {
        $orgId = (int) ($_SESSION['organization_id'] ?? 0);
        $urlParams = '';
        if ($_SESSION['user_role'] === 'super_admin') {
            if (isset($_GET['system'])) {
                $orgId = -1;
                $urlParams = '&system=1';
            } elseif (isset($_GET['org_id'])) {
                $orgId = (int) $_GET['org_id'];
                $urlParams = '&org_id=' . $orgId;
            }
        }

        $service = new NotificationService();
        $count = $service->processOrganizationNotifications($this->repository->getSettingsByOrganizationId($orgId));
        $_SESSION['flash_success'] = "$count notification(s) simulée(s) (voir logs/mail.log).";
        $this->redirect('index.php?page=settings&action=notifications' . $urlParams);
    }

    /**
     * Envoie un vrai mail de test à l'utilisateur courant
     */
    public function testSmtp(): void
    {
        $orgId = (int) ($_SESSION['organization_id'] ?? 0);
        $urlParams = '';
        if ($_SESSION['user_role'] === 'super_admin') {
            if (isset($_GET['system'])) {
                $orgId = -1;
                $urlParams = '&system=1';
            } elseif (isset($_GET['org_id'])) {
                $orgId = (int) $_GET['org_id'];
                $urlParams = '&org_id=' . $orgId;
            }
        }

        try {
            $userRepo = new \App\Repository\UserRepository();
            $user = $userRepo->find((int) $_SESSION['user_id']);
            if (!$user) {
                throw new Exception("Utilisateur non trouvé.");
            }

            $mailService = new \App\Service\MailService();
            $subject = "🧪 Test de configuration SMTP - RGPD Manager";
            $body = "Ceci est un email de test pour confirmer que vos paramètres SMTP sont corrects.\n\nEnvoyé le : " . date('d/m/Y H:i:s');
            if ($orgId === -1) {
                $mailService->sendSystemMail($user->email, $subject, $body);
            } else {
                $mailService->sendFromOrganization($orgId, $user->email, $subject, $body);
            }

            $_SESSION['flash_success'] = "Email de test envoyé avec succès à " . $user->email;
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Échec du test SMTP : " . $e->getMessage();
        }

        $this->redirect('index.php?page=settings&action=notifications' . $urlParams);
    }
}
