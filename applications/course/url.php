<?php
    class CourseURL{
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
            $courseControl = new CourseControl();
            $courseNotificationControl = new CourseNotificationControl();
            $courseRegisterControl = new CourseRegisterControl();
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
                    $courseControl->update($this->urlarray[1]);
                }
                else{
                    if($this->urlarray[1] === 'notification'){
                        $courseNotificationControl->update($this->urlarray[2]);
                    }
                    else if($this->urlarray[1] === 'register'){
                        $courseRegisterControl->update($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }
            }
            elseif($method === "GET"){
                if(count($this->urlarray) === 1){
                    $courseControl->get();
                }
                else{
                    if($this->urlarray[1] === 'notification'){
                        $courseNotificationControl->get();
                    }
                    else if($this->urlarray[1] === 'register'){
                        $courseRegisterControl->get();
                    }
                    else {
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }

            }
            elseif($method === "POST"){
                if(count($this->urlarray) === 1){
                    $courseControl->post();
                }
                else{
                    if($this->urlarray[1] === 'notification'){
                        $courseNotificationControl->post();
                    }
                    else if($this->urlarray[1] === 'register'){
                        $courseRegisterControl->post();
                    }
                    else {
                        $courseControl->updatePic($this->urlarray[1]);
                    }

                }
            }
            elseif($method === "DELETE"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error' => "an id value is required"]);
                    http_response_code('400');
                    return;
                }
                elseif (count($this->urlarray) === 2){
                    $courseControl->delete($this->urlarray[1]);
                }
                else{
                    if($this->urlarray[1] === 'notification'){
                        $courseNotificationControl->delete($this->urlarray[2]);
                    }
                    else if($this->urlarray[1] === 'register'){
                        $courseRegisterControl->delete($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
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