<?php
class Database{
    public $conn;
 
    // get the database connection
    public function getConnection($config){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $config["mysql_host"] . ";dbname=" . $config["mysql_database"], 
                                  $config["mysql_username"], $config["mysql_password"]);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }

    public static function stop($statement) {
        $errorInfo= $statement->errorInfo();
        /*
        0	SQLSTATE error code (a five characters alphanumeric identifier defined in the ANSI SQL standard).
        1	Driver specific error code.
        2	Driver specific error message.*/
        raise_error($errorInfo[1], "Database Error: " . $errorInfo[2] . "[" . $errorInfo[0]. "." . $errorInfo[1] . "]", 500);
        die();
    }
}

