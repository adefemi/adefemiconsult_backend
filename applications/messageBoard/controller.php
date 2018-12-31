<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/fileupload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/verifier.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
require_once __DIR__.'/model.php';

class MessageControl{
    function __construct()
    {
        $messageModel = new MessageModel();
        $this->tablename = $messageModel->message;
        $this->tabledata = $messageModel->messageData;
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
        $arrayValue = [['string'=>'sender_id'],['string'=>'course_id'],['string'=>'receiver_id'],['string'=>'timeStamp'],['string'=>'detail']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }

        $course_id = addslashes($_REQUEST['course_id']);
        $sender_id = addslashes($_REQUEST['sender_id']);
        $receiver_id = addslashes($_REQUEST['receiver_id']);
        $timeStamp = addslashes($_REQUEST['timeStamp']);
        $detail = addslashes($_REQUEST['detail']);
        $timestamp = time();
        $uuid = "\"".$this->misc->gen_uuid()."\"";

        $sql = "INSERT INTO $this->tablename (course_id,sender_id,receiver_id,`timeStamp`,detail, uuid, created_on, updated_on) VALUES
 ('$course_id','$sender_id','$receiver_id','$timeStamp','$detail','$uuid','$timestamp','$timestamp')";
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

        $course_id = "";$sender_id = "";$receiver_id = "";$timeStamp = "";$detail = "";$seen ="";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $course_id = $row['course_id'];$sender_id = $row['sender_id'];$receiver_id = $row['receiver_id'];
            $detail = $row['detail'];$seen = $row['seen'];$timeStamp = $row['timeStamp'];
        }

        isset($_REQUEST['course_id']) ? $course_id = addslashes($_REQUEST['course_id']) : null;
        isset($_REQUEST['sender_id']) ? $sender_id = addslashes($_REQUEST['sender_id']) : null;
        isset($_REQUEST['receiver_id']) ? $receiver_id = addslashes($_REQUEST['receiver_id']) : null;
        isset($_REQUEST['detail']) ? $detail = addslashes($_REQUEST['detail']) : null;
        isset($_REQUEST['seen']) ? $seen = addslashes($_REQUEST['seen']) : null;
        isset($_REQUEST['timeStamp']) ? $timeStamp = addslashes($_REQUEST['timeStamp']) : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET course_id='$course_id',sender_id='$sender_id', receiver_id='$receiver_id',`timeStamp`='$timeStamp', detail='$detail',seen='$seen',  updated_on='$timestamp' where id = $id";

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