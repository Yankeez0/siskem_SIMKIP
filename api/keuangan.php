<?php
require_once 'config/auth.php';
require_once 'config/csrf.php';

// Check if user is logged in and is admin
if (!$auth->isAdmin()) {
    header("Location: login.php");
    exit;
}

// Handle form submission for updating payment status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment'])) {
    if (!CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token. Please try again.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE mahasiswa SET 
                status_pembayaran = ?,
                tanggal_pencairan = ?,
                jumlah_pencairan = ?,
                keterangan = ?
                WHERE `id Mahasiswa` = ?");
            
            $stmt->execute([
                $_POST['status_pembayaran'],
                $_POST['tanggal_pencairan'],
                $_POST['jumlah_pencairan'],
                $_POST['keterangan'],
                $_POST['mahasiswa_id']
            ]);
            
            $success = "Status pembayaran berhasil diperbarui.";
        } catch (PDOException $e) {
            $error = "Gagal memperbarui status: " . $e->getMessage();
        }
    }
}

// Fetch all students with KIP
try {
    $stmt = $pdo->query("SELECT * FROM mahasiswa WHERE `No KIP` IS NOT NULL ORDER BY `Nomor Induk Mahasiswa (NIM)`");
    $mahasiswa = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Gagal mengambil data: " . $e->getMessage();
    $mahasiswa = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Keuangan - SIM-KIP</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'templates/header.php'; ?>

        <div class="page-header">
            <h2>Pengelolaan Keuangan KIP</h2>
            <p>Kelola pencairan dana KIP mahasiswa.</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="section-container">
            <h3>Data Pencairan KIP</h3>
            <div class="table-actions">
                <input type="text" id="searchMahasiswa" placeholder="Cari mahasiswa...">
                <button class="button-primary" onclick="exportData()">
                    <i class="fas fa-download"></i> Export Data
                </button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>No KIP</th>
                            <th>Status</th>
                            <th>Tanggal Pencairan</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mahasiswa as $mhs): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mhs['Nomor Induk Mahasiswa (NIM)']); ?></td>
                            <td><?php echo htmlspecialchars($mhs['Nama Siswa']); ?></td>
                            <td><?php echo htmlspecialchars($mhs['No KIP']); ?></td>
                            <td>
                                <span class="status-badge <?php echo strtolower($mhs['status_pembayaran'] ?? 'pending'); ?>">
                                    <?php echo htmlspecialchars($mhs['status_pembayaran'] ?? 'Pending'); ?>
                                </span>
                            </td>
                            <td><?php echo $mhs['tanggal_pencairan'] ? date('d-m-Y', strtotime($mhs['tanggal_pencairan'])) : '-'; ?></td>
                            <td><?php echo $mhs['jumlah_pencairan'] ? 'Rp ' . number_format($mhs['jumlah_pencairan'], 0, ',', '.') : '-'; ?></td>
                            <td><?php echo htmlspecialchars($mhs['keterangan'] ?? '-'); ?></td>
                            <td>
                                <button class="button-secondary" onclick="updatePayment(<?php echo $mhs['id Mahasiswa']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal untuk update pembayaran -->
        <div id="paymentModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Update Status Pembayaran</h3>
                <form id="paymentForm" method="POST" action="">
                    <?php echo CSRF::getTokenField(); ?>
                    <input type="hidden" name="update_payment" value="1">
                    <input type="hidden" name="mahasiswa_id" id="mahasiswa_id">
                    
                    <div class="form-group">
                        <label for="status_pembayaran">Status</label>
                        <select name="status_pembayaran" id="status_pembayaran" required>
                            <option value="Pending">Pending</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Dicairkan">Dicairkan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="tanggal_pencairan">Tanggal Pencairan</label>
                        <input type="date" name="tanggal_pencairan" id="tanggal_pencairan">
                    </div>
                    
                    <div class="form-group">
                        <label for="jumlah_pencairan">Jumlah (Rp)</label>
                        <input type="number" name="jumlah_pencairan" id="jumlah_pencairan">
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="button-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Search functionality
        document.getElementById('searchMahasiswa').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Modal functionality
        const modal = document.getElementById('paymentModal');
        const span = document.getElementsByClassName('close')[0];

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function updatePayment(id) {
            document.getElementById('mahasiswa_id').value = id;
            modal.style.display = "block";
        }

        function exportData() {
            window.location.href = 'export/export-keuangan.php';
        }
    </script>
</body>
</html>
