<?php

    class Miscellaneous{
        function __construct()
        {

        }

        function normalizePath($path){
            $newSplitPath = stripslashes($path);
            return $newSplitPath;
        }

        function generateRandomString($length = 10){
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';
            $charactersLength = strlen($characters);
            $randomString = '';
            for($i = 0; $i < $length; $i++){
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        function gen_uuid() {
            return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

                // 16 bits for "time_mid"
                mt_rand( 0, 0xffff ),

                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand( 0, 0x0fff ) | 0x4000,

                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand( 0, 0x3fff ) | 0x8000,

                // 48 bits for "node"
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
            );
        }

        function getAuthorizationHeader(){
            $headers = null;
            if(isset($_SERVER['Authorization'])){
                $headers = trim($_SERVER["Authorization"]);
            }
            elseif (isset($_SERVER['HTTP_AUTHORIZATION'])){
                $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
            }
            elseif(function_exists('apache_request_headers')){
                $requestHeaders = apache_request_headers();
                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                if(isset($requestHeaders['Authorization'])){
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
            return $headers;
        }

        function getBearerToken(){
            $headers = apache_request_headers();
            if(isset($headers['Authorization'])){
                $matches = array();
                preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches);
                if(isset($matches[1])){
                    return $matches[1];
                }
            }
            return null;
        }

        function slugify($text)
        {
            // replace non letter or digits by -
            $text = preg_replace('~[^\pL\d]+~u', '-', $text);

            // transliterate
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

            // remove unwanted characters
            $text = preg_replace('~[^-\w]+~', '', $text);

            // trim
            $text = trim($text, '-');

            // remove duplicate -
            $text = preg_replace('~-+~', '-', $text);

            // lowercase
            $text = strtolower($text);

            if (empty($text)) {
                return 'n-a';
            }

            return $text;
        }
    }

?>