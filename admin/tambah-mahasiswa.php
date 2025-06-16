<?php
require_once '../proses/config.php';
require_once '../proses/encrypt_util.php';

$conn = getDbConnection();

$nim = $_POST['nim'];
$nama = $_POST['nama'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$tempat_lahir = $_POST['tempat_lahir'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$agama = $_POST['agama'];
$nama_ibu_plain = $_POST['nama_ibu'];
$no_rekening_plain = $_POST['no_rekening'];
$no_hp_ortu = $_POST['no_hp_ortu'];
$email_ortu = $_POST['email_ortu'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$nama_ibu = encryptData($nama_ibu_plain);
$no_rekening = encryptData($no_rekening_plain);

$stmt = $conn->prepare("INSERT INTO mahasiswa 
    (nim, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, nama_ibu, no_rekening, no_hp_ortu, email_ortu, password) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssss", $nim, $nama, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $agama, $nama_ibu, $no_rekening, $no_hp_ortu, $email_ortu, $password);

if ($stmt->execute()) {
    header("Location: data-mahasiswa.php?success=1");
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>
