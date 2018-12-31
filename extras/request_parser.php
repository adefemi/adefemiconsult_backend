<?php

    class RequestParser{
        function __construct()
        {
            $this->requestData = file_get_contents("php://input");
        }

        function parseData(){
            $raw_data = $this->requestData;
            $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

            if(empty($boundary)){
                parse_str($raw_data, $data);
                return $data;
            }

            $parts = array_slice(explode($boundary, $raw_data), 1);
            $data = array();

            foreach ($parts as $part){
                if ($part == "--\r\n") break;


                $part = ltrim($part, "\r\n");
                list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);


                $raw_headers = explode("\r\n", $raw_headers);
                $headers = array();

                foreach ($raw_headers as $header){
                    list($name, $value) = explode(':', $header);
                    $headers[strtolower($name)] = ltrim($value, ' ');
                }

                if(isset($headers['content-disposition'])){
                    $filename = null;
                    preg_match(
                        '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                        $headers['content-disposition'],
                        $matches
                    );

                    list(, $type, $name) = $matches;
                    isset($matches[4]) and $filename = $matches[4];

                    switch ($name){
                        case 'userfile':
                            file_put_contents($filename, $body);
                            break;

                        default:
                            $data[$name] = substr($body, 0, strlen($body) - 2);
                            break;
                    }
                }
            }

            return $data;
        }

    }

?>