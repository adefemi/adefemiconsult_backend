<?php
    class PageSettingModel{
        function __construct($state=null)
        {
            //main list
            $this->images = 'image';
            $this->socialHandle = 'social_handle';
            $this->contactInfo = 'contact_info';

            $this->imagesData = ['id','title','coverpic','created_on','updated_on'];
            $this->socialHandleData = ['id','title','link','created_on','updated_on'];
            $this->contactInfoData = ['id','description','email','email2','telephone','telephone2','location','copyright','created_on','updated_on'];


            if($state != null){
                $this->TableImages($state);
                $this->TableSocialHandle($state);
                $this->TableContactInfo($state);
            }
        }

        function TableImages($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->images (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        title VARCHAR(50) NOT NULL UNIQUE,
                        coverpic VARCHAR(200) NULL,                    
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL                     
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->images)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->images);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }
        function TableSocialHandle($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->socialHandle (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,             
                        title VARCHAR(50) NOT NULL UNIQUE ,                
                        link VARCHAR(256) NOT NULL,                
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->socialHandle)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->socialHandle);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }
        function TableContactInfo($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->contactInfo (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        description MEDIUMTEXT NOT NULL,                
                        email VARCHAR(256) NOT NULL,                
                        email2 VARCHAR(256) NOT NULL,                
                        telephone VARCHAR(20) NOT NULL,                
                        telephone2 VARCHAR(20) NOT NULL,                
                        location MEDIUMTEXT NOT NULL,                
                        copyright VARCHAR(256) NOT NULL,                
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL                 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        $timestamp = time();
                        $sql = "INSERT INTO $this->contactInfo (description, email, email2, telephone, telephone2, location, copyright, created_on, updated_on) 
VALUES ('null','null', 'null','null','null', 'null','null', '$timestamp', '$timestamp')";
                        $conn->query($sql);
                        echo "Table ".ucfirst($this->contactInfo)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->contactInfo);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }


        function drop($tablename){
            $sql = "DROP TABLE $tablename";
            $connection = new Connection();
            $conn = $connection->connent();
            if ($conn->query($sql) === TRUE) {
                echo "Table ".ucfirst($tablename)." dropped successfully"."<br>";
            } else {
                echo "Error: " . $conn->error."<br>";
            }
            $conn->close();
        }

    }
?>