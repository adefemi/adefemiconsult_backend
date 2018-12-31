<?php
    class ControlURL{
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
            $controlsControl = new controlsController();
            $method = $_SERVER['REQUEST_METHOD'];
            if($method == "GET"){
                if(count($this->urlarray)<2){
                    echo json_encode(['error' => "No logic defined"]);
                    http_response_code('400');
                    return;
                }
                elseif ($this->urlarray[1] == "createsuperuser"){
                    $controlsControl->createSuperUser();
                }
                else{
                    echo json_encode(['error' => "Logic not found!"]);
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