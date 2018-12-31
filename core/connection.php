<?php
class Connection {
    function __construct()
    {
        $loadENV = new LoadENV();
        $my_env = $loadENV->getENV();

        $this->HOST = $my_env['DB_HOST'];
        $this->USERNAME = $my_env['DB_USERNAME'];
        $this->PASSWORD = $my_env['DB_PASSWORD'];
        $this->DBNAME = $my_env['DB_DATABASE'];
    }

    function connent(){

        $conn = mysqli_connect($this->HOST, $this->USERNAME, $this->PASSWORD, $this->DBNAME);

        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            return false;
        }
        else{

            return $conn;
        }
    }
}




?>