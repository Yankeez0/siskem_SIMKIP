<?php
session_start();
require_once './proses/config.php';

if (isset($_SESSION['id']) && $_SESSION['role'] === 'mahasiswa') {
    header('Location: ./mahasiswa/dashboard-mahasiswa.php');
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Mahasiswa - SIM-KIP</title>
    <link rel="stylesheet" href="./style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <img src="./assets/images/image.png" alt="Logo" class="login-logo">
            <h2>Login Mahasiswa</h2>
            <p>Masukkan NIM dan password Anda</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form class="login-form" action="./proses/auth-mahasiswa.php" method="post">
            <div class="input-group">
                <i class="fas fa-id-card"></i>
                <input type="text" name="nim" placeholder="NIM" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="loginmahasiswa">
            <a href="login.php" class="mahasiswa-login">
                <i class="fas fa-user-shield"></i> Login sebagai Admin
            </a>
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>
    </div>
</body>
</html>
