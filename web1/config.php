<?php
// Koneksi ke database
$host = "localhost";
$user = "root";      // default XAMPP
$password = "";      // default XAMPP (kosong)
$database = "database";  // ganti dengan nama database Anda

$conn = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>