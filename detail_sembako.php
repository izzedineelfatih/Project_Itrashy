<?php
session_start();
include 'config.php'; // Memuat koneksi database

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query database untuk mendapatkan detail sembako
$stmt = $pdo->prepare("SELECT * FROM sembako WHERE id = ?");
$stmt->execute([$id]);
$sembako = $stmt->fetch();

if (!$sembako) {
    die("Item tidak ditemukan!");
}

// Ambil total poin yang dimiliki oleh user
$user_id = $_SESSION['user_id'];
$stmtPoin = $pdo->prepare("SELECT poin_terkumpul FROM users WHERE id = ?");
$stmtPoin->execute([$user_id]);
$poin_terkumpul = $stmtPoin->fetchColumn();

// Proses penukaran poin jika konfirmasi diterima
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_exchange'])) {
    $poinRequired = $sembako['points'];

    // Cek apakah poin cukup
    if ($poin_terkumpul >= $poinRequired) {
        try {
            // Mulai transaksi
            $pdo->beginTransaction();

            // Kurangi poin dan update di database
            $new_points = $poin_terkumpul - $poinRequired;
            $stmtUpdate = $pdo->prepare("UPDATE users SET poin_terkumpul = ? WHERE id = ?");
            $stmtUpdate->execute([$new_points, $user_id]);

            // Insert ke order_sembako
            $stmtInsert = $pdo->prepare("INSERT INTO order_sembako (user_id, sembako, jumlah_poin, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
            $stmtInsert->execute([$user_id, $sembako['id'], $poinRequired]);

            // Tambahkan notifikasi
            $pesan_notifikasi = "Anda telah menukarkan " . htmlspecialchars($sembako['title']) . " dengan " . $poinRequired . " poin.";
            $stmtNotifikasi = $pdo->prepare("INSERT INTO notifications (message, created_at) VALUES (?, NOW())");
            $stmtNotifikasi->execute([$pesan_notifikasi]);

            // Commit transaksi
            $pdo->commit();

            // Sukses, tampilkan modal penukaran berhasil
            $penukaran_berhasil = true;
        } catch (Exception $e) {
            // Rollback jika terjadi error
            $pdo->rollBack();
            // Log error atau tampilkan pesan error
            die("Terjadi kesalahan: " . $e->getMessage());
        }
    } else {
        // Poin tidak cukup
        $penukaran_berhasil = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sembako</title>
    <meta name="page-title" content="Detail Sembako">
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
        <?php include 'sidebar.php'; ?>
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <?php include 'header.php'; ?>

            <div class="flex-1 overflow-y-auto p-7">
                <div class="flex flex-col bg-white max-w-md mx-auto p-5 rounded-lg shadow-lg">
                    <img src="assets/image/<?= htmlspecialchars($sembako['image']); ?>" alt="Gambar <?= htmlspecialchars($sembako['title']); ?>" class="mb-4 rounded-lg">
                    <div class="pr-5 pl-5">
                        <h1 class="font-bold text-lg mb-4"><?= htmlspecialchars($sembako['title']); ?></h1>
                        <p class="text-gray-600 mb-4 text-justify"><?= htmlspecialchars($sembako['description']); ?></p>
                        <div>
                            <h2 class="text-gray-600">Ketentuan Penukaran:</h2>
                            <ul class="list-disc pl-5">
                                <li class="text-gray-500 font-bold">Penukaran tidak dapat dibatalkan atau diubah setelah dikonfirmasi</li>
                                <li class="text-gray-500 font-bold">Pengiriman dilakukan saat penjemputan sampah selanjutnya</li>
                            </ul>
                        </div>
                        <div class="mt-6 mb-6 mx-auto border-t-2 border-black-300"></div>
                        <div class="flex justify-between mb-4">
                            <div class="flex items-center">
                                <img src="assets/icon/poin logo.png" alt="Trash Poin" class="w-6 h-6 mr-2">
                                <span class="font-bold">Trash Poin</span>
                            </div>
                            <span class="font-bold text-lg"><?= htmlspecialchars($sembako['points']); ?></span>
                        </div>
                    </div>

                    <!-- Tombol Tukar -->
                    <form method="POST" class="w-full">
                        <button type="button" id="tukar-btn" class="w-full bg-blue-500 text-white mt-8 py-2.5 rounded-full hover:bg-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">Tukar</button>
                        <input type="hidden" name="confirm_exchange" value="1">
                    </form>
                </div>
            </div>

            <!-- Modal Konfirmasi -->
            <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-70 hidden flex justify-center items-center z-50">
                <div class="bg-white p-5 rounded-lg shadow-lg w-96">
                    <h2 class="text-xl text-center font-bold mb-8">Konfirmasi Penukaran</h2>
                    <p class="text-center">Yakin ingin menukarkan poin untuk sembako <strong><?= htmlspecialchars($sembako['title']); ?></strong>?</p>
                    <p class="text-gray-500 mt-2 text-center text-sm">*Pengiriman akan dilakukan pada penjemputan sampah selanjutnya.</p>
                    <div class="flex space-x-4 justify-center mt-8">
                        <button id="cancel-btn" class="px-4 py-2 bg-gray-300 text-black rounded-md">Batal</button>
                        <button id="confirm-btn" class="px-4 py-2 bg-blue-500 text-white rounded-md">Yakin</button>
                    </div>
                </div>
            </div>

            <!-- Modal Penukaran Berhasil -->
            <?php if (isset($penukaran_berhasil)): ?>
                <?php if ($penukaran_berhasil): ?>
                    <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-center z-50">
                        <div class="bg-white p-5 rounded-lg shadow-lg w-96">
                            <h2 class="text-lg font-bold mb-4">Penukaran Poin Berhasil</h2>
                            <p>Poin Anda telah berhasil ditukarkan dengan sembako <?= htmlspecialchars($sembako['title']); ?>!</p>
                            <div class="flex justify-center mt-4">
                                <button id="close-success-btn" class="px-4 py-2 bg-blue-500 text-white rounded-md">Tutup</button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
                        <div class="bg-white p-5 rounded-lg shadow-lg w-96">
                            <h2 class="text-lg font-bold mb-4">Penukaran Gagal</h2>
                            <p>Maaf, poin Anda tidak cukup untuk melakukan penukaran sembako ini.</p>
                            <div class="flex justify-center mt-4">
                                <button id="close-error-btn" class="px-4 py-2 bg-red-500 text-white rounded-md">Tutup</button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php include 'footer.php'; ?>
        </div>
    </div>

    <script>
        const tukarBtn = document.getElementById('tukar-btn');
        const confirmModal = document.getElementById('confirm-modal');
        const cancelBtn = document.getElementById('cancel-btn');
        const confirmBtn = document.getElementById('confirm-btn');
        const successModal = document.getElementById('success-modal');
        const closeSuccessBtn = document.getElementById('close-success-btn');
        const errorModal = document.getElementById('error-modal');
        const closeErrorBtn = document.getElementById('close-error-btn');

        // Tampilkan modal konfirmasi saat tombol "Tukar" ditekan
        tukarBtn.addEventListener('click', function () {
            confirmModal.classList.remove('hidden');
        });

        // Jika pengguna menekan "Batal" pada modal konfirmasi
        cancelBtn.addEventListener('click', function () {
            confirmModal.classList.add('hidden');
        });

        // Jika pengguna menekan "Yakin" pada modal konfirmasi
        confirmBtn.addEventListener('click', function () {
            // Submit form untuk melakukan proses penukaran
            document.querySelector('form').submit();
        });

        // Tutup modal penukaran berhasil
        if (successModal) {
            closeSuccessBtn.addEventListener('click', function () {
                successModal.classList.add('hidden');
            });
        }

        // Tutup modal error jika poin tidak cukup
        if (errorModal) {
            closeErrorBtn.addEventListener('click', function () {
                errorModal.classList.add('hidden');
            });
        }
    </script>
</body>
</html>