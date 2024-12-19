<?php
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<div id="menu" class="fixed top-0 left-0 w-60 h-full bg-white shadow-lg transform -translate-x-full transition-transform duration-300 z-50 lg:translate-x-0 lg:static lg:w-[230px]">
    <div class="p-5 flex items-center justify-between lg:justify-start space-x-4">
        <img src="assets/image/Logo Itrashy.png" alt="Logo Itrashy" class="w-8 h-10">
        <h1 class="text-lg font-semibold lg:block hidden">I-Trashy</h1>
        <button id="menuClose" class="lg:hidden">
            <svg class="w-6 h-6" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="bg-gradient-to-r from-[#FED4B4] to-[#54B68B] pt-2 pb-2 ml-5 mr-5 rounded-lg shadow-lg">
        <div class="flex flex-col items-start pl-12">
            <p class="text-sm pb-1">Poin Anda</p>
            <div class="flex space-x-2 justify-center">
                <img src="assets/icon/poin logo.png" alt="Poin" class="h-6 w-6">
                <h4 class="text-xl lg:text-xl font-bold">50.000</h4>
            </div>    
        </div>
    </div>

    <nav class="p-5 mt-5 space-y-8">
        <!-- Menu items... -->
        <a href="dashboard.php" class="nav-link flex items-center space-x-4"  data-title="Dashboard">
            <img src="assets/icon/dashboard.png" alt="Dashboard Icon" class="w-5 h-5">
            <span>Dashboard</span>
        </a>
        <a href="jemputSampah.php" class="nav-link flex items-center space-x-4" data-title="Jemput Sampah">
            <img src="assets/icon/jemput.png" alt="Jemput Sampah Icon" class="w-6 h-6">
            <span>Setor Sampah</span>
        </a>
        <a href="tukarPoin.php" class="nav-link flex items-center space-x-4"  data-title="Tukar Poin">
            <img src="assets/icon/poin.png" alt="TrashPay Icon" class="w-5 h-5">
            <span>Tukar Poin</span>
        </a>
        <a href="edukasi.php" class="nav-link flex items-center space-x-4"  data-title="Edukasi">
            <img src="assets/icon/edukasi.png" alt="Edukasi Icon" class="w-5 h-5">
            <span>Edukasi</span>
        </a>
        <a href="pencapian.php" class="nav-link flex items-center space-x-4"  data-title="Pencapaian">
            <img src="assets/icon/pencapaian.png" alt="Pencapaian Icon" class="w-6 h-6">
            <span>Pencapaian</span>
        </a>
        <a href="riwayat.php" class="nav-link flex items-center space-x-4"  data-title="Riwayat">
            <img src="assets/icon/riwayat.png" alt="Riwayat Icon" class="w-6 h-6">
            <span>Riwayat</span>
        </a>
    </nav>
    <div class="p-5 border-t mt-10">
        <a href="#" class="block text-black-600 hover:text-blue-500">Bantuan</a>
        <a href="#" class="block text-black-600 hover:text-blue-500 mt-3">Pengaturan</a>
    </div>
</div>