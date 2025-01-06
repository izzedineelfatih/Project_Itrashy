
<head>
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<!-- Sidebar -->
<div class="w-64 bg-white shadow-md min-h-screen p-5">
    <div class="flex items-center mb-10">
        <img src="assets/image/Logo Itrashy.png" alt="I-Trashy Logo" class="w-10 mr-3">
        <h1 class="text-xl font-bold">I-Trashy Admin</h1>
    </div>

    <nav>
        <ul class="space-y-2">
            <!-- Menu Dashboard untuk Semua Pengguna -->
            <li>
                <a href="<?php echo ($_SESSION['staff_role'] === 'admin') ? 'admin_dashboard.php' : 'driver_dashboard.php'; ?>" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <i data-feather="home" class="mr-3"></i>
                    Dashboard
                </a>
            </li>

            <!-- Menu Daftar Order hanya untuk Admin -->
            <?php if ($_SESSION['staff_role'] === 'driver'): ?>
            <li>
                <a href="daftar_order.php" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <i data-feather="truck" class="mr-3"></i>
                    Daftar Order
                </a>
            </li>
            <?php endif; ?>

            <?php if ($_SESSION['staff_role'] === 'driver'): ?>
            <li>
                <a href="order_tukarPoin.php" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <i data-feather="truck" class="mr-3"></i>
                    Order Tukar Poin
                </a>
            </li>
            <?php endif; ?>

            

            <!-- Dropdown Menu Manajemen Konten hanya untuk Admin -->
            <?php if ($_SESSION['staff_role'] === 'admin'): ?>
            <li x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i data-feather="file-text" class="mr-3"></i>
                        Manajemen Konten
                    </div>
                    <i data-feather="chevron-down" class="ml-auto" x-show="!open"></i>
                    <i data-feather="chevron-up" class="ml-auto" x-show="open" style="display:none;"></i>
                </button>

                <ul 
                    x-show="open" 
                    x-transition 
                    class="pl-8 mt-2 space-y-2"
                    style="display:none;"
                >
                    <li>
                        <a href="katalog_sampah.php" class="block p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                            Katalog Sampah
                        </a>
                    </li>
                    <li>
                        <a href="katalog_sembako.php" class="block p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                            Katalog Sembako
                        </a>
                    </li>
                    <li>
                        <a href="katalog_voucher.php" class="block p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                            Katalog Voucher
                        </a>
                    </li>
                    <li>
                        <a href="katalog_donasi.php" class="block p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                            Katalog Donasi
                        </a>
                    </li>
                    <li>
                        <a href="katalog_edukasi.php" class="block p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                            Katalog Edukasi
                        </a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>

            <li>
                <a href="<?php echo ($_SESSION['staff_role'] === 'admin') ? 'driver_riwayat_order.php' : 'driver_riwayat_order.php'; ?>" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <i data-feather="home" class="mr-3"></i>
                    Riwayat Order
                </a>
            </li>
            
            <li>
                <a href="<?php echo ($_SESSION['staff_role'] === 'admin') ? 'riwayat_order_tukarPoin.php' : 'riwayat_order_tukarPoin.php'; ?>" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <i data-feather="home" class="mr-3"></i>
                    Riwayat Tukar Poin
                </a>
            </li>

            <!-- Menu Manajemen User hanya untuk Admin -->
            <?php if ($_SESSION['staff_role'] === 'admin'): ?>
            <li>
                <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <i data-feather="users" class="mr-3"></i>
                    Manajemen User
                </a>
            </li>
            <?php endif; ?>

            <!-- Menu Laporan hanya untuk Admin -->
            <?php if ($_SESSION['staff_role'] === 'admin'): ?>
            <li>
                <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <i data-feather="pie-chart" class="mr-3"></i>
                    Laporan
                </a>
            </li>
            <?php endif; ?>

            <!-- Menu Logout untuk Semua Pengguna -->
            <li>
                <a href="staff_logout.php" class="flex items-center p-2 text-red-500 hover:bg-red-50 rounded-lg">
                    <i data-feather="log-out" class="mr-3"></i>
                    Logout
                </a>
            </li>
        </ul>
    </nav>
</div>

<!-- Alpine.js untuk dropdown -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    feather.replace();
</script>