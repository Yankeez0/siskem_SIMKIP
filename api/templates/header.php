<div class="header-main">
    <div class="header-left">
        <span class="<?php echo $auth->isAdmin() ? 'admin-label' : 'mahasiswa-label'; ?>">
            <?php echo $auth->isAdmin() ? 'admin' : 'mahasiswa'; ?>
        </span>
    </div>
    <div class="header-right">
        <div class="header-icons">
            <i class="fas fa-search"></i>
            <i class="fas fa-bell"></i>
        </div>
        <div class="user-profile-toggle" id="userProfileToggle">
            <img src="https://via.placeholder.com/35" alt="Avatar" class="user-avatar">
            <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <i class="fas fa-caret-down caret-icon"></i>
        </div>
        <div id="profileDropdown" class="profile-dropdown">
            <a href="profile-settings.php">Pengaturan Profil</a>
            <a href="help.php">Bantuan</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>
