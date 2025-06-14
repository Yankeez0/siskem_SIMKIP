document.addEventListener('DOMContentLoaded', function () {
    // --- Helper function to redirect based on role ---
    // Fungsi ini harus berada di scope global atau awal agar dapat diakses oleh semua bagian kode
    function checkUserRole(allowedRoles) {
        const isLoggedIn = localStorage.getItem('isLoggedIn');
        const userRole = localStorage.getItem('userRole');
        const currentPath = window.location.pathname.split('/').pop();

        // Izinkan akses ke halaman login tanpa perlu check role
        if (currentPath === 'login.html' || currentPath === 'login-mahasiswa.html') {
            return true;
        }

        if (!isLoggedIn || !allowedRoles.includes(userRole)) {
            // console.log('Akses ditolak! Anda tidak memiliki izin untuk melihat halaman ini.');
            window.location.href = 'login.html'; // Redirect ke halaman login utama
            return false;
        }
        return true;
    }

    // --- Contoh data mahasiswa (simulasi) ---
    // Ini harus berada di awal scope DOMContentLoaded agar bisa diakses oleh semua fungsi
    let mahasiswaData = [
        {
            nim: '12345678',
            password: 'budi001',

        },
        {
            nim: '2021005',
            password: 'passmahasiswa2',

        },
        {
            nim: '2023010',
            password: 'passmahasiswa3',

        }
    ];

    const currentPath = window.location.pathname.split('/').pop();
    const userRole = localStorage.getItem('userRole');


    // --- Role-based Access Control for Page Load ---
    // Jalankan checkUserRole di awal untuk setiap halaman yang membutuhkan otorisasi
    if (currentPath === 'index.html' || currentPath === 'data-mahasiswa.html' || currentPath === 'rekapitulasi.html' || currentPath === 'keuangan.html') {
        if (!checkUserRole(['admin'])) {
            return; // Hentikan eksekusi script jika tidak diizinkan
        }
    } else if (currentPath === 'dashboard-mahasiswa.html' || currentPath === 'profile-settings-mahasiswa.html' || currentPath === 'pembayaran.html') {
        if (!checkUserRole(['mahasiswa'])) {
            return; // Hentikan eksekusi script jika tidak diizinkan
        }
    }
    // Halaman lain seperti help.html bisa diakses semua (atau tambahkan role di sini jika diperlukan)


    // --- Header Profile Dropdown Logic ---
    const userProfileToggle = document.getElementById('userProfileToggle');
    const profileDropdown = document.getElementById('profileDropdown');

    if (userProfileToggle && profileDropdown) {
        function toggleDropdown() {
            profileDropdown.classList.toggle('show');
            userProfileToggle.classList.toggle('active');
        }

        userProfileToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            toggleDropdown();
        });

        document.addEventListener('click', function (event) {
            if (!profileDropdown.contains(event.target) && !userProfileToggle.contains(event.target)) {
                if (profileDropdown.classList.contains('show')) {
                    toggleDropdown();
                }
            }
        });
    }

    // --- Sidebar Active Link Logic ---
    const sidebarLinks = document.querySelectorAll('.sidebar a');
    sidebarLinks.forEach(link => {
        const linkPath = link.getAttribute('href').split('/').pop();

        if (currentPath === linkPath) {
            const currentlyActive = document.querySelector('.sidebar a.active');
            if (currentlyActive) {
                currentlyActive.classList.remove('active');
            }
            link.classList.add('active');
        }
    });

    // --- Handle all Logout links ---
    const logoutLinks = document.querySelectorAll('[id^="mahasiswaLogout"], [id^="adminLogout"], #profileDropdown a[href="login.html"], #profileDropdownLogout');
    logoutLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            localStorage.removeItem('isLoggedIn');
            localStorage.removeItem('loggedInUser');
            localStorage.removeItem('loggedInUserNim'); // Hapus NIM mahasiswa
            localStorage.removeItem('userRole');
            window.location.href = 'login.html'; // Arahkan ke halaman login utama
        });
    });


    // --- Login Admin Form Logic (only runs on login.html) ---
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const loginMessage = document.getElementById('loginMessage');

        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const username = usernameInput.value.trim();
            const password = passwordInput.value.trim();

            const validAdminUsername = 'alip';
            const validAdminPassword = 'melpi';

            if (username === validAdminUsername && password === validAdminPassword) {
                loginMessage.textContent = 'Login Admin berhasil! Mengarahkan ke dashboard...';
                loginMessage.className = 'message success show';
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('loggedInUser', username);
                localStorage.setItem('userRole', 'admin');
                setTimeout(() => {
                    window.location.href = 'index.html'; // Dashboard Admin
                }, 1500);
            } else {
                loginMessage.textContent = 'Username atau password salah!';
                loginMessage.className = 'message error show';
            }
        });
    }

    // --- Profile Settings Page Logic (for Admin, only runs on profile-settings.html) ---
    const profileInfoFormAdmin = document.getElementById('profileInfoForm');
    if (profileInfoFormAdmin && userRole === 'admin') {
        const fullNameInput = document.getElementById('fullName');
        const usernameInputProfile = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const roleInput = document.getElementById('role');

        const editProfileButton = document.getElementById('editProfileButton');
        const editActions = profileInfoFormAdmin.querySelector('.edit-actions');
        const cancelEditButton = document.getElementById('cancelEditButton');
        const profileMessage = document.getElementById('profileMessage');

        const avatarUpload = document.getElementById('avatarUpload');
        const profileAvatarPreview = document.getElementById('profileAvatarPreview');

        let originalProfileData = {
            fullName: "Admin SIM-KIP",
            username: "alip",
            email: "admin@simkip.com",
            role: "Admin"
        };

        if (fullNameInput) fullNameInput.value = originalProfileData.fullName;
        if (usernameInputProfile) usernameInputProfile.value = originalProfileData.username;
        if (emailInput) emailInput.value = originalProfileData.email;
        if (roleInput) roleInput.value = originalProfileData.role;


        function toggleProfileEdit(enable) {
            if (fullNameInput) fullNameInput.readOnly = !enable;
            if (emailInput) emailInput.readOnly = !enable;
            if (editProfileButton) editProfileButton.style.display = enable ? 'none' : 'block';
            if (editActions) editActions.style.display = enable ? 'flex' : 'none';
            [fullNameInput, emailInput].forEach(input => {
                if (input) {
                    if (enable) {
                        input.classList.remove('non-editable');
                    } else {
                        input.classList.add('non-editable');
                    }
                }
            });
        }

        toggleProfileEdit(false);

        if (editProfileButton) {
            editProfileButton.addEventListener('click', function () {
                toggleProfileEdit(true);
                if (profileMessage) {
                    profileMessage.textContent = '';
                    profileMessage.classList.remove('success', 'error', 'show');
                }
            });
        }

        if (cancelEditButton) {
            cancelEditButton.addEventListener('click', function () {
                if (fullNameInput) fullNameInput.value = originalProfileData.fullName;
                if (usernameInputProfile) usernameInputProfile.value = originalProfileData.username;
                if (emailInput) emailInput.value = originalProfileData.email;
                if (roleInput) roleInput.value = originalProfileData.role;
                if (profileAvatarPreview) profileAvatarPreview.src = "https://via.placeholder.com/100";
                toggleProfileEdit(false);
                if (profileMessage) {
                    profileMessage.textContent = '';
                    profileMessage.classList.remove('success', 'error', 'show');
                }
            });
        }

        profileInfoFormAdmin.addEventListener('submit', function (event) {
            event.preventDefault();
            const updatedData = {
                fullName: fullNameInput ? fullNameInput.value.trim() : '',
                email: emailInput ? emailInput.value.trim() : ''
            };

            console.log("Saving admin profile data:", updatedData);
            if (updatedData.email && (!updatedData.email.includes('@') || !updatedData.email.includes('.'))) {
                if (profileMessage) {
                    profileMessage.textContent = 'Format email tidak valid!';
                    profileMessage.className = 'message error show';
                }
                return;
            }

            setTimeout(() => {
                if (profileMessage) {
                    profileMessage.textContent = 'Profil berhasil diperbarui!';
                    profileMessage.className = 'message success show';
                }
                originalProfileData.fullName = updatedData.fullName;
                originalProfileData.email = updatedData.email;
                toggleProfileEdit(false);
                setTimeout(() => {
                    if (profileMessage) profileMessage.classList.remove('show');
                }, 3000);
            }, 1000);
        });

        if (avatarUpload) {
            avatarUpload.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        if (profileAvatarPreview) profileAvatarPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }


    // --- Change Password Form Logic (for Admin, only runs on profile-settings.html) ---
    const changePasswordFormAdmin = document.getElementById('changePasswordForm');
    if (changePasswordFormAdmin && userRole === 'admin') {
        const currentPasswordInput = document.getElementById('currentPassword');
        const newPasswordInput = document.getElementById('newPassword');
        const confirmNewPasswordInput = document.getElementById('confirmNewPassword');
        const passwordMessage = document.getElementById('passwordMessage');

        changePasswordFormAdmin.addEventListener('submit', function (event) {
            event.preventDefault();

            const currentPassword = currentPasswordInput.value.trim();
            const newPassword = newPasswordInput.value.trim();
            const confirmNewPassword = confirmNewPasswordInput.value.trim();

            const validCurrentPassword = 'melpi'; // Password admin saat ini

            if (currentPassword !== validCurrentPassword) {
                passwordMessage.textContent = 'Password saat ini salah!';
                passwordMessage.className = 'message error show';
                return;
            }

            if (newPassword.length < 6) {
                passwordMessage.textContent = 'Password baru minimal 6 karakter!';
                passwordMessage.className = 'message error show';
                return;
            }

            if (newPassword !== confirmNewPassword) {
                passwordMessage.textContent = 'Konfirmasi password tidak cocok!';
                passwordMessage.className = 'message error show';
                return;
            }

            if (newPassword === currentPassword) {
                passwordMessage.textContent = 'Password baru tidak boleh sama dengan password lama!';
                passwordMessage.className = 'message error show';
                return;
            }

            console.log("Changing admin password...");
            setTimeout(() => {
                passwordMessage.textContent = 'Password berhasil diubah!';
                passwordMessage.className = 'message success show';
                currentPasswordInput.value = '';
                newPasswordInput.value = '';
                confirmNewPasswordInput.value = '';
                setTimeout(() => {
                    passwordMessage.classList.remove('show');
                }, 3000);
            }, 1500);
        });
    }

    // --- Help Page Accordion Logic (Existing Code) ---
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    if (accordionHeaders.length > 0) {
        accordionHeaders.forEach(header => {
            header.addEventListener('click', function () {
                const accordionItem = this.parentElement;
                const accordionContent = this.nextElementSibling;

                document.querySelectorAll('.accordion-item.open').forEach(item => {
                    if (item !== accordionItem) {
                        item.classList.remove('open');
                        item.querySelector('.accordion-header').classList.remove('active');
                        item.querySelector('.accordion-content').style.maxHeight = 0;
                        item.querySelector('.accordion-content').style.paddingTop = '0';
                        item.querySelector('.accordion-content').style.paddingBottom = '0';
                    }
                });

                accordionItem.classList.toggle('open');
                this.classList.toggle('active');

                if (accordionItem.classList.contains('open')) {
                    accordionContent.style.maxHeight = accordionContent.scrollHeight + 'px';
                    accordionContent.style.paddingTop = '15px';
                    accordionContent.style.paddingBottom = '15px';
                } else {
                    accordionContent.style.maxHeight = 0;
                    accordionContent.style.paddingTop = '0';
                    accordionContent.style.paddingBottom = '0';
                }
            });
        });
    }

    // --- Data Mahasiswa Page Logic (only runs on data-mahasiswa.html for Admin) ---
    const addMahasiswaForm = document.getElementById('addMahasiswaForm');
    const mahasiswaTableBody = document.getElementById('mahasiswaTableBody');
    const addMahasiswaMessage = document.getElementById('addMahasiswaMessage');

    // Fungsi untuk menampilkan data mahasiswa ke tabel
    function displayMahasiswaData() {
        if (!mahasiswaTableBody) return; // Pastikan elemen tabel ada di halaman ini

        mahasiswaTableBody.innerHTML = '';
        mahasiswaData.forEach((data, index) => {
            const row = mahasiswaTableBody.insertRow();
            let statusClass = '';
            if (data.statusKip === 'Aktif') {
                statusClass = 'status-success';
            } else if (data.statusKip === 'Pending') {
                statusClass = 'status-pending';
            } else if (data.statusKip === 'Tidak Aktif') {
                statusClass = 'status-error';
            }

            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${data.nim}</td>
                <td>${data.nama}</td>
                <td>${data.jenis_kelamin}</td>
                <td>${data.tempat_tgl_lahir}</td>
                <td>${data.agama}</td>
                <td>${data.nama_ibu}</td>
                <td>${data.no_hp_ortu}</td>
                <td>${data.email_ortu || '-'}</td>
                <td><span class="${statusClass}">${data.statusKip || 'N/A'}</span></td>
                <td>${data.periodeAktif || 'Belum Ditetapkan'}</td>
                <td>
                    <button class="button-small edit-mahasiswa" data-nim="${data.nim}"><i class="fas fa-edit"></i> Edit</button>
                    <button class="button-small delete-mahasiswa" style="background-color: #dc3545;" data-nim="${data.nim}"><i class="fas fa-trash-alt"></i> Hapus</button>
                </td>
            `;
        });

        // Event listener untuk tombol edit/hapus
        document.querySelectorAll('.edit-mahasiswa').forEach(button => {
            button.addEventListener('click', function () {
                const nimToEdit = this.dataset.nim;
                alert('Fungsi Edit untuk NIM: ' + nimToEdit + ' akan segera diimplementasikan. Anda bisa mengambil data mahasiswa dengan NIM ini dan mengisi ulang form untuk diedit.');
                const mahasiswaToEdit = mahasiswaData.find(mhs => mhs.nim === nimToEdit);
                if (mahasiswaToEdit) {
                    document.getElementById('nim').value = mahasiswaToEdit.nim;
                    document.getElementById('nama').value = mahasiswaToEdit.nama;
                    document.getElementById('jenis_kelamin').value = mahasiswaToEdit.jenis_kelamin;
                    document.getElementById('tempat_tgl_lahir').value = mahasiswaToEdit.tempat_tgl_lahir;
                    document.getElementById('agama').value = mahasiswaToEdit.agama;
                    document.getElementById('nama_ibu').value = mahasiswaToEdit.nama_ibu;
                    document.getElementById('no_hp_ortu').value = mahasiswaToEdit.no_hp_ortu;
                    document.getElementById('email_ortu').value = mahasiswaToEdit.email_ortu;
                    // Anda mungkin perlu menambahkan input untuk statusKip dan periodeAktif di form admin
                    if (document.getElementById('statusKipInput')) {
                        document.getElementById('statusKipInput').value = mahasiswaToEdit.statusKip;
                    }
                    if (document.getElementById('periodeAktifInput')) {
                        document.getElementById('periodeAktifInput').value = mahasiswaToEdit.periodeAktif;
                    }
                }
            });
        });

        document.querySelectorAll('.delete-mahasiswa').forEach(button => {
            button.addEventListener('click', function () {
                if (confirm('Anda yakin ingin menghapus data mahasiswa ini?')) {
                    const nimToDelete = this.dataset.nim;
                    mahasiswaData = mahasiswaData.filter(mhs => mhs.nim !== nimToDelete);
                    displayMahasiswaData();
                    addMahasiswaMessage.textContent = `Data mahasiswa dengan NIM ${nimToDelete} berhasil dihapus.`;
                    addMahasiswaMessage.className = 'message success show';
                    setTimeout(() => addMahasiswaMessage.classList.remove('show'), 3000);
                }
            });
        });
    }

    // Panggil fungsi ini saat halaman dimuat untuk pertama kali jika di halaman data-mahasiswa.html
    if (mahasiswaTableBody && userRole === 'admin') {
        displayMahasiswaData();
    }

    if (addMahasiswaForm && userRole === 'admin') {
        addMahasiswaForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const nim = document.getElementById('nim').value.trim();
            const nama = document.getElementById('nama').value.trim();
            const jenis_kelamin = document.getElementById('jenis_kelamin').value;
            const tempat_tgl_lahir = document.getElementById('tempat_tgl_lahir').value.trim();
            const agama = document.getElementById('agama').value.trim();
            const nama_ibu = document.getElementById('nama_ibu').value.trim();
            const no_hp_ortu = document.getElementById('no_hp_ortu').value.trim();
            const email_ortu = document.getElementById('email_ortu').value.trim();
            const statusKipInput = document.getElementById('statusKipInput'); // Anda perlu menambahkan ini di form admin
            const periodeAktifInput = document.getElementById('periodeAktifInput'); // Anda perlu menambahkan ini di form admin

            if (!nim || !nama || !jenis_kelamin || !tempat_tgl_lahir || !agama || !nama_ibu || !no_hp_ortu) {
                addMahasiswaMessage.textContent = 'Semua field wajib diisi (kecuali Email Orang Tua)!';
                addMahasiswaMessage.className = 'message error show';
                setTimeout(() => addMahasiswaMessage.classList.remove('show'), 3000);
                return;
            }

            if (mahasiswaData.some(mhs => mhs.nim === nim)) {
                addMahasiswaMessage.textContent = 'NIM sudah terdaftar. Gunakan NIM lain.';
                addMahasiswaMessage.className = 'message error show';
                setTimeout(() => addMahasiswaMessage.classList.remove('show'), 3000);
                return;
            }

            const newMahasiswa = {
                nim: nim,
                password: 'default_password', // Anda perlu cara untuk mengatur password saat menambahkan mahasiswa
                nama: nama,
                jenis_kelamin: jenis_kelamin,
                tempat_tgl_lahir: tempat_tgl_lahir,
                agama: agama,
                nama_ibu: nama_ibu,
                no_hp_ortu: no_hp_ortu,
                email_ortu: email_ortu,
                statusKip: statusKipInput ? statusKipInput.value : 'Pending', // Default
                periodeAktif: periodeAktifInput ? periodeAktifInput.value : 'Belum Ditetapkan', // Default
                pembayaran: [], // Inisialisasi kosong
                rekening: {}    // Inisialisasi kosong
            };

            console.log("Mengirim data mahasiswa baru ke database (simulasi):", newMahasiswa);

            setTimeout(() => {
                addMahasiswaMessage.textContent = 'Penambahan data sukses!';
                addMahasiswaMessage.className = 'message success show';
                addMahasiswaForm.reset();

                mahasiswaData.push(newMahasiswa);
                displayMahasiswaData(); // Perbarui tabel

                setTimeout(() => {
                    addMahasiswaMessage.classList.remove('show');
                }, 3000);
            }, 1000);
        });
    }

    // --- Login Mahasiswa Form Logic (only runs on login-mahasiswa.html) ---
    const loginMahasiswaForm = document.getElementById('loginMahasiswaForm');
    if (loginMahasiswaForm) {
        const nimInput = document.getElementById('nim');
        const passwordInputMahasiswa = document.getElementById('password');
        const loginMahasiswaMessage = document.getElementById('loginMahasiswaMessage');

        loginMahasiswaForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const nim = nimInput.value.trim();
            const password = passwordInputMahasiswa.value.trim();

            const foundMahasiswa = mahasiswaData.find(mhs => mhs.nim === nim && mhs.password === password);

            if (foundMahasiswa) {
                loginMahasiswaMessage.textContent = 'Login Mahasiswa berhasil! Mengarahkan ke dashboard...';
                loginMahasiswaMessage.className = 'message success show';
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('loggedInUser', foundMahasiswa.nama);
                localStorage.setItem('loggedInUserNim', foundMahasiswa.nim); // Simpan NIM mahasiswa
                localStorage.setItem('userRole', 'mahasiswa');
                setTimeout(() => {
                    window.location.href = 'dashboard-mahasiswa.html'; // Redirect ke dashboard mahasiswa
                }, 1500);
            } else {
                loginMahasiswaMessage.textContent = 'NIM atau password salah!';
                loginMahasiswaMessage.className = 'message error show';
            }
        });
    }


    // --- Dashboard Mahasiswa Page Logic (only runs on dashboard-mahasiswa.html) ---
    if (currentPath === 'dashboard-mahasiswa.html' && userRole === 'mahasiswa') {
        const loggedInMahasiswaNim = localStorage.getItem('loggedInUserNim');
        const loggedInMahasiswaName = localStorage.getItem('loggedInUser');
        const displayMahasiswaNameElem = document.getElementById('loggedInMahasiswaName'); // Untuk header

        if (displayMahasiswaNameElem) {
            displayMahasiswaNameElem.textContent = loggedInMahasiswaName || 'Mahasiswa';
        }

        const currentMahasiswa = mahasiswaData.find(mhs => mhs.nim === loggedInMahasiswaNim);

        if (currentMahasiswa) {
            document.getElementById('displayNIM').textContent = currentMahasiswa.nim;
            document.getElementById('displayNama').textContent = currentMahasiswa.nama;
            document.getElementById('displayJenisKelamin').textContent = currentMahasiswa.jenis_kelamin;
            document.getElementById('displayTempatTglLahir').textContent = currentMahasiswa.tempat_tgl_lahir;
            document.getElementById('displayAgama').textContent = currentMahasiswa.agama;
            document.getElementById('displayNamaIbu').textContent = currentMahasiswa.nama_ibu;
            document.getElementById('displayNoHpOrtu').textContent = currentMahasiswa.no_hp_ortu;
            document.getElementById('displayEmailOrtu').textContent = currentMahasiswa.email_ortu || '-';

            const displayStatusKIP = document.getElementById('displayStatusKIP');
            if (displayStatusKIP) {
                displayStatusKIP.textContent = currentMahasiswa.statusKip;
                // Hapus kelas status sebelumnya jika ada
                displayStatusKIP.classList.remove('Aktif', 'Pending', 'Tidak-Aktif');
                displayStatusKIP.classList.add(currentMahasiswa.statusKip.toLowerCase().replace(' ', '-'));
            }

            document.getElementById('displayPeriodeAktif').textContent = currentMahasiswa.periodeAktif || 'Belum Ditetapkan';

            const statusKIPNote = document.getElementById('statusKIPNote');
            if (statusKIPNote) {
                if (currentMahasiswa.statusKip === 'Aktif') {
                    statusKIPNote.textContent = 'Selamat! Status KIP Anda aktif untuk periode yang disebutkan. Pastikan Anda memenuhi semua persyaratan yang berlaku.';
                } else if (currentMahasiswa.statusKip === 'Pending') {
                    statusKIPNote.textContent = 'Status KIP Anda masih dalam proses peninjauan. Mohon bersabar atau hubungi bagian administrasi KIP untuk informasi lebih lanjut.';
                } else if (currentMahasiswa.statusKip === 'Tidak Aktif') {
                    statusKIPNote.textContent = 'Mohon maaf, status KIP Anda saat ini tidak aktif. Silakan hubungi bagian administrasi KIP untuk mengetahui alasan dan prosedur pengaktifan kembali.';
                } else {
                    statusKIPNote.textContent = 'Informasi status KIP tidak tersedia. Harap hubungi administrasi.';
                }
            }
        } else {
            alert('Data mahasiswa tidak ditemukan. Mohon login kembali.');
            localStorage.removeItem('isLoggedIn');
            localStorage.removeItem('loggedInUser');
            localStorage.removeItem('loggedInUserNim');
            localStorage.removeItem('userRole');
            window.location.href = 'login-mahasiswa.html';
        }
    }


    // --- Profile Settings Mahasiswa Page Logic (only runs on profile-settings-mahasiswa.html) ---
    const profileInfoFormMahasiswa = document.getElementById('profileInfoFormMahasiswa');
    if (profileInfoFormMahasiswa && userRole === 'mahasiswa') {
        const nimMahasiswaProfile = document.getElementById('nimMahasiswaProfile');
        const namaMahasiswaProfile = document.getElementById('namaMahasiswaProfile');
        const jenisKelaminMahasiswaProfile = document.getElementById('jenisKelaminMahasiswaProfile');
        const tempatTglLahirMahasiswaProfile = document.getElementById('tempatTglLahirMahasiswaProfile');
        const agamaMahasiswaProfile = document.getElementById('agamaMahasiswaProfile');
        const namaIbuMahasiswaProfile = document.getElementById('namaIbuMahasiswaProfile');
        const noHpOrtuMahasiswaProfile = document.getElementById('noHpOrtuMahasiswaProfile');
        const emailOrtuMahasiswaProfile = document.getElementById('emailOrtuMahasiswaProfile');

        const editProfileButtonMahasiswa = document.getElementById('editProfileButtonMahasiswa');
        const editActionsMahasiswa = profileInfoFormMahasiswa.querySelector('.edit-actions');
        const cancelEditButtonMahasiswa = document.getElementById('cancelEditButtonMahasiswa');
        const profileMessageMahasiswa = document.getElementById('profileMessageMahasiswa');

        const avatarUploadMahasiswa = document.getElementById('avatarUploadMahasiswa');
        const profileAvatarPreviewMahasiswa = document.getElementById('profileAvatarPreviewMahasiswa');
        const loggedInMahasiswaNameProfile = document.getElementById('loggedInMahasiswaNameProfile');


        let currentMahasiswaData = null;

        function loadMahasiswaProfile() {
            const loggedInNim = localStorage.getItem('loggedInUserNim');
            currentMahasiswaData = mahasiswaData.find(mhs => mhs.nim === loggedInNim);

            if (currentMahasiswaData) {
                if (nimMahasiswaProfile) nimMahasiswaProfile.value = currentMahasiswaData.nim;
                if (namaMahasiswaProfile) namaMahasiswaProfile.value = currentMahasiswaData.nama;
                if (jenisKelaminMahasiswaProfile) jenisKelaminMahasiswaProfile.value = currentMahasiswaData.jenis_kelamin;
                if (tempatTglLahirMahasiswaProfile) tempatTglLahirMahasiswaProfile.value = currentMahasiswaData.tempat_tgl_lahir;
                if (agamaMahasiswaProfile) agamaMahasiswaProfile.value = currentMahasiswaData.agama;
                if (namaIbuMahasiswaProfile) namaIbuMahasiswaProfile.value = currentMahasiswaData.nama_ibu;
                if (noHpOrtuMahasiswaProfile) noHpOrtuMahasiswaProfile.value = currentMahasiswaData.no_hp_ortu;
                if (emailOrtuMahasiswaProfile) emailOrtuMahasiswaProfile.value = currentMahasiswaData.email_ortu || '';
                if (loggedInMahasiswaNameProfile) loggedInMahasiswaNameProfile.textContent = currentMahasiswaData.nama;
            } else {
                alert('Data profil mahasiswa tidak ditemukan. Mohon login ulang.');
                localStorage.removeItem('isLoggedIn');
                localStorage.removeItem('loggedInUser');
                localStorage.removeItem('loggedInUserNim');
                localStorage.removeItem('userRole');
                window.location.href = 'login-mahasiswa.html';
            }
            toggleProfileEditMahasiswa(false);
        }

        function toggleProfileEditMahasiswa(enable) {
            if (namaMahasiswaProfile) namaMahasiswaProfile.readOnly = !enable;
            if (jenisKelaminMahasiswaProfile) jenisKelaminMahasiswaProfile.readOnly = !enable;
            if (tempatTglLahirMahasiswaProfile) tempatTglLahirMahasiswaProfile.readOnly = !enable;
            if (agamaMahasiswaProfile) agamaMahasiswaProfile.readOnly = !enable;
            if (namaIbuMahasiswaProfile) namaIbuMahasiswaProfile.readOnly = !enable;
            if (noHpOrtuMahasiswaProfile) noHpOrtuMahasiswaProfile.readOnly = !enable;
            if (emailOrtuMahasiswaProfile) emailOrtuMahasiswaProfile.readOnly = !enable;

            if (editProfileButtonMahasiswa) editProfileButtonMahasiswa.style.display = enable ? 'none' : 'block';
            if (editActionsMahasiswa) editActionsMahasiswa.style.display = enable ? 'flex' : 'none';

            [namaMahasiswaProfile, jenisKelaminMahasiswaProfile, tempatTglLahirMahasiswaProfile,
                agamaMahasiswaProfile, namaIbuMahasiswaProfile, noHpOrtuMahasiswaProfile,
                emailOrtuMahasiswaProfile].forEach(input => {
                    if (input) {
                        if (enable) {
                            input.classList.remove('non-editable');
                        } else {
                            input.classList.add('non-editable');
                        }
                    }
                });
        }

        loadMahasiswaProfile();

        if (editProfileButtonMahasiswa) {
            editProfileButtonMahasiswa.addEventListener('click', function () {
                toggleProfileEditMahasiswa(true);
                if (profileMessageMahasiswa) {
                    profileMessageMahasiswa.textContent = '';
                    profileMessageMahasiswa.classList.remove('success', 'error', 'show');
                }
            });
        }

        if (cancelEditButtonMahasiswa) {
            cancelEditButtonMahasiswa.addEventListener('click', function () {
                loadMahasiswaProfile();
                if (profileMessageMahasiswa) {
                    profileMessageMahasiswa.textContent = '';
                    profileMessageMahasiswa.classList.remove('success', 'error', 'show');
                }
            });
        }

        profileInfoFormMahasiswa.addEventListener('submit', function (event) {
            event.preventDefault();

            const updatedData = {
                nim: nimMahasiswaProfile ? nimMahasiswaProfile.value.trim() : '',
                nama: namaMahasiswaProfile ? namaMahasiswaProfile.value.trim() : '',
                jenis_kelamin: jenisKelaminMahasiswaProfile ? jenisKelaminMahasiswaProfile.value.trim() : '',
                tempat_tgl_lahir: tempatTglLahirMahasiswaProfile ? tempatTglLahirMahasiswaProfile.value.trim() : '',
                agama: agamaMahasiswaProfile ? agamaMahasiswaProfile.value.trim() : '',
                nama_ibu: namaIbuMahasiswaProfile ? namaIbuMahasiswaProfile.value.trim() : '',
                no_hp_ortu: noHpOrtuMahasiswaProfile ? noHpOrtuMahasiswaProfile.value.trim() : '',
                email_ortu: emailOrtuMahasiswaProfile ? emailOrtuMahasiswaProfile.value.trim() : ''
            };

            if (updatedData.email_ortu && (!updatedData.email_ortu.includes('@') || !updatedData.email_ortu.includes('.'))) {
                if (profileMessageMahasiswa) {
                    profileMessageMahasiswa.textContent = 'Format email orang tua tidak valid!';
                    profileMessageMahasiswa.className = 'message error show';
                }
                return;
            }

            console.log("Saving student profile data (simulasi):", updatedData);

            setTimeout(() => {
                const index = mahasiswaData.findIndex(mhs => mhs.nim === updatedData.nim);
                if (index !== -1) {
                    mahasiswaData[index].nama = updatedData.nama;
                    mahasiswaData[index].jenis_kelamin = updatedData.jenis_kelamin;
                    mahasiswaData[index].tempat_tgl_lahir = updatedData.tempat_tgl_lahir;
                    mahasiswaData[index].agama = updatedData.agama;
                    mahasiswaData[index].nama_ibu = updatedData.nama_ibu;
                    mahasiswaData[index].no_hp_ortu = updatedData.no_hp_ortu;
                    mahasiswaData[index].email_ortu = updatedData.email_ortu;
                    localStorage.setItem('loggedInUser', updatedData.nama); // Update nama di localStorage
                }

                if (profileMessageMahasiswa) {
                    profileMessageMahasiswa.textContent = 'Profil berhasil diperbarui!';
                    profileMessageMahasiswa.className = 'message success show';
                }
                toggleProfileEditMahasiswa(false);
                loadMahasiswaProfile(); // Reload data untuk menampilkan perubahan
                setTimeout(() => {
                    if (profileMessageMahasiswa) profileMessageMahasiswa.classList.remove('show');
                }, 3000);
            }, 1000);
        });

        if (avatarUploadMahasiswa) {
            avatarUploadMahasiswa.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        if (profileAvatarPreviewMahasiswa) profileAvatarPreviewMahasiswa.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    // --- Change Password Form Logic Mahasiswa (only runs on profile-settings-mahasiswa.html) ---
    const changePasswordFormMahasiswa = document.getElementById('changePasswordFormMahasiswa');
    if (changePasswordFormMahasiswa && userRole === 'mahasiswa') {
        const currentPasswordInputMahasiswa = document.getElementById('currentPasswordMahasiswa');
        const newPasswordInputMahasiswa = document.getElementById('newPasswordMahasiswa');
        const confirmNewPasswordInputMahasiswa = document.getElementById('confirmNewPasswordMahasiswa');
        const passwordMessageMahasiswa = document.getElementById('passwordMessageMahasiswa');

        changePasswordFormMahasiswa.addEventListener('submit', function (event) {
            event.preventDefault();

            const loggedInNim = localStorage.getItem('loggedInUserNim');
            const currentMahasiswa = mahasiswaData.find(mhs => mhs.nim === loggedInNim);

            if (!currentMahasiswa) {
                if (passwordMessageMahasiswa) {
                    passwordMessageMahasiswa.textContent = 'Terjadi kesalahan: Data mahasiswa tidak ditemukan.';
                    passwordMessageMahasiswa.className = 'message error show';
                }
                return;
            }

            const currentPassword = currentPasswordInputMahasiswa.value.trim();
            const newPassword = newPasswordInputMahasiswa.value.trim();
            const confirmNewPassword = confirmNewPasswordInputMahasiswa.value.trim();

            if (currentPassword !== currentMahasiswa.password) {
                if (passwordMessageMahasiswa) {
                    passwordMessageMahasiswa.textContent = 'Password saat ini salah!';
                    passwordMessageMahasiswa.className = 'message error show';
                }
                return;
            }

            if (newPassword.length < 6) {
                if (passwordMessageMahasiswa) {
                    passwordMessageMahasiswa.textContent = 'Password baru minimal 6 karakter!';
                    passwordMessageMahasiswa.className = 'message error show';
                }
                return;
            }

            if (newPassword !== confirmNewPassword) {
                if (passwordMessageMahasiswa) {
                    passwordMessageMahasiswa.textContent = 'Konfirmasi password baru tidak cocok!';
                    passwordMessageMahasiswa.className = 'message error show';
                }
                return;
            }

            if (newPassword === currentPassword) {
                if (passwordMessageMahasiswa) {
                    passwordMessageMahasiswa.textContent = 'Password baru tidak boleh sama dengan password lama!';
                    passwordMessageMahasiswa.className = 'message error show';
                }
                return;
            }

            console.log("Changing student password (simulasi)...");
            setTimeout(() => {
                currentMahasiswa.password = newPassword; // Update password di data simulasi

                if (passwordMessageMahasiswa) {
                    passwordMessageMahasiswa.textContent = 'Password berhasil diubah!';
                    passwordMessageMahasiswa.className = 'message success show';
                }
                currentPasswordInputMahasiswa.value = '';
                newPasswordInputMahasiswa.value = '';
                confirmNewPasswordInputMahasiswa.value = '';
                setTimeout(() => {
                    if (passwordMessageMahasiswa) passwordMessageMahasiswa.classList.remove('show');
                }, 3000);
            }, 1500);
        });
    }

    // --- Informasi Pembayaran Page Logic (only runs on pembayaran.html for Mahasiswa) ---
    if (currentPath === 'pembayaran.html' && userRole === 'mahasiswa') {
        const loggedInMahasiswaNim = localStorage.getItem('loggedInUserNim');
        const loggedInMahasiswaName = localStorage.getItem('loggedInUser');
        const displayUserNameHeader = document.getElementById('loggedInUserName'); // Untuk header

        if (displayUserNameHeader) {
            displayUserNameHeader.textContent = loggedInMahasiswaName || 'Mahasiswa';
        }

        const currentMahasiswa = mahasiswaData.find(mhs => mhs.nim === loggedInMahasiswaNim);

        if (currentMahasiswa) {
            // Status Pembayaran KIP Kuliah
            // Urutkan pembayaran berdasarkan tanggal cair (jika ada) atau periode untuk mendapatkan yang terakhir
            const sortedPayments = currentMahasiswa.pembayaran ? [...currentMahasiswa.pembayaran].sort((a, b) => {
                const dateA = a.tanggalCair !== '-' ? new Date(a.tanggalCair) : new Date(0); // Gunakan epoch jika tidak ada tanggal
                const dateB = b.tanggalCair !== '-' ? new Date(b.tanggalCair) : new Date(0);
                return dateA - dateB;
            }) : [];

            const latestPayment = sortedPayments.length > 0 ? sortedPayments[sortedPayments.length - 1] : null;

            document.getElementById('statusPencairan').textContent = latestPayment ? latestPayment.status : 'N/A';
            document.getElementById('tanggalPencairan').textContent = latestPayment ? latestPayment.tanggalCair : 'N/A';
            document.getElementById('jumlahDanaCair').textContent = latestPayment ? latestPayment.jumlah : 'N/A';
            document.getElementById('periodePembayaran').textContent = latestPayment ? latestPayment.periode : 'N/A';

            // Riwayat Pembayaran
            const riwayatTableBody = document.getElementById('riwayatPembayaranTableBody');
            if (riwayatTableBody && currentMahasiswa.pembayaran) {
                riwayatTableBody.innerHTML = ''; // Bersihkan tabel
                currentMahasiswa.pembayaran.forEach((p, index) => {
                    const row = riwayatTableBody.insertRow();
                    let statusClass = '';
                    if (p.status === 'Cair') {
                        statusClass = 'status-success';
                    } else if (p.status === 'Pending') {
                        statusClass = 'status-pending';
                    } else if (p.status === 'Gagal') {
                        statusClass = 'status-error';
                    }

                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${p.periode}</td>
                        <td>${p.tanggalCair}</td>
                        <td>${p.jumlah}</td>
                        <td><span class="${statusClass}">${p.status}</span></td>
                        <td>${p.keterangan || '-'}</td>
                    `;
                });
            }

            // Informasi Rekening Penerima
            const rekeningInfo = currentMahasiswa.rekening;
            if (rekeningInfo) {
                document.getElementById('namaBank').textContent = rekeningInfo.namaBank || 'N/A';
                document.getElementById('nomorRekening').textContent = rekeningInfo.nomorRekening || 'N/A';
                document.getElementById('atasNamaRekening').textContent = rekeningInfo.atasNama || 'N/A';
                document.getElementById('cabangBank').textContent = rekeningInfo.cabangBank || 'N/A';
            } else {
                // Sembunyikan atau beri pesan jika info rekening tidak ada
                document.getElementById('namaBank').textContent = 'Belum Ada';
                document.getElementById('nomorRekening').textContent = 'Belum Ada';
                document.getElementById('atasNamaRekening').textContent = 'Belum Ada';
                document.getElementById('cabangBank').textContent = 'Belum Ada';
            }

        } else {
            alert('Data mahasiswa tidak ditemukan. Mohon login kembali.');
            localStorage.removeItem('isLoggedIn');
            localStorage.removeItem('loggedInUser');
            localStorage.removeItem('loggedInUserNim');
            localStorage.removeItem('userRole');
            window.location.href = 'login-mahasiswa.html';
        }
    }


    // --- Role-based Navigation Display ---
    const adminSidebar = document.querySelector('.sidebar:not(#mahasiswaSidebar)');
    const mahasiswaSidebar = document.getElementById('mahasiswaSidebar');

    if (userRole === 'mahasiswa') {
        if (adminSidebar) adminSidebar.style.display = 'none';
        if (mahasiswaSidebar) mahasiswaSidebar.style.display = 'block';

        // Update profile link in dropdown for mahasiswa
        const profileSettingsLinkDropdown = document.querySelector('#profileDropdown a[href="profile-settings.html"]');
        if (profileSettingsLinkDropdown) {
            profileSettingsLinkDropdown.href = 'profile-settings-mahasiswa.html';
            profileSettingsLinkDropdown.textContent = 'Pengaturan Profil'; // Pastikan teksnya benar
        }

        // Update profile link in admin sidebar (if accidentally loaded)
        // Ini tidak perlu di sini karena adminSidebar disembunyikan
        // const profileSettingsLinkAdminSidebar = document.querySelector('.sidebar a[href="profile-settings.html"]');
        // if (profileSettingsLinkAdminSidebar) {
        //     profileSettingsLinkAdminSidebar.href = 'profile-settings-mahasiswa.html';
        // }

    } else if (userRole === 'admin') {
        if (adminSidebar) adminSidebar.style.display = 'block';
        if (mahasiswaSidebar) mahasiswaSidebar.style.display = 'none';

        // Update profile link in dropdown for admin (if exists and points to admin)
        const profileSettingsLinkDropdown = document.querySelector('#profileDropdown a[href="profile-settings-mahasiswa.html"]');
        if (profileSettingsLinkDropdown) {
            profileSettingsLinkDropdown.href = 'profile-settings.html';
            profileSettingsLinkDropdown.textContent = 'Pengaturan Profil'; // Pastikan teksnya benar
        }

    } else {
        // If not logged in or no role, hide all sidebars
        if (adminSidebar) adminSidebar.style.display = 'none';
        if (mahasiswaSidebar) mahasiswaSidebar.style.display = 'none';
    }


    // Mengubah label peran di header
    const userRoleLabelElem = document.querySelector('.user-role-label');
    if (userRoleLabelElem) {
        if (userRole === 'admin') {
            userRoleLabelElem.textContent = 'Admin';
            userRoleLabelElem.style.backgroundColor = '#007bff'; // Warna biru untuk admin
        } else if (userRole === 'mahasiswa') {
            userRoleLabelElem.textContent = 'Mahasiswa';
            userRoleLabelElem.style.backgroundColor = '#5cb85c'; // Warna hijau untuk mahasiswa
        } else {
            userRoleLabelElem.style.display = 'none'; // Sembunyikan jika tidak ada peran
        }
    }

    // Update logged in user name in header for admin (if not specific to mahasiswa)
    // loggedInUserName di header diubah untuk menampilkan nama dari localStorage.loggedInUser
    const loggedInUserNameHeader = document.getElementById('loggedInUserName');
    if (loggedInUserNameHeader) {
        loggedInUserNameHeader.textContent = localStorage.getItem('loggedInUser') || 'Pengguna';
    }

}); // End of DOMContentLoaded