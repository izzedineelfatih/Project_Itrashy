<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donasi</title>
    <meta name="page-title" content="Donasi">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['THICCCBOI'],
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-[#f5f6fb] font-sans">
    <!-- Main Layout Container -->
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <!-- Header -->
            <?php include 'header.php'; ?>

            <div class="flex-1 overflow-y-auto">
                <!-- kode tulis di sini -->
                <div class="p-5">
                    <div class="shadow-lg mb-6">
                        <img src="assets/image/poster5.png" alt="Banner Image" class="w-full rounded-lg">
                    </div>

                    <div class="lg:hidden block bg-gradient-to-r from-[#FED4B4] to-[#54B68B] p-4 rounded-lg mb-10">
                        <div class="flex items-center space-x-4">
                            <img class="h-10 w-10" src="assets/icon/poin logo.png" alt="Poin">
                            <h4 class="text-2xl font-bold">Rp 50.000</h4>
                        </div>
                    </div>

                    <!-- donasi card -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-2 lg:gap-5">
                    <?php 
                        $donasi = [
                            [
                                'image' => 'bank sampah.png',
                                'title' => 'Pembangunan Fasilitas Bank Sampah di Bandung',
                                'progress' => 20,
                                'collected' => 'Rp 10.000.000',
                                'goal' => 'Rp 100.000.000'
                            ],
                            [
                                'image' => 'menanam pohon.png',
                                'title' => 'Penanaman 1000 Bibit Mangrove di Pantai Indonesia',
                                'progress' => 40,
                                'collected' => 'Rp 20.000.000',
                                'goal' => 'Rp 50.000.000'
                            ],
                            [
                                'image' => 'petugas kebersihan.png',
                                'title' => 'Bantuan Kemanusiaan Untuk Petugas Kebersihan di Indonesia',
                                'progress' => 50,
                                'collected' => 'Rp 50.000.000',
                                'goal' => 'Rp 100.000.000'
                            ],
                            [
                                'image' => 'terumbu karang.png',
                                'title' => 'Restorasi Terumbu Karang di Pantai Sumatera',
                                'progress' => 90,
                                'collected' => 'Rp 10.000.000',
                                'goal' => 'Rp 15.000.000'
                            ],
                            [
                                'image' => 'daur ulang kerajinan.png',
                                'title' => 'Pendanaan UMKM kerajinan Daur Ulang Sampah',
                                'progress' => 30,
                                'collected' => 'Rp 10.000.000',
                                'goal' => 'Rp 40.000.000'
                            ],
                            [
                                'image' => 'membersihkan sungai.png',
                                'title' => 'Program Bersih - Bersih Sungai di Indonesia',
                                'progress' => 70,
                                'collected' => 'Rp 15.000.000',
                                'goal' => 'Rp 25.000.000'
                            ],
                        ];

                        foreach ($donasi as $donasi_item) {
                            echo '<div class="bg-white rounded-lg shadow-md p-3 donasi-card">';
                            echo '<a href="#">';
                            echo '<img src="assets/image/' . $donasi_item['image'] . '" alt="Donasi Image" class="w-full h-32 md:h-40 object-cover rounded-lg">';
                            echo '<h4 class="font-semibold mt-2">' . $donasi_item['title'] . '</h4>';
                            echo '<div class="relative mt-4 mb-4">';
                            echo '<div class="flex mt-2">';
                            echo '<div class="relative flex mb-2 w-full">';
                            echo '<div class="flex-1 bg-gray-300 rounded-full h-2.5">';
                            echo '<div class="bg-blue-600 h-2.5 rounded-full" style="width: ' . $donasi_item['progress'] . '%"></div>';
                            echo '</div></div></div>';
                            echo '<label for="progress" class="donasi-terkumpul block text-sm font-medium text-gray-700"><strong>' . $donasi_item['collected'] . ' terkumpul</strong> dari ' . $donasi_item['goal'] . '</label>';
                            echo '</div></a></div>';
                        }
                        ?>
                    </div>
                </div>
                <?php include 'footer.php'; ?>
            </div>  
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    initializeMenu();
    });

    function initializeMenu() {
        const menuToggle = document.getElementById('menuToggle');
        const menuClose = document.getElementById('menuClose');
        const menu = document.getElementById('menu');
        const navLinks = document.querySelectorAll('.nav-link');
        const pageTitle = document.getElementById('pageTitle');
        
        // Dapatkan nama halaman dari URL saat ini
        const currentPage = window.location.pathname.split('/').pop().replace('.php', '');

        // Mobile menu toggles
        if (menuToggle && menuClose && menu) {
            menuToggle.addEventListener('click', () => menu.classList.remove('-translate-x-full'));
            menuClose.addEventListener('click', () => menu.classList.add('-translate-x-full'));
        }

        // Set active menu item dan update page title
        navLinks.forEach(link => {
            const href = link.getAttribute('href').replace('.php', '');
            const menuText = link.querySelector('span').textContent;
            
            if (href.includes(currentPage)) {
                link.classList.add('active');
                // Update page title sesuai menu yang aktif
                if (pageTitle) {
                    if (currentPage === 'dashboard') {
                        pageTitle.textContent = `Halo, ${pageTitle.dataset.username}👋`;
                    } else {
                        pageTitle.textContent = menuText;
                    }
                }
            }
        });
    }
    </script>
</body>
</html>