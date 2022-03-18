<?php

namespace EmailManager\Services;

use Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;


class MailerService
{
    /**
     * Envía un mensaje con los datos de la cuenta predeterminada del sistema
     * @param string $email destinatario
     * @param string $name Nombre destinatario
     * @param string $subject Asunto
     * @param string $bodyHtml Mensaje HTML
     * @param string $altBody Mensaje Alternativo plain/text
     * @param string $replayTo Email para la respuesta
     * @param string $replayToName Nombre a quien se le enviará la respuesta
     * @return bool Retorna true si no hay errores o false en caso contrario
     */
    public static function send(
        array $mailConfig,
        string $email,
        string $name,
        string $subject,
        string $bodyHtml,
        string $altBody = '',
        string $replayTo = '',
        string $replayToName = '',
        bool $isProduction = true
    ): bool {
        //Create an instance; passing `true` enables exceptions
        $mail = static::createMailer($mailConfig);
        try {
            if (empty($replayTo)) {
                $replayTo = $mailConfig["reply_to"];
            }
            if (empty($replayToName)) {
                $replayToName = $mailConfig["reply_to_name"];
            }
            //Recipients
            $mail->setFrom($mailConfig['sender'], $mailConfig['sender_name']);

            

            if ($isProduction) {
                $emailAddress = $email;
                $mail->addAddress($email, $name);     //Add a recipient
            } else {
                $emailAddress = $mailConfig["test_email_address"];
            }
            if (empty($emailAddress)) {
                return false;
            }
            $mail->addAddress($emailAddress);     //Add a recipient
            
            if (!empty($replayTo)) {
                $mail->addReplyTo($replayTo, $replayToName);
            }

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $bodyHtml;
            $mail->AltBody = $altBody;

            $ok = $mail->send();
            return $ok;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createMailer(array $mailConfig): ?PHPMailer
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                         //Send using SMTP
            $mail->Host       = $mailConfig['host'];                 //Set the SMTP server to send through
            $mail->SMTPAuth   = $mailConfig['auth'];                 //Enable SMTP authentication
            $mail->Username   = $mailConfig['username'];             //SMTP username
            $mail->Password   = $mailConfig['password'];             //SMTP password
            $mail->SMTPSecure = $mailConfig['secure'];               //Enable implicit TLS encryption
            $mail->Port       = $mailConfig['port'];                 //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->CharSet    = $mailConfig['charset'];               //Codificación de carácteres para el texto del email

            return $mail;
        } catch (Exception $e) {
            return null;
        }
    }
}
