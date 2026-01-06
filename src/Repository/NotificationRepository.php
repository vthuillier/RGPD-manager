<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\NotificationSetting;
use PDO;

class NotificationRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function getSettingsByOrganizationId(int $organizationId): NotificationSetting
    {
        $stmt = $this->pdo->prepare('SELECT * FROM notification_settings WHERE organization_id = :org_id');
        $stmt->execute(['org_id' => $organizationId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            // Create default settings if not exists
            $this->pdo->prepare('INSERT INTO notification_settings (organization_id) VALUES (?)')
                ->execute([$organizationId]);
            return $this->getSettingsByOrganizationId($organizationId);
        }

        return NotificationSetting::fromArray($data);
    }

    public function saveSettings(NotificationSetting $settings): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE notification_settings SET 
                enable_rights_reminders = :err,
                rights_reminder_days = :rrd,
                enable_treatment_review_reminders = :etrr,
                treatment_review_interval_years = :triy,
                enable_aipd_draft_reminders = :eadr,
                aipd_draft_reminder_days = :adrd,
                notification_emails = :emails,
                smtp_host = :sh,
                smtp_port = :sp,
                smtp_user = :su,
                smtp_pass = :spass,
                smtp_encryption = :se,
                from_email = :fe,
                from_name = :fn,
                updated_at = CURRENT_TIMESTAMP
            WHERE organization_id = :org_id'
        );

        $stmt->execute([
            'err' => (int) $settings->enableRightsReminders,
            'rrd' => $settings->rightsReminderDays,
            'etrr' => (int) $settings->enableTreatmentReviewReminders,
            'triy' => $settings->treatmentReviewIntervalYears,
            'eadr' => (int) $settings->enableAipdDraftReminders,
            'adrd' => $settings->aipdDraftReminderDays,
            'emails' => $settings->notificationEmails,
            'sh' => $settings->smtpHost,
            'sp' => $settings->smtpPort,
            'su' => $settings->smtpUser,
            'spass' => $settings->smtpPass,
            'se' => $settings->smtpEncryption,
            'fe' => $settings->fromEmail,
            'fn' => $settings->fromName,
            'org_id' => $settings->organizationId
        ]);
    }

    public function hasSentNotification(int $organizationId, string $type, string $entityType, int $entityId): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) FROM notifications 
            WHERE organization_id = :org_id 
            AND type = :type 
            AND entity_type = :entity_type 
            AND entity_id = :entity_id
            AND sent_at > (CURRENT_TIMESTAMP - INTERVAL \'30 days\') -- Don\'t resend the same alert within 30 days
        ');
        $stmt->execute([
            'org_id' => $organizationId,
            'type' => $type,
            'entity_type' => $entityType,
            'entity_id' => $entityId
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function logNotification(int $organizationId, string $type, string $entityType, int $entityId, string $recipientEmail): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO notifications (organization_id, type, entity_type, entity_id, recipient_email)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([$organizationId, $type, $entityType, $entityId, $recipientEmail]);
    }

    public function getAllOrganizationSettings(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM notification_settings WHERE organization_id != -1');
        return array_map(fn($data) => NotificationSetting::fromArray($data), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getSystemSettings(): NotificationSetting
    {
        return $this->getSettingsByOrganizationId(-1);
    }
}
