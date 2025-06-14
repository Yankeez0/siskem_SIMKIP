<div class="sidebar">
    <h3>SIM - KIP</h3>
    <?php if ($auth->isAdmin()): ?>
        <a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Dashboard</a>
        <a href="rekapitulasi.php" <?php echo basename($_SERVER['PHP_SELF']) == 'rekapitulasi.php' ? 'class="active"' : ''; ?>>Rekapitulasi</a>
        <a href="data-mahasiswa.php" <?php echo basename($_SERVER['PHP_SELF']) == 'data-mahasiswa.php' ? 'class="active"' : ''; ?>>Data Mahasiswa</a>
        <a href="keuangan.php" <?php echo basename($_SERVER['PHP_SELF']) == 'keuangan.php' ? 'class="active"' : ''; ?>>Keuangan</a>
    <?php else: ?>
        <a href="dashboard-mahasiswa.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard-mahasiswa.php' ? 'class="active"' : ''; ?>>Dashboard</a>
        <a href="profile-settings-mahasiswa.php" <?php echo basename($_SERVER['PHP_SELF']) == 'profile-settings-mahasiswa.php' ? 'class="active"' : ''; ?>>Profil</a>
        <a href="ajukan-perubahan-data.php" <?php echo basename($_SERVER['PHP_SELF']) == 'ajukan-perubahan-data.php' ? 'class="active"' : ''; ?>>Ajukan Perubahan</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</div>
