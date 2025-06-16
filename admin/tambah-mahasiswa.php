<?php
require_once '../proses/config.php';

$conn = getDbConnection();

$nim = $_POST['nim'];
$nama = $_POST['nama'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$tempat_lahir = $_POST['tempat_lahir'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$agama = $_POST['agama'];
$nama_ibu = $_POST['nama_ibu'];
$no_rekening = $_POST['no_rekening'];
$no_hp_ortu = $_POST['no_hp_ortu'];
$email_ortu = $_POST['email_ortu'];
$password = $_POST['password'];

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
