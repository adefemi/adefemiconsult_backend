<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/fileupload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/verifier.php';
require_once __DIR__.'/model.php';

class TeamControl{
    function __construct()
    {
        $teamModel = new TeamModel();
        $this->tablename = $teamModel->team;
        $this->tabledata = $teamModel->teamData;
        $this->verifier = new Verifier();
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
        $arrayValue = [['string'=>'fulname'],['string'=>'designation'],['file'=>'coverpic']];
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

        $fulname = $_REQUEST['fulname'];
        $designation = addslashes($_REQUEST['designation']);
        $coverpic = $result;

        isset($_REQUEST['facebook']) ? $facebook = addslashes($_REQUEST['facebook']) : $facebook = "null";
        isset($_REQUEST['instagram']) ? $instagram = addslashes($_REQUEST['instagram']) : $instagram = "null";
        isset($_REQUEST['twitter']) ? $twitter = addslashes($_REQUEST['twitter']) : $twitter = "null";
        isset($_REQUEST['google']) ? $google = addslashes($_REQUEST['google']) : $google = "null";
        isset($_REQUEST['linkedin']) ? $linkedin = addslashes($_REQUEST['linkedin']) : $linkedin = "null";


        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (fulname, designation, coverpic, facebook, instagram, twitter, google,linkedin, created_on, updated_on)".
                "VALUES ('$fulname','$designation','$coverpic','$facebook','$instagram','$twitter','$google','$linkedin','$timestamp','$timestamp')";
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

        $fulname = ""; $designation= "";$facebook= "";$instagram= "";$twitter= "";$google= "";$linkedin= "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $fulname = $row['fulname']; $designation= $row['designation'];$facebook= $row['facebook'];
            $instagram= $row['instagram'];$twitter= $row['twitter'];$google= $row['google'];$linkedin= $row['linkedin'];
        }


        isset($_REQUEST['fulname']) ? $fulname = $_REQUEST['fulname'] : null;
        isset($_REQUEST['designation']) ? $designation = addslashes($_REQUEST['designation']) : null;
        isset($_REQUEST['facebook']) ? $facebook = addslashes($_REQUEST['facebook']) : $facebook = "null";
        isset($_REQUEST['instagram']) ? $instagram = addslashes($_REQUEST['instagram']) : $instagram = "null";
        isset($_REQUEST['twitter']) ? $twitter = addslashes($_REQUEST['twitter']) : $twitter = "null";
        isset($_REQUEST['google']) ? $google = addslashes($_REQUEST['google']) : $google = "null";
        isset($_REQUEST['linkedin']) ? $linkedin = addslashes($_REQUEST['linkedin']) : $linkedin = "null";

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET fulname='$fulname', designation='$designation',
facebook='$facebook', instagram='$instagram', twitter='$twitter',google='$google',linkedin='$linkedin',updated_on='$timestamp' where id = $id";

        if($conn->query($sql)){
            $this->get($id);
        }
        else{
            echo json_encode(['error'=>$conn->error]);
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
            echo json_encode(['error' =>$conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }
}
?>