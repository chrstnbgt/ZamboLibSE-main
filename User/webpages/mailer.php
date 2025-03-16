<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/vendor/autoload.php";

$mail = new PHPMailer(true);

// Enable SMTP debugging
$mail->SMTPDebug = SMTP::DEBUG_SERVER;

// Set mailer to use SMTP
$mail->isSMTP();

// Specify main and backup SMTP servers
$mail->Host = 'smtp-relay.sendinblue.com';

// Enable SMTP authentication
$mail->SMTPAuth = true;

// SMTP username (your Sendinblue SMTP username)
$mail->Username = 'yujames543@gmail.com';

// SMTP password (your Sendinblue SMTP API key)
$mail->Password = 'xsmtpsib-7f6f32cb72c5f4548f7f339c654db4787ae43610bf9998ba5d87811b459a11f1-8VkYTmDFLMjEhCQt';

// Specify the authentication method
$mail->AuthType = 'LOGIN';

// Enable TLS encryption, `ssl` also accepted
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

// TCP port to connect to
$mail->Port = 587;

// Set email format to HTML
$mail->isHTML(true);

// Set sender
$mail->setFrom('yujames543@gmail.com', 'Zamboanga City Library');

return $mail;
