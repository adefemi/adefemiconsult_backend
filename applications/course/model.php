<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
    class CourseModel{
        function __construct($state=null)
        {
            $this->course = 'course';
            $this->user = 'user';
            $this->courseNotification = 'courseNotification';
            $this->courseRegister = 'courseRegister';
            $this->misc = new Miscellaneous();

            $this->courseData = ['id','title','detail', 'coverpic', 'slug','uuid',  'created_on','updated_on'];
            $this->courseNotificationData = ['id','courseID','userID','notification', 'type', 'seen', 'uuid', 'created_on','updated_on'];
            $this->courseRegisterData = ['id','courseID','userID','created_on','updated_on'];

            if($state != null){
                $this->TableCourse($state);
                $this->TableCourseNotification($state);
                $this->TableCourseRegister($state);
            }
        }

        function TableCourse($state){
            switch ($state){
                case 1:
                    $uuid = "\"".$this->misc->gen_uuid()."\"";
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->course (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        title VARCHAR(50) NOT NULL,
                        detail MEDIUMTEXT NOT NULL,
                        coverpic VARCHAR(200) NULL,
                        slug VARCHAR(256) NOT NULL,
                        uuid VARCHAR(256) NOT NULL DEFAULT $uuid,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->course)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->course);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }

        function TableCourseNotification($state){
            switch ($state){
                case 1:
                    $uuid = "\"".$this->misc->gen_uuid()."\"";
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->courseNotification (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        courseID INT(6) UNSIGNED, 
                        userID INT(6) UNSIGNED, 
                        notification VARCHAR(200) NULL,
                        type VARCHAR(20) NULL,
                        seen INT(2) DEFAULT 0,
                        uuid VARCHAR(256) NOT NULL DEFAULT $uuid,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (courseID) REFERENCES $this->course(id) on DELETE CASCADE,
                        FOREIGN KEY (userID) REFERENCES $this->user(id) on DELETE CASCADE
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->courseNotification)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->courseNotification);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }

        function TableCourseRegister($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->courseRegister (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        courseID INT(6) UNSIGNED, 
                        userID INT(6) UNSIGNED, 
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (courseID) REFERENCES $this->course(id) on DELETE CASCADE,
                        FOREIGN KEY (userID) REFERENCES $this->user(id) on DELETE CASCADE
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->courseRegister)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->courseRegister);
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