<?php
    class ServiceModel{
        function __construct($state=null)
        {
            $this->service = 'service';
            $this->serviceData = ['id','title','detail','coverpic','created_on','updated_on'];
            if($state != null){
                $this->TableService($state);
            }
        }

        function TableService($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->service (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        title VARCHAR(50) NOT NULL ,
                        detail MEDIUMTEXT NOT NULL,
                        coverpic VARCHAR(200) NULL,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->service)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->service);
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