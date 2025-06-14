<?php
require_once 'config/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($auth->loginMahasiswa($nim, $password)) {
        header("Location: dashboard-mahasiswa.php");
        exit;
    } else {
        $error = "NIM atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Mahasiswa - SIM-KIP</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <img src="GAMBAR/image.png" alt="Logo" class="login-logo">
            <h2>Login Mahasiswa SIM-KIP</h2>
            <p>Masuk ke akun mahasiswa Anda</p>
        </div>
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form class="login-form" method="POST" action=""> 
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="nim" placeholder="Nomor Induk Mahasiswa (NIM)" required> 
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required> 
            </div>
            <div class="remember-forgot">
                <label>
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
                <a href="#">Lupa Password?</a>
            </div>
            <button type="submit" class="login-button">Login</button>
            <div class="signup-link">
                Login Sebagai Admin <a href="login.php">Login</a>
            </div>
        </form>
    </div>
    <script src="JS/script.js"></script>
</body>
</html>
