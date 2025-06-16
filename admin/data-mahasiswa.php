<?php
session_start();
require_once '../proses/config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
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
  <title>Data Mahasiswa SIM-KIP</title>
  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    <h3>SIM - KIP</h3>
    <a href="index.php">Dashboard</a>
    <a href="rekapitulasi.php">Rekapitulasi</a>
    <a href="data-mahasiswa.php" class="active">Data Mahasiswa</a>
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
      <h2>Data Mahasiswa</h2>
      <p>Kelola data mahasiswa KIP.</p>
    </div>

    <div class="section-container">
      <h3>Daftar Mahasiswa Terdaftar</h3>

      <button class="button-primary" onclick="document.getElementById('formModal').style.display='flex'">
        + Tambah Mahasiswa
      </button>

      <div class="table-container">
        <table border="1" width="100%">
          <thead>
            <tr>
              <th>No</th>
              <th>NIM</th>
              <th>Nama</th>
              <th>Jenis Kelamin</th>
              <th>Tempat, Tgl Lahir</th>
              <th>Agama</th>
              <th>Nama Ibu</th>
              <th>No. Rekening</th>
              <th>No. HP Ortu</th>
              <th>Email Ortu</th>
              <th>Status KIP</th>
              <th>Periode Aktif</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $result = $conn->query("SELECT * FROM mahasiswa ORDER BY nama ASC");
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                $tgl_lahir = date('d-m-Y', strtotime($row['tanggal_lahir']));
                $tempat_lahir = $row['tempat_lahir'] ?? '-';
                $nama_ibu = !empty($row['nama_ibu']) ? $row['nama_ibu'] : '-';
                $no_rekening = !empty($row['no_rekening']) ? $row['no_rekening'] : '-';

                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                echo '<td>' . htmlspecialchars($row['nim']) . '</td>';
                echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                echo '<td>' . htmlspecialchars($row['jenis_kelamin']) . '</td>';
                echo '<td>' . htmlspecialchars($tempat_lahir . ', ' . $tgl_lahir) . '</td>';
                echo '<td>' . htmlspecialchars($row['agama']) . '</td>';
                echo '<td>' . htmlspecialchars($nama_ibu) . '</td>';
                echo '<td>' . htmlspecialchars($no_rekening) . '</td>';
                echo '<td>' . htmlspecialchars($row['no_hp_ortu']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email_ortu']) . '</td>';
                echo '<td>' . htmlspecialchars($row['status'] ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($row['periode_aktif'] ?? '-') . '</td>';
                echo '<td>
                <button onclick="openEditStatusModal(' . $row['id'] . ', \'' . $row['status'] . '\', \'' . $row['periode_aktif'] . '\')">Edit Status</button>
                <form action="hapus-mahasiswa.php" method="post" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus mahasiswa ini?\')">
                  <input type="hidden" name="id" value="' . $row['id'] . '">
                  <button type="submit" class="button-danger">Hapus</button>
                </form>
              </td>';
                echo '</tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Modal Form Tambah Mahasiswa -->
  <div id="formModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('formModal').style.display='none'">&times;</span>
      <h3>Tambah Mahasiswa Baru</h3>
      <form action="tambah-mahasiswa.php" method="post">
        <input type="text" name="nim" placeholder="NIM" required>
        <input type="text" name="nama" placeholder="Nama Lengkap" required>

        <select name="jenis_kelamin" required>
          <option value="" disabled selected>Pilih Jenis Kelamin</option>
          <option value="Laki-laki">Laki-laki</option>
          <option value="Perempuan">Perempuan</option>
        </select>

        <input type="text" name="tempat_lahir" placeholder="Tempat Lahir" required>
        <input type="date" name="tanggal_lahir" required>
        <input type="text" name="agama" placeholder="Agama" required>
        <input type="text" name="nama_ibu" placeholder="Nama Ibu" required>
        <input type="text" name="no_rekening" placeholder="Nomor Rekening" required>
        <input type="text" name="no_hp_ortu" placeholder="No HP Orang Tua" required>
        <input type="email" name="email_ortu" placeholder="Email Orang Tua" required>
        <input type="password" name="password" placeholder="Password Akun" required>
        <button type="submit" class="button-primary">Simpan</button>
      </form>
    </div>
  </div>

  <!-- Modal Edit Status -->
  <div id="editStatusModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('editStatusModal').style.display='none'">&times;</span>
      <h3>Edit Status KIP Mahasiswa</h3>
      <form action="update-status.php" method="post">
        <input type="hidden" name="id" id="editId">
        
        <label>Status KIP:</label>
        <select name="status" id="editStatus" required>
          <option value="Aktif">Aktif</option>
          <option value="Pending">Pending</option>
          <option value="Tidak Aktif">Tidak Aktif</option>
        </select>

        <label>Periode Aktif:</label>
        <input type="text" name="periode_aktif" id="editPeriodeAktif" placeholder="Contoh: 2022/2023" required>

        <button type="submit" class="button-primary">Simpan Perubahan</button>
      </form>
    </div>
  </div>

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

    const modal = document.getElementById('formModal');
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
        modal.style.display = 'none';
        }
    });
    function openEditStatusModal(id, status, periode) {
      document.getElementById('editId').value = id;
      document.getElementById('editStatus').value = status;
      document.getElementById('editPeriodeAktif').value = periode;
      document.getElementById('editStatusModal').style.display = 'flex';
    }
  </script>
</body>
</html>
