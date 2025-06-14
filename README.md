# SIM-KIP: Sistem Informasi Manajemen Kartu Indonesia Pintar
  Selamat datang di SIM-KIP, platform digital yang aman untuk manajemen data mahasiswa penerima program Kartu Indonesia Pintar (KIP). Aplikasi ini menyediakan antarmuka yang modern dan fungsional untuk Admin dan Mahasiswa, memastikan data terkelola dengan baik dan terlindungi.

  Proyek ini dikembangkan sebagai bagian dari pemenuhan tugas untuk mata kuliah "Sistem Keamanan". Tujuan utama dari proyek ini adalah untuk menerapkan konsep keamanan fundamental dan modern—mulai dari hashing password yang aman hingga enkripsi data—dalam sebuah aplikasi web yang fungsional dan relevan.

# Mata Kuliah: Sistem Keamanan
## Dosen Pengampu: Niken Nerfita Sari, S.T, M.Cs. dan Feri Irawan, S.Kom., M.Cs.

## Fitur Utama
Aplikasi ini memiliki fitur yang berbeda tergantung pada hak akses pengguna:

  ### Untuk Admin
- Login Aman: Halaman login khusus yang dilindungi mekanisme hashing modern.
- Dashboard: Tampilan ringkasan data penting secara real-time.
- Manajemen Data Mahasiswa: Kemampuan untuk menambah, melihat, mengubah, dan menghapus data mahasiswa (CRUD).
- Rekapitulasi Keuangan: Melihat rekapitulasi pembayaran dan laporan status KIP.
- Pengaturan Profil: Mengubah informasi dan kredensial akun admin.

  ### Untuk Mahasiswa
- Login Mahasiswa: Halaman login aman menggunakan NIM dan password.
- Dashboard Pribadi: Menampilkan data diri lengkap yang terenkripsi dan status KIP (Aktif, Pending, Tidak Aktif).
- Pengajuan Perubahan Data: Mengirimkan formulir pengajuan perubahan data yang akan ditinjau oleh admin.
- Pengaturan Profil: Mahasiswa dapat mengubah beberapa data pribadi dan mengganti password mereka dengan aman.

## Keamanan Sistem
Keamanan adalah prioritas utama dalam SIM-KIP. Kami mengimplementasikan beberapa lapisan perlindungan menggunakan algoritma standar industri untuk melindungi semua data pengguna.

- Hashing Password dengan Scrypt
  Setiap password pengguna tidak pernah disimpan dalam bentuk teks biasa. Kami menggunakan Scrypt, sebuah fungsi derivasi kunci (KDF) yang dirancang untuk menjadi intensif secara memori dan komputasi. Hal ini memberikan perlindungan yang sangat kuat terhadap serangan brute-force dan serangan menggunakan perangkat keras khusus (ASIC/FPGA).

- Enkripsi Data dengan AES-256-GCM
  Semua data sensitif mahasiswa—seperti nama ibu, no. HP orang tua, dan detail pribadi lainnya—dienkripsi di tingkat database menggunakan AES-256-GCM. Algoritma ini adalah standar emas dalam enkripsi simetris yang tidak hanya menjamin kerahasiaan data, tetapi juga integritas dan autentikasinya (melalui GCM). Ini berarti data tidak dapat dibaca atau dimodifikasi oleh pihak yang tidak berwenang tanpa terdeteksi.

- Derivasi Kunci Menggunakan HKDF
  Untuk menghasilkan kunci enkripsi yang kuat bagi setiap pengguna, kami memanfaatkan HKDF (HMAC-based Key Derivation Function). HKDF mengambil password pengguna (setelah di-hash) dan menghasilkan kunci kriptografis yang aman dan unik, yang kemudian digunakan untuk proses enkripsi dan dekripsi data dengan AES.
