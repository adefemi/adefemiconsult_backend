<?php
    class StoreModel{
        function __construct($state=null)
        {
            $this->user = 'user';

            $this->store = 'store';
            $this->storeReaction = 'store_reaction';
            $this->storeDownload = 'store_download';
            $this->storeCapture = 'store_capture';
            $this->storeHighlight = 'store_highlight';
            $this->storeCategory = 'store_category';

            $this->storeData = ['id','title','type','slug','detail','price','coverpic','file','created_on','updated_on'];
            $this->storeReactionData = ['id','store_id','type','ip_address','user_id','updated_on'];
            $this->storeDownloadData = ['id','store_id','ip_address','created_on'];
            $this->storeCaptureData = ['id','store_id','coverpic','alt','created_on','updated_on'];
            $this->storeHighlightData = ['id','store_id','comment','name','user_id','created_on'];
            $this->storeCategoryData = ['id','title','created_on','updated_on'];

            if($state != null){
                $this->TableStore($state);
                $this->TablestoreReaction($state);
                $this->TablestoreDownload($state);
                $this->TableCapture($state);
                $this->TablestoreHighlight($state);
                $this->TablestoreCategory($state);
            }
        }

        function TableStore($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->store (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        title VARCHAR(100) NOT NULL UNIQUE,
                        type INT(6) UNSIGNED NOT NULL,
                        slug VARCHAR(256) NOT NULL,
                        detail MEDIUMTEXT NOT NULL,
                        price FLOAT NOT NULL,                      
                        coverpic VARCHAR(256) NOT NULL,
                        file VARCHAR(256) NOT NULL,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (type) REFERENCES $this->storeCategory(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->store)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->store);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }
        function TablestoreReaction($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->storeReaction (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        store_id INT(6) UNSIGNED NOT NULL,
                        type INT(2) NOT NULL,
                        ip_address VARCHAR(20) NOT NULL,
                        user_id INT(6) UNSIGNED NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES  $this->user(id) on DELETE CASCADE, 
                        FOREIGN KEY (store_id) REFERENCES $this->store(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->storeReaction)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->storeReaction);
            }

        }
        function TablestoreDownload($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->storeDownload (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        store_id INT(6) UNSIGNED NOT NULL,
                        ip_address VARCHAR(20) NOT NULL,
                        created_on INT(11) NOT NULL,
                        FOREIGN KEY (store_id) REFERENCES $this->store(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->storeDownload)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->storeDownload);
            }

        }
        function TableCapture($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->storeCapture (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        store_id INT(6) UNSIGNED NOT NULL,
                        coverpic VARCHAR(256) NOT NULL,
                        alt VARCHAR(256) NOT NULL,                        
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (store_id) REFERENCES $this->store(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->storeCapture)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->storeCapture);
            }

        }
        function TablestoreHighlight($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->storeHighlight (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        store_id INT(6) UNSIGNED NOT NULL,
                        comment MEDIUMTEXT NOT NULL,
                        name VARCHAR(200) NOT NULL,
                        user_id INT(6) NOT NULL,
                        created_on INT(11) NOT NULL,
                        FOREIGN KEY (store_id) REFERENCES $this->store(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->storeHighlight)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->storeHighlight);
            }

        }
        function TablestoreCategory($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->storeCategory (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,             
                        title VARCHAR(200) NOT NULL UNIQUE,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->storeCategory)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->storeCategory);
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