<?php
    class AuthURL{
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
            $authControl = new AuthController();

            $method = $_SERVER['REQUEST_METHOD'];
            if($method == "POST"){
                if(count($this->urlarray) == 1){
                    $authControl->Authenticate();
                }
                elseif (count($this->urlarray) == 2 && $this->urlarray[1] == "refresh"){
                    $authControl->RefreshAuth();
                }
                else{
                    echo json_encode(['error' => "endpoint not found!"]);
                    http_response_code('404');
                    return;
                }
            }
            elseif ($method == "OPTIONS"){
                echo json_encode('success');
            }
            else{
                echo json_encode(['error' => "method not allowed!"]);
                http_response_code('400');
                return;
            }


        }
    }
?>