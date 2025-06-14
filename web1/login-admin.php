<?php
session_start();
require_once 'template/config.php';
require_once 'modelAdmin.php';

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

// Initialize error and success variables
$error = '';
$success = '';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get and sanitize input
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate input
        if (empty($username) || empty($password)) {
            $error = "Username dan password harus diisi";
        } else {
            // Set username for login
            $admin->username = $username;
            
            // Attempt to login
            $result = $admin->login();
            
            if ($result) {
                // Debug password verification
                error_log("Verifying password for admin: " . $username);
                
                if (password_verify($password, $result['password'])) {
                    // Set session variables
                    $_SESSION['admin_id'] = $result['id'];
                    $_SESSION['role'] = 'admin';
                    $_SESSION['username'] = $result['username'];
                    $_SESSION['last_activity'] = time();
                    
                    // Redirect to dashboard
                    header("Location: index.php");
                    exit();
                } else {
                    error_log("Password verification failed for admin: " . $username);
                    $error = "Username atau password salah";
                }
            } else {
                error_log("No admin found with username: " . $username);
                $error = "Username atau password salah";
            }
        }
    }
} catch (Exception $e) {
    error_log("Admin login error: " . $e->getMessage());
    $error = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Admin SIM-KIP</title>
    <link rel="stylesheet" href="style/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        .input-group input {
            padding-left: 40px;
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>

<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <img src="gambar/image.png" alt="Logo" class="login-logo">
            <h2>SIM-KIP Admin Login</h2>
            <p>Masuk ke akun admin Anda</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" 
                       name="username" 
                       placeholder="Username" 
                       required 
                       value="<?= htmlspecialchars($username ?? '') ?>">
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" 
                       name="password" 
                       placeholder="Password" 
                       required>
                <i class="fas fa-eye-slash toggle-password"></i>
            </div>
            
            <div class="remember-forgot">
                <label>
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
                <a href="forgot-password.php">Lupa Password?</a>
            </div>
            
            <button type="submit" class="login-button">Login</button>
            
            <div class="login-links">
                <a href="login-mahasiswa.php">Login sebagai Mahasiswa</a>
            </div>
        </form>
    </div>

    <script>
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                } else {
                    input.type = 'password';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                }
            });
        });
    </script>
</body>
</html>