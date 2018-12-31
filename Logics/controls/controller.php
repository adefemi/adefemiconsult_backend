<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/user/model.php';
    class controlsController{
        function __construct()
        {
            $userModel = new UserModel();
            $this->tablename = $userModel->user;
            $this->tabledata = $userModel->userData;
        }

        function createSuperUser(){
            parse_str($_SERVER['QUERY_STRING'], $_request);
            $arrayValue = [['string'=>'username'],['email'=>'email'],['string'=>'password'],['string'=>'c_password']];
            $validator = new Validator($arrayValue );
            $error = $validator->validatepost();
            if(count((array)$error) > 0){
                echo json_encode($error);
                http_response_code('400');
                return;
            }

            $username = $_request['username'];
            $email = $_request['email'];
            $password = md5($_request['password']);
            $c_password = md5($_request['c_password']);
            $is_superuser = 1;
            $is_staff = 1;
            $is_active = 1;

            if($password !== $c_password){
                echo json_encode(['error' => "Password does not match the Confirm password"]);
                http_response_code(400);
                return;
            }

            $misc = new Miscellaneous();
            $secret_key = $misc->generateRandomString(20).'-'.$_REQUEST['password'].'-'.$misc->generateRandomString(34);
            $timestamp = time();
            $connection = new Connection();
            $conn = $connection->connent();

            //check if superuser exist
            $sql = "select * from $this->tablename where is_superuser = 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo json_encode(['error' => "A superuser already exist, you need to remove that to add another"]);
                http_response_code(400);
                $conn->close();
                return;
            }
            $sql = "INSERT INTO $this->tablename (username, email, password, secret_key, is_superuser, is_staff, is_active, last_login, created_on, updated_on) VALUES ('$username','$email','$password','$secret_key', '$is_superuser','$is_staff','$is_active','$timestamp','$timestamp','$timestamp')";


            if($conn->query($sql)){
                echo "Superuser created successfully";
            }
            else{
                echo $conn->error;
            }

            $conn->close();


        }
    }

?>