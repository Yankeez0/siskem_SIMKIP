<?php
// Memulai session
session_start();

// Memeriksa apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include file konfigurasi database
require_once 'config.php';

$message = '';
$messageType = '';

// Proses tambah mahasiswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_mahasiswa'])) {
    $nim = $conn->real_escape_string($_POST['nim']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $jenis_kelamin = $conn->real_escape_string($_POST['jenis_kelamin']);
    $tempat_tgl_lahir = $conn->real_escape_string($_POST['tempat_tgl_lahir']);
    $agama = $conn->real_escape_string($_POST['agama']);
    $nama_ibu = $conn->real_escape_string($_POST['nama_ibu']);
    $no_hp_ortu = $conn->real_escape_string($_POST['no_hp_ortu']);
    $email_ortu = $conn->real_escape_string($_POST['email_ortu']);
    
    // Generate password default (NIM)
    $password = password_hash($nim, PASSWORD_DEFAULT);
    
    // Query untuk menambahkan mahasiswa baru
    $query = "INSERT INTO mahasiswa (nim, nama_lengkap, jenis_kelamin, tempat_tgl_lahir, agama, 
              nama_ibu, no_hp_ortu, email_ortu, password, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssss", $nim, $nama, $jenis_kelamin, $tempat_tgl_lahir, 
                      $agama, $nama_ibu, $no_hp_ortu, $email_ortu, $password);
    
    if ($stmt->execute()) {
        $message = 'Mahasiswa berhasil ditambahkan';
        $messageType = 'success';
    } else {
        $message = 'Gagal menambahkan mahasiswa: ' . $conn->error;
        $messageType = 'error';
    }
}

// Proses hapus mahasiswa
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $query = "DELETE FROM mahasiswa WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = 'Mahasiswa berhasil dihapus';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus mahasiswa: ' . $conn->error;
        $messageType = 'error';
    }
}

// Ambil data mahasiswa dengan pencarian
$search = isset($_GET['cari']) ? $conn->real_escape_string($_GET['cari']) : '';
$query = "SELECT * FROM mahasiswa ";
$whereClause = [];
$params = [];
$types = '';

if (!empty($search)) {
    $whereClause[] = "(nim LIKE ? OR nama_lengkap LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

if (!empty($whereClause)) {
    $query .= " WHERE " . implode(" AND ", $whereClause);
}

$query .= " ORDER BY nama_lengkap ASC";
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Mahasiswa SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="sidebar">
        <h3>SIM - KIP</h3>
        <a href="index.php">Dashboard</a>
        <a href="rekapitulasi.php">Rekapitulasi</a>
        <a href="data-mahasiswa.php" class="active">Data Mahasiswa</a>
        <a href="keuangan.php">Keuangan</a>
        <a href="logout.php">Logout</a>
    </div>

    <main class="main-content">
        <div class="header-main">
            <div class="header-left">
                <span class="admin-label">Admin</span>
            </div>
            <div class="header-right">
                <div class="header-icons">
                    <i class="fas fa-search"></i>
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-profile-toggle" id="userProfileToggle">
                    <img src="https://via.placeholder.com/35" alt="Avatar" class="user-avatar">
                    <span class="username"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                    <i class="fas fa-caret-down caret-icon"></i>
                </div>
                <div id="profileDropdown" class="profile-dropdown">
                    <a href="profile-settings.php">Pengaturan Profil</a>
                    <a href="help.php">Bantuan</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>

        <div class="page-header">
            <h2>Data Mahasiswa</h2>
            <p>Kelola data mahasiswa KIP.</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
        <?php endif; ?>

        <div class="section-container">
            <h3>Input Data Mahasiswa</h3>
            <form method="POST" action="" class="form-section">
                <input type="hidden" name="tambah_mahasiswa" value="1">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nim">NIM</label>
                        <input type="text" id="nim" name="nim" placeholder="Masukkan NIM" required />
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" name="nama" placeholder="Masukkan Nama Lengkap" required />
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tempat_tgl_lahir">Tempat, Tgl Lahir</label>
                        <input type="text" id="tempat_tgl_lahir" name="tempat_tgl_lahir" placeholder="Contoh: Jakarta, 01-01-2000" required />
                    </div>
                    <div class="form-group">
                        <label for="agama">Agama</label>
                        <input type="text" id="agama" name="agama" placeholder="Masukkan Agama" required />
                    </div>
                    <div class="form-group">
                        <label for="nama_ibu">Nama Ibu</label>
                        <input type="text" id="nama_ibu" name="nama_ibu" placeholder="Masukkan Nama Ibu Kandung" required />
                    </div>
                    <div class="form-group">
                        <label for="no_hp_ortu">No. HP Orang Tua</label>
                        <input type="tel" id="no_hp_ortu" name="no_hp_ortu" placeholder="Contoh: 081234567890" required />
                    </div>
                    <div class="form-group">
                        <label for="email_ortu">Email Orang Tua</label>
                        <input type="email" id="email_ortu" name="email_ortu" placeholder="Contoh: email@contoh.com" />
                    </div>
                </div>
                <button type="submit" class="button-primary">Tambahkan Mahasiswa</button>
            </form>
        </div>

        <div class="section-container">
            <h3>Daftar Mahasiswa Terdaftar</h3>
            <div class="table-actions">
                <form method="GET" action="" class="search-form">
                    <input type="text" name="cari" id="searchMahasiswa" placeholder="Cari mahasiswa..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="button-primary"><i class="fas fa-search"></i> Cari</button>
                    <a href="data-mahasiswa.php" class="button-secondary">Reset</a>
                </form>
                <a href="export-mahasiswa.php" class="button-success"><i class="fas fa-file-export"></i> Export Data</a>
            </div>
            <div class="table-container">
                <table id="mahasiswaTable" class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Lengkap</th>
                            <th>Jenis Kelamin</th>
                            <th>No. HP</th>
                            <th>Status KIP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['jenis_kelamin']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['no_hp_ortu']) . "</td>";
                            echo "<td><span class='status-badge " . ($row['status_kip'] == 'Penerima' ? 'active' : 'inactive') . "'>" . 
                                 htmlspecialchars($row['status_kip'] ?? 'Non-Penerima') . "</span></td>";
                            echo "<td class='action-buttons'>";
                            echo "<a href='edit-mahasiswa.php?id=" . $row['id'] . "' class='button-edit'><i class='fas fa-edit'></i></a> ";
                            echo "<a href='data-mahasiswa.php?hapus=" . $row['id'] . "' class='button-delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'><i class='fas fa-trash-alt'></i></a> ";
                            echo "<a href='detail-mahasiswa.php?id=" . $row['id'] . "' class='button-view'><i class='fas fa-eye'></i></a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        if ($no === 1) {
                            echo "<tr><td colspan='7' class='text-center'>Tidak ada data mahasiswa</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Edit Mahasiswa -->
    <div id="editMahasiswaModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Edit Data Mahasiswa</h3>
            <form id="editMahasiswaForm" class="form-section">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_nim">NIM</label>
                        <input type="text" id="edit_nim" name="nim" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama">Nama</label>
                        <input type="text" id="edit_nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_jenis_kelamin">Jenis Kelamin</label>
                        <select id="edit_jenis_kelamin" name="jenis_kelamin" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_tempat_tgl_lahir">Tempat, Tgl Lahir</label>
                        <input type="text" id="edit_tempat_tgl_lahir" name="tempat_tgl_lahir" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_agama">Agama</label>
                        <input type="text" id="edit_agama" name="agama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama_ibu">Nama Ibu</label>
                        <input type="text" id="edit_nama_ibu" name="nama_ibu" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_no_hp_ortu">No. HP Orang Tua</label>
                        <input type="tel" id="edit_no_hp_ortu" name="no_hp_ortu" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email_ortu">Email Orang Tua</label>
                        <input type="email" id="edit_email_ortu" name="email_ortu">
                    </div>
                    <div class="form-group">
                        <label for="edit_status_kip">Status KIP</label>
                        <select id="edit_status_kip" name="status_kip">
                            <option value="Penerima">Penerima</option>
                            <option value="Non-Penerima">Non-Penerima</option>
                            <option value="Dalam Proses">Dalam Proses</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="button-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            $('#mahasiswaTable').DataTable({
                "pageLength": 10,
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data",
                    "infoFiltered": "(disaring dari _MAX_ total data)",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                },
                "columnDefs": [
                    { "orderable": false, "targets": [0, 6] } // Non-aktifkan pengurutan untuk kolom No dan Aksi
                ]
            });

            // Toggle dropdown profil
            const profileToggle = document.getElementById('userProfileToggle');
            const profileDropdown = document.getElementById('profileDropdown');
            
            if (profileToggle && profileDropdown) {
                profileToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('show');
                });

                // Tutup dropdown saat klik di luar
                document.addEventListener('click', function() {
                    if (profileDropdown.classList.contains('show')) {
                        profileDropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>
</html>
