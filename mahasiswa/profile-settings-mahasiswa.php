<?php
session_start();
require_once '../proses/config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ./../loginmahasiswa.php');
    exit;
}

$conn = getDbConnection();
$user_id = $_SESSION['id'];
$success = "";
$error = "";

// Ambil data mahasiswa
$stmt = $conn->prepare("SELECT nim, nama, avatar FROM mahasiswa WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$mahasiswa = $result->fetch_assoc();
$stmt->close();

if (!$mahasiswa) {
    $error = "Data mahasiswa tidak ditemukan.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = trim($_POST['nim']);
    $nama = trim($_POST['nama']);
    $password = $_POST['password'];
    $avatar = $_FILES['avatar'];

    $upload_filename = $mahasiswa['avatar'];

    if ($avatar['error'] === 0 && is_uploaded_file($avatar['tmp_name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($avatar['type'], $allowed_types)) {
            $upload_dir = '../assets/uploads/';
            $safe_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.-]/', '', $avatar['name']);
            $target_path = $upload_dir . $safe_filename;

            if (move_uploaded_file($avatar['tmp_name'], $target_path)) {
                $upload_filename = 'assets/uploads/' . $safe_filename;
            } else {
                $error = "Gagal mengunggah avatar.";
            }
        } else {
            $error = "Format file tidak didukung. Hanya JPG, PNG, GIF.";
        }
    }

    if (empty($error)) {
        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE mahasiswa SET nim=?, nama=?, password=?, avatar=? WHERE id=?");
            $stmt->bind_param("ssssi", $nim, $nama, $password, $upload_filename, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE mahasiswa SET nim=?, nama=?, avatar=? WHERE id=?");
            $stmt->bind_param("sssi", $nim, $nama, $upload_filename, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION['nim'] = $nim;
            $success = "Profil berhasil diperbarui.";
            $mahasiswa['nim'] = $nim;
            $mahasiswa['nama'] = $nama;
            $mahasiswa['avatar'] = $upload_filename;
        } else {
            $error = "Gagal memperbarui profil.";
        }
        $stmt->close();
    }
}

$avatar_path = $mahasiswa['avatar'] ?? 'assets/uploads/default.png';
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
  <title>Pengaturan Profil Mahasiswa</title>
  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    <h3>SIM - KIP</h3>
    <a href="dashboard-mahasiswa.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </div>

  <main class="main-content">
    <div class="header-main">
      <div class="header-left">
        <span class="admin-label">mahasiswa</span>
      </div>
      <div class="header-right">
        <div class="user-profile-toggle" id="userProfileToggle">
          <img src="../<?php echo htmlspecialchars($avatar_path); ?>" alt="Avatar" class="user-avatar" />
          <span class="nim"><?php echo htmlspecialchars($mahasiswa['nim']); ?></span>
          <i class="fas fa-caret-down caret-icon"></i>
        </div>
        <div id="profileDropdown" class="dropdown-menu">
          <a href="profile-mahasiswa.php">Pengaturan Profil</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>

    <div class="page-header">
      <h2>Pengaturan Profil</h2>
    </div>

    <div class="section-container profile-form">
      <h3>Ubah Profil Mahasiswa</h3>

      <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
      <?php elseif ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="form-group-profile">
          <label for="nim">nim</label>
          <input type="text" name="nim" value="<?php echo htmlspecialchars($mahasiswa['nim']); ?>" required>
        </div>

        <div class="form-group-profile">
          <label for="nama">Nama Lengkap</label>
          <input type="text" name="nama" value="<?php echo htmlspecialchars($mahasiswa['nama']); ?>" required>
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
