<?php
    class PortfolioModel{
        function __construct($state=null)
        {
            $this->portfolio = 'portfolio';
            $this->portfolioCapture = 'portfolio_capture';
            $this->portfolioHighlight = 'portfolio_highlight';
            $this->portfolioCategory = 'portfolio_category';

            $this->portfolioData = ['id','title','detail', 'category', 'status', 'hosted','git','coverpic', 'slug',  'created_on','updated_on'];
            $this->portfolioCaptureData = ['id','p_id','coverpic','alt','created_on','updated_on'];
            $this->portfolioCategoryData = ['id','title','created_on','updated_on'];
            $this->portfolioHighlightData = ['id','p_id','comment','name','email','created_on','updated_on'];

            if($state != null){
                $this->TablePortfolioCategory($state);
                $this->TablePortfolio($state);
                $this->TablePortfolioCapture($state);
                $this->TablePortfolioHighlight($state);
            }
        }

        function TablePortfolio($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->portfolio (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        title VARCHAR(50) NOT NULL,
                        detail MEDIUMTEXT NOT NULL,
                        category INT(6) UNSIGNED NOT NULL,
                        status VARCHAR(20) NOT NULL,
                        hosted VARCHAR(100) DEFAULT 'null',
                        git VARCHAR(100) DEFAULT 'null',
                        slug VARCHAR(256) NOT NULL,
                        coverpic VARCHAR(200) NULL,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (category) REFERENCES $this->portfolioCategory(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->portfolio)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->portfolio);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }
        function TablePortfolioCapture($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->portfolioCapture (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        p_id INT(6) UNSIGNED NOT NULL,
                        coverpic VARCHAR(256) NOT NULL,
                        alt VARCHAR (100) NOT NULL,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (p_id) REFERENCES $this->portfolio(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->portfolioCapture)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->portfolioCapture);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }
        function TablePortfolioCategory($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->portfolioCategory (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        title VARCHAR (50) NOT NULL UNIQUE,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->portfolioCategory)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->portfolioCategory);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }
        function TablePortfolioHighlight($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->portfolioHighlight (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        p_id INT(6) UNSIGNED NOT NULL,
                        comment MEDIUMTEXT NOT NULL,
                        name VARCHAR(200) NOT NULL,
                        email VARCHAR(256) NOT NULL,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (p_id) REFERENCES $this->portfolio(id) on DELETE CASCADE 
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->portfolioHighlight)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->portfolioHighlight);
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