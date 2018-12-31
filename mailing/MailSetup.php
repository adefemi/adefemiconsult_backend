<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once $_SERVER['DOCUMENT_ROOT'].'/PHPMailer-master/src/PHPMailer.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/PHPMailer-master/src/Exception.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/PHPMailer-master/src/SMTP.php';

    class MailSetup{
        function __construct($subject, $bodyHTML, $reMail)
        {
            $this->fromName = "AdefemiConsult";
            $this->fromEmail = "contact_admin@adefemiconsult.com";
            $this->subject = $subject;
            $this->bodyHtml = $bodyHTML;
            $this->reciepientEmail = $reMail === 1 ? $this->fromEmail : $reMail;
        }

        function setUpMailer(){
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = 'adefemigreat1995@gmail.com';                 // SMTP username
                $mail->Password = 'adebayo1995';                           // SMTP password
                $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 587;                                    // TCP port to connect to

                //Recipients
                $mail->setFrom($this->fromEmail, $this->fromName);
                $mail->addAddress($this->reciepientEmail);     // Add a recipient

                //Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = $this->subject;
                $mail->Body    =  $this->bodyHtml;
                $mail->AltBody = strip_tags($this->bodyHtml);

                $mail->send();
                return "sent";
            } catch (Exception $e) {
                return $e;
            }

        }

    }

?>