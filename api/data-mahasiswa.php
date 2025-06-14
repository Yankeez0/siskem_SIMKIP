<?php
require_once 'config/auth.php';

// Check if user is logged in and is admin
if (!$auth->isAdmin()) {
    header("Location: login.php");
    exit;
}

// Handle form submission for adding new student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Generate a secure password for the new student
        $password = bin2hex(random_bytes(8)); // Generate random initial password
        $hashData = Security::hashPassword($password);
        
        $stmt = $pdo->prepare("INSERT INTO mahasiswa 
            (`Nomor Induk Mahasiswa (NIM)`, `Nama Siswa`, `Password`, `salt`,
            `Jenis Kelamin`, `Tempat Lahir`, `Tanggal Lahir`, `Agama`, 
            `Nama Ibu`, `No Handphone`, `Email`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Extract date from tempat_tgl_lahir
        $tempat_tgl_lahir = $_POST['tempat_tgl_lahir'];
        preg_match('/^(.*?),\s*(\d{2}-\d{2}-\d{4})$/', $tempat_tgl_lahir, $matches);
        $tempat_lahir = $matches[1];
        $tanggal_lahir = date('Y-m-d', strtotime(str_replace('-', '/', $matches[2])));
        
        $stmt->execute([
            $_POST['nim'],
            $_POST['nama'],
            $hashData['hash'],
            $hashData['salt'],
            $_POST['jenis_kelamin'],
            $tempat_lahir,
            $tanggal_lahir,
            $_POST['agama'],
            $_POST['nama_ibu'],
            $_POST['no_hp_ortu'],
            $_POST['email_ortu']
        ]);
        
        $success = "Data mahasiswa berhasil ditambahkan. Password awal: " . $password;
    } catch (PDOException $e) {
        $error = "Gagal menambahkan data: " . $e->getMessage();
    }
}

// Fetch existing students
try {
    $stmt = $pdo->query("SELECT * FROM mahasiswa ORDER BY `Nomor Induk Mahasiswa (NIM)`");
    $mahasiswa = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Gagal mengambil data mahasiswa: " . $e->getMessage();
    $mahasiswa = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Mahasiswa SIM-KIP</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'templates/header.php'; ?>

        <div class="page-header">
            <h2>Data Mahasiswa</h2>
            <p>Kelola data mahasiswa KIP.</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="section-container">
            <h3>Input Data Mahasiswa</h3>
            <form id="addMahasiswaForm" class="form-section" method="POST" action="">
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
                            <option value="Laki-Laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tempat_tgl_lahir">Tempat, Tgl Lahir</label>
                        <input type="text" id="tempat_tgl_lahir" name="tempat_tgl_lahir" 
                               placeholder="Contoh: Jakarta, 01-01-2000" required />
                    </div>
                    <div class="form-group">
                        <label for="agama">Agama</label>
                        <input type="text" id="agama" name="agama" placeholder="Masukkan Agama" required />
                    </div>
                    <div class="form-group">
                        <label for="nama_ibu">Nama Ibu</label>
                        <input type="text" id="nama_ibu" name="nama_ibu" 
                               placeholder="Masukkan Nama Ibu Kandung" required />
                    </div>
                    <div class="form-group">
                        <label for="no_hp_ortu">No. HP Orang Tua</label>
                        <input type="tel" id="no_hp_ortu" name="no_hp_ortu" 
                               placeholder="Contoh: 081234567890" required />
                    </div>
                    <div class="form-group">
                        <label for="email_ortu">Email Orang Tua</label>
                        <input type="email" id="email_ortu" name="email_ortu" 
                               placeholder="Contoh: email@contoh.com" />
                    </div>
                </div>
                <button type="submit" class="button-primary">Tambahkan Mahasiswa</button>
            </form>
        </div>

        <div class="section-container">
            <h3>Daftar Mahasiswa Terdaftar</h3>
            <div class="table-actions">
                <input type="text" id="searchMahasiswa" placeholder="Cari mahasiswa...">
                <button class="button-primary"><i class="fas fa-filter"></i> Filter</button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat, Tgl Lahir</th>
                            <th>Agama</th>
                            <th>Nama Ibu</th>
                            <th>No. HP</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mahasiswa as $mhs): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mhs['Nomor Induk Mahasiswa (NIM)']); ?></td>
                            <td><?php echo htmlspecialchars($mhs['Nama Siswa']); ?></td>
                            <td><?php echo htmlspecialchars($mhs['Jenis Kelamin']); ?></td>
                            <td><?php echo htmlspecialchars($mhs['Tempat Lahir'] . ', ' . 
                                date('d-m-Y', strtotime($mhs['Tanggal Lahir']))); ?></td>
                            <td><?php echo htmlspecialchars($mhs['Agama']); ?></td>
                            <td><?php echo htmlspecialchars($mhs['Nama Ibu']); ?></td>
                            <td><?php echo htmlspecialchars($mhs['No Handphone']); ?></td>
                            <td><?php echo htmlspecialchars($mhs['Email']); ?></td>
                            <td class="action-buttons">
                                <a href="edit-mahasiswa.php?id=<?php echo $mhs['id Mahasiswa']; ?>" 
                                   class="button-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="delete-button" 
                                        onclick="deleteMahasiswa(<?php echo $mhs['id Mahasiswa']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="JS/script.js"></script>
</body>
</html>
