<?php
    require __DIR__.'/MailSetup.php';
    require __DIR__ .'/EnquiryMail.php';

    class SendMail{
        function __construct()
        {

        }

        function SendEnquiry($fullname, $email, $telephone, $message){
            $VMailer = new EnquiryMail($fullname, $email, $telephone, $message);
            $mailer = new MailSetup($VMailer->Subject(), $VMailer->htmlBody(), 1);
            $mail = $mailer->setUpMailer();
            return $mail;
        }
    }

?>