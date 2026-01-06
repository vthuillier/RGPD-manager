<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\NotificationRepository;
use App\Repository\RightsExerciseRepository;
use App\Repository\TreatmentRepository;
use App\Repository\AipdRepository;
use App\Repository\UserRepository;
use App\Entity\NotificationSetting;

class NotificationService
{
    private NotificationRepository $notificationRepo;
    private MailService $mailService;

    public function __construct()
    {
        $this->notificationRepo = new NotificationRepository();
        $this->mailService = new MailService();
    }

    /**
     * Exécute toutes les vérifications pour toutes les organisations.
     * Cette méthode devrait être appelée par un cron job / tâche planifiée.
     */
    public function processAllNotifications(): int
    {
        $allSettings = $this->notificationRepo->getAllOrganizationSettings();
        $totalSent = 0;

        foreach ($allSettings as $settings) {
            $totalSent += $this->processOrganizationNotifications($settings);
        }

        return $totalSent;
    }

    public function processOrganizationNotifications(NotificationSetting $settings): int
    {
        $sentCount = 0;
        $orgId = $settings->organizationId;

        // 1. Exercice des droits (limite de 30 jours)
        if ($settings->enableRightsReminders) {
            $sentCount += $this->checkRightsExercises($settings);
        }

        // 2. Revues de traitements (annuelles par défaut)
        if ($settings->enableTreatmentReviewReminders) {
            $sentCount += $this->checkTreatmentReviews($settings);
        }

        // 3. AIPD en brouillon
        if ($settings->enableAipdDraftReminders) {
            $sentCount += $this->checkAipdDrafts($settings);
        }

        return $sentCount;
    }

    private function checkRightsExercises(NotificationSetting $settings): int
    {
        $repo = new RightsExerciseRepository();
        $pending = $repo->findAllByOrganizationId($settings->organizationId);
        $sent = 0;

        foreach ($pending as $request) {
            if ($request->status !== 'En attente')
                continue;

            $requestDate = new \DateTime($request->requestDate);
            $deadline = (clone $requestDate)->modify('+30 days');
            $now = new \DateTime();

            $daysToDeadline = $now->diff($deadline)->days;
            $isPast = $now > $deadline;

            if ($isPast || $daysToDeadline <= $settings->rightsReminderDays) {
                if (!$this->notificationRepo->hasSentNotification($settings->organizationId, 'rights_exercise', 'rights_exercise', $request->id)) {
                    $recipient = $this->getRecipient($settings);
                    $subject = "🚨 Rappel : Exercice des droits approchant l'échéance";

                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $url = "$protocol://$host/index.php?page=rights&action=list";

                    $content = sprintf(
                        "<p>La demande d'exercice de droits de <strong>%s</strong> reçue le %s arrive à échéance le <strong>%s</strong>.</p>" .
                        "<p>Il reste <strong>%d jours</strong> pour répondre conformément au RGPD.</p>",
                        htmlspecialchars($request->applicantName),
                        $request->requestDate,
                        $deadline->format('d/m/Y'),
                        $isPast ? -$daysToDeadline : $daysToDeadline
                    );

                    $htmlBody = $this->mailService->getHtmlLayout($subject, $content, "Gérer les demandes", $url);

                    $this->mailService->sendFromOrganization($settings->organizationId, $recipient, $subject, $htmlBody, true);
                    $this->notificationRepo->logNotification($settings->organizationId, 'rights_exercise', 'rights_exercise', $request->id, $recipient);
                    $sent++;
                }
            }
        }
        return $sent;
    }

    private function checkTreatmentReviews(NotificationSetting $settings): int
    {
        $repo = new TreatmentRepository();
        $treatments = $repo->findAllByOrganizationId($settings->organizationId);
        $sent = 0;
        $interval = $settings->treatmentReviewIntervalYears;

        foreach ($treatments as $treatment) {
            $lastUpdate = new \DateTime($treatment->updatedAt ?? $treatment->createdAt);
            $now = new \DateTime();
            $nextReview = (clone $lastUpdate)->modify("+$interval years");

            if ($now >= $nextReview) {
                if (!$this->notificationRepo->hasSentNotification($settings->organizationId, 'treatment_review', 'treatment', $treatment->id)) {
                    $recipient = $this->getRecipient($settings);
                    $subject = "📅 Revue annuelle du traitement : " . $treatment->name;

                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $url = "$protocol://$host/index.php?page=treatment&action=list";

                    $content = sprintf(
                        "<p>Le traitement <strong>'%s'</strong> n'a pas été mis à jour depuis le %s.</p>" .
                        "<p>Une revue de conformité est nécessaire (intervalle configuré de %d an(s)).</p>",
                        htmlspecialchars($treatment->name),
                        $lastUpdate->format('d/m/Y'),
                        $interval
                    );

                    $htmlBody = $this->mailService->getHtmlLayout("Revue de traitement", $content, "Voir le registre", $url);

                    $this->mailService->sendFromOrganization($settings->organizationId, $recipient, $subject, $htmlBody, true);
                    $this->notificationRepo->logNotification($settings->organizationId, 'treatment_review', 'treatment', $treatment->id, $recipient);
                    $sent++;
                }
            }
        }
        return $sent;
    }

    private function checkAipdDrafts(NotificationSetting $settings): int
    {
        $repo = new AipdRepository();
        $aipds = $repo->findAllByOrganizationId($settings->organizationId);
        $sent = 0;
        $threshold = $settings->aipdDraftReminderDays;

        foreach ($aipds as $aipd) {
            if ($aipd->status !== 'draft')
                continue;

            $lastUpdate = new \DateTime($aipd->updatedAt ?? $aipd->createdAt);
            $now = new \DateTime();
            $ageDays = $now->diff($lastUpdate)->days;

            if ($ageDays >= $threshold) {
                if (!$this->notificationRepo->hasSentNotification($settings->organizationId, 'aipd_draft', 'aipd', $aipd->id)) {
                    $recipient = $this->getRecipient($settings);
                    $subject = "📝 AIPD en attente de finalisation";

                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $url = "$protocol://$host/index.php?page=aipd&action=list";

                    $content = sprintf(
                        "<p>L'Analyse d'Impact (AIPD) pour le traitement <strong>#%d</strong> est en mode brouillon depuis %d jours.</p>" .
                        "<p>Pensez à la finaliser pour assurer la conformité de ce traitement à risque.</p>",
                        $aipd->treatmentId,
                        $ageDays
                    );

                    $htmlBody = $this->mailService->getHtmlLayout($subject, $content, "Finaliser l'AIPD", $url);

                    $this->mailService->sendFromOrganization($settings->organizationId, $recipient, $subject, $htmlBody, true);
                    $this->notificationRepo->logNotification($settings->organizationId, 'aipd_draft', 'aipd', $aipd->id, $recipient);
                    $sent++;
                }
            }
        }
        return $sent;
    }

    private function getRecipient(NotificationSetting $settings): string
    {
        if ($settings->notificationEmails) {
            return explode(',', $settings->notificationEmails)[0];
        }

        // Sinon on cherche un admin dans l'organisation
        $userRepo = new UserRepository();
        $users = $userRepo->findAllByOrganizationId($settings->organizationId);
        foreach ($users as $user) {
            if ($user->role === 'admin') {
                return $user->email;
            }
        }

        // Fallback sur le premier utilisateur trouvé
        return $users[0]->email ?? 'admin@rgpd-manager.local';
    }
}
