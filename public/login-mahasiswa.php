<?php
session_start();
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mahasiswa') {
    header('Location: dashboard-mahasiswa.php');
    exit;
}
require_once 'php/config.php';
require_once 'php/scrypt.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'] ?? '';
    $password = $_POST['password'] ?? '';
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT id, nim, password_hash, role, nama, jenis_kelamin, tempat_tgl_lahir, agama, nama_ibu, no_hp_ortu, email_ortu, status_kip, periode_aktif FROM users WHERE nim = ? AND role = "mahasiswa"');
    $stmt->bind_param('s', $nim);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && scrypt_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nim'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['jenis_kelamin'] = $user['jenis_kelamin'];
        $_SESSION['tempat_tgl_lahir'] = $user['tempat_tgl_lahir'];
        $_SESSION['agama'] = $user['agama'];
        $_SESSION['nama_ibu'] = $user['nama_ibu'];
        $_SESSION['no_hp_ortu'] = $user['no_hp_ortu'];
        $_SESSION['email_ortu'] = $user['email_ortu'];
        $_SESSION['status_kip'] = $user['status_kip'];
        $_SESSION['periode_aktif'] = $user['periode_aktif'];
        header('Location: dashboard-mahasiswa.php');
        exit;
    } else {
        $error = 'NIM atau password salah!';
    }
    $stmt->close();
    $conn->close();
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
        <form id="loginMahasiswaForm" class="login-form" method="post">
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
                    <input type="checkbox" id="rememberMe"> Ingat saya
                </label>
                <a href="#">Lupa Password?</a>
            </div>
            <button type="submit" class="button-primary">Login</button>
            <div class="message" id="loginMahasiswaMessage" style="color:red;">
                <?php if ($error) echo htmlspecialchars($error); ?>
            </div>
        </form>
        <div class="login-footer">
            Belum punya akun? <a href="#">Daftar sekarang</a> (Fitur ini belum tersedia)
            <br>
            <a href="login.php">Login sebagai Admin</a>
        </div>
    </div>
</body>
</html>
