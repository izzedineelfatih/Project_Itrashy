<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

// Function to get user's point balance
function getUserPoints($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT poin_terkumpul FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchColumn();
}

// Function to update user's point balance
function updateUserPoints($pdo, $user_id, $points) {
    $stmt = $pdo->prepare("UPDATE users SET poin_terkumpul = :points WHERE id = :user_id");
    return $stmt->execute([
        'points' => $points,
        'user_id' => $user_id
    ]);
}

// Function to generate transfer ID
function generateTransferId() {
    return 'TRF' . date('YmdHis') . rand(1000, 9999);
}

$success_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transfer'])) {
    $user_id = $_SESSION['user_id'];
    $e_wallet = $_POST['e_wallet'];
    $phone_number = $_POST['phone_number'];
    $amount = (float)$_POST['amount'];
    $admin_fee = 2500;
    $total = $amount + $admin_fee;
    $transfer_id = generateTransferId();

    // Get current point balance
    $current_points = getUserPoints($pdo, $user_id);

    // Check if user has enough points
    if ($current_points < $amount) {
        echo "<script>
            alert('Poin tidak mencukupi untuk melakukan transfer ini. Poin Anda: " . number_format($current_points, 0, ',', '.') . "');
            window.history.back();
        </script>";
        exit();
    }

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Insert transfer record
        $query = "INSERT INTO transfer (id, user_id, e_wallet, phone_number, amount, admin_fee, total, transfer_date) 
                  VALUES (:transfer_id, :user_id, :e_wallet, :phone_number, :amount, :admin_fee, :total, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':transfer_id' => $transfer_id,
            ':user_id' => $user_id,
            ':e_wallet' => $e_wallet,
            ':phone_number' => $phone_number,
            ':amount' => $amount,
            ':admin_fee' => $admin_fee,
            ':total' => $total
        ]);

        // Update user points
        $new_points = $current_points - $amount;
        updateUserPoints($pdo, $user_id, $new_points);

        // Commit transaction
        $pdo->commit();

        // Set success data for modal
        $success_data = [
            'transfer_id' => $transfer_id,
            'e_wallet' => $e_wallet,
            'phone_number' => $phone_number,
            'amount' => $amount,
            'admin_fee' => $admin_fee,
            'total' => $total,
            'date' => date('Y-m-d H:i:s'),
            'remaining_points' => $new_points
        ];

        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('successModal').classList.remove('hidden');
            });
        </script>";
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo "<script>
            alert('Terjadi kesalahan saat memproses transfer. Silakan coba lagi.');
            window.history.back();
        </script>";
        exit();
    }
}

// Get current point balance for display
$current_points = getUserPoints($pdo, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer E-Wallet</title>
    <meta name="page-title" content="Transfer">
    <script src="https://cdn.tailwindcss.com"></script>
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

            <!-- Main Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <div class="bg-white rounded-2xl shadow-xl p-8 max-w-2xl mx-auto">
                    <!-- Point Balance Display -->
                    <div class="mb-8 p-4 bg-blue-50 rounded-xl">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-semibold">Poin Tersedia</span>
                            <span class="text-blue-600 font-bold text-xl">
                                <?php echo number_format($current_points, 0, ',', '.'); ?> Poin
                            </span>
                        </div>
                    </div>

                    <?php if (!isset($_POST['preview'])): ?>
                        <!-- Form Transfer -->
                        <form method="POST" class="space-y-6">
                            <div class="space-y-4">
                                <label class="block font-semibold text-gray-700 mb-4">Pilih E-Wallet</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <button type="button" onclick="selectWallet(this, 'Gopay')" class="wallet-btn group relative bg-white border-2 border-gray-200 rounded-xl p-4 transition-all duration-200 hover:border-blue-500 focus:outline-none">
                                        <input type="radio" name="e_wallet" value="Gopay" class="hidden" required>
                                        <img src="assets/image/gopay.png" alt="Gopay" class="w-full h-16 object-contain opacity-60 group-hover:opacity-100 transition-opacity duration-200">
                                        <p class="text-gray-600 pt-2">Gopay</p>
                                        <div class="absolute inset-0 bg-blue-500 bg-opacity-0 group-hover:bg-opacity-5 rounded-xl transition-all duration-200"></div>
                                    </button>
                                    <button type="button" onclick="selectWallet(this, 'ShopeePay')" class="wallet-btn group relative bg-white border-2 border-gray-200 rounded-xl p-4 transition-all duration-200 hover:border-blue-500 focus:outline-none">
                                        <input type="radio" name="e_wallet" value="ShopeePay" class="hidden" required>
                                        <img src="assets/image/shopee.png" alt="ShopeePay" class="w-full h-16 object-contain opacity-60 group-hover:opacity-100 transition-opacity duration-200">
                                        <p class="text-gray-600 pt-2">ShopeePay</p>
                                        <div class="absolute inset-0 bg-blue-500 bg-opacity-0 group-hover:bg-opacity-5 rounded-xl transition-all duration-200"></div>
                                    </button>
                                    <button type="button" onclick="selectWallet(this, 'Dana')" class="wallet-btn group relative bg-white border-2 border-gray-200 rounded-xl p-4 transition-all duration-200 hover:border-blue-500 focus:outline-none">
                                        <input type="radio" name="e_wallet" value="Dana" class="hidden" required>
                                        <img src="assets/image/dana.png" alt="Dana" class="w-full h-16 object-contain opacity-60 group-hover:opacity-100 transition-opacity duration-200">
                                        <p class="text-gray-600 pt-2">Dana</p>
                                        <div class="absolute inset-0 bg-blue-500 bg-opacity-0 group-hover:bg-opacity-5 rounded-xl transition-all duration-200"></div>
                                    </button>
                                    <button type="button" onclick="selectWallet(this, 'OVO')" class="wallet-btn group relative bg-white border-2 border-gray-200 rounded-xl p-4 transition-all duration-200 hover:border-blue-500 focus:outline-none">
                                        <input type="radio" name="e_wallet" value="OVO" class="hidden" required>
                                        <img src="assets/image/ovo.png" alt="OVO" class="w-full h-16 object-contain opacity-60 group-hover:opacity-100 transition-opacity duration-200">
                                        <p class="text-gray-600 pt-2">OVO</p>
                                        <div class="absolute inset-0 bg-blue-500 bg-opacity-0 group-hover:bg-opacity-5 rounded-xl transition-all duration-200"></div>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block font-semibold text-gray-700">Nomor Ponsel</label>
                                <input type="text" name="phone_number" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200" required>
                            </div>

                            <div class="space-y-2">
                                <label class="block font-semibold text-gray-700">Nominal</label>
                                <input type="number" name="amount" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200" required>
                                <p class="text-sm text-gray-500 mt-2">Biaya Admin Rp 2.500</p>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" name="preview" class="bg-blue-500 text-white px-8 py-3 rounded-xl hover:bg-blue-600 transform hover:-translate-y-1 transition-all duration-200">
                                    Lanjut
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <!-- Halaman Konfirmasi -->
                        <h1 class="text-2xl font-bold mb-8 text-gray-800">Detail Transaksi</h1>
                        <div class="bg-gray-50 rounded-xl p-6 mb-8 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">E-Wallet</span>
                                <span class="font-semibold"><?php echo htmlspecialchars($_POST['e_wallet']); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">No. Ponsel</span>
                                <span class="font-semibold"><?php echo htmlspecialchars($_POST['phone_number']); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Nominal</span>
                                <span class="font-semibold">Rp <?php echo number_format($_POST['amount'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Biaya Admin</span>
                                <span class="font-semibold">Rp 2.500</span>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t">
                                <span class="text-gray-800 font-semibold">Total</span>
                                <span class="text-blue-600 font-bold text-xl">Rp <?php echo number_format($_POST['amount'] + 2500, 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Sisa Poin</span>
                                <span class="font-semibold">
                                    <?php echo number_format($current_points - $_POST['amount'], 0, ',', '.'); ?> Poin
                                </span>
                            </div>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="e_wallet" value="<?php echo htmlspecialchars($_POST['e_wallet']); ?>">
                            <input type="hidden" name="phone_number" value="<?php echo htmlspecialchars($_POST['phone_number']); ?>">
                            <input type="hidden" name="amount" value="<?php echo htmlspecialchars($_POST['amount']); ?>">
                            <div class="flex justify-between">
                                <button type="button" onclick="window.history.back();" class="bg-gray-500 text-white px-6 py-3 rounded-xl hover:bg-gray-600 transform hover:-translate-y-1 transition-all duration-200">
                                    Kembali
                                </button>
                                <button type="submit" name="transfer" class="bg-blue-500 text-white px-8 py-3 rounded-xl hover:bg-blue-600 transform hover:-translate-y-1 transition-all duration-200">
                                    Konfirmasi Transfer
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div> 
            </div>
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 transform transition-all duration-300">
            <div class="text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full mx-auto flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Transfer Berhasil!</h2>
                
                <?php if ($success_data): ?>
                <div class="bg-gray-50 rounded-xl p-6 mb-6 text-left">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">ID Transaksi</span>
                            <span class="font-semibold"><?php echo $success_data['transfer_id']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Tanggal</span>
                            <span class="font-semibold"><?php echo date('d M Y H:i', strtotime($success_data['date'])); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">E-Wallet</span>
                            <span class="font-semibold"><?php echo $success_data['e_wallet']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">No. Ponsel</span>
                            <span class="font-semibold"><?php echo $success_data['phone_number']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Nominal</span>
                            <span class="font-semibold">Rp <?php echo number_format($success_data['amount'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Biaya Admin</span>
                            <span class="font-semibold">Rp <?php echo number_format($success_data['admin_fee'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t">
                            <span class="text-gray-800 font-semibold">Total</span>
                            <span class="text-blue-600 font-bold">Rp <?php echo number_format($success_data['total'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t">
                            <span class="text-gray-600">Sisa Poin</span>
                            <span class="font-semibold text-green-600"><?php echo number_format($success_data['remaining_points'], 0, ',', '.'); ?> Poin</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="flex justify-center space-x-4">
                    <button onclick="window.location.href='dashboard.php'" class="bg-gray-500 text-white px-6 py-3 rounded-xl hover:bg-gray-600 transform hover:-translate-y-1 transition-all duration-200">
                        Ke Beranda
                    </button>
                    <button onclick="downloadReceipt()" class="bg-blue-500 text-white px-6 py-3 rounded-xl hover:bg-blue-600 transform hover:-translate-y-1 transition-all duration-200">
                        Unduh Bukti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectWallet(button, wallet) {
            // Reset semua button
            document.querySelectorAll('.wallet-btn').forEach(btn => {
                btn.classList.remove('border-blue-500');
                btn.querySelector('img').classList.remove('opacity-100');
                btn.querySelector('img').classList.add('opacity-60');
                btn.querySelector('input').checked = false;
            });

            // Set button yang dipilih
            button.classList.add('border-blue-500');
            button.querySelector('img').classList.remove('opacity-60');
            button.querySelector('img').classList.add('opacity-100');
            button.querySelector('input').checked = true;
        }

        function downloadReceipt() {
            // Create a blob URL for the receipt
            const receiptContent = document.querySelector('#successModal .bg-gray-50').innerHTML;
            const formattedContent = `
                <html>
                    <head>
                        <title>Bukti Transfer</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            .receipt { max-width: 500px; margin: 0 auto; }
                            .header { text-align: center; margin-bottom: 20px; }
                            .detail-row { display: flex; justify-content: space-between; margin: 10px 0; }
                            .divider { border-top: 1px solid #eee; margin: 10px 0; }
                        </style>
                    </head>
                    <body>
                        <div class="receipt">
                            <div class="header">
                                <h1>Bukti Transfer</h1>
                                <p>Transfer E-Wallet Berhasil</p>
                            </div>
                            ${receiptContent}
                        </div>
                    </body>
                </html>
            `;

            const blob = new Blob([formattedContent], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'bukti-transfer.html';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
    </script>
</body>
</html>