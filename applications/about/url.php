<?php
    class AboutURL{
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
            $urlControl = new AboutControl();
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
                $urlControl->get();
            }
            elseif ($method === "POST"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error' => "an id value is required"]);
                    http_response_code('400');
                    return;
                }
                else{
                    if(!isset($_SERVER['QUERY_STRING'])){
                        $urlControl->updatePic($this->urlarray[1]);
                    }
                    else{
                        parse_str($_SERVER['QUERY_STRING'], $query);
                        $query['type'] === 'file' ? $urlControl->updateFile($this->urlarray[1]) : $urlControl->updatePic($this->urlarray[1]);
                    }
                }
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
?>