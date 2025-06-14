<?php
// Memulai session
session_start();

// Memeriksa apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: login.php");
    exit();
}

// Include file konfigurasi database
require_once 'config.php';

// Ambil data mahasiswa yang sedang login
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM mahasiswa WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$mahasiswa = $result->fetch_assoc();

// Proses form jika ada pengajuan perubahan
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_baru = $_POST['requestNama'] ?? '';
    $jenis_kelamin_baru = $_POST['requestJenisKelamin'] ?? '';
    $tempat_tgl_lahir_baru = $_POST['requestTempatTglLahir'] ?? '';
    $agama_baru = $_POST['requestAgama'] ?? '';
    $nama_ibu_baru = $_POST['requestNamaIbu'] ?? '';
    $no_hp_ortu_baru = $_POST['requestNoHpOrtu'] ?? '';
    $email_ortu_baru = $_POST['requestEmailOrtu'] ?? '';
    $alasan = $_POST['alasanPerubahan'] ?? '';
    
    // Validasi data
    if (empty($alasan)) {
        $message = 'Alasan perubahan harus diisi';
        $messageType = 'error';
    } else {
        // Simpan pengajuan perubahan ke database
        $query = "INSERT INTO pengajuan_perubahan 
                 (mahasiswa_id, nama_baru, jenis_kelamin_baru, tempat_tgl_lahir_baru, agama_baru, 
                  nama_ibu_baru, no_hp_ortu_baru, email_ortu_baru, alasan, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu', NOW())";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issssssss", $user_id, $nama_baru, $jenis_kelamin_baru, $tempat_tgl_lahir_baru, 
                          $agama_baru, $nama_ibu_baru, $no_hp_ortu_baru, $email_ortu_baru, $alasan);
        
        if ($stmt->execute()) {
            $message = 'Pengajuan perubahan data berhasil dikirim. Mohon menunggu verifikasi dari admin.';
            $messageType = 'success';
        } else {
            $message = 'Terjadi kesalahan saat mengirim pengajuan. Silakan coba lagi.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ajukan Perubahan Data - SIM-KIP</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="sidebar" id="mahasiswaSidebar">
        <h3>SIM - KIP</h3>
        <a href="dashboard-mahasiswa.php">Dashboard Saya</a>
        <a href="ajukan-perubahan-data.php" class="active">Ajukan Perubahan Data</a>
        <a href="profile-settings-mahasiswa.php">Pengaturan Profil</a>
        <a href="logout.php" id="mahasiswaLogout">Logout</a>
    </div>

    <main class="main-content">
        <div class="header-main">
            <div class="header-left">
                <span class="user-role-label">Mahasiswa</span>
            </div>
            <div class="header-right">
                <div class="header-icons">
                    <i class="fas fa-search"></i>
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-profile-toggle" id="userProfileToggle">
                    <img src="https://via.placeholder.com/35" alt="User Avatar" />
                    <span id="loggedInUserName"><?php echo htmlspecialchars($mahasiswa['nama_lengkap'] ?? 'Mahasiswa'); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="dropdown-menu" id="userProfileDropdown">
                    <a href="profile-settings-mahasiswa.php" id="profileSettingsLinkDropdown">Pengaturan Profil</a>
                    <a href="help.php">Bantuan</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>

        <div class="page-header">
            <h2>Ajukan Perubahan Data</h2>
            <p>Formulir ini digunakan untuk mengajukan perubahan data pribadi Anda kepada administrator.</p>
            <?php if ($message): ?>
                <div class="message <?php echo $messageType === 'success' ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="section-container">
            <div class="profile-card">
                <h3>Formulir Perubahan Data</h3>
                <form id="requestDataChangeForm" class="profile-form" method="POST" action="">
                    <div class="form-group-profile">
                        <label for="currentNIM">NIM (Tidak Dapat Diubah)</label>
                        <input type="text" id="currentNIM" value="<?php echo htmlspecialchars($mahasiswa['nim'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-group-profile">
                        <label for="currentNama">Nama Lengkap (Saat Ini)</label>
                        <input type="text" id="currentNama" value="<?php echo htmlspecialchars($mahasiswa['nama_lengkap'] ?? ''); ?>" readonly>
                    </div>

                    <h4>Data yang Diajukan untuk Perubahan</h4>
                    <div class="form-group-profile">
                        <label for="requestNama">Nama Lengkap Baru</label>
                        <input type="text" id="requestNama" name="requestNama" placeholder="Masukkan nama lengkap baru (jika ada)">
                    </div>
                    <div class="form-group-profile">
                        <label for="requestJenisKelamin">Jenis Kelamin Baru</label>
                        <select id="requestJenisKelamin" name="requestJenisKelamin">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" <?php echo (isset($mahasiswa['jenis_kelamin']) && $mahasiswa['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo (isset($mahasiswa['jenis_kelamin']) && $mahasiswa['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group-profile">
                        <label for="requestTempatTglLahir">Tempat, Tanggal Lahir Baru</label>
                        <input type="text" id="requestTempatTglLahir" name="requestTempatTglLahir" 
                               value="<?php echo htmlspecialchars($mahasiswa['tempat_tgl_lahir'] ?? ''); ?>" 
                               placeholder="Contoh: Jakarta, 01-01-2000">
                    </div>
                    <div class="form-group-profile">
                        <label for="requestAgama">Agama Baru</label>
                        <input type="text" id="requestAgama" name="requestAgama" 
                               value="<?php echo htmlspecialchars($mahasiswa['agama'] ?? ''); ?>" 
                               placeholder="Masukkan agama baru">
                    </div>
                    <div class="form-group-profile">
                        <label for="requestNamaIbu">Nama Ibu Kandung Baru</label>
                        <input type="text" id="requestNamaIbu" name="requestNamaIbu" 
                               value="<?php echo htmlspecialchars($mahasiswa['nama_ibu'] ?? ''); ?>" 
                               placeholder="Masukkan nama ibu kandung baru">
                    </div>
                    <div class="form-group-profile">
                        <label for="requestNoHpOrtu">No. HP Orang Tua Baru</label>
                        <input type="text" id="requestNoHpOrtu" name="requestNoHpOrtu" 
                               value="<?php echo htmlspecialchars($mahasiswa['no_hp_ortu'] ?? ''); ?>" 
                               placeholder="Masukkan nomor HP orang tua baru">
                    </div>
                    <div class="form-group-profile">
                        <label for="requestEmailOrtu">Email Orang Tua Baru</label>
                        <input type="email" id="requestEmailOrtu" name="requestEmailOrtu" 
                               value="<?php echo htmlspecialchars($mahasiswa['email_ortu'] ?? ''); ?>" 
                               placeholder="Masukkan email orang tua baru">
                    </div>

                    <div class="form-group-profile">
                        <label for="alasanPerubahan">Alasan Perubahan</label>
                        <textarea id="alasanPerubahan" name="alasanPerubahan" rows="4"
                            placeholder="Jelaskan mengapa Anda mengajukan perubahan data ini (misal: kesalahan data, perubahan status, dll.)"
                            required></textarea>
                    </div>

                    <button type="submit" class="button-primary">Ajukan Perubahan</button>
                    <div class="message" id="changeRequestMessage"></div>
                </form>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Validasi form sebelum submit
            const requestDataChangeForm = document.getElementById('requestDataChangeForm');
            const changeRequestMessage = document.getElementById('changeRequestMessage');

            requestDataChangeForm.addEventListener('submit', function (event) {
                // Reset pesan error
                changeRequestMessage.textContent = '';
                changeRequestMessage.style.color = '';

                // Validasi setidaknya satu kolom perubahan diisi
                const requestNama = document.getElementById('requestNama').value;
                const requestJenisKelamin = document.getElementById('requestJenisKelamin').value;
                const requestTempatTglLahir = document.getElementById('requestTempatTglLahir').value;
                const requestAgama = document.getElementById('requestAgama').value;
                const requestNamaIbu = document.getElementById('requestNamaIbu').value;
                const requestNoHpOrtu = document.getElementById('requestNoHpOrtu').value;
                const requestEmailOrtu = document.getElementById('requestEmailOrtu').value;
                const alasanPerubahan = document.getElementById('alasanPerubahan').value;

                if (!requestNama && !requestJenisKelamin && !requestTempatTglLahir && !requestAgama &&
                    !requestNamaIbu && !requestNoHpOrtu && !requestEmailOrtu) {
                    event.preventDefault();
                    changeRequestMessage.textContent = 'Mohon isi setidaknya satu kolom perubahan data.';
                    changeRequestMessage.style.color = 'red';
                    return false;
                }

                if (!alasanPerubahan.trim()) {
                    event.preventDefault();
                    changeRequestMessage.textContent = 'Alasan perubahan harus diisi.';
                    changeRequestMessage.style.color = 'red';
                    return false;
                }

                return true;
            });
        });
    </script>
</body>
</html>
