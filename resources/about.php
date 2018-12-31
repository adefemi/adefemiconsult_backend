<?php
    class AboutControl{
        function __construct()
        {
           $method = $_SERVER['REQUEST_METHOD'];
           $this->tablename = 'about';
           switch ($method){
               case 'GET':
                   $this->get();
                   break;
               case 'PATCH':
                   $this->update();
                   break;
               case 'PUT':
                   $this->update();
                   break;
               default:
                   echo json_encode('method not allowed');
                   http_response_code('400');
           }
        }

        function get(){

            $sql = "SELECT * FROM ".$this->tablename;
            $connection = new Connection();
            $conn = $connection->connent();
            $result = $conn->query($sql);
            $aboutContent = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $aboutContent['id'] = $row['id'];
                    $aboutContent['statement'] = $row['statement'];
                    $aboutContent['created_on'] = $row['created_on'];
                    $aboutContent['updated_on'] = $row['updated_on'];
                }
            } else {
                echo "0 results found";
            }
            echo json_encode($aboutContent);
            http_response_code('200');
        }

        function update(){
            $request = new RequestParser();
            echo json_encode($request->parseData());

//            if(count($_REQUEST) < 1 ){
//                echo json_encode('Provide a STATEMENT value');
//                http_response_code('400');
//            }

        }
    }
?>