<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'config.php';
$user_id = $_SESSION['user_id'];

// Query untuk mengambil data dari tabel orders berdasarkan user_id
$ordersQuery = "SELECT id, user_id, pickup_location, pickup_date, pickup_time, total_amount, status FROM orders WHERE user_id = :user_id";
$stmtOrders = $pdo->prepare($ordersQuery);
$stmtOrders->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtOrders->execute();
$ordersResult = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data dari tabel voucher_redeem dan voucher berdasarkan user_id
$voucherQuery = "
    SELECT vr.id, vr.user_id, vr.voucher_id, v.points, vr.created_at, v.title
    FROM voucher_redeem vr
    INNER JOIN voucher v ON vr.voucher_id = v.id
    WHERE vr.user_id = :user_id
";
$stmtVoucher = $pdo->prepare($voucherQuery);
$stmtVoucher->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Menggunakan user_id dari sesi login
$stmtVoucher->execute();
$voucherResult = $stmtVoucher->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data dari tabel order_sembako dan sembako
$sembakoQuery = "
    SELECT os.id, os.user_id, os.sembako, os.jumlah_poin, os.pickup_date, os.pickup_time, os.pickup_location, os.status, os.created_at, os.pickup_order_id, s.title
    FROM order_sembako os
    INNER JOIN sembako s ON os.sembako = s.id
    WHERE os.user_id = :user_id
";
$stmtSembako = $pdo->prepare($sembakoQuery);
$stmtSembako->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Menggunakan user_id dari sesi login
$stmtSembako->execute();
$sembakoResult = $stmtSembako->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data dari tabel transfer berdasarkan user_id
$transferQuery = "
    SELECT id, user_id, e_wallet, phone_number, amount, admin_fee, total, transfer_date
    FROM transfer
    WHERE user_id = :user_id
";
$stmtTransfer = $pdo->prepare($transferQuery);
$stmtTransfer->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Menggunakan user_id dari sesi login
$stmtTransfer->execute();
$transferResult = $stmtTransfer->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data dari tabel pulsa berdasarkan user_id
$pulsaQuery = "SELECT id, user_id, phone_number, operator, amount, points_used, transaction_date, status FROM pulsa WHERE user_id = :user_id";
$stmtPulsa = $pdo->prepare($pulsaQuery);
$stmtPulsa->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtPulsa->execute();
$pulsaResult = $stmtPulsa->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data dari tabel token_listrik berdasarkan user_id
$tokenListrikQuery = "SELECT id, user_id, customer_id, amount, points_used, token_number, transaction_date, status FROM token_listrik WHERE user_id = :user_id";
$stmtTokenListrik = $pdo->prepare($tokenListrikQuery);
$stmtTokenListrik->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtTokenListrik->execute();
$tokenListrikResult = $stmtTokenListrik->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data dari tabel transaksi_donasi berdasarkan user_id
$donasiQuery = "SELECT id, user_id, katalog_donasi_id, poin_amount, created_at FROM transaksi_donasi WHERE user_id = :user_id";
$stmtDonasi = $pdo->prepare($donasiQuery);
$stmtDonasi->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtDonasi->execute();
$donasiResult = $stmtDonasi->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="page-title" content="Riwayat">
    <title>I-Trashy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
            
            <!-- Tabs -->
            <div class="m-8 border-b border-black">
                <button id="semua-btn" class="px-4 py-2 text-gray-600 hover:text-blue-600">Semua</button>
                <button id="jemput-btn" class="px-4 py-2 text-gray-600 hover:text-blue-600">Jemput Sampah</button>
                <button id="tukar-btn" class="px-4 py-2 text-gray-600 hover:text-blue-600">Tukar Poin</button>
            </div>
            
            <!-- Data List -->
            <div class="ml-4 p-4 space-y-4 max-h-96 overflow-y-auto" id="data-list">
                <?php
                if (!empty($ordersResult)) {
                    foreach ($ordersResult as $order) {
                        echo '<div class="data-item jemput-sampah">';
                        echo '<div class="flex justify-between bg-white p-4 rounded-md shadow-sm">';
                        echo '<img src="assets/icon/paket.png" alt="Icon" class="w-10 h-10 mr-4">';
                        echo '<div class="flex-1">';
                        echo '<h3 class="font-bold text-gray-700">Penjemputan Sampah</h3>';
                        echo '<p class="text-sm text-gray-500">Status: <span class="text-green-600">' . htmlspecialchars($order['status']) . '</span></p>';
                        echo '</div>';
                        echo '<div class="text-sm text-gray-600">';
                      
                        echo '</div>';
                        echo '<div class="ml-4">';
                        echo '<button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 detail-btn" data-details="' . htmlspecialchars(json_encode($order)) . '">Detail</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }

                if (!empty($voucherResult)) {
                    foreach ($voucherResult as $voucher) {
                        echo '<div class="data-item tukar-poin">';
                        echo '<div class="flex justify-between bg-white p-4 rounded-md shadow-sm">';
                        echo '<img src="assets/icon/poin logo.png" alt="Icon" class="w-10 h-10 mr-4">';
                        echo '<div class="flex-1">';
                        echo '<h3 class="font-bold text-gray-700"> ' . htmlspecialchars($voucher['title']) . '</h3>';
                        echo '<p class="text-sm text-gray-500">Status: <span class="text-green-600">Selesai</span></p>';
                        echo '</div>';
                        echo '<div class="text-sm text-gray-600">';
                        
                        echo '</div>';
                        echo '<div class="ml-4">';
                        echo '<button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 detail-btn-voucher" data-details="' . htmlspecialchars(json_encode($voucher)) . '">Detail</button>';

                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
               

if (!empty($sembakoResult)) {
    foreach ($sembakoResult as $item) {
        echo '<div class="data-item tukar-poin">';
        echo '<div class="flex justify-between bg-white p-4 rounded-md shadow-sm">';
        echo '<img src="assets/icon/poin logo.png" alt="Sembako Icon" class="w-10 h-10 mr-4">'; // Icon default untuk sembako
        echo '<div class="flex-1">';
        echo '<h3 class="font-bold text-gray-700">Sembako ' . htmlspecialchars($item['title']) . '</h3>';
        echo '<p class="text-sm text-gray-500">Status: <span class="text-green-600">' . htmlspecialchars($item['status']) . '</span></p>';
        echo '</div>';
        echo '<div class="text-sm text-gray-600">';
        echo '</div>';
        echo '<div class="ml-4">';
        echo '<button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 detail-btn-sembako" data-details="' . htmlspecialchars(json_encode($item)) . '">Detail</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

if (!empty($transferResult)) {
    foreach ($transferResult as $transfer) {
        echo '<div class="data-item tukar-poin">';
        echo '<div class="flex justify-between bg-white p-4 rounded-md shadow-sm">';
        echo '<img src="assets/icon/poin logo.png" alt="E-Wallet Icon" class="w-10 h-10 mr-4">';
        echo '<div class="flex-1">';
        echo '<h3 class="font-bold text-gray-700">Transfer E-Wallet</h3>';
        echo '<p class="text-sm text-gray-500">E-Wallet: <span class="text-blue-600">' . htmlspecialchars($transfer['e_wallet']) . '</span></p>';
        echo '<p class="text-sm text-gray-500">Phone Number: <span class="text-blue-600">' . htmlspecialchars($transfer['phone_number']) . '</span></p>';
        echo '</div>';
        echo '<div class="text-sm text-gray-600">';
        
        echo '</div>';
        echo '<div class="ml-4">';
        echo '<button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 detail-btn-transfer" data-details="' . htmlspecialchars(json_encode($transfer)) . '">Detail</button>';   
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} 

if (!empty($pulsaResult)) {
    foreach ($pulsaResult as $pulsa) {
        echo '<div class="data-item tukar-poin">';
        echo '<div class="flex justify-between bg-white p-4 rounded-md shadow-sm">';
        echo '<img src="assets/icon/poin logo.png" alt="Pulsa Icon" class="w-10 h-10 mr-4">';
        echo '<div class="flex-1">';
        echo '<h3 class="font-bold text-gray-700">Pembelian Pulsa</h3>';
        echo '<p class="text-sm text-gray-500">Status: <span class="text-green-600">' . htmlspecialchars($pulsa['status']) . '</span></p>';
        echo '<p class="text-sm text-gray-500">Operator: <span class="text-blue-600">' . htmlspecialchars($pulsa['operator']) . '</span></p>';
        echo '<p class="text-sm text-gray-500">Phone Number: <span class="text-blue-600">' . htmlspecialchars($pulsa['phone_number']) . '</span></p>';
        echo '</div>';
        echo '<div class="text-sm text-gray-600">';
      
        echo '</div>';
        echo '<div class="ml-4">';
        echo '<button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 detail-btn-pulsa" data-details="' . htmlspecialchars(json_encode($pulsa)) . '">Detail</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} 

if (!empty($tokenListrikResult)) {
    foreach ($tokenListrikResult as $token) {
        echo '<div class="data-item tukar-poin">';
        echo '<div class="flex justify-between bg-white p-4 rounded-md shadow-sm">';
        echo '<img src="assets/icon/poin logo.png" alt="Token Listrik Icon" class="w-10 h-10 mr-4">';
        echo '<div class="flex-1">';
        echo '<h3 class="font-bold text-gray-700">Pembelian Token Listrik</h3>';
        echo '<p class="text-sm text-gray-500">Status: <span class="text-green-600">' . htmlspecialchars($token['status']) . '</span></p>';
        echo '<p class="text-sm text-gray-500">Customer ID: <span class="text-blue-600">' . htmlspecialchars($token['customer_id']) . '</span></p>';
        echo '<p class="text-sm text-gray-500">Token Number: <span class="text-blue-600">' . htmlspecialchars($token['token_number']) . '</span></p>';
        echo '</div>';
        echo '<div class="text-sm text-gray-600">';
      
        echo '</div>';
        echo '<div class="ml-4">';
        echo '<button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 detail-btn-token" data-details="' . htmlspecialchars(json_encode($token)) . '">Detail</button>';

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} 

if (!empty($donasiResult)) {
    foreach ($donasiResult as $donasi) {
        echo '<div class="data-item tukar-poin">';
        echo '<div class="flex justify-between bg-white p-4 rounded-md shadow-sm">';
        echo '<img src="assets/icon/poin logo.png" alt="Donasi Icon" class="w-10 h-10 mr-4">';
        echo '<div class="flex-1">';
        echo '<h3 class="font-bold text-gray-700">Transaksi Donasi</h3>';
        echo '<p class="text-sm text-gray-500">Katalog Donasi ID: <span class="text-blue-600">' . htmlspecialchars($donasi['katalog_donasi_id']) . '</span></p>';
        echo '</div>';
        echo '<div class="text-sm text-gray-600">';
      
        echo '</div>';
        echo '<div class="ml-4">';
        echo '<button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 detail-btn-donasi" data-details="' . htmlspecialchars(json_encode($donasi)) . '">Detail</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} 

                ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 w-1/2">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold">Detail Informasi</h2>
                <button id="close-modal" class="text-gray-600 hover:text-gray-800">&times;</button>
            </div>
            <div id="modal-content" class="mt-4">
                <!-- Isi detail akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal');
    const modalContent = document.getElementById('modal-content');
    const closeModal = document.getElementById('close-modal');

    // Event listener for Transfer E-Wallet details
    document.querySelectorAll('.detail-btn-transfer').forEach(button => {
        button.addEventListener('click', function() {
            const details = JSON.parse(this.getAttribute('data-details'));
            const contentHTML = `
                <p><strong>E-Wallet:</strong> ${details.e_wallet}</p>
                <p><strong>Phone Number:</strong> ${details.phone_number}</p>
                <p><strong>Amount:</strong> + Rp ${details.amount}</p>
                <p><strong>Admin Fee:</strong> - Rp ${details.admin_fee}</p>
                <p><strong>Total:</strong> Rp ${details.total}</p>
                <p><strong>Transfer Date:</strong> ${details.transfer_date}</p>
            `;
            modalContent.innerHTML = contentHTML;
            modal.classList.remove('hidden');
        });
    });

    // Event listener for Pulsa details
    document.querySelectorAll('.detail-btn-pulsa').forEach(button => {
        button.addEventListener('click', function() {
            const details = JSON.parse(this.getAttribute('data-details'));
            const contentHTML = `
                <p><strong>Status:</strong> ${details.status}</p>
                <p><strong>Operator:</strong> ${details.operator}</p>
                <p><strong>Phone Number:</strong> ${details.phone_number}</p>
                <p><strong>Amount:</strong> Rp ${details.amount}</p>
                <p><strong>Points Used:</strong> ${details.points_used}</p>
                <p><strong>Transaction Date:</strong> ${details.transaction_date}</p>
            `;
            modalContent.innerHTML = contentHTML;
            modal.classList.remove('hidden');
        });
    });

    // Event listener for Token Listrik details
    document.querySelectorAll('.detail-btn-token').forEach(button => {
        button.addEventListener('click', function() {
            const details = JSON.parse(this.getAttribute('data-details'));
            const contentHTML = `
                <p><strong>Status:</strong> ${details.status}</p>
                <p><strong>Customer ID:</strong> ${details.customer_id}</p>
                <p><strong>Token Number:</strong> ${details.token_number}</p>
                <p><strong>Amount:</strong> Rp ${details.amount}</p>
                <p><strong>Points Used:</strong> ${details.points_used}</p>
                <p><strong>Transaction Date:</strong> ${details.transaction_date}</p>
            `;
            modalContent.innerHTML = contentHTML;
            modal.classList.remove('hidden');
        });
    });

    // Event listener for Donasi details
    document.querySelectorAll('.detail-btn-donasi').forEach(button => {
        button.addEventListener('click', function() {
            const details = JSON.parse(this.getAttribute('data-details'));
            const contentHTML = `
                <p><strong>Katalog Donasi ID:</strong> ${details.katalog_donasi_id}</p>
                <p><strong>Poin Amount:</strong> ${details.poin_amount}</p>
                <p><strong>Created At:</strong> ${details.created_at}</p>
            `;
            modalContent.innerHTML = contentHTML;
            modal.classList.remove('hidden');
        });
    });

    // Common Modal Close
    closeModal.addEventListener('click', function() {
        modal.classList.add('hidden');
    });
});

        document.addEventListener('DOMContentLoaded', function() {
    // Voucher Detail Handling
    const detailButtonsVoucher = document.querySelectorAll('.detail-btn-voucher');
    const modal = document.getElementById('modal');
    const modalContent = document.getElementById('modal-content');
    const closeModal = document.getElementById('close-modal');

    detailButtonsVoucher.forEach(button => {
        button.addEventListener('click', function() {
            const details = JSON.parse(this.getAttribute('data-details'));
            const contentHTML = `
                <p><strong>Title:</strong> ${details.title}</p>
                <p><strong>Points:</strong> ${details.points}</p>
                <p><strong>Created At:</strong> ${details.created_at}</p>
                <p><strong>Status:</strong> Selesai</p>
            `;
            modalContent.innerHTML = contentHTML;
            modal.classList.remove('hidden');
        });
    });

    // Sembako Detail Handling
    const detailButtonsSembako = document.querySelectorAll('.detail-btn-sembako');

    detailButtonsSembako.forEach(button => {
        button.addEventListener('click', function() {
            const details = JSON.parse(this.getAttribute('data-details'));
            const contentHTML = `
                <p><strong>Title:</strong> ${details.title}</p>
                <p><strong>Status:</strong> ${details.status}</p>
                <p><strong>Jumlah Poin:</strong> ${details.jumlah_poin}</p>
                <p><strong>Created At:</strong> ${details.created_at}</p>
            `;
            modalContent.innerHTML = contentHTML;
            modal.classList.remove('hidden');
        });
    });

    // Common Modal Close
    closeModal.addEventListener('click', function() {
        modal.classList.add('hidden');
    });
});

        document.addEventListener('DOMContentLoaded', function() {
            initializeMenu();
            initializeTabSwitching();

            const detailButtons = document.querySelectorAll('.detail-btn');
            const modal = document.getElementById('modal');
            const modalContent = document.getElementById('modal-content');
            const closeModal = document.getElementById('close-modal');

            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const details = JSON.parse(this.getAttribute('data-details'));
                    modalContent.innerHTML = `
                        <p><strong>ID:</strong> ${details.id}</p>
                        <p><strong>Status:</strong> ${details.status}</p>
                        <p><strong>Total Amount:</strong> Rp ${details.total_amount}</p>
                        <p><strong>Pickup Date:</strong> ${details.pickup_date}</p>
                        <p><strong>Pickup Time:</strong> ${details.pickup_time}</p>
                        <p><strong>Location:</strong> ${details.pickup_location}</p>
                    `;
                    modal.classList.remove('hidden');
                });
            });

            closeModal.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
        });

        
        // JavaScript untuk Tab Switching
        document.addEventListener('DOMContentLoaded', function() {
            initializeMenu();
            initializeTabSwitching();
        });

        // Fungsi untuk mengatur tab switching
        function initializeTabSwitching() {
            const semuaBtn = document.getElementById('semua-btn');
            const jemputBtn = document.getElementById('jemput-btn');
            const tukarBtn = document.getElementById('tukar-btn');
            const dataItems = document.querySelectorAll('.data-item');

            // Mengatur tampilan berdasarkan tombol yang dipilih
            function showTab(tab) {
                dataItems.forEach(item => {
                    if (tab === 'semua') {
                        item.style.display = 'block';
                    } else if (tab === 'jemput-sampah' && item.classList.contains('jemput-sampah')) {
                        item.style.display = 'block';
                    } else if (tab === 'tukar-poin' && item.classList.contains('tukar-poin')) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            // Event listener untuk tombol-tombol
            semuaBtn.addEventListener('click', () => {
                showTab('semua');
                resetButtonStyles();
                semuaBtn.classList.add('border-blue-600', 'text-blue-600', 'border-b-2');
            });

            jemputBtn.addEventListener('click', () => {
                showTab('jemput-sampah');
                resetButtonStyles();
                jemputBtn.classList.add('border-blue-600', 'text-blue-600', 'border-b-2');
            });

            tukarBtn.addEventListener('click', () => {
                showTab('tukar-poin');
                resetButtonStyles();
                tukarBtn.classList.add('border-blue-600', 'text-blue-600', 'border-b-2');
            });

            // Fungsi untuk mereset gaya tombol
            function resetButtonStyles() {
                semuaBtn.classList.remove('border-blue-600', 'text-blue-600', 'border-b-2');
                jemputBtn.classList.remove('border-blue-600', 'text-blue-600', 'border-b-2');
                tukarBtn.classList.remove('border-blue-600', 'text-blue-600', 'border-b-2');
            }

            // Default tampilkan Semua
            showTab('semua');
        }
    </script>
</body>
</html>


   
  
