<?php
    class TeamModel{
        function __construct($state=null)
        {
            $this->team = 'team';
            $this->teamData = ['id','fulname','designation','coverpic','facebook','instagram','twitter','google','linkedin','created_on','updated_on'];
            if($state != null){
                $this->TableTeam($state);
            }
        }

        function TableTeam($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->team (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        fulname VARCHAR(100) NOT NULL ,
                        designation  VARCHAR(30) NOT NULL,
                        coverpic VARCHAR(200) NULL,
                        facebook VARCHAR(200) NULL,
                        instagram VARCHAR(200) NULL,
                        twitter VARCHAR(200) NULL,
                        google VARCHAR(200) NULL,
                        linkedin VARCHAR(200) NULL,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->team)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->team);
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