<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'config.php';

// Ambil ID user yang sedang login
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT 
        pickup_date,
        SUM(total_berat_sampah) as total_weight
    FROM orders
    WHERE user_id = :user_id 
    AND status = 'done'
    GROUP BY DATE_FORMAT(pickup_date, '%Y-%m')
    ORDER BY pickup_date ASC
");
$stmt->execute(['user_id' => $user_id]);
$garbage_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$garbage_data = array_map(function($item) {
    // Ambil tahun sekarang
    $currentYear = date('Y');
    // Ambil tahun dari pickup_date
    $year = date('Y', strtotime($item['pickup_date']));
    // Ambil bulan singkat
    $month = date('M', strtotime($item['pickup_date']));

    // Jika tahun berbeda, tambahkan tahun ke bulan
    $monthDisplay = ($year == $currentYear) ? $month : "$month $year";

    return [
        'month' => $monthDisplay,
        'total_weight' => $item['total_weight']
    ];
}, $garbage_data);

// Hitung jejak karbon (asumsi: 1 kg sampah = 2.5 kg CO2)
$carbon_data = array_map(function($item) {
    return [
        'month' => $item['month'],
        'carbon' => $item['total_weight'] * 2.5
    ];
}, $garbage_data);
$total_weight = array_sum(array_column($garbage_data, 'total_weight'));

require('config.php');

// Query untuk mendapatkan data leaderboard
$query = "
    SELECT 
        u.id, 
        u.username, 
        u.profile_picture, 
        COALESCE(SUM(o.total_berat_sampah), 0) AS total_weight
    FROM 
        users u
    LEFT JOIN 
        orders o ON u.id = o.user_id
    GROUP BY 
        u.id
    ORDER BY 
        total_weight DESC
    LIMIT 10
";

// Eksekusi query dengan error handling
try {
    $stmt = $pdo->query($query);
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $leaderboard = []; // Pastikan $leaderboard tetap didefinisikan
}
// Ambil ID pengguna dari session
$user_id = $_SESSION['user_id'];

try {
    // Query untuk mengambil data sampah berdasarkan user_id
    $stmt = $pdo->prepare("
        SELECT 
            j.name AS waste_type,
            j.image,
            SUM(oi.quantity) AS total_quantity
        FROM 
            order_items oi
        JOIN 
            `orders` o
        ON 
            oi.order_id = o.id
        JOIN 
            users u
        ON 
            o.user_id = u.id
        JOIN 
            jenis_sampah j
        ON 
            oi.waste_type = j.name
        WHERE 
            u.id = :user_id
        GROUP BY 
            j.name, j.image
        ORDER BY 
            total_quantity DESC
        LIMIT 3
    ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Ambil hasil query
    $waste_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy Pencapaian</title>
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
    <div class="flex h-screen overflow-hidden">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
    <?php include 'header.php'; ?>
   

    <div class="min-h-screen bg-gray-50 overflow-y-auto">
        

        <div class="container mx-auto p-6 space-y-8">
            <!-- Header -->
            <h2 class="text-2xl font-bold text-gray-800 text-center sm:text-left">
                Yuk, Lihat Statistik Pencapaianmu!
            </h2>
    
            <!-- Main Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Statistik Section -->
                <div class="col-span-1 lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sampah Terkumpul -->
                <!-- Jejak Karbon -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800">Jejak Sampah Terkumpul</h3>
    <div class="h-[300px]">
    <canvas id="garbageChart" class="my-4"></canvas> <!-- Grafik Sampah Terkumpul -->
    </div>
    <p class="text-2xl font-bold text-gray-800 total-weight"></p>
    <p class="text-gray-500 text-sm">
        Ayo setorkan sampahmu!
    </p>
</div>

<!-- Grafik Jejak Karbon -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800">Jejak Karbon</h3>
    <div class="h-[300px]">
    <canvas id="carbonChart" class="my-4"></canvas> <!-- Grafik Jejak Karbon -->
    </div>
    <p class="text-2xl font-bold text-gray-800 total-carbon"></p>
    <p class="text-gray-500 text-sm">
        Ayo olah sampahmu untuk kurangi jejak karbonmu!
    </p>
</div>
</div>
<div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
    <h3 class="text-3xl font-bold  text-center border-b border-purple-600 pb-4">Leaderboard</h3>
    
    <!-- Scrollable Area -->
    <div class="mt-4 space-y-4 max-h-[400px] overflow-y-auto scrollbar-thin scrollbar-thumb-purple-500 scrollbar-track-purple-300">
        <?php if (!empty($leaderboard)): ?>
            <?php foreach ($leaderboard as $index => $user): ?>
                <div class="flex items-center bg-gray-200 p-4 rounded-lg shadow-sm hover:bg-purple-600 transition">
                    <!-- Rank -->
                    <div class="flex items-center justify-center bg-white font-bold text-xl w-12 h-12 rounded-full">
                        <?= $index + 1; ?>
                    </div>
                    <!-- Profile -->
                    <div class="ml-4 flex-shrink-0">
                        <img src="<?= !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'assets/icon/user.png'; ?>" alt="Avatar" class="w-14 h-14 rounded-full ">
                    </div>
                    <!-- User Info -->
                    <div class="ml-4">
                        <h3 class="text-lg font-medium "><?= htmlspecialchars($user['username']); ?></h3>
                        <p class="text-sm ">Total Berat: <strong><?= htmlspecialchars($user['total_weight']); ?> Kg</strong></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-purple-200 text-center">Belum ada data untuk ditampilkan.</p>
        <?php endif; ?>
    </div>
</div>

</div>

<div class=" overflow-y-auto">
        <div class="container mx-auto p-6 space-y-8 pb-20">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Paling Sering Dikumpulkan</h3>
                <div id="waste-list" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- PHP Loop untuk render data -->
                    <?php if (count($waste_data) > 0): ?>
                        <?php foreach ($waste_data as $waste): ?>
                            <div class="flex items-center space-x-4 bg-white p-4 rounded-lg shadow-md">
                                <img src="assets/image/<?php echo htmlspecialchars($waste['image']); ?>" alt="<?php echo htmlspecialchars($waste['waste_type']); ?>" class="w-12 h-12">
                                <div>
                                    <p class="font-bold text-gray-800"><?php echo htmlspecialchars($waste['waste_type']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($waste['total_quantity']); ?> Kg</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500">Tidak ada data sampah yang ditemukan untuk pengguna ini.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto">
            </div>
            <?php include 'footer.php'; ?>

        </div>
        
    </div>
    
</div>

    </div>
    




   

    <script>
        
        fetch('get_leaderboard.php')
    .then(response => response.text())
    .then(data => {
        // Memasukkan data yang diterima ke dalam elemen dengan id 'leaderboard-container'
        document.getElementById('leaderboard-container').innerHTML = data;
    })
    .catch(error => console.error('Error loading leaderboard:', error));
         // Data dari PHP
    const totalWeight = <?php echo $total_weight; ?>;
    const totalCarbon = <?php echo $total_weight * 2.5; ?>; // Menggunakan total_weight dari PHP untuk hitung CO2

    document.addEventListener('DOMContentLoaded', function() {
        // Update elemen HTML dengan data total berat
        const totalWeightElement = document.querySelector('.total-weight');
        totalWeightElement.textContent = `${totalWeight} Kg`;

        // Update elemen HTML dengan data total jejak karbon
        const totalCarbonElement = document.querySelector('.total-carbon');
        totalCarbonElement.textContent = `${totalCarbon.toFixed(2)} Kg CO₂`;
    });
        document.addEventListener('DOMContentLoaded', function() {
        initializeMenu();
        initializeCharts();
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
    </script>
</body>
</html>
