<?php
    class ServiceURL{
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
            $urlControl = new ServiceControl();
            $method = $_SERVER['REQUEST_METHOD'];
            $override = null;
            isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? $override = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : null;
            if($override === "PUT" || $method == "PUT"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error' => "an id value is required"]);
                    http_response_code('400');
                    return;
                }
                else{
                    $urlControl->update($this->urlarray[1]);
                }
            }
            elseif($method === "GET"){
                if(count($this->urlarray) > 1){
                    $urlControl->get($this->urlarray[1]);
                }
                else{
                    $urlControl->get();
                }

            }
            elseif($method === "POST"){
                if(count($this->urlarray) > 1){
                    $urlControl->updatePic($this->urlarray[1]);
                }
                else{
                    $urlControl->post();
                }
            }
            elseif($method === "DELETE"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error' => "an id value is required"]);
                    http_response_code('400');
                    return;
                }
                else{
                    $urlControl->delete($this->urlarray[1]);
                }
            }
            elseif ($method === "OPTIONS"){
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