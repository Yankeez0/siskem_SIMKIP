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

// Inisialisasi variabel
$message = '';
$messageType = '';

// Proses tambah transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_transaksi'])) {
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $jumlah = floatval(str_replace(['.', ','], ['', '.'], $_POST['jumlah']));
    $jenis = $conn->real_escape_string($_POST['jenis']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $keterangan = $conn->real_escape_string($_POST['keterangan'] ?? '');
    
    // Validasi input
    if (empty($deskripsi) || $jumlah <= 0 || empty($jenis) || empty($kategori) || empty($tanggal)) {
        $message = 'Semua field harus diisi dengan benar';
        $messageType = 'error';
    } else {
        // Query untuk menambahkan transaksi baru
        $query = "INSERT INTO transaksi (deskripsi, jumlah, jenis, kategori, tanggal, keterangan, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdssssi", $deskripsi, $jumlah, $jenis, $kategori, $tanggal, $keterangan, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $message = 'Transaksi berhasil ditambahkan';
            $messageType = 'success';
        } else {
            $message = 'Gagal menambahkan transaksi: ' . $conn->error;
            $messageType = 'error';
        }
    }
}

// Proses hapus transaksi
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $query = "DELETE FROM transaksi WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = 'Transaksi berhasil dihapus';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus transaksi: ' . $conn->error;
        $messageType = 'error';
    }
}

// Ambil data transaksi dengan filter
$filter_jenis = isset($_GET['jenis']) ? $conn->real_escape_string($_GET['jenis']) : '';
$filter_bulan = isset($_GET['bulan']) ? $conn->real_escape_string($_GET['bulan']) : date('Y-m');
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Hitung total dana
$queryTotal = "SELECT 
    SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE 0 END) as total_pemasukan,
    SUM(CASE WHEN jenis = 'Pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran
FROM transaksi";

$totalResult = $conn->query($queryTotal);
$total = $totalResult->fetch_assoc();
$saldo = $total['total_pemasukan'] - $total['total_pengeluaran'];

// Hitung transaksi pending
$queryPending = "SELECT COUNT(*) as total_pending FROM transaksi WHERE status = 'Pending'";
$pendingResult = $conn->query($queryPending);
$pending = $pendingResult->fetch_assoc()['total_pending'];

// Query untuk daftar transaksi
$query = "SELECT t.*, u.username as operator 
          FROM transaksi t 
          LEFT JOIN users u ON t.created_by = u.id 
          WHERE 1=1";

if (!empty($filter_jenis)) {
    $query .= " AND t.jenis = '$filter_jenis'";
}

if (!empty($filter_bulan)) {
    $query .= " AND DATE_FORMAT(t.tanggal, '%Y-%m') = '$filter_bulan'";
}

if (!empty($search)) {
    $query .= " AND (t.deskripsi LIKE '%$search%' OR t.keterangan LIKE '%$search%' OR t.id = '$search')";
}

$query .= " ORDER BY t.tanggal DESC, t.id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Keuangan - Admin SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="sidebar">
        <h3>SIM - KIP</h3>
        <a href="index.php">Dashboard</a>
        <a href="rekapitulasi.php">Rekapitulasi</a>
        <a href="data-mahasiswa.php">Data Mahasiswa</a>
        <a href="keuangan.php" class="active">Keuangan</a>
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
            <h2>Manajemen Keuangan</h2>
            <p>Kelola pemasukan dan pengeluaran KIP</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Saldo Tersedia</h3>
                <p class="card-value">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></p>
                <i class="fas fa-wallet card-icon"></i>
            </div>
            <div class="card">
                <h3>Total Pemasukan</h3>
                <p class="card-value">Rp <?php echo number_format($total['total_pemasukan'] ?? 0, 0, ',', '.'); ?></p>
                <i class="fas fa-arrow-down card-icon"></i>
            </div>
            <div class="card">
                <h3>Total Pengeluaran</h3>
                <p class="card-value">Rp <?php echo number_format($total['total_pengeluaran'] ?? 0, 0, ',', '.'); ?></p>
                <i class="fas fa-arrow-up card-icon"></i>
            </div>
            <div class="card">
                <h3>Transaksi Pending</h3>
                <p class="card-value"><?php echo $pending; ?></p>
                <i class="fas fa-clock card-icon"></i>
            </div>
        </div>

        <div class="section-container">
            <div class="section-header">
                <h3>Daftar Transaksi</h3>
                <button class="button-primary" onclick="document.getElementById('tambahTransaksiModal').style.display='block'">
                    <i class="fas fa-plus"></i> Tambah Transaksi
                </button>
            </div>
            
            <form method="GET" action="" class="filter-controls">
                <select name="jenis" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    <option value="Pemasukan" <?php echo ($filter_jenis === 'Pemasukan') ? 'selected' : ''; ?>>Pemasukan</option>
                    <option value="Pengeluaran" <?php echo ($filter_jenis === 'Pengeluaran') ? 'selected' : ''; ?>>Pengeluaran</option>
                </select>
                
                <input type="month" name="bulan" value="<?php echo $filter_bulan; ?>" onchange="this.form.submit()">
                
                <input type="text" name="search" placeholder="Cari transaksi..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="button-primary"><i class="fas fa-search"></i> Cari</button>
                <a href="export-keuangan.php?<?php echo http_build_query($_GET); ?>" class="button-success">
                    <i class="fas fa-file-export"></i> Export
                </a>
            </form>

            <div class="table-container">
                <table id="transaksiTable" class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Jenis</th>
                            <th>Operator</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                    <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                                    <td class="<?php echo $row['jenis'] === 'Pemasukan' ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo $row['jenis'] === 'Pemasukan' ? '+' : '-'; ?>
                                        Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($row['jenis']); ?>">
                                            <?php echo $row['jenis']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['operator'] ?? 'System'); ?></td>
                                    <td class="action-buttons">
                                        <button class="button-edit" onclick="editTransaksi(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="keuangan.php?hapus=<?php echo $row['id']; ?>" 
                                           class="button-delete" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data transaksi</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Transaksi -->
    <div id="tambahTransaksiModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('tambahTransaksiModal').style.display='none'">&times;</span>
            <h3>Tambah Transaksi Baru</h3>
            <form method="POST" action="">
                <input type="hidden" name="tambah_transaksi" value="1">
                <div class="form-group">
                    <label for="jenis">Jenis Transaksi</label>
                    <select id="jenis" name="jenis" required>
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" required>
                        <option value="">Pilih Kategori</option>
                        <option value="KIP">KIP</option>
                        <option value="Bantuan">Bantuan</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <input type="text" id="deskripsi" name="deskripsi" required>
                </div>
                <div class="form-group">
                    <label for="jumlah">Jumlah (Rp)</label>
                    <input type="text" id="jumlah" name="jumlah" class="currency" required>
                </div>
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan (Opsional)</label>
                    <textarea id="keterangan" name="keterangan" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="button-secondary" onclick="document.getElementById('tambahTransaksiModal').style.display='none'">Batal</button>
                    <button type="submit" class="button-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            $('#transaksiTable').DataTable({
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
                "order": [[1, "desc"]] // Urutkan berdasarkan kolom tanggal (indeks 1) secara descending
            });

            // Format input mata uang
            $('.currency').on('keyup', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                if (value) {
                    value = parseInt(value).toLocaleString('id-ID');
                    $(this).val(value);
                }
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

            // Tutup modal saat klik di luar konten modal
            window.onclick = function(event) {
                const modal = document.getElementById('tambahTransaksiModal');
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        });

        // Fungsi untuk mengedit transaksi
        function editTransaksi(id) {
            // Implementasi edit transaksi
            alert('Fitur edit transaksi #' + id + ' akan segera tersedia');
            // window.location.href = 'edit-transaksi.php?id=' + id;
        }

        // Format mata uang saat form disubmit
        document.querySelector('form').addEventListener('submit', function() {
            const jumlah = document.getElementById('jumlah');
            if (jumlah) {
                jumlah.value = jumlah.value.replace(/[^0-9]/g, '');
            }
        });
    </script>
</body>
</html>