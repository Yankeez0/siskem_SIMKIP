<?php
require_once 'config/auth.php';
require_once 'config/csrf.php';

// Check if user is logged in and is admin
if (!$auth->isAdmin()) {
    header("Location: login.php");
    exit;
}

// Get statistics
try {
    // Total students
    $totalStudents = $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();
    
    // Students with active KIP
    $activeKIP = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE `No KIP` IS NOT NULL")->fetchColumn();
    
    // Students by gender
    $genderStats = $pdo->query("SELECT `Jenis Kelamin`, COUNT(*) as total 
        FROM mahasiswa GROUP BY `Jenis Kelamin`")->fetchAll();
    
    // Students by province
    $provinceStats = $pdo->query("SELECT Provinsi, COUNT(*) as total 
        FROM mahasiswa GROUP BY Provinsi")->fetchAll();
    
} catch (PDOException $e) {
    $error = "Gagal mengambil data statistik: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rekapitulasi - SIM-KIP</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'templates/header.php'; ?>

        <div class="page-header">
            <h2>Rekapitulasi Data</h2>
            <p>Ringkasan statistik mahasiswa KIP.</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Total Mahasiswa</h3>
                <p class="card-value"><?php echo number_format($totalStudents); ?></p>
                <i class="fas fa-users card-icon"></i>
            </div>
            <div class="card">
                <h3>Mahasiswa KIP Aktif</h3>
                <p class="card-value"><?php echo number_format($activeKIP); ?></p>
                <i class="fas fa-user-check card-icon"></i>
            </div>
            <div class="card">
                <h3>Persentase KIP Aktif</h3>
                <p class="card-value">
                    <?php echo number_format(($activeKIP / $totalStudents) * 100, 1); ?>%
                </p>
                <i class="fas fa-percentage card-icon"></i>
            </div>
        </div>

        <div class="section-container">
            <h3>Statistik Jenis Kelamin</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($genderStats as $stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['Jenis Kelamin']); ?></td>
                            <td><?php echo number_format($stat['total']); ?></td>
                            <td><?php echo number_format(($stat['total'] / $totalStudents) * 100, 1); ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-container">
            <h3>Statistik per Provinsi</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Provinsi</th>
                            <th>Jumlah Mahasiswa</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($provinceStats as $stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['Provinsi']); ?></td>
                            <td><?php echo number_format($stat['total']); ?></td>
                            <td><?php echo number_format(($stat['total'] / $totalStudents) * 100, 1); ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-container">
            <h3>Download Laporan</h3>
            <div class="button-group">
                <a href="export/export-excel.php" class="button-primary">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
                <a href="export/export-pdf.php" class="button-secondary">
                    <i class="fas fa-file-pdf"></i> Export to PDF
                </a>
            </div>
        </div>
    </main>

    <script src="JS/script.js"></script>
</body>
</html>
