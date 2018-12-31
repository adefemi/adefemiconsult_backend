<?php
 class FileUpload{
     function __construct($filename)
     {
        $this->filename = $filename;
        $this->timestamp = time();
     }

     function imageUpload(){
         $mainpath = 'upload/image/';
        $target_dir = $_SERVER['DOCUMENT_ROOT'].'/'.$mainpath;

        //check if path exist
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.'upload/image')){
         mkdir($_SERVER['DOCUMENT_ROOT'].'/'.'upload/image', 0777, true);
        }

        if(!isset($_FILES[$this->filename])){
            return 0;
        }

        $filename_paths = pathinfo($_FILES[$this->filename]["name"]);
        $realFileName = implode('.', [$filename_paths['filename'].$this->timestamp, $filename_paths['extension']]);
        $target_file = $target_dir . $realFileName;
        $imageFileType = $filename_paths['extension'];
        // Check if image file is a actual image or fake image
         //$check = getimagesize($_FILES[$this->filename]["tmp_name"]);
         if(!@is_array(getimagesize($_FILES[$this->filename]["tmp_name"]))){
             echo json_encode(['error' => 'File is either not an image or is bad']);
             http_response_code(400);
             return 0;
         }
        // Check file size
        if ($_FILES[$this->filename]["size"] > 5000000) {
            echo json_encode(['error' => 'Sorry, your file is too large. Max is 5MB']);
            http_response_code(400);
            return 0;
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
         && $imageFileType != "gif" ) {
            echo json_encode(['error' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.']);
            http_response_code(400);
            return 0;
        }
        // Check if $uploadOk is set to 0 by an error
         if(move_uploaded_file($_FILES[$this->filename]["tmp_name"], $target_file)){
             $serverPro = explode('/', $_SERVER['SERVER_PROTOCOL']);
             $serverPro = $serverPro[0];
             $serverPro = strtolower($serverPro)."://";
             $result = stripslashes($serverPro.$_SERVER['HTTP_HOST'].'/'.$mainpath.$realFileName);
             return $result;
         }
         else{
             echo json_encode(['error' => 'Error Uploading Image']);
             http_response_code(400);
             return 0;
         }
     }

     function fileUpload(){
         $mainpath = 'upload/';
         $target_dir = $_SERVER['DOCUMENT_ROOT'].'/'.$mainpath;

         if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.'upload')){
             mkdir($_SERVER['DOCUMENT_ROOT'].'/'.'upload', 0777, true);
         }

         if(!isset($_FILES[$this->filename])){
             return 0;
         }

         $filename_paths = pathinfo($_FILES[$this->filename]["name"]);
         $realFileName = implode('.', [$filename_paths['filename'].$this->timestamp, $filename_paths['extension']]);
         $target_file = $target_dir . $realFileName;
         $fileType = $filename_paths['extension'];

         if ($_FILES[$this->filename]["size"] > 8000000) {
             echo json_encode(['error' => 'Sorry, your file is too large. Max is 8MB']);
             http_response_code(400);
             return 0;
         }

         if(move_uploaded_file($_FILES[$this->filename]["tmp_name"], $target_file)){
             $serverPro = explode('/', $_SERVER['SERVER_PROTOCOL']);
             $serverPro = $serverPro[0];
             $serverPro = strtolower($serverPro)."://";
             $result = stripslashes($serverPro.$_SERVER['HTTP_HOST'].'/'.$mainpath.$realFileName);
             return $result;
         }
         else{
             echo json_encode(['error' => 'Error Uploading File']);
             http_response_code(400);
             return 0;
         }
     }

     function removeImage($filename){
         $mainpath = 'upload/image/';
         $target_dir = $_SERVER['DOCUMENT_ROOT'].'/'.$mainpath;
         if(file_exists($target_dir.$filename)){
             unlink($target_dir.$filename);
         }
         return;
     }

     function removeFile($filename){
         $mainpath = 'upload/';
         $target_dir = $_SERVER['DOCUMENT_ROOT'].'/'.$mainpath;
         if(file_exists($target_dir.$filename)){
             unlink($target_dir.$filename);
         }
         return;
     }
 }

?>