<?php
session_start();
require_once './proses/config.php';

if (isset($_SESSION['id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ./admin/index.php');
        exit;
    } elseif ($_SESSION['role'] === 'mahasiswa') {
        header('Location: ./mahasiswa/dashboard-mahasiswa.php');
        exit;
    }
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login SIM-KIP</title>
    <link rel="stylesheet" href="./style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <img src="./assets/images/image.png" alt="Logo" class="login-logo">
            <h2>SIM - KIP Login</h2>
            <p>Masuk ke akun Anda</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form class="login-form" action="./proses/auth.php" method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>       
            <div class="loginmahasiswa">
            <a href="loginmahasiswa.php" class="mahasiswa-login">
                <i class="fas fa-user-graduate"></i> Login sebagai Mahasiswa
            </a>
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>

        
    </div>
</body>
</html>
