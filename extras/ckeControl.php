<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/fileupload.php';

class CKEController{
    function __construct()
    {
        if($_SERVER['REQUEST_METHOD'] != "POST"){
            echo json_encode(['data' => 'success']);
            http_response_code(200);
            return;
        }


        $fileuploader = new FileUpload('upload');
        $result = $fileuploader->imageUpload();
        $urlMain = '\''.$result.'\'';
        $urlStrong = $result;

        if($result === 0){
            echo json_encode(['error' => 'unable to upload image']);
            http_response_code(400);
            return;
        }

        $filename = explode('/', $result);
        $filename = $filename[count($filename) - 1];

        if(!isset($_SERVER['QUERY_STRING'])){
            echo json_encode(["fileName"=>$filename,
                "uploaded"=>1,
                "url"=>$result,
            ]);
        }
        else{
            parse_str($_SERVER['QUERY_STRING'], $result);
            if(isset($result['type']) && $result['type'] === "cke"){
                $funcNum = $_REQUEST['CKEditorFuncNum'] ;
                echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum,$urlMain,'success');</script>";
            }
            else{
                echo json_encode(["link" => $urlStrong]);
            }
        }


    }
}

?>