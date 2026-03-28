<?php
// db/db.php

class Database {
    private static $instance = null;
    private $pdo;

    // 🔹 Constructor is private to enforce Singleton
    private function __construct() {
        try {
            $host = "localhost";
            $dbname = "smartcampus_1";
            $username = "root";   // ⚡ change if your MySQL user is different
            $password = "";       // ⚡ set your MySQL password if any

            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // 🔹 Get PDO instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
