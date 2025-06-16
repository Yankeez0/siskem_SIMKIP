<?php
session_start();
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';
require_once 'php/scrypt.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT id, username, password_hash, role, nama FROM users WHERE username = ? AND role = "admin"');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && scrypt_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
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
    <title>Login SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <img src="image.png" alt="Logo" class="login-logo">
            <h2>SIM - KIP Login</h2>
            <p>Masuk ke akun Anda</p>
        </div>
        <form class="login-form" id="loginForm" method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" id="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <div class="remember-forgot">
                <label>
                    <input type="checkbox"> Ingat saya
                </label>
                <a href="#">Lupa Password?</a>
            </div>
            <button type="submit" class="login-button">Login</button>
            <div class="message" id="loginMessage" style="color:red;">
                <?php if ($error) echo htmlspecialchars($error); ?>
            </div>
            <div class="signup-link">
                Login Sebagai Mahasiswa <a href="login-mahasiswa.php">Login</a>
            </div>
        </form>
    </div>
</body>
</html>
