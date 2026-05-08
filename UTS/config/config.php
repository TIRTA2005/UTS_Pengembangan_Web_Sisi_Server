<?php
class Database {
    public mysqli $conn;
    public function __construct() {
        $this->conn = new mysqli('localhost', 'root', '', 'uts');
        if ($this->conn->connect_error) {
            die('Database connection failed');
        }
        $this->conn->set_charset('utf8');
    }
}
