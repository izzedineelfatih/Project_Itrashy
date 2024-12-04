<!-- Sidebar -->
<div class="w-64 bg-white shadow-md min-h-screen p-5">
    <div class="flex items-center mb-10">
        <img src="assets/image/Logo Itrashy.png" alt="I-Trashy Logo" class="w-10 mr-3">
        <h1 class="text-xl font-bold">I-Trashy Admin</h1>
    </div>

    <nav>
        <ul class="space-y-2">
            <li>
                <a href="admin_dashboard.php" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <i data-feather="home" class="mr-3"></i>
                    Dashboard
                </a>
            </li>
            
            <!-- Dropdown Menu Jemput Sampah -->
            <li x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="w-full flex items-center justify-between p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i data-feather="truck" class="mr-3"></i>
                        Jemput Sampah
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
                        <a href="daftar_order.php" class="block p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                            Daftar Order
                        </a>
                    </li>
                    <li>
                        <a href="edit_order.php" class="block p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                            Edit Order
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Dropdown Menu Jemput Sampah -->
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
                        <a href=".php" class="block p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                            Katalog Sembako
                        </a>
                    </li>
                    <li>
                        <a href=".php" class="block p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                            Katalog Voucher
                        </a>
                    </li>
                    <li>
                        <a href=".php" class="block p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                            Katalog Donasi
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <i data-feather="users" class="mr-3"></i>
                    Manajemen User
                </a>
            </li>

            <li>
                <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                    <i data-feather="pie-chart" class="mr-3"></i>
                    Laporan
                </a>
            </li>

            <li>
                <a href="admin_logout.php" class="flex items-center p-2 text-red-500 hover:bg-red-50 rounded-lg">
                    <i data-feather="log-out" class="mr-3"></i>
                    Logout
                </a>
            </li>
        </ul>
    </nav>
</div>
