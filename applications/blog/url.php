<?php
    class BlogURL{
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
            $BlogControl = new BlogControl();
            $BlogCommentControl = new BlogCommentControl();
            $BlogViewControl = new BlogViewControl();
            $BlogTagControl = new BlogTagControl();
            $BlogGenreControl = new BlogGenreControl();
            $BlogReactionControl = new BlogReactionControl();
            $BlogCommentReactionControl = new BlogCommentReactionControl();

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
                    if($this->urlarray[1] === 'comment' || $this->urlarray[1] === 'view' || $this->urlarray[1] === 'tag' || $this->urlarray[1] === 'genre'
                        || $this->urlarray[1] === 'reaction' || $this->urlarray[1] === 'comment-reaction'){
                        echo json_encode(['error' => "an id value is required"]);
                        http_response_code('400');
                        return;
                    }

                    else{
                        $BlogControl->update($this->urlarray[1]);
                    }
                }
                else{
                    if($this->urlarray[1] === 'comment'){
                        $BlogCommentControl->update($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'view'){
                        $BlogViewControl->update($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'tag'){
                        $BlogTagControl->update($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'genre'){
                        $BlogGenreControl->update($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'reaction'){
                        $BlogReactionControl->update($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'comment-reaction'){
                        $BlogCommentReactionControl->update($this->urlarray[2]);
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
                    $BlogControl->get();
                }
                elseif (count($this->urlarray) === 2){
                    if($this->urlarray[1] === 'comment'){
                        $BlogCommentControl->get();
                    }
                    elseif($this->urlarray[1] === 'view'){
                        $BlogViewControl->get();
                    }
                     elseif($this->urlarray[1] === 'tag'){
                         $BlogTagControl->get();
                    }
                     elseif($this->urlarray[1] === 'genre'){
                         $BlogGenreControl->get();
                    }
                     elseif($this->urlarray[1] === 'reaction'){
                         $BlogReactionControl->get();
                    }
                     elseif($this->urlarray[1] === 'comment-reaction'){
                         $BlogCommentReactionControl->get();
                    }
                    else{
                        $BlogControl->get($this->urlarray[1]);
                    }
                }
                else{
                    if($this->urlarray[1] === 'comment'){
                        $BlogCommentControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'view'){
                        $BlogViewControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'tag'){
                        $BlogTagControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'genre'){
                        $BlogGenreControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'reaction'){
                        $BlogReactionControl->get($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'comment-reaction'){
                        $BlogCommentReactionControl->get($this->urlarray[2]);
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
                    $BlogControl->post();
                }
                elseif (count($this->urlarray) === 2){
                    if($this->urlarray[1] === 'comment'){
                        $BlogCommentControl->post();
                    }
                    elseif($this->urlarray[1] === 'view'){
                        $BlogViewControl->post();
                    }
                    elseif($this->urlarray[1] === 'tag'){
                        $BlogTagControl->post();
                    }
                    elseif($this->urlarray[1] === 'genre'){
                        $BlogGenreControl->post();
                    }
                    elseif($this->urlarray[1] === 'reaction'){
                        $BlogReactionControl->post();
                    }
                    elseif($this->urlarray[1] === 'comment-reaction'){
                        $BlogCommentReactionControl->post();
                    }
                    else{
                        if(!isset($_SERVER['QUERY_STRING'])){
                            $BlogControl->updatePic($this->urlarray[1]);
                        }
                        else{
                            parse_str($_SERVER['QUERY_STRING'], $query);
                            $query['type'] === 'file' ? $BlogControl->updateFile($this->urlarray[1]) : $BlogControl->updatePic($this->urlarray[1]);
                        }
                    }
                }
                else{
                    echo json_encode(['error' => "endpoint not found!"]);
                    http_response_code('404');
                    return;
                }
            }
            elseif($method === "DELETE"){
                if(count($this->urlarray) < 2){
                    echo json_encode(['error' => "endpoint not found!"]);
                    http_response_code('404');
                    return;
                }
                elseif (count($this->urlarray) === 2){
                    $BlogControl->delete($this->urlarray[1]);
                }
                else{
                    if($this->urlarray[1] === 'comment'){
                        $BlogCommentControl->delete($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'view'){
                        $BlogViewControl->delete($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'tag'){
                        $BlogTagControl->delete($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'genre'){
                        $BlogGenreControl->delete($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'reaction'){
                        $BlogReactionControl->delete($this->urlarray[2]);
                    }
                    elseif($this->urlarray[1] === 'comment-reaction'){
                        $BlogCommentReactionControl->delete($this->urlarray[2]);
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