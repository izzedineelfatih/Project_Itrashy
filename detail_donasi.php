<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's points
$stmt = $pdo->prepare("SELECT poin_terkumpul FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_points = $stmt->fetchColumn();

// Get donasi ID from URL
$donasi_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$donasi_id) {
    header('Location: donasi.php');
    exit();
}

// Fetch donasi details
$query = "SELECT * FROM katalog_donasi WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$donasi_id]);
$donasi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$donasi) {
    header('Location: donasi.php');
    exit();
}

// Process donation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donate'])) {
    $poin_amount = (int)$_POST['poin_amount'];
    
    if ($poin_amount <= 0) {
        $error = "Jumlah poin harus lebih dari 0";
    } elseif ($poin_amount > $user_points) {
        $error = "Poin Anda tidak mencukupi";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Insert donation transaction
            $stmt = $pdo->prepare("INSERT INTO transaksi_donasi (user_id, katalog_donasi_id, poin_amount) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $donasi_id, $poin_amount]);
            
            // Update user's points
            $stmt = $pdo->prepare("UPDATE users SET poin_terkumpul = poin_terkumpul - ? WHERE id = ?");
            $stmt->execute([$poin_amount, $user_id]);
            
            // Update collected amount in katalog_donasi
            $stmt = $pdo->prepare("UPDATE katalog_donasi SET collected = collected + ? WHERE id = ?");
            $stmt->execute([$poin_amount, $donasi_id]);
            
            $pdo->commit();
            
            $_SESSION['success_message'] = "Donasi berhasil! Terima kasih atas kontribusi Anda.";
            header("Location: donasi.php");
            exit();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Terjadi kesalahan dalam proses donasi";
        }
    }
}

$progress = ($donasi['collected'] / $donasi['goal']) * 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($donasi['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-[#f5f6fb] font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <?php include 'header.php'; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="p-4 mb-4 mx-5 mt-5 bg-green-100 border-l-4 border-green-500 text-green-700">
                    <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="p-4 mb-4 mx-5 mt-5 bg-red-100 border-l-4 border-red-500 text-red-700">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="flex-1 overflow-y-auto p-5">
                <div class="max-w-5xl mx-auto">
                    <!-- Back Button -->
                    <a href="donasi.php" class="inline-flex items-center mb-6 text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar Donasi
                    </a>

                    <!-- Main Content -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <!-- Hero Image -->
                        <div class="relative h-80">
                            <img src="assets/image/<?php echo htmlspecialchars($donasi['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($donasi['title']); ?>" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                        </div>

                        <!-- Content -->
                        <div class="p-8">
                            <!-- Title and Progress Section -->
                            <div class="mb-6">
                                <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($donasi['title']); ?></h1>
                                <div class="relative bg-gray-200 rounded-full h-4 mb-2">
                                    <div class="absolute left-0 top-0 h-full bg-green-600 rounded-full" 
                                         style="width: <?php echo min($progress, 100); ?>%"></div>
                                </div>
                           
                                <div class="flex justify-between text-sm">
                                    <span class="font-semibold">Rp <?php echo number_format($donasi['collected']); ?> terkumpul</span>
                                    <span>dari Rp <?php echo number_format($donasi['goal']); ?></span>
                                </div>
                            </div>

                            <!-- Donation Form -->
                            <div class="bg-gray-100 rounded-xl p-6 mb-8">
                                <!-- Point Balance Display -->
                                <div class="mb-8 p-4 bg-blue-50 shadow-lg rounded-xl">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700 font-semibold">Poin Tersedia</span>
                                        <span class="text-blue-600 font-bold text-xl">
                                        <?php echo number_format($user_points); ?> Poin
                                        </span>
                                    </div>
                                </div>

                                <form method="POST" class="space-y-4" id="donationForm">
                                    <input type="hidden" name="donate" value="1">
                                    <div>
                                        <label for="poin_amount" class="block text-sm font-medium text-gray-700 mb-1">
                                            Jumlah Poin yang Akan Didonasikan
                                        </label>
                                        <input type="number" 
                                               id="poin_amount" 
                                               name="poin_amount" 
                                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"
                                               required
                                               min="1"
                                               max="<?php echo $user_points; ?>"
                                               placeholder="Masukkan jumlah poin">
                                    </div>
                                    <div class="flex justify-center">
                                        <button type="button" 
                                                onclick="openConfirmModal()"
                                                class="w-full lg:w-1/3 bg-blue-600 text-white py-3 rounded-3xl font-semibold hover:bg-blue-700 transition">
                                            Donasi Sekarang
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Description -->
                            <div class="mt-8 prose max-w-none">
                                <h2 class="text-xl font-semibold mb-4">Tentang Program Ini</h2>
                                <?php echo $donasi['deskripsi']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'footer.php'; ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-bold mb-4">Konfirmasi Donasi</h2>
            <p>Anda akan mendonasikan <span id="confirmAmount" class="font-bold"></span> poin untuk program ini.</p>
            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeConfirmModal()" 
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                    Batal
                </button>
                <button onclick="submitDonation()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <script>
    function openConfirmModal() {
        const pointsInput = document.getElementById('poin_amount');
        const points = pointsInput.value;
        
        if (!points || points <= 0) {
            alert('Silakan masukkan jumlah poin yang valid');
            return;
        }
        
        document.getElementById('confirmAmount').textContent = points;
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    function submitDonation() {
        document.getElementById('donationForm').submit();
    }
    </script>
</body>
</html>
