<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require __DIR__ . '/../phpmailer/src/Exception.php';
  require __DIR__ . '/../phpmailer/src/PHPMailer.php';
  require __DIR__ . '/../phpmailer/src/SMTP.php';

  function sendMail($to, $subject, $body, $replyToEmail = null, $replyToName = null) {
      $mail = new PHPMailer(true);

      try {
          // Server settings
          $mail->isSMTP();
          $mail->Host = 'smtp.gmail.com';
          $mail->SMTPAuth = true;
          $mail->Username = 'kabsuhayan@gmail.com';
          $mail->Password = 'vcxiwldaugffpfji';
          $mail->SMTPSecure = 'ssl';
          $mail->Port = 465;

          // Sender
          $mail->setFrom('kabsuhayan@gmail.com', 'Kabsuhayan Website');

          // Recipient
          $mail->addAddress($to);

          // Reply-To (user)
          if ($replyToEmail) {
              $mail->addReplyTo($replyToEmail, $replyToName ?? '');
          }

          // Content
          $mail->isHTML(true);
          $mail->Subject = $subject;
          $mail->Body = $body;

          return $mail->send();

      } catch (Exception $e) {
          error_log("Mailer Error: " . $mail->ErrorInfo);
          return false;
      }
  }
?>