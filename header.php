<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<header class="sticky top-0 bg-white shadow-md z-40">
    <div class="flex items-center justify-between p-4">
        <div class="flex items-center space-x-4">
            <button id="menuToggle" class="lg:hidden">
                <svg class="w-6 h-6" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
            <h2 id="pageTitle" class="text-lg font-semibold" data-username="<?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>">
                <!-- Title akan diupdate oleh JavaScript -->
            </h2>
        </div>
        <div class="flex items-center space-x-4">
            <a href="notifikasi.html">
                <img src="assets/icon/notifikasi.png" alt="Notifications" class="w-8 h-8">
            </a>
            <div class="w-px h-10 bg-black"></div>
            <a href="profile.php">
                <img src="assets/image/profile.jpg" alt="Profile" class="w-10 h-10 rounded-full">
            </a>
        </div>
    </div>
</header>