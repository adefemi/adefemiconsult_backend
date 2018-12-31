<?php
    class PortfolioURL{
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
            $urlControl = new PortfolioControl();
            $urlControl1 = new PortfolioCaptureControl();
            $PortfolioHighlightControl = new PortfolioHighlightControl();
            $PortfolioCategoryControl = new PortfolioCategoryControl();
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
                    if($this->urlarray[1] === 'captures'){
                        if(count($this->urlarray) < 3){
                            echo json_encode(['error' => "an id value is required"]);
                            http_response_code('400');
                            return;
                        }
                        $urlControl1->update($this->urlarray[2]);
                    }
                    if($this->urlarray[1] === 'highlight'){
                        if(count($this->urlarray) < 3){
                            echo json_encode(['error' => "an id value is required"]);
                            http_response_code('400');
                            return;
                        }
                        $PortfolioHighlightControl->update($this->urlarray[2]);
                    }
                    if($this->urlarray[1] === 'category'){
                        if(count($this->urlarray) < 3){
                            echo json_encode(['error' => "an id value is required"]);
                            http_response_code('400');
                            return;
                        }
                        $PortfolioCategoryControl->update($this->urlarray[2]);
                    }
                    else{
                        $urlControl->update($this->urlarray[1]);
                    }
                }
            }
            elseif($method === "GET"){
                if(count($this->urlarray) === 1){
                    $urlControl->get();
                }
                elseif (count($this->urlarray) === 2){
                    if($this->urlarray[1] === 'captures'){
                        $urlControl1->get();
                    }
                    elseif($this->urlarray[1] === 'highlight'){
                        $PortfolioHighlightControl->get();
                    }
                    elseif($this->urlarray[1] === 'category'){
                        $PortfolioCategoryControl->get();
                    }
                    else{
                        $urlControl->get($this->urlarray[1]);
                    }
                }
                else{
                    if($this->urlarray[1] === 'captures'){
                        $urlControl1->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'highlight'){
                        $PortfolioHighlightControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'category'){
                        $PortfolioCategoryControl->get($this->urlarray[2]);
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
                    $urlControl->post();
                }
                elseif (count($this->urlarray) === 2){
                    if($this->urlarray[1] === 'captures'){
                        $urlControl1->post();
                    }
                    elseif($this->urlarray[1] === 'highlight'){
                        $PortfolioHighlightControl->post();
                    }
                    elseif($this->urlarray[1] === 'category'){
                        $PortfolioCategoryControl->post();
                    }
                    else{
                        $urlControl->updatePic($this->urlarray[1]);
                    }
                }
                else{
                    $urlControl1->updatePic($this->urlarray[2]);
                }
            }
            elseif($method === "DELETE"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error' => "an id value is required"]);
                    http_response_code('400');
                    return;
                }
                else{
                    if($this->urlarray[1] === 'captures'){
                        if(count($this->urlarray) < 3){
                            echo json_encode(['error' => "an id value is required"]);
                            http_response_code('400');
                            return;
                        }
                        $urlControl1->delete($this->urlarray[2]);
                    }
                    if($this->urlarray[1] === 'highlight'){
                        if(count($this->urlarray) < 3){
                            echo json_encode(['error' => "an id value is required"]);
                            http_response_code('400');
                            return;
                        }
                        $PortfolioHighlightControl->delete($this->urlarray[2]);
                    }
                    if($this->urlarray[1] === 'category'){
                        if(count($this->urlarray) < 3){
                            echo json_encode(['error' => "an id value is required"]);
                            http_response_code('400');
                            return;
                        }
                        $PortfolioCategoryControl->delete($this->urlarray[2]);
                    }
                    else{
                        $urlControl->delete($this->urlarray[1]);
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