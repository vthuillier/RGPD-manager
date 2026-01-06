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
    public function sendFromOrganization(int $orgId, string $to, string $subject, string $body, bool $isHtml = false): bool
    {
        $settings = $this->notificationRepo->getSettingsByOrganizationId($orgId);
        return $this->processSend($settings, $to, $subject, $body, $isHtml);
    }

    /**
     * Envoie un mail en utilisant la configuration système (ex: mot de passe oublié).
     */
    public function sendSystemMail(string $to, string $subject, string $body, bool $isHtml = false): bool
    {
        $settings = $this->notificationRepo->getSystemSettings();
        return $this->processSend($settings, $to, $subject, $body, $isHtml);
    }

    /**
     * Envoi réel via PHPMailer si configuré, sinon logue dans mail.log.
     */
    private function processSend(NotificationSetting $settings, string $to, string $subject, string $body, bool $isHtml = false): bool
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
            $mail->SMTPDebug = 0;
        // Disable debug output for production

            $mail->Host = $settings->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $settings->smtpUser;
            $mail->Password = $settings->smtpPass;
        // Auto-correction du mode de chiffrement selon le port
            if ($settings->smtpPort == 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = ($settings->smtpEncryption === 'none') ? '' : PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->Port = $settings->smtpPort;
            $mail->Timeout = 10;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
        // Recipients
            $mail->setFrom($settings->fromEmail ?? 'noreply@rgpd-manager.local', $settings->fromName ?? 'RGPD Manager');
            $mail->addAddress($to);
        // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            if ($isHtml) {
                $mail->Body = $body;
                $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $body));
            } else {
                $mail->Body = $this->getHtmlLayout($subject, nl2br($body));
                $mail->AltBody = strip_tags($body);
            }

            return $mail->send();
        } catch (PHPMailerException $e) {
            error_log("Mail Error: " . $mail->ErrorInfo);
            throw new Exception("Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo);
        }
    }

    /**
     * Layout HTML premium pour les emails
     */
    public function getHtmlLayout(string $title, string $content, string $buttonText = null, string $buttonUrl = null): string
    {
        $buttonHtml = '';
        if ($buttonText && $buttonUrl) {
            $buttonHtml = '
                <div style="margin: 30px 0; text-align: center;">
                    <a href="' . $buttonUrl . '" style="background-color: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1), 0 2px 4px -1px rgba(79, 70, 229, 0.06);">
                        ' . $buttonText . '
                    </a>
                </div>';
        }

        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 30px 40px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.025em; }
        .content { padding: 40px; }
        .footer { background-color: #f1f5f9; padding: 20px 40px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .content h2 { color: #1e293b; margin-top: 0; font-size: 18px; font-weight: 700; }
        .content p { margin-bottom: 20px; color: #475569; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>RGPD Manager</h1>
        </div>
        <div class="content">
            <h2>' . $title . '</h2>
            <div>' . $content . '</div>
            ' . $buttonHtml . '
        </div>
        <div class="footer">
            Cet email a été envoyé par la plateforme <strong>RGPD Manager</strong>.<br>
            &copy; ' . date('Y') . ' - Sécurité et Conformité.
        </div>
    </div>
</body>
</html>';
    }

    private function logToMailLog(NotificationSetting $settings, string $to, string $subject, string $body): void
    {
        $logPath = __DIR__ . '/../../logs/mail.log';
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        $smtpInfo = $settings->smtpHost ? sprintf("SMTP[%s:%d | User:%s | Enc:%s]", $settings->smtpHost, $settings->smtpPort, $settings->smtpUser, $settings->smtpEncryption) : "INTERNAL_LOG_ONLY";
        $from = sprintf("%s <%s>", $settings->fromName ?? 'RGPD Manager', $settings->fromEmail ?? 'noreply@rgpd-manager.local');
        $content = sprintf("[%s] %s\nFrom: %s\nTo: %s\nSubject: %s\nBody: %s\n-------------------\n", date('Y-m-d H:i:s'), $smtpInfo, $from, $to, $subject, $body);
        file_put_contents($logPath, $content, FILE_APPEND);
    }
}
