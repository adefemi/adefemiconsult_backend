<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/fileupload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/verifier.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/user/model.php';

class PasswordChangeControl{
    function __construct()
    {
        $userModel = new UserModel();
        $this->tablename = $userModel->user;
        $this->tabledata = $userModel->userData;
        $this->verifier = new Verifier();
    }

    function update($id){

        if(!$this->verifier->verify($id, true)){
            return;
        }

        $connection = new Connection();
        $conn = $connection->connent();

        //check if id exist

        $sql = "select ID from $this->tablename where id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows < 1) {
            echo json_encode(['error' => "id not found!"]);
            http_response_code('400');
            $conn->close();
            return;
        }
        $misc = new Miscellaneous();
        $password = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $password = $row['password'];
        }


        if(!$this->validatePassword($_REQUEST['old_password'], $_REQUEST['new_password'], $_REQUEST['new_password'], $password)) return;

        $newpassword = md5($_REQUEST['new_password']);
        $timestamp = time();
        $secret_key = $misc->generateRandomString(20).'-'.$_REQUEST['new_password'].'-'.$misc->generateRandomString(34);
        $sql = "UPDATE $this->tablename SET secret_key='$secret_key', password='$newpassword', updated_on='$timestamp' where id = $id";

        if($conn->query($sql) === TRUE){
            echo json_encode(['success' => 'password successful updated']);
            http_response_code(200);
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }

    function validatePassword($oldpass, $newpass, $cpass, $mainpass){
        if($oldpass == "" || $newpass == '' || $cpass == ''){
            echo json_encode(['error' => "Password fields are required!"]);
            http_response_code('400');
            return false;
        }
        if(md5($oldpass) !== $mainpass){
            echo json_encode(['error' => "Your old password is not matching the existing one!"]);
            http_response_code('400');
            return false;
        };
        if($newpass !== $cpass){
            echo json_encode(['error' => "New and comfirm passwords don't match!"]);
            http_response_code('400');
            return false;
        };

        return true;
    }
}
?>