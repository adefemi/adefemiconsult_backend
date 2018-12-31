<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/fileupload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/verifier.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
require_once __DIR__.'/model.php';

class ImageControl{
    function __construct()
    {
        $pageSettingModel = new PageSettingModel();
        $this->tablename = $pageSettingModel->images;
        $this->tabledata = $pageSettingModel->imagesData;
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
        $arrayValue = [
            ['string'=>'title'],
            ['file'=>'coverpic'],
        ];
        $validator = new Validator($arrayValue);
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
        $coverpic = $result;

        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (title,coverpic,created_on,updated_on) VALUES ('$title','$coverpic','$timestamp','$timestamp')";
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

        $title = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $title = $row['title'];
        }

        isset($_REQUEST['title']) ? $title = addslashes($_REQUEST['title']) : null;


        $timestamp = time();

        $sql = "UPDATE $this->tablename SET title='$title', updated_on='$timestamp' where id = $id";

        if($conn->query($sql) === TRUE){
            $this->get();
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
            $this->get();
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

class SocialHandleControl{
    function __construct()
    {
        $pageSettingModel = new PageSettingModel();
        $this->tablename = $pageSettingModel->socialHandle;
        $this->tabledata = $pageSettingModel->socialHandleData;
        $this->verifier = new Verifier();
    }

    function get($id = null){
        $connection = new Connection();
        $conn = $connection->connent();
        if($id != null && $id != "last"){
            if(!$this->verifier->verify($id)){
                return;
            }
            if(isset($_SERVER['QUERY_STRING'])){
                $queryContents = array();
                $sql = "SELECT * FROM $this->tablename WHERE p_id = $id";
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
            else{
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

        $arrayValue = [
            ['string'=>'title'],
            ['string'=>'link'],
        ];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }

        $title = $_REQUEST['title'];
        $link = $_REQUEST['link'];

        $timestamp = time();

        $sql = "INSERT INTO $this->tablename (title,link, created_on, updated_on) VALUES ('$title','$link','$timestamp','$timestamp')";
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
        if(!filter_var($id, FILTER_VALIDATE_INT)){
            echo json_encode(['error' => "an id value is required"]);
            http_response_code('400');
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

        $title = ""; $link = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $title = $row['title'];$link = $row['link'];
        }

        isset($_REQUEST['title']) ? $title = $_REQUEST['title'] : null;
        isset($_REQUEST['link']) ? $link = $_REQUEST['link'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET title='$title', link='$link', updated_on='$timestamp' where id = $id";

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
        if(!filter_var($id, FILTER_VALIDATE_INT)){
            echo json_encode(['error' => "an id value is required"]);
            http_response_code('400');
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

class ContactInfoControl{
    function __construct()
    {
        $pageSettingModel = new PageSettingModel();
        $this->tablename = $pageSettingModel->contactInfo;
        $this->tabledata = $pageSettingModel->contactInfoData;
        $this->verifier = new Verifier();
    }

    function get($id = null){
        $connection = new Connection();
        $conn = $connection->connent();
        if($id != null && $id != "last"){
            if(!$this->verifier->verify($id)){
                return;
            }
            if(isset($_SERVER['QUERY_STRING'])){
                $queryContents = array();
                $sql = "SELECT * FROM $this->tablename WHERE p_id = $id";
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
            else{
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

    function update($id){
        if(!filter_var($id, FILTER_VALIDATE_INT)){
            echo json_encode(['error' => "an id value is required"]);
            http_response_code('400');
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
        $this->contactInfoData = ['id','description','email','email2','telephone','telephone2','location','copyright','created_on','updated_on'];


        $description = "";$email = ""; $email2 = "";$telephone = "";$telephone2 = "";$location = ""; $copyright = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $description = $row['description'];$email = $row['email']; $email2 = $row['email2'];
            $telephone = $row['telephone'];$telephone2 = $row['telephone2'];$location = $row['location'];
            $copyright = $row['copyright'];
        }

        isset($_REQUEST['description']) ? $description = addslashes($_REQUEST['description']) : null;
        isset($_REQUEST['email']) ? $email = addslashes($_REQUEST['email']) : null;
        isset($_REQUEST['email2']) ? $email2 = addslashes($_REQUEST['email2']) : null;
        isset($_REQUEST['telephone']) ? $telephone = addslashes($_REQUEST['telephone']) : null;
        isset($_REQUEST['telephone2']) ? $telephone2 = addslashes($_REQUEST['telephone2']) : null;
        isset($_REQUEST['location']) ? $location = addslashes($_REQUEST['location']) : null;
        isset($_REQUEST['copyright']) ? $copyright = addslashes($_REQUEST['copyright']) : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET description='$description',email='$email',email2='$email2',
telephone='$telephone',telephone2='$telephone2',location='$location',copyright='$copyright',
 updated_on='$timestamp' where id = $id";

        if($conn->query($sql) === TRUE){
            $this->get($id);
        }
        else{
            echo json_encode(['error' => $conn->error]);
            http_response_code('400');
        }
        $conn->close();
    }
}
?>