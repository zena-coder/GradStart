<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "mail_config.php";
require_once "phpmailer/Exception.php";
require_once "phpmailer/PHPMailer.php";
require_once "phpmailer/SMTP.php";

function send_email($to, $subject, $body)
{
    if (SMTP_USER == "your-email@gmail.com") {
        file_put_contents(__DIR__ . "/mail_error.txt", date("Y-m-d H:i:s") . " mail_config.php is not filled in yet\n", FILE_APPEND);
        return false;
    }
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = "UTF-8";
        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        file_put_contents(__DIR__ . "/mail_error.txt", date("Y-m-d H:i:s") . " " . $mail->ErrorInfo . "\n", FILE_APPEND);
        return false;
    }
}
?>
