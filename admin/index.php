<?php
session_start();
require_once '../proses/config.php';

// Cek login dan role admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ./../login.php');
    exit;
}

$conn = getDbConnection();
$user_id = $_SESSION['id'];

// Ambil data admin
$stmt = $conn->prepare("SELECT username, avatar FROM admin WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

$username = htmlspecialchars($admin['username'] ?? 'Admin');

// Path avatar dari database
$avatar_path = $admin['avatar'] ?? 'assets/uploads/default.png';

// Validasi file fisik
$full_path = realpath(__DIR__ . '/../' . $avatar_path);
if (!$full_path || !file_exists($full_path) || empty($admin['avatar'])) {
    $avatar_path = 'assets/uploads/default.png';
}

// Data dashboard
$total_mahasiswa = 0;
$mahasiswa_aktif = 0;
$mahasiswa_tidak_aktif = 0;
$dokumen_baru = 0;

$q1 = $conn->query("SELECT COUNT(*) AS total FROM mahasiswa");
if ($q1) $total_mahasiswa = $q1->fetch_assoc()['total'] ?? 0;

$q2 = $conn->query("SELECT COUNT(*) AS aktif FROM mahasiswa WHERE status = 'aktif'");
if ($q2) $mahasiswa_aktif = $q2->fetch_assoc()['aktif'] ?? 0;

$q3 = $conn->query("SELECT COUNT(*) AS tidak_aktif FROM mahasiswa WHERE status != 'aktif'");
if ($q3) $mahasiswa_tidak_aktif = $q3->fetch_assoc()['tidak_aktif'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard SIM-KIP</title>
  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    <h3>SIM - KIP</h3>
    <a href="index.php" class="active">Dashboard</a>
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
          <img src="../<?php echo htmlspecialchars($avatar_path); ?>" alt="Avatar" class="user-avatar">
          <span class="username"><?php echo $username; ?></span>
          <i class="fas fa-caret-down caret-icon"></i>
        </div>
        <div id="profileDropdown" class="dropdown-menu">
          <a href="profile-settings.php">Pengaturan Profil</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>

    <div class="page-header">
      <h2>Dashboard</h2>
      <p>Selamat datang, <?php echo $username; ?>!</p>
    </div>

    <div class="dashboard-grid">
      <div class="card">
        <h3>Total Mahasiswa</h3>
        <p class="card-value"><?php echo $total_mahasiswa; ?></p>
        <i class="fas fa-users card-icon"></i>
      </div>
      <div class="card">
        <h3>Mahasiswa Aktif KIP</h3>
        <p class="card-value"><?php echo $mahasiswa_aktif; ?></p>
        <i class="fas fa-user-check card-icon"></i>
      </div>
      <div class="card">
        <h3>Mahasiswa Tidak Aktif</h3>
        <p class="card-value"><?php echo $mahasiswa_tidak_aktif; ?></p>
        <i class="fas fa-user-times card-icon"></i>
      </div>
      <div class="card">
        <h3>Dokumen Baru</h3>
        <p class="card-value"><?php echo $dokumen_baru; ?></p>
        <i class="fas fa-file-alt card-icon"></i>
      </div>
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
