<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/fileupload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/verifier.php';
require_once __DIR__.'/model.php';

class UserControl{
    function __construct()
    {
        $userModel = new UserModel();
        $this->tablename = $userModel->user;
        $this->userData = $userModel->userData;
        $this->userProfile = $userModel->userProfile;
        $this->verifier = new Verifier();
        $this->misc = new Miscellaneous();
    }

    function get($id = null, $returnType=null){
        $connection = new Connection();
        $conn = $connection->connent();
        if($id != null && $id != "last"){
            if(!$this->verifier->verify($id)){
                return null;
            }
            $sql = "SELECT * FROM $this->tablename WHERE id = $id";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    foreach ($this->userData as $data){
                        $queryContent[$data] = $row[$data];
                    }
                }
                if($returnType != null){
                    $conn->close();
                    return $queryContent;
                }
                echo json_encode($queryContent);
                http_response_code('200');

            }
            else {
                if($returnType != null){
                    $conn->close();
                    return $queryContent;
                }
                echo json_encode($queryContent);
                http_response_code('200');

            }
            $conn->close();
        }
        else{
            $queryContents = array();
            $sql = "SELECT * FROM $this->tablename";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    foreach ($this->userData as $data){
                        $queryContent[$data] = $row[$data];
                    }
                    array_push($queryContents, $queryContent);
                }
                if($id === "last"){
                    if($returnType != null){
                        $conn->close();
                        return $queryContents[count($queryContents)-1];
                    }
                    echo json_encode($queryContents[count($queryContents)-1]);
                }
                else{
                    if($returnType != null){
                        $conn->close();
                        return $queryContents;
                    }
                    echo json_encode($queryContents);
                }
                http_response_code('200');
            }
            else {
                if($returnType != null){
                    $conn->close();
                    return $queryContents;
                }
                echo json_encode($queryContents);
                http_response_code('200');
            }
            $conn->close();
        }

        return 0;
    }

    function post(){
        $arrayValue = [['string'=>'username'],['email'=>'email'],['string'=>'password']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }


        $username = $_REQUEST['username'];
        $email = $_REQUEST['email'];
        $password = md5($_REQUEST['password']);
        $is_superuser = 0;
        $is_staff = 0;
        $is_active = 1;

        isset($_REQUEST['is_staff']) ? $is_staff = (int)$_REQUEST['is_staff'] : null;
        isset($_REQUEST['is_active']) ? $is_active = (int)$_REQUEST['is_active'] : null;

        $uuid = "\"".$this->misc->gen_uuid()."\"";

        $misc = new Miscellaneous();
        $secret_key = $misc->generateRandomString(20).'-'.$_REQUEST['password'].'-'.$misc->generateRandomString(34);
        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (username, email, password, secret_key, is_superuser, is_staff, is_active, uuid, last_login, created_on, updated_on) 
VALUES ('$username','$email','$password','$secret_key', '$is_superuser','$is_staff','$is_active','$uuid','$timestamp','$timestamp','$timestamp')";
        $connection = new Connection();
        $conn = $connection->connent();

        if($conn->query($sql)){
            $regUser = $this->get('last', 1);
            $setUpDep = $this->setUpDependencies($regUser['id']);
            if($setUpDep){
                echo json_encode($regUser);
            }
            else{
                $this->delete($regUser['id']);
            }
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }

        $conn->close();
    }

    function update($id){

        if(!$this->verifier->verify($id, true)){
            return;
        }

        if(isset($_REQUEST['email'])){
            if (!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)){
                echo json_encode(['error' => "Email address not valid"]);
                http_response_code('400');
                return;
            }
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

        $username = ""; $email= ""; $is_staff = 0; $is_active = 1;

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $username = $row['username']; $email = $row['email']; $is_staff = $row['is_staff']; $is_active = $row['is_active'];
        }

        isset($_REQUEST['username']) ? $username = $_REQUEST['username'] : null;
        isset($_REQUEST['email']) ? $email = $_REQUEST['email'] : null;
        isset($_REQUEST['is_staff']) ? $is_staff = (int)$_REQUEST['is_staff'] : null;
        isset($_REQUEST['is_active']) ? $is_active = (int)$_REQUEST['is_active'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET username='$username', email='$email', is_staff='$is_staff', is_active='$is_active', updated_on='$timestamp', last_login='$timestamp' where id = $id";

        if($conn->query($sql) === TRUE){
            $this->get($id);
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }

    function delete($id){

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

        $sql = "DELETE from $this->tablename where id = $id";


        if($conn->query($sql) === TRUE){
            $this->get();
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }

    function setUpDependencies($id){
        if(!$this->verifier->verify($id)){
            return null;
        }
        $timestamp = time();
        $serverPro = explode('/', $_SERVER['SERVER_PROTOCOL']);
        $serverPro = $serverPro[0];
        $serverPro = strtolower($serverPro)."://";
        $defaultcoverpics = stripslashes($serverPro.$_SERVER['HTTP_HOST'].'/noimage.jpg');
        $sql = "INSERT INTO $this->userProfile (user_id, fulname, country, profession, about, dob, coverpic, created_on, updated_on) VALUES ('$id','null','null','null','null','0','$defaultcoverpics','$timestamp','$timestamp')";
        $connection = new Connection();
        $conn = $connection->connent();
        if($conn->query($sql)){
            return true;
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
            return false;
        }
    }
}

class UserSearchControl{
    function __construct()
    {
        $userModel = new UserModel();
        $this->tablename = $userModel->userSearch;
        $this->userSearchData = $userModel->userSearchData;
        $this->verifier = new Verifier();
    }

    function get($id = null){
        $connection = new Connection();
        $conn = $connection->connent();
        if($id != null && $id != "last"){
            if(!$this->verifier->verify($id)){
                return;
            }
            $queryContents = array();
            $sql = "SELECT * FROM $this->tablename WHERE user_id = $id";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    foreach ($this->userSearchData as $data){
                        $queryContent[$data] = $row[$data];
                    }
                    array_push($queryContents, $queryContent);
                }
                echo json_encode($queryContents);
                http_response_code('200');

            } else {
                echo json_encode($queryContents);
                http_response_code('200');

            }
            $conn->close();
        }
        else{
            $queryContents = array();
            $sql = "SELECT * FROM $this->tablename";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    foreach ($this->userSearchData as $data){
                        $queryContent[$data] = $row[$data];
                    }
                    array_push($queryContents, $queryContent);
                }
                if($id === "last"){
                    echo json_encode($queryContents[count($queryContents)-1]);
                }
                else{
                    echo json_encode($queryContents);
                }
                http_response_code('200');
            } else {
                echo json_encode($queryContents);
                http_response_code('200');
            }
            $conn->close();
        }
    }

    function post(){
        if(!$this->verifier->verify(1, true)){
            return;
        }
        $arrayValue = [['string'=>'user_id'],['string'=>'content']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }


        $user_id = $_REQUEST['user_id'];
        $content = $_REQUEST['content'];

        $timestamp = time();

        $sql = "INSERT INTO $this->tablename (user_id, content, created_on) VALUES ('$user_id','$content','$timestamp')";
        $connection = new Connection();
        $conn = $connection->connent();

        if($conn->query($sql)){
            $this->get('last');
        }
        else{
            echo json_encode(['error' => $error]);
            http_response_code('400');
        }

        $conn->close();
    }

    function delete($id){
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

        $sql = "DELETE from $this->tablename where id = $id";


        if($conn->query($sql) === TRUE){
            $this->get();
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }
}

class UserLogControl{
    function __construct()
    {
        $userModel = new UserModel();
        $this->tablename = $userModel->userLog;
        $this->userLogData = $userModel->userLogData;
        $this->verifier = new Verifier();
    }

    function get($id = null){
        $connection = new Connection();
        $conn = $connection->connent();
        if($id != null && $id != "last"){
            if(!$this->verifier->verify($id)){
                return;
            }
            $queryContents = array();
            $sql = "SELECT * FROM $this->tablename WHERE user_id = $id";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    foreach ($this->userLogData as $data){
                        $queryContent[$data] = $row[$data];
                    }
                    array_push($queryContents, $queryContent);
                }
                echo json_encode($queryContents);
                http_response_code('200');

            } else {
                echo json_encode($queryContents);
                http_response_code('200');

            }
            $conn->close();
        }
        else{
            $queryContents = array();
            $sql = "SELECT * FROM $this->tablename";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    foreach ($this->userLogData as $data){
                        $queryContent[$data] = $row[$data];
                    }
                    array_push($queryContents, $queryContent);
                }
                if($id === "last"){
                    echo json_encode($queryContents[count($queryContents)-1]);
                }
                else{
                    echo json_encode($queryContents);
                }
                http_response_code('200');
            } else {
                echo json_encode($queryContents);
                http_response_code('200');
            }
            $conn->close();
        }
    }

    function post(){
        if(!$this->verifier->verify(1, true)){
            return;
        }
        $arrayValue = [['string'=>'user_id'],['string'=>'title'],['string'=>'description']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }


        $user_id = $_REQUEST['user_id'];
        $title = $_REQUEST['title'];
        $description = addslashes($_REQUEST['description']) ;

        $timestamp = time();

        $sql = "INSERT INTO $this->tablename (user_id, title, description, created_on) VALUES ('$user_id','$title', '$description', '$timestamp')";
        $connection = new Connection();
        $conn = $connection->connent();

        if($conn->query($sql)){
            $this->get('last');
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }

        $conn->close();
    }

    function delete($id){
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

        $sql = "DELETE from $this->tablename where id = $id";


        if($conn->query($sql) === TRUE){
            $this->get();
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }
}

class UserProfileControl{
    function __construct()
    {
        $userModel = new UserModel();
        $this->tablename = $userModel->userProfile;
        $this->userData = $userModel->userProfileData;
        $this->verifier = new Verifier();
    }

    function get($id = null){
        $connection = new Connection();
        $conn = $connection->connent();
        if($id != null && $id != "last"){
            if(!$this->verifier->verify($id)){
                return;
            }
            $sql = "SELECT * FROM $this->tablename WHERE user_id = $id";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    foreach ($this->userData as $data){
                        $queryContent[$data] = $row[$data];
                    }
                }
                echo json_encode($queryContent);
                http_response_code('200');

            } else {
                echo json_encode($queryContent);
                http_response_code('200');

            }
            $conn->close();
        }
        else{
            $queryContents = array();
            $sql = "SELECT * FROM $this->tablename";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    foreach ($this->userData as $data){
                        $queryContent[$data] = $row[$data];
                    }
                    array_push($queryContents, $queryContent);
                }
                if($id === "last"){
                    echo json_encode($queryContents[count($queryContents)-1]);
                }
                else{
                    echo json_encode($queryContents);
                }
                http_response_code('200');
            } else {
                echo json_encode($queryContents);
                http_response_code('200');
            }
            $conn->close();
        }
    }

    function update($id){
        if(!$this->verifier->verify($id, true)){
            return;
        }

        $connection = new Connection();
        $conn = $connection->connent();

        //check if id exist

        $sql = "select * from $this->tablename where user_id = 2";
        $result = $conn->query($sql);

        if ($result->num_rows < 1) {
            echo json_encode(['error'=>'id not found!']);
            $conn->close();
            return;
        }

        $fulname= ""; $country= "";$dob= "";$profession= "";$about= "";

        $sql = "select * from $this->tablename where user_id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $fulname= $row['fulname']; $country= $row['country'];$dob= $row['dob'];
            $profession= $row['profession'];$about= $row['about'];
        }

        isset($_REQUEST['fulname']) ? $fulname = $_REQUEST['fulname'] : null;
        isset($_REQUEST['country']) ? $country = $_REQUEST['country'] : null;
        isset($_REQUEST['dob']) ? $dob = $_REQUEST['dob'] : null;
        isset($_REQUEST['profession']) ? $profession = $_REQUEST['profession'] : null;
        isset($_REQUEST['about']) ? $about = addslashes($_REQUEST['about']) : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET fulname='$fulname', country='$country', dob='$dob', profession='$profession', about='$about', updated_on='$timestamp' where user_id = $id";

        if($conn->query($sql) === TRUE){
            $this->get($id);
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }

    function updatePic($id){
        if(!$this->verifier->verify($id, true)){
            return;
        }

        if(!isset($_FILES['coverpic'])){
            $this->get($id);
            return;
        }

        $connection = new Connection();
        $conn = $connection->connent();

        //check if id exist

        $sql = "select * from $this->tablename where user_id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows < 1) {
            echo json_encode(['error' => "id not found!"]);
            $conn->close();
            return;
        }

        $coverpic = "";

        $fileuploader = new FileUpload('coverpic');
        $fileuploaded = $fileuploader->imageUpload();

        if($fileuploaded === 0){
            return;
        }


        $sql = "select * from $this->tablename where user_id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $coverpic = $row['coverpic'];
        }

        $filenameToRemove = explode("/", $coverpic);
        $filenameToRemove = $filenameToRemove[count($filenameToRemove)-1];
        $fileuploader->removeImage($filenameToRemove);

        $coverpic = $fileuploaded;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET coverpic='$coverpic', updated_on='$timestamp' where user_id = $id";


        if($conn->query($sql) === TRUE){
            $this->get($id);
        }
        else{
            $fileuploader->removeImage($fileuploaded);
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }
}
?>