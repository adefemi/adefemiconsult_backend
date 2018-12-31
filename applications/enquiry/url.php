<?php
    class EnquiryURL{
        function __construct($URI)
        {
            $fullpath = implode('/', $URI);
            $this->url = $fullpath;
            $this->urlarray = $URI;

            if(count($URI)>2){
                echo json_encode(['error' => "endpoint not found!"]);
                http_response_code('404');
                return;
            }
            $this->verifyURI();
        }
        function verifyURI(){
            require __DIR__.'/controller.php';
            $urlControl = new EnquiryControl();
            $method = $_SERVER['REQUEST_METHOD'];

            if($method === "POST"){
                $urlControl->SendEnquiry();
            }
            elseif ($method === "OPTIONS"){
                echo json_encode('success');
            }
            else{
                echo json_encode("method not allowed!");
                http_response_code('400');
                return;
            }


        }
    }