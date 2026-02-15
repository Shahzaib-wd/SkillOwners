<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/SMTPMailer.php';

class MailHelper {
    
    public static function send($to, $subject, $body, $isHtml = true) {
        // If SMTP is not configured, don't attempt to send
        if (empty(SMTP_HOST) || empty(SMTP_USER) || empty(SMTP_PASS)) {
            error_log("MailHelper Error: SMTP credentials not configured.");
            return false;
        }

        try {
            $mailer = new SMTPMailer(SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS);
            // $mailer->setDebug(true); // Uncomment for debugging
            
            $result = $mailer->send(
                $to, 
                $subject, 
                $body, 
                SMTP_FROM, 
                SMTP_FROM_NAME, 
                $isHtml
            );
            
            return $result;
        } catch (Exception $e) {
            error_log("MailHelper Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a welcome email template
     */
    public static function sendWelcomeEmail($to, $name) {
        $subject = "Welcome to " . SITE_NAME;
        $body = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <h2>Welcome, $name!</h2>
            <p>Thank you for joining " . SITE_NAME . ". We are excited to have you on board.</p>
            <p>Explore our platform to find the best talent or freelance opportunities.</p>
            <br>
            <p>Best Regards,<br>The " . SITE_NAME . " Team</p>
        </div>";
        
        return self::send($to, $subject, $body);
    }
}
