<?php

class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "font_group";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Public method to get the connection
    public function getConnection() {
        return $this->conn;
    }

    public function createTableIfNotExists($tableName, $createTableQuery) {
        $query = "SHOW TABLES LIKE '$tableName'";
        $result = $this->conn->query($query);

        if ($result->num_rows == 0) {
            if ($this->conn->query($createTableQuery) === TRUE) {
                echo "Table '$tableName' created successfully\n";
            } else {
                echo "Error creating table '$tableName': " . $this->conn->error . "\n";
            }
        } else {
            echo "Table '$tableName' already exists\n";
        }
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    public function close() {
        $this->conn->close();
    }
}

?>
