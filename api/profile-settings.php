<?php
require_once 'config/auth.php';
require_once 'config/csrf.php';

// Check if user is logged in and is admin
if (!$auth->isAdmin()) {
    header("Location: login.php");
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token. Please try again.";
    } else {
        try {
            if (isset($_POST['update_profile'])) {
                // Update profile information
                $stmt = $pdo->prepare("UPDATE admin SET 
                    username = ?, 
                    email = ? 
                    WHERE id = ?");
                $stmt->execute([
                    $_POST['username'],
                    $_POST['email'],
                    $_SESSION['user_id']
                ]);
                $success = "Profil berhasil diperbarui.";
            } elseif (isset($_POST['change_password'])) {
                // Verify current password first
                $stmt = $pdo->prepare("SELECT password, salt FROM admin WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $admin = $stmt->fetch();
                if (Security::verifyPassword($_POST['current_password'], $admin['password'], $admin['salt'])) {
                    if ($_POST['new_password'] === $_POST['confirm_new_password']) {
                        // Update password
                        $hashData = Security::hashPassword($_POST['new_password']);
                        $stmt = $pdo->prepare("UPDATE admin SET password = ?, salt = ? WHERE id = ?");
                        $stmt->execute([$hashData['hash'], $hashData['salt'], $_SESSION['user_id']]);
                        $success = "Password berhasil diubah.";
                    } else {
                        $error = "Password baru tidak cocok dengan konfirmasi.";
                    }
                } else {
                    $error = "Password saat ini tidak valid.";
                }
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

// Fetch current admin data
try {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $admin = $stmt->fetch();
} catch (PDOException $e) {
    $error = "Gagal mengambil data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pengaturan Profil - SIM-KIP</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'templates/header.php'; ?>

        <div class="page-header">
            <h2>Pengaturan Profil</h2>
            <p>Kelola informasi akun Anda.</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="profile-settings-container">
            <div class="profile-card">
                <h3>Informasi Umum</h3>
                <form id="profileInfoForm" class="profile-form" method="POST" action="">
                    <?php echo CSRF::getTokenField(); ?>
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="form-group-profile">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                    </div>
                    <div class="form-group-profile">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group-profile">
                        <label for="role">Peran</label>
                        <input type="text" value="Administrator" readonly disabled>
                    </div>
                    <button type="submit" class="button-primary">Simpan Perubahan</button>
                </form>
            </div>

            <div class="profile-card">
                <h3>Ubah Password</h3>
                <form id="changePasswordForm" class="profile-form" method="POST" action="">
                    <?php echo CSRF::getTokenField(); ?>
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="form-group-profile">
                        <label for="current_password">Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group-profile">
                        <label for="new_password">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group-profile">
                        <label for="confirm_new_password">Konfirmasi Password Baru</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                    </div>
                    <button type="submit" class="button-primary">Ubah Password</button>
                </form>
            </div>
        </div>
    </main>

    <script src="JS/script.js"></script>
</body>
</html>
