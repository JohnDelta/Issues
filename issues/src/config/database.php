<?php

class Database {

    private $host = "localhost";
    private $username = "root";
	private $password = "";
	private $dbName = "issuesdb";
    public $conn;

    public function get_connection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=".$this->host.";charset=utf8mb4;".
                "dbname=".$this->dbName,
                $this->username,
                $this->password
            );
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $exception) {
            echo "Connection Error : ".$exception->getMessage();
        }
        return $this->conn;
    }
}

?>