<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'Logics/auth/controller.php';
class Verifier{
    function __construct()
    {
        $this->misc = new Miscellaneous();
        $this->AuthController = new AuthController();
    }

    function verify($id, $authorization = null){
        if(!filter_var($id, FILTER_VALIDATE_INT)){
            echo json_encode(["error"=>"an id value is required"]);
            http_response_code('400');
            return false;
        }
        if($authorization){
            $bearer  = $this->misc->getBearerToken();
            if(empty($bearer)){
                echo json_encode(['error'=>"You are not authorized to perform this operation"]);
                http_response_code('401');
                return false;
            }

            if(!$this->AuthController->AuthVerifyToken($bearer)){
                return false;
            }

        }
        return true;
    }
}

?>