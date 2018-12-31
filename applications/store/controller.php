<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/fileupload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/verifier.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
require_once __DIR__.'/model.php';

class StoreControl{
    function __construct()
    {
        $storeModel = new StoreModel();
        $this->tablename = $storeModel->store;
        $this->tabledata = $storeModel->storeData;
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
                    foreach ($this->tabledata as $data){
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
                    foreach ($this->tabledata as $data){
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

        return null;
    }

    function post(){
        if(!$this->verifier->verify(1, true)){
            return;
        }

        $arrayValue = [
            ['string'=>'title'],
            ['string'=>'detail'],
            ['string'=>'type'],
            ['file'=>'coverpic'],
        ];

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

        $title = $_REQUEST['title'];
        $detail = addslashes($_REQUEST['detail']);
        $coverpic = $result;
        $type = stripslashes($_REQUEST['type']);
        $slug = $this->misc->slugify($title).$this->misc->generateRandomString(15);
        isset($_REQUEST['price']) ? $price = $_REQUEST['price'] : $price = "0.0";
        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (title, detail, price, `type`, slug, coverpic, file, created_on, updated_on) VALUES ('$title','$detail','$price', '$type','$slug','$coverpic','null','$timestamp','$timestamp')";
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

        $title = ""; $detail= ""; $price =""; $type = ""; $slug = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $title = $row['title']; $detail = $row['detail'];
            $price = $row['price']; $type = $row['type']; $slug = $row['slug'];
        }

        if(isset($_REQUEST['title'])){
            $title = $_REQUEST['title'];

            $slug = $this->misc->slugify($title).$this->misc->generateRandomString(15);
        }
        isset($_REQUEST['detail']) ? $detail = addslashes($_REQUEST['detail']) : null;
        isset($_REQUEST['price']) ? $price = $_REQUEST['price'] : null;
        isset($_REQUEST['type']) ? $type = $_REQUEST['type'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET title='$title', detail='$detail', price='$price', `type` = '$type', slug = '$slug', updated_on='$timestamp' where id = $id";

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

    function updateFile($id){
        if(!$this->verifier->verify($id, true)){
            return;
        }

        if(!isset($_FILES['file']) || $_FILES['file'] === null){
            $this->get();
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


        $file = "";

        $fileuploader = new FileUpload('file');
        $fileuploaded = $fileuploader->fileUpload();

        if($fileuploaded === 0){
            return;
        }

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $file = $row['file'];
        }

        $filenameToRemove = explode("/", $file);
        $filenameToRemove = $filenameToRemove[count($filenameToRemove)-1];
        $fileuploader->removeFile($filenameToRemove);

        $file = $fileuploaded;



        $timestamp = time();

        $sql = "UPDATE $this->tablename SET file='$file', updated_on='$timestamp' where id = $id";

        if($conn->query($sql) === TRUE){
            $this->get();
        }
        else{
            $fileuploader->removeFile($fileuploaded);
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

class StoreReactionControl{
    function __construct()
    {
        $storeModel = new StoreModel();
        $this->tablename = $storeModel->storeReaction;
        $this->tabledata = $storeModel->storeReactionData;
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
        $arrayValue = [
            ['string'=>'store_id'],
            ['string'=>'type'],
            ['string'=>'ip_address']
        ];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }

        $store_id = $_REQUEST['store_id'];
        $type = $_REQUEST['type'];
        $ip_address = $_REQUEST['ip_address'];
        isset($_REQUEST['user_id']) ? $user_id = $_REQUEST['user_id'] : $user_id = null;


        $timestamp = time();

        if($user_id === null){
            $sql = "INSERT INTO $this->tablename (store_id, `type`, ip_address, updated_on) VALUES ('$store_id','$type','$ip_address','$timestamp')";
        }
        else{
            $sql = "INSERT INTO $this->tablename (store_id, `type`, ip_address, user_id, updated_on) VALUES ('$store_id','$type','$ip_address','$user_id','$timestamp')";
        }

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

    function update($id){

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

        $type = ""; $ip_address= ""; $user_id ="";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $type = $row['type']; $ip_address= $row['ip_address']; $user_id =$row['user_id'];
        }

        isset($_REQUEST['type']) ? $type = $_REQUEST['type'] : null;
        isset($_REQUEST['ip_address']) ? $ip_address = $_REQUEST['ip_address'] : null;
        isset($_REQUEST['user_id']) ? $user_id = $_REQUEST['user_id'] : null;

        $timestamp = time();

        if($user_id === "null"){
            $sql = "UPDATE $this->tablename SET type='$type', ip_address='$ip_address',updated_on='$timestamp' where id = $id";
        }
        else{
            $sql = "UPDATE $this->tablename SET type='$type', ip_address='$ip_address', user_id='$user_id', updated_on='$timestamp' where id = $id";
        }

        if($conn->query($sql) === TRUE){
            $this->get();
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

class StoreDownloadControl{
    function __construct()
    {
        $storeModel = new StoreModel();
        $this->tablename = $storeModel->storeDownload;
        $this->tabledata = $storeModel->storeDownloadData;
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
            $sql = "SELECT * FROM $this->tablename WHERE store_id = $id";
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
        $arrayValue = [['string'=>'store_id'],['string'=>'ip_address']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }


        $store_id = $_REQUEST['store_id'];
        $ip_address = $_REQUEST['ip_address'];

        $timestamp = time();

        $sql = "INSERT INTO $this->tablename (store_id, ip_address, created_on) VALUES ('$store_id','$ip_address', '$timestamp')";
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
            echo json_encode($conn->error);
            http_response_code('400');
        }
        $conn->close();
    }
}

class StoreCaptureControl{
    function __construct()
    {
        $storeModel = new StoreModel();
        $this->tablename = $storeModel->storeCapture;
        $this->tabledata = $storeModel->storeCaptureData;
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
        $arrayValue = [['string'=>'store_id'],['string'=>'alt'],['file'=>'coverpic']];
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


        $store_id = $_REQUEST['store_id'];
        $alt = $_REQUEST['alt'];
        $coverpic = $result;

        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (store_id, alt, coverpic, created_on, updated_on) VALUES ('$store_id','$alt','$coverpic','$timestamp','$timestamp')";
        $connection = new Connection();
        $conn = $connection->connent();

        if($conn->query($sql)){
            $this->get('last');
        }
        else{
            $fileuploader->removeImage($result);
            echo json_encode(['error' => $error]);
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
            $conn->close();
            return;
        }

        $alt= ""; $store_id = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $alt= $row['alt']; $store_id = $row['store_id'];
        }

        isset($_REQUEST['alt']) ? $alt = $_REQUEST['alt'] : null;
        isset($_REQUEST['store_id']) ? $store_id = $_REQUEST['store_id'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET store_id='$store_id', alt='$alt', updated_on='$timestamp' where id = $id";

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

class StoreHighlightControl{
    function __construct()
    {
        $storeModel = new StoreModel();
        $this->tablename = $storeModel->storeHighlight;
        $this->tabledata = $storeModel->storeHighlightData;
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
            $sql = "SELECT * FROM $this->tablename WHERE id = $id";
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
        $arrayValue = [['string'=>'store_id'],['string'=>'comment'],['string'=>'name'],['string'=>'user_id']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }


        $store_id = $_REQUEST['store_id'];
        $comment = $_REQUEST['comment'];
        $name = $_REQUEST['name'];
        $user_id = $_REQUEST['user_id'];

        $timestamp = time();

        $sql = "INSERT INTO $this->tablename (store_id, comment, name, user_id, created_on) VALUES ('$store_id','$comment','$name','$user_id', '$timestamp')";
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

class StoreCategoryControl{
    function __construct()
    {
        $storeModel = new StoreModel();
        $this->tablename = $storeModel->storeCategory;
        $this->tabledata = $storeModel->storeCategoryData;
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
        $arrayValue = [['string'=>'title']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }

        $title = $_REQUEST['title'];
        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (title, created_on, updated_on) VALUES ('$title','$timestamp','$timestamp')";
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

        $title = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $title = $row['title'];
        }

        isset($_REQUEST['title']) ? $title = $_REQUEST['title'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET title='$title', updated_on='$timestamp' where id = $id";

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
}
?>