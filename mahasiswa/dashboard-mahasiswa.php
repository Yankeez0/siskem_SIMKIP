<?php
session_start();
require_once '../proses/config.php';

// Cek autentikasi mahasiswa
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ./../loginmahasiswa.php');
    exit;
}

$conn = getDbConnection();
$user_id = $_SESSION['id'];

// Ambil data mahasiswa
$stmt = $conn->prepare("SELECT nama, nim, avatar, status, periode_aktif FROM mahasiswa WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

$nama = htmlspecialchars($data['nama'] ?? 'Mahasiswa');
$nim = htmlspecialchars($data['nim'] ?? '-');
$status = htmlspecialchars($data['status'] ?? '-');
$periode_aktif = htmlspecialchars($data['periode_aktif'] ?? '-');

// Path avatar
$avatar_path = $data['avatar'] ?? 'assets/uploads/default.png';
$full_path = realpath(__DIR__ . '/../' . $avatar_path);
if (!$full_path || !file_exists($full_path) || empty($data['avatar'])) {
    $avatar_path = 'assets/uploads/default.png';
}

$status_class = '';
switch (strtolower($status)) {
    case 'aktif':
        $status_class = 'status-aktif';
        break;
    case 'pending':
        $status_class = 'status-pending';
        break;
    case 'tidak aktif':
        $status_class = 'status-tidak-aktif';
        break;
    default:
        $status_class = '';
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Mahasiswa - SIM-KIP</title>
  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h3>SIM - KIP</h3>
    <a href="dashboard-mahasiswa.php" class="active">Dashboard</a>
    <a href="logout.php">Logout</a>
  </div>

  <!-- Main content -->
  <main class="main-content">
    <div class="header-main">
      <div class="header-left">
        <span class="admin-label">mahasiswa</span>
      </div>
      <div class="header-right">
        <div class="user-profile-toggle" id="userProfileToggle">
          <img src="../<?php echo htmlspecialchars($avatar_path); ?>" alt="Avatar" class="user-avatar">
          <span class="username"><?php echo $nama; ?></span>
          <i class="fas fa-caret-down caret-icon"></i>
        </div>
        <div id="profileDropdown" class="dropdown-menu">
          <a href="profile-settings-mahasiswa.php">Pengaturan Profil</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>

    <div class="page-header">
      <h2>Dashboard Mahasiswa</h2>
      <p>Selamat datang, <?php echo $nama; ?>!</p>
    </div>

    <div class="dashboard-grid">
      <div class="card">
        <h3>Informasi Anda</h3>
        <p><strong>Nama:</strong> <?php echo $nama; ?></p>
        <p><strong>NIM:</strong> <?php echo $nim; ?></p>
      </div>

      <div class="card">
        <h3>Status KIP</h3>
        <p><strong>Status:</strong> <span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span></p>
        <p><strong>Periode Aktif:</strong> <?php echo $periode_aktif; ?></p>
        <p class="status-note">
          <?php
          switch ($status) {
              case 'Aktif':
                  echo 'Selamat! KIP Anda aktif.';
                  break;
              case 'Pending':
                  echo 'Status KIP masih dalam peninjauan.';
                  break;
              case 'Tidak Aktif':
                  echo 'Status KIP tidak aktif. Silakan hubungi admin.';
                  break;
              default:
                  echo 'Informasi status belum tersedia.';
          }
          ?>
        </p>
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
