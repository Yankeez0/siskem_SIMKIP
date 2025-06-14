<?php
require_once 'config/auth.php';

// Check if user is logged in and is a student
if (!$auth->isMahasiswa()) {
    header("Location: login-mahasiswa.php");
    exit;
}

// Fetch student data
try {
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE `id Mahasiswa` = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $mahasiswa = $stmt->fetch();
    
    if (!$mahasiswa) {
        header("Location: logout.php");
        exit;
    }
} catch (PDOException $e) {
    $error = "Gagal mengambil data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Mahasiswa - SIM-KIP</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'templates/header.php'; ?>

        <div class="page-header">
            <h2>Dashboard Mahasiswa</h2>
            <p>Selamat datang, <?php echo htmlspecialchars($mahasiswa['Nama Siswa']); ?>!</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="section-container">
            <div class="profile-info-display">
                <h3>Informasi Mahasiswa</h3>
                <div class="info-group">
                    <label>NIM</label>
                    <span><?php echo htmlspecialchars($mahasiswa['Nomor Induk Mahasiswa (NIM)']); ?></span>
                </div>
                <div class="info-group">
                    <label>Nama Lengkap</label>
                    <span><?php echo htmlspecialchars($mahasiswa['Nama Siswa']); ?></span>
                </div>
                <div class="info-group">
                    <label>Jenis Kelamin</label>
                    <span><?php echo htmlspecialchars($mahasiswa['Jenis Kelamin']); ?></span>
                </div>
                <div class="info-group">
                    <label>Tempat, Tanggal Lahir</label>
                    <span><?php echo htmlspecialchars($mahasiswa['Tempat Lahir'] . ', ' . 
                          date('d-m-Y', strtotime($mahasiswa['Tanggal Lahir']))); ?></span>
                </div>
                <div class="info-group">
                    <label>Agama</label>
                    <span><?php echo htmlspecialchars($mahasiswa['Agama']); ?></span>
                </div>
                <div class="info-group">
                    <label>Nama Ibu Kandung</label>
                    <span><?php echo htmlspecialchars($mahasiswa['Nama Ibu']); ?></span>
                </div>
                <div class="info-group">
                    <label>No. HP</label>
                    <span><?php echo htmlspecialchars($mahasiswa['No Handphone']); ?></span>
                </div>
                <div class="info-group">
                    <label>Email</label>
                    <span><?php echo htmlspecialchars($mahasiswa['Email']); ?></span>
                </div>
            </div>
        </div>

        <div class="section-container">
            <h3>Status KIP</h3>
            <div class="status-display">
                <div class="info-group">
                    <label>Status KIP</label>
                    <?php
                    $status = !empty($mahasiswa['No KIP']) ? 'aktif' : 'tidak-aktif';
                    $statusText = !empty($mahasiswa['No KIP']) ? 'Aktif' : 'Tidak Aktif';
                    ?>
                    <span class="status-badge <?php echo $status; ?>">
                        <?php echo $statusText; ?>
                    </span>
                </div>
                <?php if (!empty($mahasiswa['No KIP'])): ?>
                <div class="info-group">
                    <label>Nomor KIP</label>
                    <span><?php echo htmlspecialchars($mahasiswa['No KIP']); ?></span>
                </div>
                <?php endif; ?>
                <p class="status-note">
                    <?php if (empty($mahasiswa['No KIP'])): ?>
                        Anda belum memiliki KIP aktif. Silakan hubungi admin untuk informasi lebih lanjut.
                    <?php else: ?>
                        KIP Anda aktif dan dapat digunakan.
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="section-container">
            <h3>Pengajuan Perubahan Data</h3>
            <p>Jika ada perubahan data, silakan ajukan melalui form perubahan data.</p>
            <a href="ajukan-perubahan-data.php" class="button-primary">
                <i class="fas fa-edit"></i> Ajukan Perubahan Data
            </a>
        </div>

    </main>

    <script src="JS/script.js"></script>
</body>
</html>
