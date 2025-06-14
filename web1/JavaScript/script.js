// --- Login Admin Form Logic ---
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        const loginMessage = document.getElementById('loginMessage');

        try {
            const response = await fetch('api/login-admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    username: username,
                    password: password
                }),
            });

            const result = await response.json();

            if (result.success) {
                loginMessage.textContent = 'Login Admin berhasil! Mengarahkan ke dashboard...';
                loginMessage.className = 'message success show';
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('loggedInUser', result.data.nama_lengkap);
                localStorage.setItem('userRole', 'admin');
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1500);
            } else {
                loginMessage.textContent = result.message || 'Username atau password salah!';
                loginMessage.className = 'message error show';
            }
        } catch (error) {
            console.error('Login error:', error);
            loginMessage.textContent = 'Terjadi kesalahan saat login. Silakan coba lagi.';
            loginMessage.className = 'message error show';
        }
    });
}

// --- Login Mahasiswa Form Logic ---
const loginMahasiswaForm = document.getElementById('loginMahasiswaForm');
if (loginMahasiswaForm) {
    loginMahasiswaForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const nim = document.getElementById('nim').value.trim();
        const password = document.getElementById('password').value.trim();
        const loginMahasiswaMessage = document.getElementById('loginMahasiswaMessage');

        try {
            const response = await fetch('api/login-mahasiswa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    nim: nim,
                    password: password
                }),
            });

            const result = await response.json();

            if (result.success) {
                loginMahasiswaMessage.textContent = 'Login Mahasiswa berhasil! Mengarahkan ke dashboard...';
                loginMahasiswaMessage.className = 'message success show';
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('loggedInUser', result.data.nama);
                localStorage.setItem('loggedInUserNim', result.data.nim);
                localStorage.setItem('userRole', 'mahasiswa');
                setTimeout(() => {
                    window.location.href = 'dashboard-mahasiswa.html';
                }, 1500);
            } else {
                loginMahasiswaMessage.textContent = result.message || 'NIM atau password salah!';
                loginMahasiswaMessage.className = 'message error show';
            }
        } catch (error) {
            console.error('Login error:', error);
            loginMahasiswaMessage.textContent = 'Terjadi kesalahan saat login. Silakan coba lagi.';
            loginMahasiswaMessage.className = 'message error show';
        }
    });
}

// Fungsi untuk mengambil data mahasiswa dari API
async function fetchMahasiswaData() {
    try {
        const response = await fetch('api/data-mahasiswa.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching mahasiswa data:', error);
        return [];
    }
}

// Fungsi untuk menampilkan data mahasiswa ke tabel
async function displayMahasiswaData() {
    if (!mahasiswaTableBody) return;

    try {
        const data = await fetchMahasiswaData();
        mahasiswaTableBody.innerHTML = '';
        
        data.records.forEach((data, index) => {
            const row = mahasiswaTableBody.insertRow();
            let statusClass = '';
            if (data.status_kip === 'Aktif') {
                statusClass = 'status-success';
            } else if (data.status_kip === 'Pending') {
                statusClass = 'status-pending';
            } else if (data.status_kip === 'Tidak Aktif') {
                statusClass = 'status-error';
            }

            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${data.nim}</td>
                <td>${data.nama}</td>
                <td>${data.jenis_kelamin}</td>
                <td>${data.tempat_lahir}, ${new Date(data.tanggal_lahir).toLocaleDateString()}</td>
                <td>${data.agama}</td>
                <td>${data.nama_ibu}</td>
                <td>${data.no_hp_ortu}</td>
                <td>${data.email_ortu || '-'}</td>
                <td><span class="${statusClass}">${data.status_kip || 'N/A'}</span></td>
                <td>${data.periode_aktif || 'Belum Ditetapkan'}</td>
                <td>
                    <button class="button-small edit-mahasiswa" data-nim="${data.nim}"><i class="fas fa-edit"></i> Edit</button>
                    <button class="button-small delete-mahasiswa" style="background-color: #dc3545;" data-nim="${data.nim}"><i class="fas fa-trash-alt"></i> Hapus</button>
                </td>
            `;
        });

        // Tambahkan event listener untuk tombol edit/hapus
        document.querySelectorAll('.edit-mahasiswa').forEach(button => {
            button.addEventListener('click', function() {
                const nimToEdit = this.dataset.nim;
                editMahasiswa(nimToEdit);
            });
        });

        document.querySelectorAll('.delete-mahasiswa').forEach(button => {
            button.addEventListener('click', function() {
                const nimToDelete = this.dataset.nim;
                deleteMahasiswa(nimToDelete);
            });
        });
    } catch (error) {
        console.error('Error displaying mahasiswa data:', error);
    }
}

// Fungsi untuk mengedit mahasiswa
async function editMahasiswa(nim) {
    try {
        const response = await fetch(`api/data-mahasiswa.php?nim=${nim}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const mahasiswa = await response.json();
        
        // Isi form dengan data mahasiswa
        document.getElementById('nim').value = mahasiswa.nim;
        document.getElementById('nama').value = mahasiswa.nama;
        document.getElementById('jenis_kelamin').value = mahasiswa.jenis_kelamin;
        document.getElementById('tempat_lahir').value = mahasiswa.tempat_lahir;
        document.getElementById('tanggal_lahir').value = mahasiswa.tanggal_lahir.split(' ')[0];
        document.getElementById('agama').value = mahasiswa.agama;
        document.getElementById('nama_ibu').value = mahasiswa.nama_ibu;
        document.getElementById('no_hp_ortu').value = mahasiswa.no_hp_ortu;
        document.getElementById('email_ortu').value = mahasiswa.email_ortu;
        document.getElementById('statusKipInput').value = mahasiswa.status_kip;
        document.getElementById('periodeAktifInput').value = mahasiswa.periode_aktif;
        
        // Scroll ke form
        document.getElementById('addMahasiswaForm').scrollIntoView({ behavior: 'smooth' });
    } catch (error) {
        console.error('Error editing mahasiswa:', error);
        alert('Gagal memuat data mahasiswa untuk diedit');
    }
}

// Fungsi untuk menghapus mahasiswa
async function deleteMahasiswa(nim) {
    if (confirm('Anda yakin ingin menghapus data mahasiswa ini?')) {
        try {
            const response = await fetch('api/data-mahasiswa.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ nim: nim }),
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const result = await response.json();
            if (result.message === 'Mahasiswa berhasil dihapus.') {
                displayMahasiswaData();
                addMahasiswaMessage.textContent = `Data mahasiswa dengan NIM ${nim} berhasil dihapus.`;
                addMahasiswaMessage.className = 'message success show';
                setTimeout(() => addMahasiswaMessage.classList.remove('show'), 3000);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error deleting mahasiswa:', error);
            addMahasiswaMessage.textContent = `Gagal menghapus data mahasiswa: ${error.message}`;
            addMahasiswaMessage.className = 'message error show';
            setTimeout(() => addMahasiswaMessage.classList.remove('show'), 3000);
        }
    }
}

// Modifikasi form submit untuk menggunakan API
if (addMahasiswaForm && userRole === 'admin') {
    addMahasiswaForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const nim = document.getElementById('nim').value.trim();
        const nama = document.getElementById('nama').value.trim();
        const jenis_kelamin = document.getElementById('jenis_kelamin').value;
        const tempat_lahir = document.getElementById('tempat_lahir').value.trim();
        const tanggal_lahir = document.getElementById('tanggal_lahir').value.trim();
        const agama = document.getElementById('agama').value.trim();
        const nama_ibu = document.getElementById('nama_ibu').value.trim();
        const no_hp_ortu = document.getElementById('no_hp_ortu').value.trim();
        const email_ortu = document.getElementById('email_ortu').value.trim();
        const statusKipInput = document.getElementById('statusKipInput');
        const periodeAktifInput = document.getElementById('periodeAktifInput');
        const password = 'default_password'; // Password default

        if (!nim || !nama || !jenis_kelamin || !tempat_lahir || !tanggal_lahir || 
            !agama || !nama_ibu || !no_hp_ortu) {
            addMahasiswaMessage.textContent = 'Semua field wajib diisi (kecuali Email Orang Tua)!';
            addMahasiswaMessage.className = 'message error show';
            setTimeout(() => addMahasiswaMessage.classList.remove('show'), 3000);
            return;
        }

        try {
            // Cek apakah NIM sudah ada
            const checkResponse = await fetch(`api/data-mahasiswa.php?nim=${nim}`);
            if (checkResponse.ok) {
                const existingData = await checkResponse.json();
                if (existingData.nim) {
                    addMahasiswaMessage.textContent = 'NIM sudah terdaftar. Gunakan NIM lain.';
                    addMahasiswaMessage.className = 'message error show';
                    setTimeout(() => addMahasiswaMessage.classList.remove('show'), 3000);
                    return;
                }
            }

            // Kirim data baru
            const response = await fetch('api/data-mahasiswa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    nim: nim,
                    nama: nama,
                    password: password,
                    jenis_kelamin: jenis_kelamin,
                    tempat_lahir: tempat_lahir,
                    tanggal_lahir: tanggal_lahir,
                    agama: agama,
                    nama_ibu: nama_ibu,
                    no_hp_ortu: no_hp_ortu,
                    email_ortu: email_ortu,
                    status_kip: statusKipInput ? statusKipInput.value : 'Pending',
                    periode_aktif: periodeAktifInput ? periodeAktifInput.value : null
                }),
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();
            if (result.message === 'Mahasiswa berhasil ditambahkan.') {
                addMahasiswaMessage.textContent = 'Penambahan data sukses!';
                addMahasiswaMessage.className = 'message success show';
                addMahasiswaForm.reset();
                displayMahasiswaData(); // Perbarui tabel

                setTimeout(() => {
                    addMahasiswaMessage.classList.remove('show');
                }, 3000);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error adding mahasiswa:', error);
            addMahasiswaMessage.textContent = `Gagal menambahkan mahasiswa: ${error.message}`;
            addMahasiswaMessage.className = 'message error show';
            setTimeout(() => addMahasiswaMessage.classList.remove('show'), 3000);
        }
    });
}

// Fungsi validasi password yang lebih kuat
function validatePassword(password) {
    // Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus
    const strongPassword = new RegExp(
        '^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})'
    );
    return strongPassword.test(password);
}

// Fungsi untuk mengecek kekuatan password
function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength += 1;
    if (password.match(/[a-z]/)) strength += 1;
    if (password.match(/[A-Z]/)) strength += 1;
    if (password.match(/[0-9]/)) strength += 1;
    if (password.match(/[!@#$%^&*]/)) strength += 1;
    return strength;
}

// Fungsi untuk menampilkan indikator kekuatan password
function showPasswordStrength(password, messageElement) {
    const strength = checkPasswordStrength(password);
    let message = '';
    let className = '';

    switch (strength) {
        case 0:
        case 1:
            message = 'Sangat Lemah';
            className = 'very-weak';
            break;
        case 2:
            message = 'Lemah';
            className = 'weak';
            break;
        case 3:
            message = 'Sedang';
            className = 'medium';
            break;
        case 4:
            message = 'Kuat';
            className = 'strong';
            break;
        case 5:
            message = 'Sangat Kuat';
            className = 'very-strong';
            break;
    }

    if (messageElement) {
        messageElement.textContent = `Kekuatan Password: ${message}`;
        messageElement.className = `message ${className} show`;
    }
}

// Event listener untuk form login
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('#loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const password = document.querySelector('#password').value;
            const nim = document.querySelector('#nim').value;
            
            // Validasi NIM
            if (!/^\d{8,}$/.test(nim)) {
                e.preventDefault();
                alert('NIM harus berupa angka minimal 8 digit!');
                return;
            }

            // Hindari pengiriman password yang tidak aman
            if (!validatePassword(password)) {
                e.preventDefault();
                alert('Password tidak memenuhi standar keamanan!');
                return;
            }
        });
    }
});