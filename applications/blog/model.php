<?php
    class BlogModel{
        function __construct($state=null)
        {
            $this->user = 'user';


            $this->blog = 'blog';
            $this->blogComment = 'blog_comment';
            $this->blogView = 'blog_view';
            $this->blogReaction = 'blog_reaction';
            $this->blogTag = 'blog_tag';
            $this->blogGenre = 'blog_genre';
            $this->blogCommentReaction = 'blog_comment_reaction';

            $this->blogData = ['id','type','coverpic','video','title','detail','genre','tags','slug','publish','created_on','updated_on'];
            $this->blogCommentData = ['id','type','blog_id','blog_comment_id','comment','user_id','created_on','updated_on'];
            $this->blogViewData = ['id','blog_id','ip','created_on'];
            $this->blogReactionData = ['id','blog_id','value','user_id','created_on','updated_on'];
            $this->blogTagData = ['id','title','created_on','updated_on'];
            $this->blogGenreData = ['id','title','type','created_on','updated_on'];
            $this->blogCommentReactionData = ['id','blog_comment_id','value','user_id','created_on','updated_on'];

            if($state != null){
                $this->TableBlog($state);
                $this->TableBlogComment($state);
                $this->TableBlogView($state);
                $this->TableBlogReaction($state);
                $this->TableBlogTag($state);
                $this->TableBlogGenre($state);
                $this->TableBlogCommentReaction($state);
            }
        }

        function TableBlog($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->blog (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        type INT(2) NOT NULL DEFAULT 0, 
                        coverpic VARCHAR(200) NOT NULL,
                        video VARCHAR(200) NOT NULL DEFAULT 'null',
                        title VARCHAR(50) NOT NULL,
                        detail MEDIUMTEXT NOT NULL,
                        genre VARCHAR(50) NOT NULL,
                        tags MEDIUMTEXT NOT NULL,
                        slug VARCHAR(256) NOT NULL,
                        publish INT(2) NOT NULL DEFAULT 0,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->blog)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->blog);
                    break;
                default:
                    echo "Not operation is available for this selection<br />";
            }

        }
        function TableBlogComment($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->blogComment (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        type INT(2) NOT NULL DEFAULT 0, 
                        blog_id INT(6) UNSIGNED NOT NULL DEFAULT 1,
                        blog_comment_id INT(6) UNSIGNED NULL,
                        comment MEDIUMTEXT NOT NULL,
                        user_id INT(6) UNSIGNED NOT NULL, 
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (blog_id) REFERENCES $this->blog(id) on DELETE CASCADE,
                        FOREIGN KEY (user_id) REFERENCES $this->user(id) on DELETE CASCADE,
                        FOREIGN KEY (blog_comment_id) REFERENCES $this->blogComment(id) on DELETE CASCADE
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->blogComment)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->blogComment);
            }

        }
        function TableBlogView($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->blogView (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        blog_id INT(6) UNSIGNED NOT NULL,
                        ip VARCHAR(30) NOT NULL,
                        created_on INT(11) NOT NULL,
                        FOREIGN KEY (blog_id) REFERENCES $this->blog(id) on DELETE CASCADE
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->blogView)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->blogView);
            }

        }
        function TableBlogReaction($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->blogReaction (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        blog_id INT(6) UNSIGNED NOT NULL,
                        value INT(2) NOT NULL DEFAULT 0, 
                        user_id INT(6) UNSIGNED NOT NULL, 
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (blog_id) REFERENCES $this->blog(id) on DELETE CASCADE,
                        FOREIGN KEY (user_id) REFERENCES $this->user(id) on DELETE CASCADE
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->blogReaction)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->blogReaction);
            }

        }
        function TableBlogTag($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->blogTag (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        title VARCHAR(30) NOT NULL UNIQUE,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->blogTag)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->blogTag);
            }

        }
        function TableBlogGenre($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->blogGenre (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        title VARCHAR(30) NOT NULL UNIQUE,
                        type INT(2) NOT NULL DEFAULT 0,
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->blogGenre)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->blogGenre);
            }

        }
        function TableBlogCommentReaction($state){
            switch ($state){
                case 1:
                    $connection = new Connection();
                    $conn = $connection->connent();
                    $sql = "CREATE TABLE $this->blogCommentReaction (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                        blog_comment_id INT(6) UNSIGNED NOT NULL,
                        value INT(2) NOT NULL DEFAULT 0, 
                        user_id INT(6) UNSIGNED NOT NULL, 
                        created_on INT(11) NOT NULL,
                        updated_on INT(11) NOT NULL,
                        FOREIGN KEY (blog_comment_id) REFERENCES $this->blogComment(id) on DELETE CASCADE,
                        FOREIGN KEY (user_id) REFERENCES $this->user(id) on DELETE CASCADE
                        )ENGINE = InnoDB";

                    if ($conn->query($sql) === TRUE) {
                        echo "Table ".ucfirst($this->blogCommentReaction)." created successfully ".time()."<br>";
                    } else {
                        echo "Error: " . $conn->error."<br>";
                    }
                    $conn->close();
                    break;
                case 2:
                    $this->drop($this->blogCommentReaction);
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