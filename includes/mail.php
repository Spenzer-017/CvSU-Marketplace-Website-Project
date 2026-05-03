<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require __DIR__ . '/../phpmailer/src/Exception.php';
    require __DIR__ . '/../phpmailer/src/PHPMailer.php';
    require __DIR__ . '/../phpmailer/src/SMTP.php';

    require_once __DIR__ . '/env.php';
    loadEnv(__DIR__ . '/../.env');

    function sendMail(string $fromEmail, string $fromName, string $subject, string $body) {
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

    function sendVerificationCode(PDO $pdo, string $toEmail, string $toName): array {
        // Delete any previous pending codes for this email
        $pdo->prepare('DELETE FROM email_verifications WHERE email = ?')->execute([$toEmail]);

        // Generate a secure 6-digit code
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Expires in 15 minutes
        $expiresAt = date('Y-m-d H:i:s', time() + 900);

        // Store in DB
        $stmt = $pdo->prepare(
            'INSERT INTO email_verifications (email, code, expires_at) VALUES (?, ?, ?)'
        );
        $stmt->execute([$toEmail, $code, $expiresAt]);

        // Build the HTML email body
        $appName = 'Kabsuhayan';
        $year = date('Y');
        $body = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verify your email</title>
    </head>
    <body style='margin:0;padding:0;background-color:#f4f4f0;font-family:\"Helvetica Neue\",Arial,sans-serif;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background:#f4f4f0;padding:40px 0;'>
        <tr>
        <td align='center'>
            <table width='480' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.07);'>

            <!-- Header -->
            <tr>
                <td style='background:#163616;padding:32px 40px;text-align:center;'>
                <h1 style='margin:0;color:#4CAF50;font-size:22px;font-weight:700;letter-spacing:-0.3px;'>
                    Kabsu<span style='color:#C0B87A;'>hayan</span>
                </h1>
                <p style='margin:6px 0 0;color:#a8c5a8;font-size:13px;'>CvSU Student Marketplace</p>
                </td>
            </tr>

            <!-- Body -->
            <tr>
                <td style='padding:36px 40px 28px;'>
                <h2 style='margin:0 0 8px;color:#1A2B1A;font-size:18px;font-weight:600;'>Verify your email address</h2>
                <p style='margin:0 0 24px;color:#555;font-size:14px;line-height:1.6;'>
                    Hi <strong>" . htmlspecialchars($toName) . "</strong>, thanks for signing up!<br>
                    Use the code below to complete your registration. It expires in <strong>15 minutes</strong>.
                </p>

                <!-- OTP Box -->
                <div style='background:#f0f7f0;border:2px dashed #005F02;border-radius:10px;padding:24px;text-align:center;margin:0 0 24px;'>
                    <p style='margin:0 0 4px;color:#7A8C7A;font-size:12px;text-transform:uppercase;letter-spacing:0.08em;font-weight:600;'>Your verification code</p>
                    <p style='margin:0;color:#005F02;font-size:40px;font-weight:700;letter-spacing:10px;font-family:\"Courier New\",monospace;'>{$code}</p>
                </div>

                <p style='margin:0 0 8px;color:#888;font-size:13px;line-height:1.6;'>
                    If you did not create a Kabsuhayan account, you can safely ignore this email.
                </p>
                <p style='margin:0;color:#888;font-size:13px;line-height:1.6;'>
                    <strong>Do not share this code</strong> with anyone.
                </p>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td style='background:#f8f8f5;padding:18px 40px;border-top:1px solid #e8e8e0;text-align:center;'>
                <p style='margin:0;color:#aaa;font-size:12px;'>
                    &copy; {$year} {$appName} &mdash; Cavite State University<br>
                    This is an automated message, please do not reply.
                </p>
                </td>
            </tr>

            </table>
        </td>
        </tr>
    </table>
    </body>
    </html>";

        // Send via PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Kabsuhayan');
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = "Your Kabsuhayan verification code: {$code}";
            $mail->Body = $body;
            $mail->AltBody = "Your Kabsuhayan verification code is: {$code}\n\nIt expires in 15 minutes. Do not share this code with anyone.";

            $mail->send();
            return ['success' => true, 'error' => null];
        } catch (Exception $e) {
            error_log('Verification mail error: ' . $mail->ErrorInfo);

            // Clean up the stored code if sending failed
            $pdo->prepare('DELETE FROM email_verifications WHERE email = ?')->execute([$toEmail]);
            return ['success' => false, 'error' => $mail->ErrorInfo];
        }
    }
?>