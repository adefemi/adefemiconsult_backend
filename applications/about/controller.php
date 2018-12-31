<?php
require_once __DIR__.'/model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/verifier.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/fileupload.php';
class AboutControl{
    function __construct()
    {
        $aboutModel = new AboutModel();
        $this->tablename = $aboutModel->about;
        $this->tabledata = $aboutModel->aboutData;
        $this->verifier = new Verifier();
    }

    function get(){
        $sql = "SELECT * FROM $this->tablename";
        $connection = new Connection();
        $conn = $connection->connent();
        $result = $conn->query($sql);
        $queryContent = [];
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                foreach ($this->tabledata as $data){
                    $queryContent[$data] = $row[$data];
                }
            }
        } else {
            echo "0 results found";
        }
        echo json_encode($queryContent);
        http_response_code('200');
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

        $fulname = ""; $statement= ""; $designation ="";

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $fulname = $row['fulname']; $statement = $row['statement'];
            $designation = $row['designation'];
        }



        isset($_REQUEST['fulname']) ? $fulname = $_REQUEST['fulname'] : null;
        isset($_REQUEST['statement']) ? $statement = addslashes($_REQUEST['statement']) : null;
        isset($_REQUEST['designation']) ? $designation = $_REQUEST['designation'] : null;

        $timestamp = time();

        $sql = "UPDATE $this->tablename SET fulname='$fulname', statement='$statement', designation='$designation', updated_on='$timestamp' where id = $id";

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

        if(!isset($_FILES['coverpic']) || $_FILES['coverpic'] === null){
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

        if(!isset($_FILES['resume']) || $_FILES['resume'] === null){
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


        $resume = "";

        $fileuploader = new FileUpload('resume');
        $fileuploaded = $fileuploader->fileUpload();



        if($fileuploaded === 0){
            return;
        }

        $sql = "select * from $this->tablename where id = $id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $resume = $row['resume'];
        }

        $filenameToRemove = explode("/", $resume);
        $filenameToRemove = $filenameToRemove[count($filenameToRemove)-1];
        $fileuploader->removeFile($filenameToRemove);

        $resume = $fileuploaded;



        $timestamp = time();

        $sql = "UPDATE $this->tablename SET resume='$resume', updated_on='$timestamp' where id = $id";

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
}
?>