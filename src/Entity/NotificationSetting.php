<?php

declare(strict_types=1);

namespace App\Entity;

class NotificationSetting
{
    public function __construct(
        public ?int $id,
        public int $organizationId,
        public bool $enableRightsReminders = true,
        public int $rightsReminderDays = 7,
        public bool $enableTreatmentReviewReminders = true,
        public int $treatmentReviewIntervalYears = 1,
        public bool $enableAipdDraftReminders = true,
        public int $aipdDraftReminderDays = 15,
        public ?string $notificationEmails = null,
        public ?string $smtpHost = null,
        public int $smtpPort = 587,
        public ?string $smtpUser = null,
        public ?string $smtpPass = null,
        public string $smtpEncryption = 'tls',
        public ?string $fromEmail = null,
        public ?string $fromName = null,
        public ?string $updatedAt = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            (int) ($data['organization_id'] ?? 0),
            (bool) ($data['enable_rights_reminders'] ?? true),
            (int) ($data['rights_reminder_days'] ?? 7),
            (bool) ($data['enable_treatment_review_reminders'] ?? true),
            (int) ($data['treatment_review_interval_years'] ?? 1),
            (bool) ($data['enable_aipd_draft_reminders'] ?? true),
            (int) ($data['aipd_draft_reminder_days'] ?? 15),
            $data['notification_emails'] ?? null,
            $data['smtp_host'] ?? null,
            (int) ($data['smtp_port'] ?? 587),
            $data['smtp_user'] ?? null,
            $data['smtp_pass'] ?? null,
            $data['smtp_encryption'] ?? 'tls',
            $data['from_email'] ?? null,
            $data['from_name'] ?? null,
            $data['updated_at'] ?? null
        );
    }
}
