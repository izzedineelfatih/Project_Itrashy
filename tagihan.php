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
    <title>Tagihan</title>
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
<div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <!-- Header -->
            <?php include 'header.php'; ?>

            <div class="flex-1 overflow-y-auto">
                <div class="p-5">
                    <div class="lg:hidden block bg-gradient-to-r from-[#FED4B4] to-[#54B68B] p-4 rounded-lg mb-10">
                        <div class="flex items-center space-x-4">
                            <img class="h-10 w-10" src="assets/icon/poin logo.png" alt="Poin">
                            <h4 class="text-2xl font-bold">Rp 50.000</h4>
                        </div>
                    </div>
                
                    <!-- Pilihan Kategori -->
                    <div class="flex space-x-4 mb-6">
                        <button id="pulsa-btn" class="px-4 py-2 rounded-full bg-blue-500 text-white focus:outline-none">
                            Pulsa
                        </button>
                        <button id="token-btn" class="px-4 py-2 rounded-full bg-gray-200 text-gray-700 focus:outline-none">
                            Token Listrik
                        </button>
                    </div>

                    <!-- Kontainer Formulir -->
                    <div id="pulsa-form" class="block">
                        <div class="bg-white rounded-lg p-5">
                            <h3 class="text-lg font-semibold mb-4">Pulsa</h3>
                            
                            <!-- Pilihan Operator -->
                            <div class="grid grid-cols-4 gap-4 mb-4">
                                <?php 
                                $operators = [
                                    ['name' => 'Telkomsel', 'logo' => 'telkomsel.png'],
                                    ['name' => 'Indosat', 'logo' => 'im3.png'],
                                    ['name' => 'XL', 'logo' => 'xl.png'],
                                    ['name' => 'Tri', 'logo' => 'tri.png']
                                ];

                                foreach ($operators as $operator): ?>
                                    <div class="operator-item cursor-pointer bg-gray-200 rounded-lg p-2 text-center hover:bg-blue-100 transition">
                                        <img src="assets/image/<?= $operator['logo'] ?>" alt="<?= $operator['name'] ?>" class="w-16 h-16 mx-auto">
                                        <p class="text-sm"><?= $operator['name'] ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Input Nomor HP -->
                            <div class="mb-4">
                                <label class="block text-gray-700 mb-2">Nomor Handphone</label>
                                <div class="flex">
                                    <input 
                                        type="tel" 
                                        id="phone-number" 
                                        class="rounded-lg flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Masukkan nomor handphone"
                                    >
                                </div>
                            </div>

                            <!-- Pilihan Nominal -->
                            <div class="grid grid-cols-4 gap-4 mt-10">
                                <?php 
                                $nominals = [
                                    ['value' => 2000, 'points' => 3500],
                                    ['value' => 5000, 'points' => 6500],
                                    ['value' => 10000, 'points' => 12000],
                                    ['value' => 20000, 'points' => 22000],
                                    ['value' => 50000, 'points' => 52000],
                                    ['value' => 100000, 'points' => 102000],
                                    ['value' => 200000, 'points' => 202000],
                                    ['value' => 500000, 'points' => 502000]
                                ];

                                foreach ($nominals as $nominal): ?>
                                    <div class="nominal-item cursor-pointer bg-[url('assets/image/bg.png')] bg-cover bg-center h-22 w-56 rounded-xl p-3 text-center hover:shadow-xl">
                                        <p class="font-bold text-xl text-white">Rp <?= number_format($nominal['value'], 0, ',', '.') ?></p>
                                        <p class="font-semibold text-white mt-2">
                                            <img src="assets/icon/poin logo.png" class="inline w-5 h-5 mr-1 mb-1">
                                            <?= number_format($nominal['points'], 0, ',', '.') ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Formulir Token Listrik -->
                    <div id="token-form" class="hidden">
                        <div class="bg-white rounded-lg p-5">
                            <h3 class="text-lg font-semibold mb-4">Token Listrik</h3>
                            
                            <!-- Input ID Pelanggan -->
                            <div class="mb-4">
                                <label class="block text-gray-700 mb-2">ID Pelanggan</label>
                                <input 
                                    type="text" 
                                    id="customer-id" 
                                    class="rounded-lg appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan ID Pelanggan"
                                >
                            </div>

                            <!-- Pilihan Nominal Token -->
                            <div class="grid grid-cols-3 gap-4 mt-10">
                                <?php 
                                $tokenNominals = [
                                    ['value' => 20000, 'points' => 22000],
                                    ['value' => 50000, 'points' => 52000],
                                    ['value' => 100000, 'points' => 102000],
                                    ['value' => 200000, 'points' => 202000],
                                    ['value' => 500000, 'points' => 502000],
                                    ['value' => 1000000, 'points' => 1002000]
                                ];

                                foreach ($tokenNominals as $nominal): ?>
                                    <div class="token-nominal-item cursor-pointer bg-[url('assets/image/bg.png')] bg-cover bg-center h-22 w-56 rounded-xl p-3 text-center hover:shadow-xl">
                                        <p class="font-bold text-xl text-white">Rp <?= number_format($nominal['value'], 0, ',', '.') ?></p>
                                        <p class="font-semibold text-white mt-2">
                                            <img src="assets/icon/poin logo.png" class="inline w-5 h-5 mr-1 mb-1">
                                            <?= number_format($nominal['points'], 0, ',', '.') ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Kirim -->
                    <div class="mt-6">
                        <button id="submit-btn" class="px-4 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 focus:outline-none">
                            Kirim
                        </button>
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

    document.addEventListener('DOMContentLoaded', function() {
        const pulsaBtn = document.getElementById('pulsa-btn');
        const tokenBtn = document.getElementById('token-btn');
        
        if (pulsaBtn && tokenBtn) {
            pulsaBtn.addEventListener('click', function() {
                document.getElementById('pulsa-form').classList.remove('hidden');
                document.getElementById('token-form').classList.add('hidden');
                this.classList.add('bg-blue-500');
                this.classList.remove('bg-gray-200', 'text-gray-700');
                document.getElementById('token-btn').classList.add('bg-gray-200', 'text-gray-700');
                document.getElementById('token-btn').classList.remove('bg-blue-500', 'text-white');
            });

            tokenBtn.addEventListener('click', function() {
                document.getElementById('token-form').classList.remove('hidden');
                document.getElementById('pulsa-form').classList.add('hidden');
                this.classList.add('bg-blue-500');
                this.classList.remove('bg-gray-200', 'text-gray-700');
                document.getElementById('pulsa-btn').classList.add('bg-gray-200', 'text-gray-700');
                document.getElementById('pulsa-btn').classList.remove('bg-blue-500', 'text-white');
            });
        }
    })
    </script>
</body>
</html>