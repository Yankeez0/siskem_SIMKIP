<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'database.php';
require_once 'security.php';

class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function loginAdmin($username, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && Security::verifyPassword($password, $admin['password'], $admin['salt'])) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['role'] = 'admin';
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    public function loginMahasiswa($nim, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM mahasiswa WHERE `Nomor Induk Mahasiswa (NIM)` = ?");
            $stmt->execute([$nim]);
            $mahasiswa = $stmt->fetch();
            
            if ($mahasiswa && Security::verifyPassword($password, $mahasiswa['Password'], $mahasiswa['salt'] ?? '')) {
                $_SESSION['user_id'] = $mahasiswa['id Mahasiswa'];
                $_SESSION['username'] = $mahasiswa['Nama Siswa'];
                $_SESSION['nim'] = $mahasiswa['Nomor Induk Mahasiswa (NIM)'];
                $_SESSION['role'] = 'mahasiswa';
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    public function isMahasiswa() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'mahasiswa';
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
}

// Initialize Auth
$auth = new Auth($pdo);
?>
