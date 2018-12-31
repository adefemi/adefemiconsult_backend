<?php
    class UserURL{
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
            $userUrlControl = new UserControl();
            $userSearchUrlControl = new UserSearchControl();
            $userLogUrlControl = new UserLogControl();
            $userProfileUrlControl = new UserProfileControl();
            $method = $_SERVER['REQUEST_METHOD'];
            $override = null;
            isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? $override = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : null;
            if($override == "PUT" || $method == "PUT"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error'=>"an id value is required"]);
                    http_response_code('400');
                    return;
                }
                elseif (count($this->urlarray) == 2){
                    if($this->urlarray[1] == 'log' || $this->urlarray[1] == 'search' || $this->urlarray[1] == 'profile'){
                        echo json_encode(['error'=>"an id value is required"]);
                        http_response_code('400');
                        return;
                    }
                    else{
                        $userUrlControl->update($this->urlarray[1]);
                    }
                }
                else{
                    if($this->urlarray[1] == 'profile'){
                        $userProfileUrlControl->update($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(["error"=>"endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }
            }
            elseif($method == "GET"){
                if(count($this->urlarray) == 1){
                    $userUrlControl->get();
                }
                elseif (count($this->urlarray) == 2){
                    if($this->urlarray[1] == 'search'){
                        $userSearchUrlControl->get();
                    }
                    elseif($this->urlarray[1] == 'log'){
                        $userLogUrlControl->get();
                    }
                    elseif($this->urlarray[1] == 'profile'){
                        $userProfileUrlControl->get();
                    }
                    else{
                        $userUrlControl->get($this->urlarray[1]);
                    }
                }
                else{
                    if($this->urlarray[1] == 'search'){
                        $userSearchUrlControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] == 'log'){
                        $userLogUrlControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] == 'profile'){
                        $userProfileUrlControl->get($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(["error"=>"endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }

            }
            elseif($method == "POST"){
                if(count($this->urlarray) == 1){
                    $userUrlControl->post();
                }
                elseif (count($this->urlarray) == 2){
                    if($this->urlarray[1] == 'search'){
                        $userSearchUrlControl->post();
                    }
                    elseif($this->urlarray[1] == 'log'){
                        $userLogUrlControl->post();
                    }
                    elseif($this->urlarray[1] == 'profile'){
                        echo json_encode(['error'=>"an id value is required"]);
                        http_response_code('400');
                        return;
                    }
                    else{
                        echo json_encode(["error"=>"endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }
                else{
                    if($this->urlarray[1] == 'profile'){
                        $userProfileUrlControl->updatePic($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(["error"=>"endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }
            }
            elseif($method == "DELETE"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error'=>"an id value is required"]);
                    http_response_code('400');
                    return;
                }
                elseif (count($this->urlarray) == 2){
                    $userUrlControl->delete($this->urlarray[1]);
                }
                else{
                    if($this->urlarray[1] == 'search'){
                        $userSearchUrlControl->delete($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] == 'log'){
                        $userLogUrlControl->delete($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(["error"=>"endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }
            }
            elseif ($method === "OPTIONS"){
                echo json_encode('success');
            }
            else{
                echo json_encode(['error'=>"method not allowed!"]);
                http_response_code('400');
                return;
            }


        }
    }
?>