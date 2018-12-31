<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/validator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/fileupload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/verifier.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
require_once __DIR__.'/model.php';

class BlogControl{
    function __construct()
    {
        $blogModel = new BlogModel();
        $this->tablename = $blogModel->blog;
        $this->tabledata = $blogModel->blogData;
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

        $arrayValue = [['string'=>'type'],['string'=>'title'],['string'=>'detail'],['string'=>'genre'],['string'=>'tags'],['file'=>'coverpic']];
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

        $type = $_REQUEST['type'];
        $title = addslashes($_REQUEST['title']);
        $detail = addslashes( $_REQUEST['detail']);
        $genre = addslashes( $_REQUEST['genre']);
        $tags = addslashes( $_REQUEST['tags']);
        $coverpic = $result;
        isset($_REQUEST['publish']) ? $publish = (int)$_REQUEST['publish'] : $publish = 0;
        $slug = $this->misc->slugify($title).$this->misc->generateRandomString(15);
        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (`type`, coverpic,title, detail, genre,tags, slug,publish, created_on, updated_on) 
VALUES ('$type','$coverpic','$title','$detail','$genre','$tags', '$slug','$publish','$timestamp','$timestamp')";
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

        $type = "";$title = ""; $detail= ""; $genre = ""; $tags = ""; $publish = ""; $slug="";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $type = $row['type'];$title = $row['title']; $detail = addslashes($row['detail']);$genre = $row['genre']; $publish = $row['publish'];
            $slug = $row['slug'];$tags = $row['tags'];
        }

        if(isset($_REQUEST['title'])){
            $title = $_REQUEST['title'];

            $slug = $this->misc->slugify($title).$this->misc->generateRandomString(15);

        }
        isset($_REQUEST['type']) ? $type = $_REQUEST['type'] : null;
        isset($_REQUEST['detail']) ? $detail = addslashes($_REQUEST['detail']) : null;
        isset($_REQUEST['genre']) ? $genre = addslashes($_REQUEST['genre']) : null;
        isset($_REQUEST['tags']) ? $tags = addslashes($_REQUEST['tags']) : null;
        isset($_REQUEST['publish']) ? $publish = addslashes($_REQUEST['publish']) : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET `type`='$type',title='$title', detail='$detail', genre = '$genre', tags = '$tags', publish='$publish', slug='$slug',updated_on='$timestamp' where id = $id";

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

    function updateFile($id){


        if(!$this->verifier->verify($id, true)){
            return;
        }

        if(!isset($_FILES['video'])){
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

        $video = "";

        $fileuploader = new FileUpload('video');
        $fileuploaded = $fileuploader->fileUpload();

        if($fileuploaded === 0){
            return;
        }

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $video = $row['video'];
        }

        $filenameToRemove = explode("/", $video);
        $filenameToRemove = $filenameToRemove[count($filenameToRemove)-1];
        $fileuploader->removeFile($filenameToRemove);

        $video = $fileuploaded;



        $timestamp = time();

        $sql = "UPDATE $this->tablename SET video='$video', updated_on='$timestamp' where id = $id";


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

class BlogCommentControl{
    function __construct()
    {
        $blogModel = new BlogModel();
        $this->tablename = $blogModel->blogComment;
        $this->tabledata = $blogModel->blogCommentData;
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
        $type = 0;

        $arrayValue = [['string'=>'comment'],['string'=>'user_id']];

        isset($_REQUEST['type']) ? $type = (int)$_REQUEST['type'] : null;
        if($type > 0){
            array_push($arrayValue, ['string'=>'blog_comment_id']);
        }
        else{
            array_push($arrayValue, ['string'=>'blog_id']);
        }

        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }
        
        isset($_REQUEST['blog_id']) ? $blog_id = (int)$_REQUEST['blog_id'] : $blog_id = 1;
        isset($_REQUEST['blog_comment_id']) ? $blog_comment_id = (int)$_REQUEST['blog_comment_id'] : $blog_comment_id = null;
        $comment = addslashes( $_REQUEST['comment']);
        $user_id = $_REQUEST['user_id'];
        $timestamp = time();

        if($type > 0){
            $sql = "INSERT INTO $this->tablename (`type`,blog_id, blog_comment_id,comment, user_id, created_on, updated_on) VALUES 
('$type','$blog_id','$blog_comment_id','$comment','$user_id','$timestamp','$timestamp')";
        }
        else{
            $sql = "INSERT INTO $this->tablename (`type`,blog_id,comment, user_id, created_on, updated_on) VALUES 
('$type','$blog_id','$comment','$user_id','$timestamp','$timestamp')";
        }


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

        $type = ""; $blog_id = ""; $blog_comment_id = ""; $comment= ""; $user_id = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $type = $row['type']; $blog_id = $row['blog_id']; $comment = addslashes($row['comment']); $user_id = $row['user_id'];
            $blog_comment_id = $row['blog_comment_id'];
        }

        isset($_REQUEST['type']) ? $type = $_REQUEST['type'] : null;
        isset($_REQUEST['blog_id']) ? $blog_id = $_REQUEST['blog_id'] : null;
        isset($_REQUEST['blog_comment_id']) ? $blog_comment_id = $_REQUEST['blog_comment_id'] : null;
        isset($_REQUEST['comment']) ? $comment = addslashes($_REQUEST['comment']) : null;
        isset($_REQUEST['user_id']) ? $user_id = $_REQUEST['user_id'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET `type`='$type', blog_id='$blog_id', blog_comment_id='$blog_comment_id', 
comment='$comment', user_id = '$user_id', updated_on='$timestamp' where id = $id";

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

class BlogViewControl{
    function __construct()
    {
        $blogModel = new BlogModel();
        $this->tablename = $blogModel->blogView;
        $this->tabledata = $blogModel->blogViewData;
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
        $arrayValue = [['string'=>'blog_id'],['string'=>'ip']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }
        $blog_id = $_REQUEST['blog_id'];
        $ip = $_REQUEST['ip'];
        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (blog_id, ip, created_on) VALUES ('$blog_id','$ip','$timestamp')";
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

        $blog_id = ""; $ip="";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $blog_id = $row['blog_id']; $ip = $row['ip'];
        }

        isset($_REQUEST['blog_id']) ? $blog_id = $_REQUEST['blog_id'] : null;
        isset($_REQUEST['ip']) ? $ip = $_REQUEST['ip'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET blog_id='$blog_id', ip='$ip', updated_on='$timestamp' where id = $id";


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

class BlogTagControl{
    function __construct()
    {
        $blogModel = new BlogModel();
        $this->tablename = $blogModel->blogTag;
        $this->tabledata = $blogModel->blogTagData;
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

class BlogGenreControl{
    function __construct()
    {
        $blogModel = new BlogModel();
        $this->tablename = $blogModel->blogGenre;
        $this->tabledata = $blogModel->blogGenreData;
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
        $arrayValue = [['string'=>'title'], ['string'=>'type']];
        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }
        $title = $_REQUEST['title'];
        $type = $_REQUEST['type'];
        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (title,`type`, created_on, updated_on) VALUES ('$title','$type','$timestamp','$timestamp')";
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

        $title = "";$type= "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $title = $row['title'];
            $type = $row['type'];
        }

        isset($_REQUEST['title']) ? $title = $_REQUEST['title'] : null;
        isset($_REQUEST['type']) ? $type = $_REQUEST['type'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET title='$title',`type`='$type', updated_on='$timestamp' where id = $id";


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

class BlogReactionControl{
    function __construct()
    {
        $blogModel = new BlogModel();
        $this->tablename = $blogModel->blogReaction;
        $this->tabledata = $blogModel->blogReactionData;
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

        $arrayValue = [['string'=>'blog_id'],['string'=>'value'],['string'=>'user_id']];

        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }

        isset($_REQUEST['blog_id']) ? $blog_id = (int)$_REQUEST['blog_id'] : $blog_id = 1;
        $value = addslashes( $_REQUEST['value']);
        $user_id = $_REQUEST['user_id'];
        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (blog_id, `value`, user_id, created_on, updated_on) VALUES 
('$blog_id','$value','$user_id','$timestamp','$timestamp')";
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

        $blog_id = ""; $value= ""; $user_id = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $blog_id = $row['blog_id']; $value = $row['value']; $user_id = $row['user_id'];
        }

        isset($_REQUEST['blog_id']) ? $blog_id = $_REQUEST['blog_id'] : null;
        isset($_REQUEST['value']) ? $value = $_REQUEST['value'] : null;
        isset($_REQUEST['user_id']) ? $user_id = $_REQUEST['user_id'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET blog_id='$blog_id', `value`='$value', user_id = '$user_id', updated_on='$timestamp' where id = $id";

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

class BlogCommentReactionControl{
    function __construct()
    {
        $blogModel = new BlogModel();
        $this->tablename = $blogModel->blogCommentReaction;
        $this->tabledata = $blogModel->blogCommentReactionData;
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

        $arrayValue = [['string'=>'blog_comment_id'],['string'=>'value'],['string'=>'user_id']];

        $validator = new Validator($arrayValue );
        $error = $validator->validatepost();
        if(count((array)$error) > 0){
            echo json_encode($error);
            http_response_code('400');
            return;
        }

        isset($_REQUEST['blog_comment_id']) ? $blog_comment_id = (int)$_REQUEST['blog_comment_id'] : $blog_comment_id = 1;
        $value = addslashes( $_REQUEST['value']);
        $user_id = $_REQUEST['user_id'];
        $timestamp = time();
        $sql = "INSERT INTO $this->tablename (blog_comment_id, `value`, user_id, created_on, updated_on) VALUES 
('$blog_comment_id','$value','$user_id','$timestamp','$timestamp')";
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

        $blog_comment_id = ""; $value= ""; $user_id = "";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $blog_comment_id = $row['blog_comment_id']; $value = $row['value']; $user_id = $row['user_id'];
        }

        isset($_REQUEST['blog_comment_id']) ? $blog_comment_id = $_REQUEST['blog_comment_id'] : null;
        isset($_REQUEST['value']) ? $value = $_REQUEST['value'] : null;
        isset($_REQUEST['user_id']) ? $user_id = $_REQUEST['user_id'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET blog_comment_id='$blog_comment_id', `value`='$value', user_id = '$user_id', updated_on='$timestamp' where id = $id";

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
?>