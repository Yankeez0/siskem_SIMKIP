<?php
session_start();
require_once '../proses/config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$conn = getDbConnection();
$user_id = $_SESSION['id'];
$success = "";
$error = "";

// Ambil data admin
$stmt = $conn->prepare("SELECT username, nama_lengkap, email, avatar FROM admin WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    $error = "Data admin tidak ditemukan.";
}

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $password = $_POST['password'];
    $avatar = $_FILES['avatar'];

    $upload_filename = $admin['avatar']; // default

    if ($avatar['error'] === 0 && is_uploaded_file($avatar['tmp_name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($avatar['type'], $allowed_types)) {
            $upload_dir = '../assets/uploads/';
            $safe_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.-]/', '', $avatar['name']);
            $target_path = $upload_dir . $safe_filename;

            if (move_uploaded_file($avatar['tmp_name'], $target_path)) {
                $upload_filename = 'assets/uploads/' . $safe_filename; // simpan path relatif untuk HTML
            } else {
                $error = "Gagal mengunggah avatar.";
            }
        } else {
            $error = "Format file tidak didukung. Hanya JPG, PNG, GIF.";
        }
    }

    if (empty($error)) {
        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE admin SET username=?, nama_lengkap=?, password=?, avatar=? WHERE id=?");
            $stmt->bind_param("ssssi", $username, $nama_lengkap, $password, $upload_filename, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE admin SET username=?, nama_lengkap=?, avatar=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $nama_lengkap, $upload_filename, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $success = "Profil berhasil diperbarui.";
            $admin['username'] = $username;
            $admin['nama_lengkap'] = $nama_lengkap;
            $admin['avatar'] = $upload_filename;
        } else {
            $error = "Gagal memperbarui profil.";
        }
        $stmt->close();
    }
}

// Avatar path untuk <img src>
$avatar_path = $admin['avatar'] ?? 'assets/uploads/default.png';
$full_path = realpath(__DIR__ . '/../' . $avatar_path);
if (!$full_path || !file_exists($full_path)) {
    $avatar_path = 'assets/uploads/default.png';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pengaturan Profil Admin</title>
  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    <h3>SIM - KIP</h3>
    <a href="index.php">Dashboard</a>
    <a href="rekapitulasi.php">Rekapitulasi</a>
    <a href="data-mahasiswa.php">Data Mahasiswa</a>
    <a href="logout.php">Logout</a>
  </div>

  <main class="main-content">
    <div class="header-main">
      <div class="header-left">
        <span class="admin-label">admin</span>
      </div>
      <div class="header-right">
        <div class="user-profile-toggle" id="userProfileToggle">
          <img src="../<?php echo htmlspecialchars($avatar_path); ?>" alt="Avatar" class="user-avatar" />
          <span class="username"><?php echo htmlspecialchars($admin['username']); ?></span>
          <i class="fas fa-caret-down caret-icon"></i>
        </div>
        <div id="profileDropdown" class="dropdown-menu">
          <a href="profile-settings.php">Pengaturan Profil</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>

    <div class="page-header">
      <h2>Pengaturan Profil</h2>
    </div>

    <div class="section-container profile-form">
      <h3>Ubah Profil Admin</h3>

      <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
      <?php elseif ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="form-group-profile">
          <label for="username">Username</label>
          <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
        </div>

        <div class="form-group-profile">
          <label for="nama_lengkap">Nama Lengkap</label>
          <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($admin['nama_lengkap']); ?>" required>
        </div>

        <div class="form-group-profile">
          <label for="password">Kata Sandi Baru (kosongkan jika tidak ingin mengubah)</label>
          <input type="password" name="password" placeholder="********">
        </div>

        <div class="form-group-profile">
          <label for="avatar">Ganti Foto Profil (opsional)</label>
          <input type="file" name="avatar" accept="image/*">
        </div>

        <button type="submit" class="button-primary">Simpan Perubahan</button>
      </form>
    </div>
  </main>

  <script>
    const profileToggle = document.getElementById('userProfileToggle');
    const profileDropdown = document.getElementById('profileDropdown');

    profileToggle.addEventListener('click', () => {
      profileDropdown.classList.toggle('show');
      profileToggle.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
      if (!profileToggle.contains(e.target)) {
        profileDropdown.classList.remove('show');
        profileToggle.classList.remove('active');
      }
    });
  </script>
</body>
</html>
