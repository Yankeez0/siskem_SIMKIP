<?php
session_start();
require_once '../proses/config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$conn = getDbConnection();
$user_id = $_SESSION['id'];

$stmt = $conn->prepare("SELECT username, avatar FROM admin WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

$username = htmlspecialchars($admin['username'] ?? 'Admin');
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rekapitulasi SIM-KIP</title>
  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    <h3>SIM - KIP</h3>
    <a href="index.php">Dashboard</a>
    <a href="rekapitulasi.php" class="active">Rekapitulasi</a>
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
      <h2>Rekapitulasi Data</h2>
      <p>Data seluruh mahasiswa dan pembayaran terakhirnya.</p>
    </div>

    <div class="section-container">
      <table border="1" width="100%">
        <thead>
          <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama</th>
            <th>Status KIP</th>
            <th>Periode Aktif</th>
            <th>Pembayaran Terakhir</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "
            SELECT 
              m.nim,
              m.nama,
              m.status,
              m.periode_aktif,
              MAX(p.tanggal_cair) AS pembayaran_terakhir
            FROM mahasiswa m
            LEFT JOIN pembayaran p ON m.id = p.mahasiswa_id
            GROUP BY m.id
            ORDER BY m.nama ASC
          ";
          $result = $conn->query($query);
          $no = 1;
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $no++ . "</td>";
              echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
              echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
              echo "<td>" . htmlspecialchars($row['status']) . "</td>";
              echo "<td>" . htmlspecialchars($row['periode_aktif']) . "</td>";
              echo "<td>" . ($row['pembayaran_terakhir'] ?? '-') . "</td>";
              echo "</tr>";
          }
          ?>
        </tbody>
      </table>
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
