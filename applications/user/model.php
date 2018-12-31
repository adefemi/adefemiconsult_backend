<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/'.'extras/misc.php';
    class UserModel{
        function __construct($state=null)
        {
            $this->user = 'user';
            $this->userSearch = 'user_search';
            $this->userLog = 'user_log';
            $this->userProfile = 'user_profile';
            $this->misc = new Miscellaneous();

            $this->userData = ['id','username','email', 'is_superuser', 'is_staff', 'uuid','is_active','created_on','last_login'];
            $this->userSearchData = ['id','user_id','uuid','content','created_on'];
            $this->userLogData = ['id','user_id','title','uuid','description','created_on'];
            $this->userProfileData = ['user_id','fulname','country','uuid','profession','about','coverpic','dob','created_on','updated_on'];

            if($state != null){
                $this->TableUser($state);
                $this->TableUserSearch($state);
                $this->TableUserLog($state);
                $this->TableUserProfile($state);
            }
        }

        function TableUser($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->user (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        username VARCHAR(50) NOT NULL UNIQUE,
                        email VARCHAR(256) NOT NULL UNIQUE,
                        password VARCHAR(256) NOT NULL,
                        secret_key VARCHAR(256) NOT NULL,
                        is_superuser INT(2) NOT NULL,
                        is_staff INT(2) NOT NULL,
                        is_active INT(2) NOT NULL,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        last_login INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) == TRUE) {
                        echo "Table ".ucfirst($this->user)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->user);
                    break;
                case 3:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $uuid = "\"".$this->misc->gen_uuid()."\"";
                    $sql = "ALTER TABLE $this->user ADD uuid VARCHAR(256) NOT NULL DEFAULT $uuid";
                    if ($conn->query($sql) == TRUE) {
                        echo "Table ".ucfirst($this->user)." updated successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }
        function TableUserSearch($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->userSearch (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        user_id INT(6) UNSIGNED NOT NULL,
                        content VARCHAR(256) NOT NULL,
                        created_on INT(11) NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES $this->user(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) == TRUE) {
                        echo "Table ".ucfirst($this->userSearch)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->userSearch);
                    break;
                case 3:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $uuid = "\"".$this->misc->gen_uuid()."\"";
                    $sql = "ALTER TABLE $this->userSearch ADD uuid VARCHAR(256) NOT NULL DEFAULT $uuid";
                    if ($conn->query($sql) == TRUE) {
                        echo "Table ".ucfirst($this->userSearch)." updated successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }
        function TableUserLog($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->userLog (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        user_id INT(6) UNSIGNED NOT NULL,
                        title VARCHAR(256) NOT NULL,
                        description MEDIUMTEXT NOT NULL,
                        created_on INT(11) NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES $this->user(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) == TRUE) {
                        echo "Table ".ucfirst($this->userLog)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->userLog);
                    break;
                case 3:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $uuid = "\"".$this->misc->gen_uuid()."\"";
                    $sql = "ALTER TABLE $this->userLog ADD uuid VARCHAR(256) NOT NULL DEFAULT $uuid";
                    if ($conn->query($sql) == TRUE) {
                        echo "Table ".ucfirst($this->userLog)." updated successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }
        function TableUserProfile($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->userProfile (
                        user_id INT(6) UNSIGNED NOT NULL PRIMARY KEY,
                        fulname VARCHAR(256) NOT NULL,
                        country VARCHAR(100) NOT NULL,
                        profession VARCHAR(100) NOT NULL,
                        about MEDIUMTEXT NOT NULL,
                        coverpic VARCHAR(256) NOT NULL,
                        dob INT(11) NOT NULL,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES $this->user(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) == TRUE) {
                        echo "Table ".ucfirst($this->userProfile)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->userProfile);
                    break;
                case 3:
                    $uuid = "\"".$this->misc->gen_uuid()."\"";
                    $sql = "ALTER TABLE $this->userProfile
                    MODIFY dob VARCHAR(20),
                    ADD uuid VARCHAR(256) NOT NULL DEFAULT $uuid
                    ";
                    $connection = new Connection();
                    $conn = $connection->connent();
                    if ($conn->query($sql) == TRUE) {
                        echo "Table ".ucfirst($this->user)." updated successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
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