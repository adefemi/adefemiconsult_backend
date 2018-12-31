<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'mailing/sendMail.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php';

class EnquiryControl{
    function __construct()
    {
        $this->SendMail = new SendMail();
    }

    function SendEnquiry(){
        $arrayValue = [['string'=>'fullname'],['email'=>'email'],['string'=>'phone'],['string'=>'message']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }

        $fullname = addslashes($_REQUEST['fullname']);
        $email = addslashes( $_REQUEST['email']);
        $phone = addslashes( $_REQUEST['phone']);
        $message = addslashes( $_REQUEST['message']);

        $mail = $this->SendMail->SendEnquiry($fullname, $email, $phone, $message);

        if($mail === "sent"){
            echo json_encode(['success' => "message sent"]);
            http_response_code('200');
        }
        else{
            echo json_encode(['error' => $mail]);
            http_response_code('400');
        }

        return;
    }

}
?>