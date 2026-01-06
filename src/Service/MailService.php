<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\NotificationRepository;
use App\Entity\NotificationSetting;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Exception;

class MailService
{
    private NotificationRepository $notificationRepo;

    public function __construct()
    {
        $this->notificationRepo = new NotificationRepository();
    }

    /**
     * Envoie un mail en utilisant la configuration d'une organisation spécifique.
     */
    public function sendFromOrganization(int $orgId, string $to, string $subject, string $body): bool
    {
        $settings = $this->notificationRepo->getSettingsByOrganizationId($orgId);
        return $this->processSend($settings, $to, $subject, $body);
    }

    /**
     * Envoie un mail en utilisant la configuration système (ex: mot de passe oublié).
     */
    public function sendSystemMail(string $to, string $subject, string $body): bool
    {
        $settings = $this->notificationRepo->getSystemSettings();
        return $this->processSend($settings, $to, $subject, $body);
    }

    /**
     * Envoi réel via PHPMailer si configuré, sinon logue dans mail.log.
     */
    private function processSend(NotificationSetting $settings, string $to, string $subject, string $body): bool
    {
        // Toujours loguer l'intention d'envoi pour le debug
        $this->logToMailLog($settings, $to, $subject, $body);

        // Si le SMTP n'est pas configuré, on s'arrête au log
        if (empty($settings->smtpHost)) {
            return true;
        }

        // Augmenter le temps d'exécution pour le SMTP (éviter le timeout 30s)
        set_time_limit(60);

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->SMTPDebug = 3; // Debug level 3: connection messages
            $mail->Debugoutput = function ($str, $level) {
                file_put_contents(__DIR__ . '/../../logs/smtp_debug.log', date('Y-m-d H:i:s') . ' ' . $str, FILE_APPEND);
            };

            $mail->Host = $settings->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $settings->smtpUser;
            $mail->Password = $settings->smtpPass;

            // Auto-correction du mode de chiffrement selon le port
            if ($settings->smtpPort == 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Force SSL (Implicit)
            } else {
                $mail->SMTPSecure = ($settings->smtpEncryption === 'none') ? '' : PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->Port = $settings->smtpPort;
            $mail->Timeout = 10; // Réduire le timeout pour rater plus vite et ne pas bloquer PHP
            $mail->CharSet = 'UTF-8';

            // Options pour certains serveurs qui ont des certificats auto-signés ou des problèmes TLS
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Recipients
            $mail->setFrom(
                $settings->fromEmail ?? 'noreply@rgpd-manager.local',
                $settings->fromName ?? 'RGPD Manager'
            );
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br($body);
            $mail->AltBody = strip_tags($body);

            return $mail->send();
        } catch (PHPMailerException $e) {
            error_log("Mail Error: " . $mail->ErrorInfo);
            throw new Exception("Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo);
        }
    }

    private function logToMailLog(NotificationSetting $settings, string $to, string $subject, string $body): void
    {
        $logPath = __DIR__ . '/../../logs/mail.log';
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        $smtpInfo = $settings->smtpHost ? sprintf(
            "SMTP[%s:%d | User:%s | Enc:%s]",
            $settings->smtpHost,
            $settings->smtpPort,
            $settings->smtpUser,
            $settings->smtpEncryption
        ) : "INTERNAL_LOG_ONLY";

        $from = sprintf("%s <%s>", $settings->fromName ?? 'RGPD Manager', $settings->fromEmail ?? 'noreply@rgpd-manager.local');

        $content = sprintf(
            "[%s] %s\nFrom: %s\nTo: %s\nSubject: %s\nBody: %s\n-------------------\n",
            date('Y-m-d H:i:s'),
            $smtpInfo,
            $from,
            $to,
            $subject,
            $body
        );

        file_put_contents($logPath, $content, FILE_APPEND);
    }
}
