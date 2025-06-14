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

// Inisialisasi variabel filter
$angkatan = isset($_GET['angkatan']) ? intval($_GET['angkatan']) : '';
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Query untuk mendapatkan data pembayaran KIP dengan filter
$query = "SELECT p.*, m.nama_lengkap, m.nim 
          FROM pembayaran_kip p 
          JOIN mahasiswa m ON p.mahasiswa_id = m.id 
          WHERE 1=1";

$params = [];
$types = '';

if (!empty($angkatan)) {
    $query .= " AND YEAR(p.tanggal_bayar) = ?";
    $params[] = $angkatan;
    $types .= 'i';
}

if (!empty($status)) {
    $query .= " AND p.status = ?";
    $params[] = $status;
    $types .= 's';
}

if (!empty($search)) {
    $query .= " AND (m.nim LIKE ? OR m.nama_lengkap LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

$query .= " ORDER BY p.tanggal_bayar DESC";

// Eksekusi query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Hitung total pembayaran
$totalPembayaran = 0;
$pembayaranPerStatus = [
    'Lunas' => 0,
    'Belum Lunas' => 0,
    'Tertunda' => 0
];

while ($row = $result->fetch_assoc()) {
    $totalPembayaran += $row['jumlah'];
    $pembayaranPerStatus[$row['status']] = ($pembayaranPerStatus[$row['status']] ?? 0) + $row['jumlah'];
}

// Reset pointer result
$result->data_seek(0);

// Ambil daftar angkatan untuk filter
$angkatanQuery = "SELECT DISTINCT YEAR(tanggal_bayar) as tahun FROM pembayaran_kip ORDER BY tahun DESC";
$angkatanResult = $conn->query($angkatanQuery);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rekapitulasi SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="sidebar">
        <h3>SIM - KIP</h3>
        <a href="index.php">Dashboard</a>
        <a href="rekapitulasi.php" class="active">Rekapitulasi</a>
        <a href="data-mahasiswa.php">Data Mahasiswa</a>
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
            <h2>Rekapitulasi Data</h2>
            <p>Ringkasan dan laporan data penting.</p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Total Pembayaran</h3>
                <p class="card-value">Rp <?php echo number_format($totalPembayaran, 0, ',', '.'); ?></p>
                <i class="fas fa-money-bill-wave card-icon"></i>
            </div>
            <div class="card">
                <h3>Lunas</h3>
                <p class="card-value">Rp <?php echo number_format($pembayaranPerStatus['Lunas'] ?? 0, 0, ',', '.'); ?></p>
                <i class="fas fa-check-circle card-icon"></i>
            </div>
            <div class="card">
                <h3>Belum Lunas</h3>
                <p class="card-value">Rp <?php echo number_format($pembayaranPerStatus['Belum Lunas'] ?? 0, 0, ',', '.'); ?></p>
                <i class="fas fa-clock card-icon"></i>
            </div>
            <div class="card">
                <h3>Tertunda</h3>
                <p class="card-value">Rp <?php echo number_format($pembayaranPerStatus['Tertunda'] ?? 0, 0, ',', '.'); ?></p>
                <i class="fas fa-exclamation-triangle card-icon"></i>
            </div>
        </div>

        <div class="section-container">
            <h3>Rekapitulasi Pembayaran KIP</h3>
            <form method="GET" action="" class="filter-controls">
                <select name="angkatan" onchange="this.form.submit()">
                    <option value="">Semua Angkatan</option>
                    <?php while ($row = $angkatanResult->fetch_assoc()): ?>
                        <option value="<?php echo $row['tahun']; ?>" <?php echo ($angkatan == $row['tahun']) ? 'selected' : ''; ?>>
                            <?php echo $row['tahun']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <select name="status" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Lunas" <?php echo ($status == 'Lunas') ? 'selected' : ''; ?>>Lunas</option>
                    <option value="Belum Lunas" <?php echo ($status == 'Belum Lunas') ? 'selected' : ''; ?>>Belum Lunas</option>
                    <option value="Tertunda" <?php echo ($status == 'Tertunda') ? 'selected' : ''; ?>>Tertunda</option>
                </select>
                
                <input type="text" name="search" placeholder="Cari NIM/Nama..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="button-primary"><i class="fas fa-search"></i> Cari</button>
                <a href="export-rekapitulasi.php?<?php echo http_build_query($_GET); ?>" class="button-success"><i class="fas fa-download"></i> Export Data</a>
            </form>
            
            <div class="table-container">
                <table id="rekapitulasiTable" class="data-table">
                    <thead>
                        <tr>
                            <th>ID Pembayaran</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['nim']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td>Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_bayar'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <a href="detail-pembayaran.php?id=<?php echo $row['id']; ?>" class="button-view" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="cetak-kwitansi.php?id=<?php echo $row['id']; ?>" class="button-print" title="Cetak Kwitansi" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($result->num_rows === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data pembayaran</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-container">
            <h3>Statistik Pembayaran KIP</h3>
            <div class="chart-container">
                <canvas id="pembayaranChart" height="300"></canvas>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            $('#rekapitulasiTable').DataTable({
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
                "order": [[4, "desc"]] // Urutkan berdasarkan kolom tanggal (indeks 4) secara descending
            });

            // Inisialisasi grafik
            const ctx = document.getElementById('pembayaranChart').getContext('2d');
            const pembayaranChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Jumlah Pembayaran',
                        data: [12000000, 15000000, 18000000, 20000000, 22000000, 25000000, 28000000, 30000000, 28000000, 26000000, 24000000, 22000000],
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.raw.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }
                            }
                        }
                    }
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
        });
    </script>
</body>
</html>
