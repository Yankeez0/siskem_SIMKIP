<?php
// Memulai session
session_start();

// Jika sudah login, redirect ke dashboard mahasiswa
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mahasiswa') {
    header("Location: dashboard-mahasiswa.php");
    exit();
}

// Include file konfigurasi database
require_once 'config.php';

$error = '';

// Proses form login
// Ganti baris yang memeriksa login dengan ini:
$nim = $_POST['nim'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($nim) || empty($password)) {
    $error = 'NIM dan password harus diisi';
} else {
    // Sesuaikan query dengan nama kolom yang ada
    $query = "SELECT * FROM mahasiswa WHERE `Nomor Induk Mahasiswa (NIM)` = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($mahasiswa = $result->fetch_assoc()) {
        // Verifikasi password (asumsi password disimpan tanpa hash)
        if ($password === $mahasiswa['Password']) {
            // Set session
            $_SESSION['user_id'] = $mahasiswa['id Mahasiswa'];
            $_SESSION['username'] = $mahasiswa['Nomor Induk Mahasiswa (NIM)'];
            $_SESSION['role'] = 'mahasiswa';
            $_SESSION['nama'] = $mahasiswa['Nama Siswa'];
            
            // Update last login jika ada kolomnya
            // $updateQuery = "UPDATE mahasiswa SET last_login = NOW() WHERE `id Mahasiswa` = ?";
            // $updateStmt = $conn->prepare($updateQuery);
            // $updateStmt->bind_param("i", $mahasiswa['id Mahasiswa']);
            // $updateStmt->execute();
            
            header("Location: dashboard-mahasiswa.php");
            exit();
        } else {
            $error = 'NIM atau password salah';
        }
    } else {
        $error = 'NIM atau password salah';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Mahasiswa - SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <h2>Login Mahasiswa</h2>
            <p>Masukkan NIM dan password Anda untuk masuk.</p>
        </div>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" class="login-form">
            <div class="form-group">
                <label for="nim">NIM</label>
                <input type="text" id="nim" name="nim" placeholder="Masukkan NIM Anda" required />
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan Password Anda" required />
            </div>
            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="rememberMe"> Ingat saya
                </label>
                <a href="#">Lupa Password?</a>
            </div>
            <button type="submit" class="button-primary">Login</button>
        </form>
        
        <div class="login-footer">
            Belum punya akun? <a href="#">Daftar sekarang</a> (Fitur ini belum tersedia)
            <br>
            <a href="login.php">Login sebagai Admin</a>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
