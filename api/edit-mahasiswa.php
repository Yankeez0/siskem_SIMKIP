<?php
require_once 'config/auth.php';

// Check if user is logged in and is admin
if (!$auth->isAdmin()) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: data-mahasiswa.php");
    exit;
}

// Handle form submission for updating student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE mahasiswa SET 
            `Nomor Induk Mahasiswa (NIM)` = ?,
            `Nama Siswa` = ?,
            `Jenis Kelamin` = ?,
            `Tempat Lahir` = ?,
            `Tanggal Lahir` = ?,
            `Agama` = ?,
            `Nama Ibu` = ?,
            `No Handphone` = ?,
            `Email` = ?
            WHERE `id Mahasiswa` = ?");
        
        // Extract date from tempat_tgl_lahir
        $tempat_tgl_lahir = $_POST['tempat_tgl_lahir'];
        preg_match('/^(.*?),\s*(\d{2}-\d{2}-\d{4})$/', $tempat_tgl_lahir, $matches);
        $tempat_lahir = $matches[1];
        $tanggal_lahir = date('Y-m-d', strtotime(str_replace('-', '/', $matches[2])));
        
        $stmt->execute([
            $_POST['nim'],
            $_POST['nama'],
            $_POST['jenis_kelamin'],
            $tempat_lahir,
            $tanggal_lahir,
            $_POST['agama'],
            $_POST['nama_ibu'],
            $_POST['no_hp_ortu'],
            $_POST['email_ortu'],
            $id
        ]);
        
        // If password change is requested
        if (!empty($_POST['new_password'])) {
            $hashData = Security::hashPassword($_POST['new_password']);
            $stmt = $pdo->prepare("UPDATE mahasiswa SET 
                `Password` = ?,
                `salt` = ?
                WHERE `id Mahasiswa` = ?");
            $stmt->execute([$hashData['hash'], $hashData['salt'], $id]);
        }
        
        $success = "Data mahasiswa berhasil diperbarui.";
    } catch (PDOException $e) {
        $error = "Gagal memperbarui data: " . $e->getMessage();
    }
}

// Fetch student data
try {
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE `id Mahasiswa` = ?");
    $stmt->execute([$id]);
    $mahasiswa = $stmt->fetch();
    
    if (!$mahasiswa) {
        header("Location: data-mahasiswa.php");
        exit;
    }
} catch (PDOException $e) {
    $error = "Gagal mengambil data mahasiswa: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Data Mahasiswa - SIM-KIP</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'templates/header.php'; ?>

        <div class="page-header">
            <h2>Edit Data Mahasiswa</h2>
            <p>Edit data mahasiswa KIP.</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="section-container">
            <h3>Edit Data Mahasiswa</h3>
            <form class="form-section" method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nim">NIM</label>
                        <input type="text" id="nim" name="nim" 
                               value="<?php echo htmlspecialchars($mahasiswa['Nomor Induk Mahasiswa (NIM)']); ?>" 
                               required />
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" name="nama" 
                               value="<?php echo htmlspecialchars($mahasiswa['Nama Siswa']); ?>" 
                               required />
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="Laki-Laki" <?php echo $mahasiswa['Jenis Kelamin'] == 'Laki-Laki' ? 'selected' : ''; ?>>
                                Laki-laki
                            </option>
                            <option value="Perempuan" <?php echo $mahasiswa['Jenis Kelamin'] == 'Perempuan' ? 'selected' : ''; ?>>
                                Perempuan
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tempat_tgl_lahir">Tempat, Tgl Lahir</label>
                        <input type="text" id="tempat_tgl_lahir" name="tempat_tgl_lahir" 
                               value="<?php echo htmlspecialchars($mahasiswa['Tempat Lahir'] . ', ' . 
                                     date('d-m-Y', strtotime($mahasiswa['Tanggal Lahir']))); ?>" 
                               required />
                    </div>
                    <div class="form-group">
                        <label for="agama">Agama</label>
                        <input type="text" id="agama" name="agama" 
                               value="<?php echo htmlspecialchars($mahasiswa['Agama']); ?>" 
                               required />
                    </div>
                    <div class="form-group">
                        <label for="nama_ibu">Nama Ibu</label>
                        <input type="text" id="nama_ibu" name="nama_ibu" 
                               value="<?php echo htmlspecialchars($mahasiswa['Nama Ibu']); ?>" 
                               required />
                    </div>
                    <div class="form-group">
                        <label for="no_hp_ortu">No. HP Orang Tua</label>
                        <input type="tel" id="no_hp_ortu" name="no_hp_ortu" 
                               value="<?php echo htmlspecialchars($mahasiswa['No Handphone']); ?>" 
                               required />
                    </div>
                    <div class="form-group">
                        <label for="email_ortu">Email Orang Tua</label>
                        <input type="email" id="email_ortu" name="email_ortu" 
                               value="<?php echo htmlspecialchars($mahasiswa['Email']); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="new_password">Password Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" id="new_password" name="new_password" />
                    </div>
                </div>
                <button type="submit" class="button-primary">Simpan Perubahan</button>
                <a href="data-mahasiswa.php" class="button-secondary">Batal</a>
            </form>
        </div>
    </main>

    <script src="JS/script.js"></script>
</body>
</html>
