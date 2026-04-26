<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require __DIR__ . '/../phpmailer/src/Exception.php';
  require __DIR__ . '/../phpmailer/src/PHPMailer.php';
  require __DIR__ . '/../phpmailer/src/SMTP.php';

  require_once __DIR__ . '/env.php';
  loadEnv(__DIR__ . '/../.env');

  function sendMail($fromEmail, $fromName, $subject, $body) {
      $mail = new PHPMailer(true);

      try {
          // SMTP config
          $mail->isSMTP();
          $mail->Host = $_ENV['MAIL_HOST'];
          $mail->SMTPAuth = true;
          $mail->Username = $_ENV['MAIL_USERNAME'];
          $mail->Password = $_ENV['MAIL_PASSWORD'];
          $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
          $mail->Port = $_ENV['MAIL_PORT'];

          // Sender (always your email)
          $mail->setFrom($_ENV['MAIL_USERNAME'], 'Kabsuhayan Support');

          // Receiver (your inbox)
          $mail->addAddress($_ENV['MAIL_USERNAME']);

          // User becomes reply-to
          $mail->addReplyTo($fromEmail, $fromName);

          // Content
          $mail->isHTML(true);
          $mail->Subject = $subject;
          $mail->Body = $body;

          return $mail->send();

      } catch (Exception $e) {
          error_log("Mail Error: " . $mail->ErrorInfo);
          return false;
      }
  }
?>