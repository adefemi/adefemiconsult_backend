<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/fileupload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/verifier.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
require_once __DIR__.'/model.php';

class CourseControl{
    function __construct()
    {
        $courseModel = new CourseModel();
        $this->tablename = $courseModel->course;
        $this->tabledata = $courseModel->courseData;
        $this->verifier = new Verifier();
        $this->misc = new Miscellaneous();
    }

    function get($id = null){
        $connection = new Connection();
        $conn = $connection->connent();
        if($id != null && $id != "last"){
            if(!$this->verifier->verify($id)){
                return;
            }
            $sql = "SELECT * FROM $this->tablename WHERE id = $id";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    foreach ($this->tabledata as $data){
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
                    foreach ($this->tabledata as $data){
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
        $arrayValue = [['string'=>'title'],['string'=>'detail'],['file'=>'coverpic']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }

        $fileuploader = new FileUpload('coverpic');
        $result = $fileuploader->imageUpload();

        if($result === 0){
            return;
        }

        $title = addslashes($_REQUEST['title']);
        $detail = addslashes( $_REQUEST['detail']);
        $coverpic = $result;
        $slug = $this->misc->slugify($title).$this->misc->generateRandomString(15);
        $timestamp = time();
        $uuid = "\"".$this->misc->gen_uuid()."\"";
        $sql = "INSERT INTO $this->tablename (title, detail, coverpic, slug, uuid,created_on, updated_on) 
VALUES ('$title','$detail','$coverpic', '$slug','$uuid','$timestamp','$timestamp')";
        $connection = new Connection();
        $conn = $connection->connent();

        if($conn->query($sql)){
            $this->get('last');
        }
        else{
            $fileuploader->removeImage($result);
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }

        $conn->close();
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

        $title = ""; $detail= ""; $slug="";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $title = $row['title']; $detail = $row['detail']; $slug = $row['slug'];
        }

        if(isset($_REQUEST['title'])){
            $title = $_REQUEST['title'];

            $slug = $this->misc->slugify($title).$this->misc->generateRandomString(15);

        }
        isset($_REQUEST['detail']) ? $detail = addslashes($_REQUEST['detail']) : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET title='$title', detail='$detail', slug='$slug',  updated_on='$timestamp' where id = $id";

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

        $sql = "select ID from $this->tablename where id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows < 1) {
            echo json_encode(['error' => "id not found!"]);
            http_response_code('400');
            $conn->close();
            return;
        }

        $coverpic = "";

        $fileuploader = new FileUpload('coverpic');
        $fileuploaded = $fileuploader->imageUpload();

        if($fileuploaded === 0){
            return;
        }

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $coverpic = $row['coverpic'];
        }

        $filenameToRemove = explode("/", $coverpic);
        $filenameToRemove = $filenameToRemove[count($filenameToRemove)-1];
        $fileuploader->removeImage($filenameToRemove);

        $coverpic = $fileuploaded;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET coverpic='$coverpic', updated_on='$timestamp' where id = $id";


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

        $fileuploader = new FileUpload('coverpic');
        $coverpic = "";
        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $coverpic = $row['coverpic'];
        }

        $filenameToRemove = explode("/", $coverpic);
        $filenameToRemove = $filenameToRemove[count($filenameToRemove)-1];

        $sql = "DELETE from $this->tablename where id = $id";


        if($conn->query($sql) === TRUE){
            $fileuploader->removeImage($filenameToRemove);
            $this->get();
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }
}
class CourseNotificationControl{
    function __construct()
    {
        $courseModel = new CourseModel();
        $this->tablename = $courseModel->courseNotification;
        $this->tabledata = $courseModel->courseNotificationData;
        $this->verifier = new Verifier();
        $this->misc = new Miscellaneous();
    }

    function get($id = null){
        $connection = new Connection();
        $conn = $connection->connent();
        if($id != null && $id != "last"){
            if(!$this->verifier->verify($id)){
                return;
            }
            $sql = "SELECT * FROM $this->tablename WHERE id = $id";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    foreach ($this->tabledata as $data){
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
                    foreach ($this->tabledata as $data){
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
        $arrayValue = [['string'=>'notification'],['string'=>'userID'],['string'=>'courseID'],['string'=>'type']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }

        $notification = addslashes($_REQUEST['notification']);
        $userID = addslashes($_REQUEST['userID']);
        $courseID = addslashes($_REQUEST['courseID']);
        $type = addslashes($_REQUEST['type']);
        $timestamp = time();
        $uuid = "\"".$this->misc->gen_uuid()."\"";
        $sql = "INSERT INTO $this->tablename (notification,userID,courseID,`type`, uuid, created_on, updated_on) 
              VALUES ('$notification','$userID','$courseID','$type','$uuid','$timestamp','$timestamp')";
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

    function update($id){

        if(!$this->verifier->verify($id)){
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

        $notification = "";$userID = "";$courseID = "";$type = "";$seen = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $notification = $row['notification']; $userID = $row['userID']; $courseID = $row['courseID'];
            $type = $row['type'];$seen = $row['seen'];
        }

        isset($_REQUEST['notification']) ? $notification = addslashes($_REQUEST['notification']) : null;
        isset($_REQUEST['userID']) ? $userID = addslashes($_REQUEST['userID']) : null;
        isset($_REQUEST['courseID']) ? $courseID = addslashes($_REQUEST['courseID']) : null;
        isset($_REQUEST['type']) ? $type = addslashes($_REQUEST['type']) : null;
        isset($_REQUEST['seen']) ? $seen = addslashes($_REQUEST['seen']) : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET notification='$notification',userID='$userID',courseID='$courseID',
          `type`='$type', seen='$seen',  updated_on='$timestamp' where id = $id";

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

        $fileuploader = new FileUpload('coverpic');
        $coverpic = "";
        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $coverpic = $row['coverpic'];
        }

        $filenameToRemove = explode("/", $coverpic);
        $filenameToRemove = $filenameToRemove[count($filenameToRemove)-1];

        $sql = "DELETE from $this->tablename where id = $id";


        if($conn->query($sql) === TRUE){
            $fileuploader->removeImage($filenameToRemove);
            $this->get();
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }
}
class CourseRegisterControl{
    function __construct()
    {
        $courseModel = new CourseModel();
        $this->tablename = $courseModel->courseRegister;
        $this->tabledata = $courseModel->courseRegisterData;
        $this->verifier = new Verifier();
        $this->misc = new Miscellaneous();
    }

    function get($id = null){
        $connection = new Connection();
        $conn = $connection->connent();
        if($id != null && $id != "last"){
            if(!$this->verifier->verify($id)){
                return;
            }
            $sql = "SELECT * FROM $this->tablename WHERE id = $id";
            $result = $conn->query($sql);
            $queryContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    foreach ($this->tabledata as $data){
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
                    foreach ($this->tabledata as $data){
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
        $arrayValue = [['string'=>'userID'],['string'=>'courseID']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }

        $userID = addslashes($_REQUEST['userID']);
        $courseID = addslashes($_REQUEST['courseID']);
        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (userID,courseID,created_on, updated_on) VALUES
                ('$userID','$courseID','$timestamp','$timestamp')";
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

    function update($id){

        if(!$this->verifier->verify($id)){
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

        $userID = "";$courseID = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $userID = $row['userID']; $courseID = $row['courseID'];
        }

        isset($_REQUEST['userID']) ? $userID = addslashes($_REQUEST['userID']) : null;
        isset($_REQUEST['courseID']) ? $courseID = addslashes($_REQUEST['courseID']) : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET userID='$userID',courseID='$courseID', updated_on='$timestamp' where id = $id";

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

        $fileuploader = new FileUpload('coverpic');
        $coverpic = "";
        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $coverpic = $row['coverpic'];
        }

        $filenameToRemove = explode("/", $coverpic);
        $filenameToRemove = $filenameToRemove[count($filenameToRemove)-1];

        $sql = "DELETE from $this->tablename where id = $id";


        if($conn->query($sql) === TRUE){
            $fileuploader->removeImage($filenameToRemove);
            $this->get();
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }
}
?>