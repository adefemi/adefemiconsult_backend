<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
    class MessageModel{
        function __construct($state=null)
        {
            $this->message = 'message';
            $this->user = 'user';
            $this->course = 'course';
            $this->misc = new Miscellaneous();

            $this->messageData = ['id','course_id','sender_id','receiver_id','detail','uuid','seen','timeStamp','created_on','updated_on'];

            if($state != null){
                $this->TableMessage($state);
            }
        }

        function TableMessage($state){
            switch ($state){
                case 1:
                    $uuid = "\"".$this->misc->gen_uuid()."\"";
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->message (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        course_id INT(6) UNSIGNED NOT NULL, 
                        sender_id INT(6) UNSIGNED NOT NULL, 
                        receiver_id INT(6) UNSIGNED NOT NULL, 
                        detail MEDIUMTEXT NOT NULL,
                        uuid VARCHAR(256) NOT NULL DEFAULT $uuid,
                        seen INT(2) NOT NULL DEFAULT 0,
                        timeStamp INT(11) NOT NULL,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (course_id) REFERENCES $this->course(id) on DELETE CASCADE,
                        FOREIGN KEY (sender_id) REFERENCES $this->user(id) on DELETE CASCADE,
                        FOREIGN KEY (receiver_id) REFERENCES $this->user(id) on DELETE CASCADE
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->message)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->message);
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