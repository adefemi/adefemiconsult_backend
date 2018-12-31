<?php
    class PageSettingURL{
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
            $ImageControl = new ImageControl();
            $SocialHandleControl = new SocialHandleControl();
            $ContactInfoControl = new ContactInfoControl();
            $override = null;
            isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? $override = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : null;
            $method = $_SERVER['REQUEST_METHOD'];
            if($override === "PUT" || $method == "PUT"){
                if(count($this->urlarray) < 3){
                    echo json_encode(['error' => "an id value is required"]);
                    http_response_code('400');
                    return;
                }
                elseif (count($this->urlarray) === 3){
                    if($this->urlarray[1] === 'images'){
                        $ImageControl->update($this->urlarray[2]);
                    }
                    else if($this->urlarray[1] === 'socials'){
                        $SocialHandleControl->update($this->urlarray[2]);
                    }
                    else if($this->urlarray[1] === 'contact'){
                        $ContactInfoControl->update($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }
                else{
                    echo json_encode(['error' => "endpoint not found!"]);
                    http_response_code('404');
                    return;
                }
            }
            elseif($method === "GET"){
                if(count($this->urlarray) === 1){
                    echo json_encode(['error' => "control not defined"]);
                    http_response_code('404');
                    return;
                }
                elseif (count($this->urlarray) === 2){
                    if($this->urlarray[1] === 'images'){
                        $ImageControl->get();
                    }
                    else if($this->urlarray[1] === 'socials'){
                        $SocialHandleControl->get();
                    }
                    else if($this->urlarray[1] === 'contact'){
                        $ContactInfoControl->get();
                    }
                    else{
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }
                else{
                    if($this->urlarray[1] === 'images'){
                        $ImageControl->get($this->urlarray[2]);
                    }
                    else if($this->urlarray[1] === 'socials'){
                        $SocialHandleControl->get($this->urlarray[2]);
                    }
                    else if($this->urlarray[1] === 'contact'){
                        $ContactInfoControl->get($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }

            }
            elseif($method === "POST"){
                if(count($this->urlarray) === 1){
                    echo json_encode(['error' => "control not defined"]);
                    http_response_code('404');
                    return;
                }
                elseif(count($this->urlarray) === 2){
                    if($this->urlarray[1] === 'images'){
                        $ImageControl->post();
                    }
                    else if($this->urlarray[1] === 'socials'){
                        $SocialHandleControl->post();
                    }
                    else{
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }
                elseif (count($this->urlarray) === 3){
                    if($this->urlarray[1] === 'images'){
                        $ImageControl->updatePic($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }
                else{
                    echo json_encode(['error' => "endpoint not found!"]);
                    http_response_code('404');
                    return;
                }
            }
            elseif($method === "DELETE"){
                if(count($this->urlarray) < 3){
                    echo json_encode(['error' => "an id value is required"]);
                    http_response_code('400');
                    return;
                }
                elseif (count($this->urlarray) === 3){
                    if($this->urlarray[1] === 'images'){
                        $ImageControl->delete($this->urlarray[2]);
                    }
                    else if($this->urlarray[1] === 'socials'){
                        $SocialHandleControl->delete($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
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