<?php
require_once 'config/auth.php';
require_once 'config/csrf.php';

// Check if user is logged in and is a student
if (!$auth->isMahasiswa()) {
    header("Location: login-mahasiswa.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token. Please try again.";
    } else {
        try {
            // Insert the change request
            $stmt = $pdo->prepare("INSERT INTO perubahan_data 
                (mahasiswa_id, field_name, current_value, requested_value, alasan, status, tanggal_pengajuan)
                VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
            
            $stmt->execute([
                $_SESSION['user_id'],
                $_POST['field_name'],
                $_POST['current_value'],
                $_POST['requested_value'],
                $_POST['alasan']
            ]);
            
            // Handle file upload if exists
            if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] == 0) {
                $targetDir = "uploads/dokumen/";
                $fileName = $_SESSION['user_id'] . "_" . date('YmdHis') . "_" . basename($_FILES["dokumen"]["name"]);
                $targetFile = $targetDir . $fileName;
                
                if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $targetFile)) {
                    // Update the request with document path
                    $stmt = $pdo->prepare("UPDATE perubahan_data 
                        SET dokumen_pendukung = ? 
                        WHERE id = LAST_INSERT_ID()");
                    $stmt->execute([$fileName]);
                }
            }
            
            $success = "Pengajuan perubahan data berhasil disubmit.";
        } catch (PDOException $e) {
            $error = "Gagal mengajukan perubahan: " . $e->getMessage();
        }
    }
}

// Fetch student data
try {
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE `id Mahasiswa` = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $mahasiswa = $stmt->fetch();
    
    // Fetch previous requests
    $stmt = $pdo->prepare("SELECT * FROM perubahan_data WHERE mahasiswa_id = ? ORDER BY tanggal_pengajuan DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $previous_requests = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Gagal mengambil data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ajukan Perubahan Data - SIM-KIP</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'templates/header.php'; ?>

        <div class="page-header">
            <h2>Ajukan Perubahan Data</h2>
            <p>Ajukan perubahan data pribadi Anda.</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="section-container">
            <h3>Form Pengajuan Perubahan</h3>
            <form class="form-section" method="POST" action="" enctype="multipart/form-data">
                <?php echo CSRF::getTokenField(); ?>
                
                <div class="form-group">
                    <label for="field_name">Data yang Ingin Diubah</label>
                    <select name="field_name" id="field_name" required onchange="updateCurrentValue()">
                        <option value="">Pilih Data</option>
                        <option value="No Handphone">Nomor HP</option>
                        <option value="Email">Email</option>
                        <option value="Alamat">Alamat</option>
                        <option value="Provinsi">Provinsi</option>
                        <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="current_value">Nilai Saat Ini</label>
                    <input type="text" id="current_value" name="current_value" readonly>
                </div>

                <div class="form-group">
                    <label for="requested_value">Nilai yang Diinginkan</label>
                    <input type="text" id="requested_value" name="requested_value" required>
                </div>

                <div class="form-group">
                    <label for="alasan">Alasan Perubahan</label>
                    <textarea id="alasan" name="alasan" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="dokumen">Dokumen Pendukung (opsional)</label>
                    <input type="file" id="dokumen" name="dokumen" accept=".pdf,.jpg,.jpeg,.png">
                    <small>Format yang diizinkan: PDF, JPG, JPEG, PNG (max 2MB)</small>
                </div>

                <button type="submit" class="button-primary">Submit Pengajuan</button>
            </form>
        </div>

        <div class="section-container">
            <h3>Riwayat Pengajuan</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Data</th>
                            <th>Nilai Lama</th>
                            <th>Nilai Baru</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($previous_requests as $request): ?>
                        <tr>
                            <td><?php echo date('d-m-Y', strtotime($request['tanggal_pengajuan'])); ?></td>
                            <td><?php echo htmlspecialchars($request['field_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['current_value']); ?></td>
                            <td><?php echo htmlspecialchars($request['requested_value']); ?></td>
                            <td>
                                <span class="status-badge <?php echo strtolower($request['status']); ?>">
                                    <?php echo htmlspecialchars($request['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($request['keterangan'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function updateCurrentValue() {
            const field = document.getElementById('field_name').value;
            const currentValueField = document.getElementById('current_value');
            
            // Data from PHP variable
            const mahasiswaData = <?php echo json_encode($mahasiswa); ?>;
            
            if (field && mahasiswaData[field]) {
                currentValueField.value = mahasiswaData[field];
            } else {
                currentValueField.value = '';
            }
        }

        // File size validation
        document.getElementById('dokumen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.size > 2 * 1024 * 1024) { // 2MB
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                e.target.value = '';
            }
        });
    </script>
</body>
</html>
