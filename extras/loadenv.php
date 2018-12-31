<?php

    class LoadENV{
        function __construct()
        {
            $this->filecontent = explode("\n", file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.'env.txt'));
        }

        function getENV(){
            $my_env = [];
            for($i = 0; $i<count($this->filecontent)-1; $i++){
                $item = array_map('trim', explode('=', $this->filecontent[$i]));
                $my_env[$item[0]] = $item[1];
            }
            return $my_env;
        }
    }

?>
