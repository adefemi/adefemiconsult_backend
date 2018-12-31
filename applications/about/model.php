<?php
    class AboutModel{
        function __construct($state=null)
        {
            $this->about = 'about';

            $this->aboutData = ['id','statement', 'fulname','designation','coverpic','resume','created_on','updated_on'];

            if($state != null){
                $this->TableAbout($state);
            }
        }

        function TableAbout($state){
            switch ($state){
                case 1:
                    $checkIFExist = "SELECT ID FROM $this->about";
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->about (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        statement MEDIUMTEXT NOT NULL,
                        fulname VARCHAR(100) NOT NULL,
                        designation VARCHAR(100) NOT NULL,
                        coverpic VARCHAR(256) NOT NULL,                      
                        resume VARCHAR(256) NOT NULL,                      
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) == TRUE) {
                        $timestamp = time();
                        $serverPro = explode('/', $_SERVER['SERVER_PROTOCOL']);
                        $serverPro = $serverPro[0];
                        $serverPro = strtolower($serverPro)."://";
                        $defaultcoverpics = stripslashes($serverPro.$_SERVER['HTTP_HOST'].'/noimage.jpg');
                        $sql = "INSERT INTO $this->about (statement, fulname, designation, coverpic, resume, created_on, updated_on) VALUES ('about me','hello', 'hello', '$defaultcoverpics', '0', '$timestamp', '$timestamp')";
                        $conn->query($sql);
                        echo "Table ".ucfirst($this->about)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->about);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }

        function drop($tablename){
            $sql = "DROP TABLE $tablename";
            $connection = new Connection();
            $conn = $connection->connent();
            if ($conn->query($sql) == TRUE) {
                echo "Table ".ucfirst($tablename)." dropped successfully"."<br>";
            } else {
                echo "Error: " . $conn->error."<br>";
            }
            $conn->close();
        }
    }
?>