<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/'.'applications/user/model.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php');
    require_once ($_SERVER['DOCUMENT_ROOT'].'/'.'extras/my-jwt.php');

    class AuthController{

        function __construct()
        {
            $userModel = new UserModel();
            $this->usertable = $userModel->user;
        }

        function Authenticate(){
            $user_id = $this->loginInUser();

            if($user_id == null){
                return;
            }
            $access_exp = time()+(10*60*60);

            if(isset($_REQUEST['remember_me'])){
                $refresh_exp = time()+(3*24*60*60);
            }
            else{
                $refresh_exp = time()+(24*60*60);
            }

            $JWTController = new JWTController();

            $access_token = $JWTController->encode(['id' => $user_id, 'type' => 'access', 'exp' => $access_exp]);
            $refresh_token = $JWTController->encode(['id' => $user_id, 'type' => 'refresh', 'exp' => $refresh_exp]);


            echo json_encode(['access' => $access_token, 'refresh' => $refresh_token]);

        }

        function RefreshAuth(){
            $arrayValue = [['string'=>'refresh']];
            $validator = new Validator($arrayValue );
            $error = $validator->validatepost();
            if(count((array)$error) > 0){
                echo json_encode($error);
                http_response_code(400);
                return null;
            }

            $refresh = $_REQUEST['refresh'];
            $JWTController = new JWTController();
            $verification = $JWTController->verify($refresh);

            if(!$verification){
                echo json_encode(['error'=>'invalid token']);
                http_response_code(400);
                return;
            }

            $payload = $JWTController->decode($refresh);
            $newpayload = json_decode($payload);

            if($newpayload->type != "refresh"){
                echo json_encode(['error'=>'invalid token type. A refresh token is required!']);
                http_response_code(400);
                return;
            }

            if(time() > $newpayload->exp){
                echo json_encode(['error'=>'This token has expired, authenticate to get another.']);
                http_response_code(403);
                return;
            }

            $access_exp = time()+(10*60);

            $access_token = $JWTController->encode(['id' => $newpayload->id, 'type' => 'access', 'exp' => $access_exp]);

            echo json_encode(['access' => $access_token]);

        }

        function AuthVerifyToken($token){
            $JWTController = new JWTController();
            $verification = $JWTController->verify($token);

            if(!$verification){
                echo json_encode(['error'=>'invalid token']);
                http_response_code(400);
                return false;
            }

            $payload = $JWTController->decode($token);
            $newpayload = json_decode($payload);

            if($newpayload->type != "access"){
                echo json_encode(['error'=>'invalid token type. An access token is required!']);
                http_response_code('400');
                return false;
            }

            if(time() > $newpayload->exp){
                echo json_encode(['error'=>'This token has expired, authenticate to get another.']);
                http_response_code(401);
                return false;
            }

            return true;
        }

        function loginInUser(){
            $arrayValue = [['string'=>'username'],['string'=>'password']];
            $validator = new Validator($arrayValue );
            $error = $validator->validatepost();
            if(count((array)$error) > 0){
                echo json_encode($error);
                http_response_code(400);
                return null;
            }

            $username = $_REQUEST['username'];
            $password = md5($_REQUEST['password']);

            $sql = "select * from $this->usertable where username = '$username' and password = '$password'";

            $connection = new Connection();
            $conn = $connection->connent();
            $result = $conn->query($sql);

            $user_id = null;

            if(!$result){
                echo json_encode(['error' => "Invalid username or password"]);
                http_response_code(400);
                return null;
            }



            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $user_id = $row['id'];
                }

            }
            $conn->close();
            if($user_id == null){
                echo json_encode(['error' => "Invalid username or password"]);
                http_response_code(400);
            }
            return $user_id;
        }

    }

?>