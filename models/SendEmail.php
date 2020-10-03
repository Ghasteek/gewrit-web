<?php 
require "PHPMailer/PHPMailer.php";
require "PHPMailer/SMTP.php";
require "PHPMailer/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
        // odesle email
    class SendEmail {
        public function send($adress, $subject, $text, $from){
            $mail = new PHPMailer(true);

            try {
                $login = PasswordsHandler::getLogin('mail'); // nacti si username a password 
                //Server settings
                $mail->isSMTP(); 
                $mail->CharSet    = 'UTF-8';                                    // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                           // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                       // Enable SMTP authentication
                $mail->Username   = $login['username'];                    // SMTP username
                $mail->Password   = $login['password'];                                 // SMTP password
                //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;             // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;                                        // TCP port to connect to
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                ); 

                //Recipients
                $mail->setFrom('gewrit.4fan@gmail.com', 'Seznam seriálů a mangy GEWRIT');
                $mail->addAddress($adress);                                     // Add a recipient
                $mail->addReplyTo($from);                                       // Add reply to                                                 

                $mailBodyFinal = "<!DOCTYPE html>
                    <html lang='cs'>
                    <title>Zprávva z GEWRIT </title>
                    <meta name=description content='Seznam rozdívaných seriálů, anime a rozečtených mang a komixů.'>
                    <meta charset='UTF-8'>
                    <body style='text-align:left; background-color: #f5f5f5; font-size: 1.3em; font-family: Arial, Helvetica, sans-serif;'>
                    " . $text . "
                    </body>
                    </html>";
                // Content
                $mail->isHTML(true);                                            // Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body    = $mailBodyFinal;
                $mail->send();
            } catch (UserError $error) {
                $this->addMessage($error->getMessage());
            }
        }

            // zkontroluje vyplneny a spravne zadany antispam
        public function sendWithAntispam ($year, $adress, $subject, $text, $from){
            if ($year != date('Y'))
                throw new UserError('Chybně vyplněný AntiSPAM.');
            $this->send($adress, $subject, $text, $from);
        }


        // zkontroluje vyplneny a spravne zadany antispam
        public function sendToken ( $adress, $token){
            $adress = trim($adress);
            $subject = "Obnovení zapomenutého hesla - GEWRIT";
            $text = "<p>Na serveru <a href='http://gewrit.4fan.cz' target='_blank'>gewrit.4fan.cz</a> jste požádal o obnovení hesla. Pokud jste to nebyl/a Vy, tento email ignorujte.</p>" . 
            "<p>Pro obnovu zapomenutého hesla prosím klikněte na tento <a href='http://gewrit.4fan.cz/obnovit/" . $token . "' target='_blank'>odkaz</a></p>";
            $from = "gewrit.4fan@gmail.com";

            $this->send($adress, $subject, $text, $from);
        }
    }
?>