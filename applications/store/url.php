<?php
    class StoreURL{
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
            $storeUrlControl = new StoreControl();
            $storeReactionUrlControl = new StoreReactionControl();
            $storeDownloadUrlControl = new StoreDownloadControl();
            $storeCaptureUrlControl = new StoreCaptureControl();
            $storeHighlightUrlControl = new StoreHighlightControl();
            $storeCategoryControl = new StoreCategoryControl();
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
                    if($this->urlarray[1] === 'reaction' || $this->urlarray[1] === 'capture'){
                        echo json_encode(['error' => "an id value is required"]);
                        http_response_code('400');
                        return;
                    }
                    elseif($this->urlarray[1] === 'download' || $this->urlarray[1] === 'highlight'){
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                    else{
                        $storeUrlControl->update($this->urlarray[1]);
                    }
                }
                else{
                    if($this->urlarray[1] === 'reaction'){
                        $storeReactionUrlControl->update($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'capture'){
                        $storeCaptureUrlControl->update($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'category'){
                        $storeCategoryControl->update($this->urlarray[2]);
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
                    $storeUrlControl->get();
                }
                elseif (count($this->urlarray) === 2){
                    if($this->urlarray[1] === 'reaction'){
                        $storeReactionUrlControl->get();
                    }
                    elseif($this->urlarray[1] === 'download'){
                        $storeDownloadUrlControl->get();
                    }
                    elseif($this->urlarray[1] === 'capture'){
                        $storeCaptureUrlControl->get();
                    }
                    elseif($this->urlarray[1] === 'highlight'){
                        $storeHighlightUrlControl->get();
                    }
                    elseif($this->urlarray[1] === 'category'){
                        $storeCategoryControl->get();
                    }
                    else{
                        $storeUrlControl->get($this->urlarray[1]);
                    }
                }
                else{
                    if($this->urlarray[1] === 'reaction'){
                        $storeReactionUrlControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'download'){
                        $storeDownloadUrlControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'capture'){
                        $storeCaptureUrlControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'highlight'){
                        $storeHighlightUrlControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'category'){
                        $storeCategoryControl->get($this->urlarray[2]);
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
                    $storeUrlControl->post();
                }
                elseif (count($this->urlarray) === 2){
                    if($this->urlarray[1] === 'reaction'){
                        $storeReactionUrlControl->post();
                    }
                    elseif($this->urlarray[1] === 'download'){
                        $storeDownloadUrlControl->post();
                    }
                    elseif($this->urlarray[1] === 'capture'){
                        $storeCaptureUrlControl->post();
                    }
                    elseif($this->urlarray[1] === 'highlight'){
                        $storeHighlightUrlControl->post();
                    }
                    elseif($this->urlarray[1] === 'category'){
                        $storeCategoryControl->post();
                    }
                    else{
                        if(!isset($_SERVER['QUERY_STRING'])){
                            $storeUrlControl->updatePic($this->urlarray[1]);
                        }
                        else{
                            parse_str($_SERVER['QUERY_STRING'], $query);
                            $query['type'] === 'file' ? $storeUrlControl->updateFile($this->urlarray[1]) : $storeUrlControl->updatePic($this->urlarray[1]);
                        }
                    }
                }
                else{
                    if($this->urlarray[1] === 'capture'){
                        $storeCaptureUrlControl->updatePic($this->urlarray[2]);
                    }
                    else{
                        echo json_encode(['error' => "endpoint not found!"]);
                        http_response_code('404');
                        return;
                    }
                }
            }

            elseif($method === "DELETE"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error' => "endpoint not found!"]);
                    http_response_code('404');
                    return;
                }
                elseif (count($this->urlarray) === 2){
                    $storeUrlControl->delete($this->urlarray[1]);
                }
                else{
                    if($this->urlarray[1] === 'reaction'){
                        $storeReactionUrlControl->delete($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'download'){
                        $storeDownloadUrlControl->delete($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'capture'){
                        $storeCaptureUrlControl->delete($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'highlight'){
                        $storeHighlightUrlControl->delete($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'category'){
                        $storeCategoryControl->delete($this->urlarray[2]);
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