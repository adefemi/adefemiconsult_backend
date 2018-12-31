<?php
    class MessageURL{
        function __construct($URI)
        {
            $fullpath = implode('/', $URI);
            $this->url = $fullpath;
            $this->urlarray = $URI;

            if(count($URI)>3){
                echo json_encode(['error' => "endpoint not found!"]);
                http_response_code('404');
                return;
            }
            $this->verifyURI();
        }
        function verifyURI(){
            require __DIR__.'/controller.php';
            $messageControl = new MessageControl();
            $method = $_SERVER['REQUEST_METHOD'];
            $override = null;
            isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? $override = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : null;
            if($override === "PUT" || $method == "PUT"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error' => "an id value is required"]);
                    http_response_code('400');
                    return;
                }
                elseif (count($this->urlarray) === 2){
                    $messageControl->update($this->urlarray[1]);
                }
                else{
                    echo json_encode(['error' => "endpoint not found!"]);
                    http_response_code('404');
                    return;
                }
            }
            elseif($method === "GET"){
                if(count($this->urlarray) === 1){
                    $messageControl->get();
                }
                else{
                    echo json_encode(['error' => "endpoint not found!"]);
                    http_response_code('404');
                    return;
                }

            }
            elseif($method === "POST"){
                if(count($this->urlarray) === 1){
                    $messageControl->post();
                }
                else{
                    echo json_encode(['error' => "endpoint not found!"]);
                    http_response_code('404');
                    return;

                }
            }
            elseif($method === "DELETE"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error' => "an id value is required"]);
                    http_response_code('400');
                    return;
                }
                elseif (count($this->urlarray) === 2){
                    $messageControl->delete($this->urlarray[1]);
                }
                else{
                    echo json_encode(['error' => "endpoint not found!"]);
                    http_response_code('404');
                    return;
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