<?php
    class SkillSetModel{
        function __construct($state=null)
        {
            $this->skillset = 'skillset';
            $this->skillsetData = ['id','name','value','created_on','updated_on'];

            if($state != null){
                $this->TableSkillSet($state);
            }
        }

        function TableSkillSet($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->skillset (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        name VARCHAR(100) NOT NULL UNIQUE ,
                        value INT(6) NOT NULL,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->skillset)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->skillset);
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