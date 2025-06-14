<?php
class Database {
    // Database configuration
    protected $host = "localhost";
    protected $db_name = "data mahasiswa";
    protected $username = "root";
    protected $password = "Yudha.Kelana23";
    protected $conn = null;

    public function getConnection() {
        try {
            if ($this->conn === null) {
                // Buat koneksi PDO
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                    $this->username,
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );

                error_log("Database connection established successfully");
            }
            return $this->conn;
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw new Exception("Koneksi database gagal: " . $e->getMessage());
        }
    }

    public function closeConnection() {
        $this->conn = null;
        error_log("Database connection closed");
    }

    // Helper method untuk testing koneksi
    public function testConnection() {
        try {
            $this->getConnection();
            $this->conn->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            error_log("Test connection failed: " . $e->getMessage());
            return false;
        }
    }
}