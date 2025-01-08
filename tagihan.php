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

// Function to generate transaction ID
function generateTransactionId($prefix) {
    return $prefix . date('YmdHis') . rand(1000, 9999);
}

// Function to generate PLN token with 20 digits and hyphens
function generatePLNToken() {
    $token = strtoupper(substr(str_shuffle(str_repeat('0123456789', 2)), 0, 20)); // 20 random digits
    return implode('-', str_split($token, 4)); // Format as 4 digits separated by hyphens
}

$success_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $transaction_type = $_POST['type'];
    $amount = (float)$_POST['amount'];
    $points_required = (float)$_POST['points'];
    
    // Get current point balance
    $current_points = getUserPoints($pdo, $user_id);

    // Check if user has enough points
    if ($current_points < $points_required) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Poin tidak mencukupi'
        ]);
        exit();
    }

    try {
        $pdo->beginTransaction();

        if ($transaction_type === 'pulsa') {
            $transaction_id = generateTransactionId('PLS');
            $phone_number = $_POST['phone_number'];
            $operator = $_POST['operator'];

            $query = "INSERT INTO pulsa (id, user_id, phone_number, operator, amount, points_used, status) 
                      VALUES (:id, :user_id, :phone_number, :operator, :amount, :points_used, 'success')";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'id' => $transaction_id,
                'user_id' => $user_id,
                'phone_number' => $phone_number,
                'operator' => $operator,
                'amount' => $amount,
                'points_used' => $points_required
            ]);

            $success_data = [
                'type' => 'pulsa',
                'transaction_id' => $transaction_id,
                'operator' => $operator,
                'phone_number' => $phone_number,
                'amount' => $amount,
                'points_used' => $points_required,
                'date' => date('Y-m-d H:i:s')
            ];
        } else {
            $transaction_id = generateTransactionId('TKN');
            $customer_id = $_POST['customer_id'];
            $token_number = generatePLNToken();

            $query = "INSERT INTO token_listrik (id, user_id, customer_id, amount, points_used, token_number, status) 
                      VALUES (:id, :user_id, :customer_id, :amount, :points_used, :token_number, 'success')";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'id' => $transaction_id,
                'user_id' => $user_id,
                'customer_id' => $customer_id,
                'amount' => $amount,
                'points_used' => $points_required,
                'token_number' => $token_number
            ]);

            $success_data = [
                'type' => 'token',
                'transaction_id' => $transaction_id,
                'customer_id' => $customer_id,
                'token_number' => $token_number,
                'amount' => $amount,
                'points_used' => $points_required,
                'date' => date('Y-m-d H:i:s')
            ];
        }

        // Update user points
        $new_points = $current_points - $points_required;
        updateUserPoints($pdo, $user_id, $new_points);

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'data' => $success_data
        ]);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat memproses transaksi'
        ]);
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
    <title>Tagihan</title>
    <meta name="page-title" content="Pembelian">
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
                
                    <div class="flex justify-center">
                        <!-- Pilihan Kategori -->
                        <div class="flex mb-10 bg-gray-200 rounded-full w-1/3">
                            <button id="pulsa-btn" class="flex-1 py-2 rounded-full bg-blue-500 text-white focus:outline-none text-center">
                                Pulsa
                            </button>
                            <button id="token-btn" class="flex-1 py-2 rounded-full bg-gray-200 text-gray-700 focus:outline-none text-center">
                                Token Listrik
                            </button>
                        </div>
                    </div>


                    <!-- Kontainer Formulir -->
                    <div id="pulsa-form" class="block">
                        <div class="bg-white rounded-lg p-5">
                            <h3 class="text-lg font-semibold mb-4">Pulsa</h3>
                            <!-- Point Balance Display -->
                            <div class="mb-8 p-4 bg-blue-50 rounded-xl">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-semibold">Poin Tersedia</span>
                                    <span class="text-blue-600 font-bold text-xl">
                                        <?php echo number_format($current_points, 0, ',', '.'); ?> Poin
                                    </span>
                                </div>
                            </div>
                            
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
                            <!-- Point Balance Display -->
                            <div class="mb-8 p-4 bg-blue-50 rounded-xl">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-semibold">Poin Tersedia</span>
                                    <span class="text-blue-600 font-bold text-xl">
                                        <?php echo number_format($current_points, 0, ',', '.'); ?> Poin
                                    </span>
                                </div>
                            </div>
                            
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
                    <div class="flex mt-6 justify-center">
                        <button id="submit-btn" class="px-24 py-2 rounded-full bg-blue-500 text-white hover:bg-blue-600 focus:outline-none">
                            Kirim
                        </button>
                    </div>
                </div>
                <?php include 'footer.php'; ?>
            </div>
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
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Transaksi Berhasil!</h2>
                
                <div id="successContent" class="bg-gray-50 rounded-xl p-6 mb-6 text-left">
                    <!-- Content will be populated by JavaScript -->
                </div>

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

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4">
            <h2 class="text-2xl font-bold mb-6">Konfirmasi Transaksi</h2>
            <div id="confirmContent" class="bg-gray-50 rounded-xl p-6 mb-6">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="flex justify-center space-x-4">
                <button onclick="hideConfirmModal()" class="px-6 py-2 bg-gray-500 text-white rounded-xl">
                    Batal
                </button>
                <button onclick="processTransaction()" class="px-6 py-2 bg-blue-500 text-white rounded-xl">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>

    <script>
        // [Previous JavaScript for menu initialization remains the same]

        let selectedOperator = '';
        let selectedAmount = 0;
        let selectedPoints = 0;
        let transactionType = 'pulsa';
        let transactionData = {};

        // Operator selection
        document.querySelectorAll('.operator-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all operators
                document.querySelectorAll('.operator-item').forEach(op => {
                    op.classList.remove('bg-blue-100');
                });
                // Add active class to selected operator
                this.classList.add('bg-blue-100');
                selectedOperator = this.querySelector('p').textContent;
            });
        });

        // Nominal selection for both pulsa and token
        document.querySelectorAll('.nominal-item, .token-nominal-item').forEach(item => {
            item.addEventListener('click', function() {
                const parent = this.closest('div[id$="-form"]');
                parent.querySelectorAll('.nominal-item, .token-nominal-item').forEach(nom => {
                    nom.classList.remove('ring-2', 'ring-blue-500');
                });
                this.classList.add('ring-2', 'ring-blue-500');
                
                selectedAmount = parseInt(this.querySelector('p:first-child').textContent.replace(/[^\d]/g, ''));
                selectedPoints = parseInt(this.querySelector('p:last-child').textContent.replace(/[^\d]/g, ''));
            });
        });

        // Submit button click handler
        document.getElementById('submit-btn').addEventListener('click', function() {
            if (transactionType === 'pulsa') {
                const phoneNumber = document.getElementById('phone-number').value;
                if (!phoneNumber || !selectedOperator || !selectedAmount) {
                    alert('Mohon lengkapi semua data');
                    return;
                }
                showConfirmModal('pulsa', {
                    phone_number: phoneNumber,
                    operator: selectedOperator,
                    amount: selectedAmount,
                    points: selectedPoints
                });
            } else {
                const customerId = document.getElementById('customer-id').value;
                if (!customerId || !selectedAmount) {
                    alert('Mohon lengkapi semua data');
                    return;
                }
                showConfirmModal('token', {
                    customer_id: customerId,
                    amount: selectedAmount,
                    points: selectedPoints
                });
            }
        });

        function showConfirmModal(type, data) {
            transactionType = type;
            transactionData = data;
            
            let content = '';
            if (type === 'pulsa') {
                content = `
                    <div class="space-y-3">
                        <div class="flex justify-between gap-16">
                            <span class="text-gray-600">Operator</span>
                            <span class="font-semibold">${data.operator}</span>
                        </div>
                        <div class="flex justify-between gap-16">
                            <span class="text-gray-600">Nomor HP</span>
                            <span class="font-semibold">${data.phone_number}</span>
                        </div>
                        <div class="flex justify-between gap-16">
                            <span class="text-gray-600">Nominal</span>
                            <span class="font-semibold">Rp ${data.amount.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t gap-16">
                            <span class="text-gray-600">Poin Digunakan</span>
                            <span class="font-semibold">${data.points.toLocaleString()} Poin</span>
                        </div>
                    </div>
                `;
            } else {
                content = `
                    <div class="space-y-3">
                        <div class="flex justify-between gap-16">
                            <span class="text-gray-600">ID Pelanggan</span>
                            <span class="font-semibold">${data.customer_id}</span>
                        </div>
                        <div class="flex justify-between gap-16">
                            <span class="text-gray-600">Nominal</span>
                            <span class="font-semibold">Rp ${data.amount.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t gap-16">
                            <span class="text-gray-600">Poin Digunakan</span>
                            <span class="font-semibold">${data.points.toLocaleString()} Poin</span>
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('confirmContent').innerHTML = content;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function hideConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

        function processTransaction() {
            const formData = new FormData();
            formData.append('type', transactionType);
            Object.keys(transactionData).forEach(key => {
                formData.append(key, transactionData[key]);
            });

            fetch('tagihan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    hideConfirmModal();
                    showSuccessModal(result.data);
                } else {
                    alert(result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses transaksi');
            });
        }

        function showSuccessModal(data) {
            let content = '';
            if (data.type === 'pulsa') {
                content = `
                    <div class="space-y-3">
                        <div class="flex justify-between items-center gap-16">
                            <span class="text-gray-600">ID Transaksi</span>
                            <span class="font-semibold">${data.transaction_id}</span>
                        </div>
                        <div class="flex justify-between items-center gap-16">
                            <span class="text-gray-600">Tanggal</span>
                            <span class="font-semibold">${new Date(data.date).toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex justify-between items-center gap-16">
                            <span class="text-gray-600">Operator</span>
                            <span class="font-semibold">${data.operator}</span>
                        </div>
                        <div class="flex justify-between items-center gap-16">
                            <span class="text-gray-600">Nomor HP</span>
                            <span class="font-semibold">${data.phone_number}</span>
                        </div>
                        <div class="flex justify-between items-center gap-16">
                            <span class="text-gray-600">Nominal</span>
                            <span class="font-semibold">Rp ${data.amount.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t gap-16">
                            <span class="text-gray-600">Poin Digunakan</span>
                            <span class="font-semibold text-blue-600">${data.points_used.toLocaleString()} Poin</span>
                        </div>
                    </div>
                `;
            } else {
                content = `
                    <div class="space-y-3">
                        <div class="flex justify-between items-center gap-16">
                            <span class="text-gray-600">ID Transaksi</span>
                            <span class="font-semibold">${data.transaction_id}</span>
                        </div>
                        <div class="flex justify-between items-center gap-16">
                            <span class="text-gray-600">Tanggal</span>
                            <span class="font-semibold">${new Date(data.date).toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex justify-between items-center gap-16">
                            <span class="text-gray-600">ID Pelanggan</span>
                            <span class="font-semibold">${data.customer_id}</span>
                        </div>
                        <div class="flex justify-between items-center gap-16">
                            <span class="text-gray-600">Nominal</span>
                            <span class="font-semibold">Rp ${data.amount.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t gap-16">
                            <span class="text-gray-600">Token PLN</span>
                            <div class="flex items-center">
                                <span class="font-mono font-bold text-lg mr-2">${data.token_number}</span>
                                <button onclick="copyToken('${data.token_number}')" class="text-blue-500 hover:text-blue-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t gap-16">
                            <span class="text-gray-600">Poin Digunakan</span>
                            <span class="font-semibold text-blue-600">${data.points_used.toLocaleString()} Poin</span>
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('successContent').innerHTML = content;
            document.getElementById('successModal').classList.remove('hidden');
        }

        function copyToken(token) {
            navigator.clipboard.writeText(token).then(() => {
                alert('Token berhasil disalin!');
            }).catch(err => {
                console.error('Failed to copy token:', err);
                alert('Gagal menyalin token');
            });
        }

        function downloadReceipt() {
            const receiptContent = document.getElementById('successContent').innerHTML;
            const formattedContent = `
                <html>
                    <head>
                        <title>Bukti Transaksi</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                padding: 20px;
                                max-width: 800px;
                                margin: 0 auto;
                            }
                            .header {
                                text-align: center;
                                margin-bottom: 30px;
                            }
                            .receipt {
                                border: 1px solid #ddd;
                                padding: 20px;
                                border-radius: 10px;
                            }
                            .flex {
                                display: flex;
                                justify-content: space-between;
                                margin: 10px 0;
                            }
                            .border-t {
                                border-top: 1px solid #ddd;
                                padding-top: 10px;
                                margin-top: 10px;
                            }
                            .font-semibold {
                                font-weight: 600;
                            }
                            .text-blue-600 {
                                color: #2563eb;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h1>Bukti Transaksi</h1>
                            <p>${new Date().toLocaleString('id-ID')}</p>
                        </div>
                        <div class="receipt">
                            ${receiptContent}
                        </div>
                    </body>
                </html>
            `;

            const blob = new Blob([formattedContent], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'bukti-transaksi.html';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }

        // Toggle between pulsa and token forms
        document.getElementById('pulsa-btn').addEventListener('click', function() {
            transactionType = 'pulsa';
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('bg-gray-200', 'text-gray-700');
            document.getElementById('token-btn').classList.add('bg-gray-200', 'text-gray-700');
            document.getElementById('token-btn').classList.remove('bg-blue-500', 'text-white');
            document.getElementById('pulsa-form').classList.remove('hidden');
            document.getElementById('token-form').classList.add('hidden');
        });

        document.getElementById('token-btn').addEventListener('click', function() {
            transactionType = 'token';
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('bg-gray-200', 'text-gray-700');
            document.getElementById('pulsa-btn').classList.add('bg-gray-200', 'text-gray-700');
            document.getElementById('pulsa-btn').classList.remove('bg-blue-500', 'text-white');
            document.getElementById('token-form').classList.remove('hidden');
            document.getElementById('pulsa-form').classList.add('hidden');
        });
    </script>
</body>
</html>