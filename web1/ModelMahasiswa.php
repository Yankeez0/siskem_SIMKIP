<?php
class Mahasiswa {
    private $conn;
    private $table_name = "mahasiswa";
    
    // Constants for validation
    const MIN_PASSWORD_LENGTH = 8;
    const NIM_PATTERN = '/^\d{8}$/';
    
    // Properties
    protected $id;
    protected $nim;
    protected $password;
    protected $nama;
    protected $jenis_kelamin;
    protected $tempat_lahir;
    protected $tanggal_lahir;
    protected $agama;
    protected $nama_ibu;
    protected $no_hp_ortu;
    protected $email_ortu;
    protected $status_kip;
    protected $periode_aktif;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Getter dan Setter untuk NIM
    public function setNim($nim) {
        if (!preg_match(self::NIM_PATTERN, $nim)) {
            throw new Exception("Format NIM tidak valid");
        }
        $this->nim = $nim;
        return $this;
    }

    public function getNim() {
        return $this->nim;
    }

    // Getter dan Setter untuk Password
    public function setPassword($password) {
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw new Exception("Password terlalu pendek");
        }
        $this->password = $password;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    // Login method
    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nim = :nim LIMIT 1";
        
        try {
            // Log the attempt
            error_log("Login attempt starting for NIM: " . $this->nim);
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                error_log("Failed to prepare statement");
                throw new Exception("Database error");
            }
            
            // Bind parameter
            $stmt->bindParam(':nim', $this->nim, PDO::PARAM_STR);
            
            // Execute query
            $success = $stmt->execute();
            if (!$success) {
                error_log("Failed to execute statement: " . implode(", ", $stmt->errorInfo()));
                throw new Exception("Database error");
            }
            
            // Fetch result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Log the result
            if ($result) {
                error_log("User found for NIM: " . $this->nim);
            } else {
                error_log("No user found for NIM: " . $this->nim);
            }
            
            return $result;
        } catch(PDOException $e) {
            error_log("Database Error in login(): " . $e->getMessage());
            throw new Exception("Terjadi kesalahan saat login");
        }
    }

    private function validateInput($data) {
        $errors = [];
        
        if (isset($data['nim']) && !preg_match(self::NIM_PATTERN, $data['nim'])) {
            $errors['nim'] = "NIM harus 8 digit angka";
        }
        
        if (isset($data['password']) && strlen($data['password']) < self::MIN_PASSWORD_LENGTH) {
            $errors['password'] = "Password minimal " . self::MIN_PASSWORD_LENGTH . " karakter";
        }
        
        if (isset($data['email_ortu']) && !filter_var($data['email_ortu'], FILTER_VALIDATE_EMAIL)) {
            $errors['email_ortu'] = "Format email tidak valid";
        }
        
        return empty($errors) ? true : $errors;
    }

    // CRUD Methods
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch(PDOException $e) {
            error_log("Read Error: " . $e->getMessage());
            throw new Exception("Gagal mengambil data");
        }
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("ReadOne Error: " . $e->getMessage());
            throw new Exception("Gagal mengambil data");
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (nim, password, nama, jenis_kelamin, tempat_lahir, 
                tanggal_lahir, agama, nama_ibu, no_hp_ortu, 
                email_ortu, status_kip, periode_aktif)
                VALUES
                (:nim, :password, :nama, :jenis_kelamin, :tempat_lahir,
                :tanggal_lahir, :agama, :nama_ibu, :no_hp_ortu,
                :email_ortu, :status_kip, :periode_aktif)";

        try {
            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $this->nim = htmlspecialchars(strip_tags($this->nim));
            $this->nama = htmlspecialchars(strip_tags($this->nama));
            
            // Bind parameters
            $stmt->bindParam(":nim", $this->nim);
            $stmt->bindParam(":password", $this->password);
            $stmt->bindParam(":nama", $this->nama);
            $stmt->bindParam(":jenis_kelamin", $this->jenis_kelamin);
            $stmt->bindParam(":tempat_lahir", $this->tempat_lahir);
            $stmt->bindParam(":tanggal_lahir", $this->tanggal_lahir);
            $stmt->bindParam(":agama", $this->agama);
            $stmt->bindParam(":nama_ibu", $this->nama_ibu);
            $stmt->bindParam(":no_hp_ortu", $this->no_hp_ortu);
            $stmt->bindParam(":email_ortu", $this->email_ortu);
            $stmt->bindParam(":status_kip", $this->status_kip);
            $stmt->bindParam(":periode_aktif", $this->periode_aktif);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Create Error: " . $e->getMessage());
            throw new Exception("Gagal membuat data baru");
        }
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET nim = :nim,
                    nama = :nama,
                    jenis_kelamin = :jenis_kelamin,
                    tempat_lahir = :tempat_lahir,
                    tanggal_lahir = :tanggal_lahir,
                    agama = :agama,
                    nama_ibu = :nama_ibu,
                    no_hp_ortu = :no_hp_ortu,
                    email_ortu = :email_ortu,
                    status_kip = :status_kip,
                    periode_aktif = :periode_aktif
                WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':nim', $this->nim);
            $stmt->bindParam(':nama', $this->nama);
            $stmt->bindParam(':jenis_kelamin', $this->jenis_kelamin);
            $stmt->bindParam(':tempat_lahir', $this->tempat_lahir);
            $stmt->bindParam(':tanggal_lahir', $this->tanggal_lahir);
            $stmt->bindParam(':agama', $this->agama);
            $stmt->bindParam(':nama_ibu', $this->nama_ibu);
            $stmt->bindParam(':no_hp_ortu', $this->no_hp_ortu);
            $stmt->bindParam(':email_ortu', $this->email_ortu);
            $stmt->bindParam(':status_kip', $this->status_kip);
            $stmt->bindParam(':periode_aktif', $this->periode_aktif);

            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Delete Error: " . $e->getMessage());
            return false;
        }
    }
}