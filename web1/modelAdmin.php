<?php
class Admin {
    private $conn;
    private $table_name = "admin";
    
    // Properties
    protected $id;
    public $username;
    protected $password;
    protected $nama_lengkap;
    protected $email;
    protected $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
            
            error_log("Attempting admin login for username: " . $this->username);
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $this->username, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                error_log("Admin user found: " . $this->username);
            } else {
                error_log("No admin found with username: " . $this->username);
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Admin login error: " . $e->getMessage());
            throw new Exception("Terjadi kesalahan saat login");
        }
    }

    // Method untuk memvalidasi password
    public function verifyPassword($inputPassword, $hashedPassword) {
        return password_verify($inputPassword, $hashedPassword);
    }

    // Method untuk mengubah password
    public function updatePassword($newPassword) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET password = :password 
                     WHERE username = :username";

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':username', $this->username);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Password update error: " . $e->getMessage());
            throw new Exception("Gagal mengubah password");
        }
    }
}
?>
