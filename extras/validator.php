<?php

    class Validator{
        function __construct( $array)
        {
            $this->array = $array;

            $this->error = [];
        }

        function validatepost(){
            foreach ($this->array as $test){
                if(isset($test['string'])){
                    if(!isset($_REQUEST[$test['string']])){
                        $this->error[$test['string']] = "this field is required";
                    }
                    //echo json_encode(strlen($_REQUEST[$test['string']]));
                    else{
                        if (strlen($_REQUEST[$test['string']]) < 1 || $_REQUEST[$test['string']] == "undefined"){
                            $this->error[$test['string']] = "this field cannot be blank";
                        }
                    }

                }
                elseif(isset($test['file'])){
                    if(!isset($_FILES[$test['file']])){
                        $this->error[$test['file']] = "this field is required";
                    }

                }
                elseif(isset($test['email'])){
                    if(!isset($_REQUEST[$test['email']])){
                        $this->error[$test['email']] = "this field is required";
                    }
                    elseif (!filter_var($_REQUEST[$test['email']], FILTER_VALIDATE_EMAIL)){
                        $this->error[$test['email']] = "Email address not valid";
                    }
                }
                else{
                    return $this->error['error'] = "Invalid file type specified";
                }
            }

            return $this->error;
        }

        function validateupdate(){

        }
    }

?>