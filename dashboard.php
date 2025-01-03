<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'config.php';

// Ambil ID user yang sedang login
$user_id = $_SESSION['user_id'];

// Ambil poin terkumpul yang sudah ada di tabel users
$stmt_poin = $pdo->prepare("SELECT poin_terkumpul FROM users WHERE id = :user_id");
$stmt_poin->execute(['user_id' => $user_id]);
$poin_terkumpul = $stmt_poin->fetchColumn();

// Cek apakah poin_terkumpul sudah ada, jika tidak, hitung berdasarkan orders
if ($poin_terkumpul === null) {
    // Ambil total poin dari order yang sudah selesai
    $stmt = $pdo->prepare("SELECT SUM(orders.total_amount) AS total_poin 
                           FROM orders 
                           WHERE orders.user_id = :user_id AND orders.status = 'done'");
    $stmt->execute(['user_id' => $user_id]);
    $total_poin = $stmt->fetchColumn();
    
    // Update poin_terkumpul di tabel users
    $stmt_update = $pdo->prepare("UPDATE users 
                                 SET poin_terkumpul = :total_poin 
                                 WHERE id = :user_id");
    $stmt_update->execute(['total_poin' => $total_poin, 'user_id' => $user_id]);
    $poin_terkumpul = $total_poin;
}

// Ambil jadwal penjemputan aktif (pending dan pickup)
$stmt = $pdo->prepare("SELECT 
    orders.id,
    orders.total_berat_sampah, 
    orders.pickup_date, 
    orders.pickup_time, 
    orders.pickup_location,
    orders.total_amount AS total_points,
    orders.status
    FROM orders 
    WHERE orders.user_id = :user_id 
    AND orders.status IN ('pending', 'pickup')
    ORDER BY orders.pickup_date ASC, orders.pickup_time ASC");
$stmt->execute(['user_id' => $user_id]);
$jadwal_penjemputan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data grafik sampah terkumpul per bulan
$stmt = $pdo->prepare("
    SELECT 
        DATE_FORMAT(pickup_date, '%b') as month,
        SUM(total_berat_sampah) as total_weight
    FROM orders
    WHERE user_id = :user_id 
    AND status = 'done'
    AND pickup_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(pickup_date, '%Y-%m')
    ORDER BY pickup_date DESC
    LIMIT 6
");
$stmt->execute(['user_id' => $user_id]);
$garbage_data = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

// Hitung jejak karbon (asumsi: 1 kg sampah = 2.5 kg CO2)
$carbon_data = array_map(function($item) {
    return [
        'month' => $item['month'],
        'carbon' => $item['total_weight'] * 2.5
    ];
}, $garbage_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <!-- Main Layout -->
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <!-- Header -->
            <?php include 'header.php'; ?>

            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="p-5">
                    <!-- Content Grid -->
                    <div class="flex flex-col lg:grid lg:grid-cols-3 lg:gap-5">
                        <!-- Left Section (2 cols) -->
                        <div class="lg:col-span-2 space-y-5 order-1">
                            <!-- Banner Slider -->
                            <div class="relative overflow-hidden rounded-lg shadow-lg">
                                <div id="slider" class="flex w-full">
                                    <img src="assets/image/poster1.png" alt="Banner 1" class="w-full">
                                    <img src="assets/image/poster2.png" alt="Banner 2" class="w-full">
                                    <img src="assets/image/poster3.png" alt="Banner 3" class="w-full">
                                </div>
                            </div>

                            <!-- Balance & Actions -->
                            <div class="bg-gradient-to-r from-[#FED4B4] to-[#54B68B] p-4 rounded-lg">
                                <div class="flex lg:flex-row md:justify-around lg:justify-around justify-between">
                                    <!-- Points Display -->
                                    <div class="flex items-center space-x-2 justify-center">
                                        <img src="assets/icon/poin logo.png" alt="Poin" class="md:w-10 lg:w-10 w-8">
                                        <h4 class="text-xl lg:text-2xl font-bold"><?= number_format($poin_terkumpul, 0, ',', '.') ?></h4>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex lg:justify-end space-x-5 lg:space-x-16 md:space-x-16">
                                        <?php
                                        $actions = [
                                            ['name' => 'Transfer', 'icon' => 'transfer.png', 'link' => 'transfer.php'],
                                            ['name' => 'Pembelian', 'icon' => 'tagihan.png', 'link' => 'tagihan.php'],
                                            ['name' => 'Donasi', 'icon' => 'donasi.png', 'link' => 'donasi.php']
                                        ];
                                        foreach ($actions as $action): ?>
                                            <a href="<?= $action['link'] ?>">
                                                <div class="flex flex-col items-center">
                                                    <button class="bg-white rounded-xl w-10 h-10 lg:w-12 lg:h-12 md:w-12 md:h-12 flex items-center justify-center shadow hover:bg-gray-50">
                                                        <img src="assets/icon/<?= $action['icon'] ?>" alt="<?= $action['name'] ?>" class="md:w-6 md:h-6 lg:w-6 lg:h-6 w-5 h-5">
                                                    </button>
                                                    <span class="lg:text-sm md:text-sm text-xs mt-2"><?= $action['name'] ?></span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- News Section -->
                            <div class="hidden lg:block">
                                <h3 class="text-xl font-bold mb-4">Terbaru</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <?php
                                    $news_items = [
                                        [
                                            'image' => 'gambar2.png',
                                            'title' => 'Yuk, Belajar Mengelola Sampah Sejak Dini Secara Mandiri',
                                            'source' => 'I-Trashy'
                                        ],
                                        [
                                            'image' => 'gambar8.png',
                                            'title' => 'Cara Membuat Kerajinan Daur Ulang dari Barang Bekas',
                                            'source' => 'Kompas.com'
                                        ],
                                        [
                                            'image' => 'gambar7.png',
                                            'title' => 'Pupuk Kompos untuk Tanaman Kesayangan Anda',
                                            'source' => 'Detik.com'
                                        ]
                                    ];
                                    foreach ($news_items as $news): ?>
                                        <div class="bg-white rounded-lg shadow-md p-3 news-card">
                                            <img src="assets/image/<?= $news['image'] ?>" alt="<?= $news['title'] ?>" 
                                                 class="w-full h-32 md:h-40 object-cover rounded-lg">
                                            <h4 class="font-semibold mt-2"><?= $news['title'] ?></h4>
                                            <p class="text-sm text-gray-500 mt-1"><?= $news['source'] ?> • 02 Januari 2024</p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Right Section -->
                        <div class="space-y-4 mt-10 lg:mt-0 lg:bg-white lg:p-5 lg:rounded-lg order-2 lg:order-3">
                            <!-- Pickup Schedule -->
                            <div class="relative bg-white lg:bg-[#f5f6fb] rounded-lg shadow p-4">
                                <h4 class="font-bold text-lg mb-5">Jadwal Penjemputan</h4>
                                <?php if (count($jadwal_penjemputan) > 0): ?>
                                    <?php foreach ($jadwal_penjemputan as $jadwal): ?>
                                        <div class="cursor-pointer hover:bg-gray-50 transition-colors rounded-lg p-2" 
                                             onclick="openTrackingModal(<?php echo htmlspecialchars(json_encode($jadwal)); ?>)">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <img src="assets/icon/paket.png" alt="paketsampah" class="w-9">
                                                    <div>
                                                        <p class="text-sm"><?= number_format($jadwal['total_berat_sampah'], 2, ',', '.') ?> Kg</p>
                                                        <p class="text-sm"><?= number_format($jadwal['total_points'], 0, ',', '.') ?> poin</p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-sm"><?= $jadwal['pickup_time'] ?></p>
                                                    <p class="text-sm"><?= date('d M Y', strtotime($jadwal['pickup_date'])) ?></p>
                                                </div>
                                            </div>
                                            <div class="mx-auto mt-2 mb-2 border-t-2 border-black-400"></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="flex flex-col items-center">
                                        <img src="assets/image/Schedule.png" alt="schedule" class="w-20">
                                        <p class="text-sm pt-2 text-gray-500 text-center">Jadwal Kosong</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Charts -->
                            <div class="bg-white lg:bg-[#f5f6fb] rounded-lg shadow p-4 h-[220px]">
                                <h4 class="font-bold text-lg mb-3">Sampah Terkumpul</h4>
                                <div class="h-[150px]">
                                    <canvas id="garbageChart"></canvas>
                                </div>
                            </div>

                            <div class="bg-white lg:bg-[#f5f6fb] rounded-lg shadow p-4 h-[220px]">
                                <h4 class="font-bold text-lg mb-3">Jejak Karbon Terkurangi</h4>
                                <div class="h-[150px]">
                                    <canvas id="carbonChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile News -->
                        <div class="lg:hidden mt-10 lg:mt-0 order-3">
                            <h3 class="text-xl font-bold mb-4">Terbaru</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <?php foreach ($news_items as $news): ?>
                                    <div class="bg-white rounded-lg shadow-md p-3 news-card">
                                        <img src="assets/image/<?= $news['image'] ?>" alt="<?= $news['title'] ?>" 
                                             class="w-full h-32 object-cover rounded-lg">
                                        <h4 class="font-semibold mt-2"><?= $news['title'] ?></h4>
                                        <p class="text-sm text-gray-500 mt-1"><?= $news['source'] ?> • 02 Januari 2024</p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Tracking Modal -->
    <div id="trackingModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden z-50">
        <div class="fixed inset-0 flex items-center justify-center">
            <div class="bg-white w-[500px] p-6 rounded-lg shadow-lg relative">
                <!-- Header -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold">Jemput Sampah</h2>
                </div>

                <!-- Info Section -->
                <div class="grid grid-cols-3 gap-4 mb-8">
                    <!-- Waktu Section -->
                    <div class="col-span-2">
                        <div class="space-y-2">
                            <p class="text-gray-600">Waktu Penjemputan</p>
                            <div id="trackingDate" class="text-lg font-semibold"></div>
                            <div id="trackingTime" class="text-lg"></div>
                        </div>
                    </div>
                    
                    <!-- Estimasi Section -->
                    <div class="col-span-1 space-y-4">
                        <div>
                            <p class="text-gray-600">Estimasi Berat</p>
                            <p id="trackingWeight" class="text-lg font-semibold"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Estimasi Poin</p>
                            <p id="trackingPoints" class="text-lg font-semibold"></p>
                        </div>
                    </div>
                </div>

                <!-- Status Tracking -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-6">Lacak Status Pesanan</h3>
                    <div class="relative">
                        <!-- Progress Line -->
                        <div class="absolute left-5 top-0 h-full w-0.5 bg-gray-300"></div>
                        
                        <!-- Status Steps -->
                        <div class="space-y-8 relative">
                            <!-- Pesanan Diterima -->
                            <div class="flex items-center" id="status-received">
                                <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center bg-white relative z-10">
                                    <div class="w-6 h-6 rounded-full transition-colors duration-200"></div>
                                </div>
                                <div class="ml-4">Pesanan Diterima</div>
                            </div>

                            <!-- Mencari Driver -->
                            <div class="flex items-center" id="status-finding-driver">
                                <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center bg-white relative z-10">
                                    <div class="w-6 h-6 rounded-full transition-colors duration-200"></div>
                                </div>
                                <div class="ml-4">Mencari Driver</div>
                            </div>

                            <!-- Driver Menuju Lokasi -->
                            <div class="flex items-center" id="status-pickup">
                                <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center bg-white relative z-10">
                                    <div class="w-6 h-6 rounded-full transition-colors duration-200"></div>
                                </div>
                                <div class="ml-4">Driver Menuju Lokasi</div>
                            </div>

                            <!-- Pesanan Selesai -->
                            <div class="flex items-center" id="status-done">
                                <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center bg-white relative z-10">
                                    <div class="w-6 h-6 rounded-full transition-colors duration-200"></div>
                                </div>
                                <div class="ml-4">Pesanan Selesai</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Close Button -->
                <div class="text-center">
                    <button onclick="closeTrackingModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeMenu();
            initializeCharts();
            initializeBannerSlider();
        });

        function initializeMenu() {
            const menuToggle = document.getElementById('menuToggle');
            const menuClose = document.getElementById('menuClose');
            const menu = document.getElementById('menu');
            const navLinks = document.querySelectorAll('.nav-link');
            const pageTitle = document.getElementById('pageTitle');
            
            const currentPage = window.location.pathname.split('/').pop().replace('.php', '');

            if (menuToggle && menuClose && menu) {
                menuToggle.addEventListener('click', () => menu.classList.remove('-translate-x-full'));
                menuClose.addEventListener('click', () => menu.classList.add('-translate-x-full'));
            }

            navLinks.forEach(link => {
                const href = link.getAttribute('href').replace('.php', '');
                const menuText = link.querySelector('span').textContent;
                
                if (href.includes(currentPage)) {
                    link.classList.add('active');
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

        function initializeBannerSlider() {
            let currentIndex = 0;
            const slider = document.getElementById('slider');
            if (!slider) return;
            
            const bannerImages = slider.children;

            function slideBanner() {
                currentIndex = (currentIndex + 1) % bannerImages.length;
                slider.style.transform = `translateX(-${currentIndex * 100}%)`;
            }
            setInterval(slideBanner, 5000);
        }

        function initializeCharts() {
            const chartConfig = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            };

            // Data dari PHP
            const garbageData = <?php echo json_encode($garbage_data); ?>;
            const carbonData = <?php echo json_encode($carbon_data); ?>;

            // Garbage Chart
            const garbageChart = new Chart(document.getElementById('garbageChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: garbageData.map(item => item.month),
                    datasets: [{
                        label: 'Sampah Terkumpul (kg)',
                        data: garbageData.map(item => item.total_weight),
                        backgroundColor: '#6C63FF',
                        borderRadius: 5,
                        barThickness: 20,
                    }]
                },
                options: chartConfig
            });

            // Carbon Chart
            const carbonCtx = document.getElementById('carbonChart').getContext('2d');
            const gradient = carbonCtx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(108, 99, 255, 0.5)');
            gradient.addColorStop(1, 'rgba(108, 99, 255, 0)');

            const carbonChart = new Chart(carbonCtx, {
                type: 'line',
                data: {
                    labels: carbonData.map(item => item.month),
                    datasets: [{
                        label: 'Jejak Karbon (kg CO₂)',
                        data: carbonData.map(item => item.carbon),
                        borderColor: '#6C63FF',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: chartConfig
            });
        }

        function openTrackingModal(orderData) {
            // Set order details
            document.getElementById('trackingDate').textContent = new Date(orderData.pickup_date).toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            document.getElementById('trackingTime').textContent = orderData.pickup_time + " WIB";
            document.getElementById('trackingWeight').textContent = orderData.total_berat_sampah + ' Kg';
            document.getElementById('trackingPoints').textContent = new Intl.NumberFormat('id-ID').format(orderData.total_points);

            // Reset all status styles
            const statuses = ['received', 'finding-driver', 'pickup', 'done'];
            statuses.forEach(status => {
                const element = document.getElementById(`status-${status}`);
                const dot = element.querySelector('.w-6');
                const border = element.querySelector('.w-10');
                dot.classList.remove('bg-green-500');
                border.classList.remove('border-green-500');
            });

            // Update status based on order status
            switch(orderData.status) {
                case 'pending':
                    updateStatus(['received', 'finding-driver']);
                    break;
                case 'pickup':
                    updateStatus(['received', 'finding-driver', 'pickup']);
                    break;
                case 'done':
                    updateStatus(['received', 'finding-driver', 'pickup', 'done']);
                    break;
            }

            document.getElementById('trackingModal').classList.remove('hidden');
        }

        function updateStatus(activeStatuses) {
            activeStatuses.forEach(status => {
                const element = document.getElementById(`status-${status}`);
                const dot = element.querySelector('.w-6');
                const border = element.querySelector('.w-10');
                dot.classList.add('bg-green-500');
                border.classList.add('border-green-500');
            });
        }

        function closeTrackingModal() {
            document.getElementById('trackingModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('trackingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTrackingModal();
            }
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>