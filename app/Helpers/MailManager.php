<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;  
use PHPMailer\PHPMailer\SMTP;

require_once dirname(__DIR__, 2) . '/bootstrap.php';

class MailManager
{
    public function send(string $subject, string $email, string $message) {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            /*
            return [
                'success' => false,
                'http_code' => null,
                'response' => null,
                'error' => 'Invalid email address'
            ];
            */
            error_log("Failed to send email to {$email} due to invalid email address");
            return;
        }

        // Load HTML template
        $templatePath = dirname(__DIR__, 2) . "/templates/mail.php";

        if (!file_exists($templatePath)) {
            /*
            return [
                'success' => false,
                'http_code' => null,
                'response' => null,
                'error' => 'Email template not found'
            ];
            */
            error_log("Failed to send email to {$email} because mail template was not found");
            return;
        }

        // Get template content
        $emailBody = file_get_contents($templatePath);

        // Inject message
        $emailContent = str_replace('{{message}}', $message, $emailBody);

        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 0; // change this to 0 later
            $mail->isSMTP();      // Set mailer to use SMTP
            $mail->Host = $_ENV['MAIL_HOST'];  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;   // Enable SMTP authentication
            $mail->Username = $_ENV['MAIL_USERNAME'];  // SMTP username
            $mail->Password = $_ENV['MAIL_PASSWORD']; // SMTP password
            $mail->SMTPSecure =  $_ENV['MAIL_SECURE'];  // Enable TLS encryption, `ssl` also accepted
            $mail->Port =  $_ENV['MAIL_PORT'];   // TCP port to connect to
            $mail->setFrom($_ENV['MAIL_ADDRESS'], $_ENV['MAIL_SENDER']);
            $mail->addAddress($email);  // Add a recipient, Name is optional
            $mail->isHTML(true);   // Set email format to HTML
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $emailContent;

            if ($mail->send()) {
                /*
                return [
                    'success' => true,
                    'http_code' => 200,
                    'response' => 'Mail sent successfully',
                    'error' => null
                ];
                */
                return true;
            } else {
                /*
                return [
                    'success' => false,
                    'http_code' => 200,
                    'response' => 'Failed to send mail',
                    'error' => 'Error occured while sending mail to ' . $email
                ];
                */
                error_log("Failed to send email to {$email} for unknown reasons");
                return;
            }
        }
        catch (Exception $e) {
            /*
            return [
                'success' => false,
                'http_code' => 500,
                'response' => 'Failed to send mail',
                'error' => 'Error occured while sending mail to ' . $email . ' and the error is: ' . $e->getMessage()
            ];
            */
            error_log("Failed to send email to {$email}. Mailer Error: " . $mail->ErrorInfo) . " and exception: " . $e->getMessage();
        } 
        finally {
            $mail->smtpClose();
        }
    }
}