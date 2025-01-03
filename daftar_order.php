<?php
session_start();
require 'config.php';

// Pastikan hanya driver yang bisa mengakses
if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'driver') {
    header("Location: staff_login.php");
    exit();
}

// 1. Proses edit quantity items
if (isset($_POST['items'])) {
    $orderId = $_POST['order_id'];
    $items = $_POST['items'];
    $totalBerat = 0;
    $totalPoinBeforeFee = 0;
    
    try {
        $pdo->beginTransaction();
        
        foreach ($items as $itemId => $quantity) {
            $stmt = $pdo->prepare("SELECT price_per_kg FROM order_items WHERE id = :id");
            $stmt->execute(['id' => $itemId]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$item) {
                throw new Exception("Item dengan ID $itemId tidak ditemukan");
            }
            
            // Update quantity
            $stmt = $pdo->prepare("UPDATE order_items SET quantity = :quantity WHERE id = :id");
            $stmt->execute(['quantity' => $quantity, 'id' => $itemId]);
            
            $totalBerat += $quantity;
            $totalPoinBeforeFee += $quantity * $item['price_per_kg'];
        }

        // Hitung adminFee 20%
        $adminFee = $totalPoinBeforeFee * 0.2;
        // Poin final setelah admin fee
        $finalAmount = $totalPoinBeforeFee - $adminFee;

        // Update orders
        $stmt = $pdo->prepare("
            UPDATE orders 
               SET total_berat_sampah = :berat, 
                   admin_fee         = :admin_fee, 
                   total_amount      = :total 
             WHERE id = :id
        ");
        $stmt->execute([
            'berat'     => $totalBerat,
            'admin_fee' => $adminFee,
            'total'     => $finalAmount,
            'id'        => $orderId
        ]);

        $pdo->commit();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

// 2. Proses aksi terima, tolak, selesai
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $orderId = $_POST['order_id'];
    $action = $_POST['action'];
    
    if ($action === 'accept') {
        // Terima => status pickup
        $status = 'pickup';
    } elseif ($action === 'reject') {
        // Tolak => status cancel
        $status = 'cancel';
    } elseif ($action === 'done') {
        // Selesai => status done
        $status = 'done';
        
        // Tambah poin ke user
        $stmt = $pdo->prepare("SELECT user_id, total_amount FROM orders WHERE id = :id");
        $stmt->execute(['id' => $orderId]);
        $orderData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($orderData) {
            $stmt = $pdo->prepare("
                UPDATE users 
                   SET poin_terkumpul = COALESCE(poin_terkumpul, 0) + :points 
                 WHERE id = :user_id
            ");
            $stmt->execute([
                'points' => $orderData['total_amount'],
                'user_id' => $orderData['user_id']
            ]);
        }
    }

    // Update status di tabel orders
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $orderId]);
    
    // Jika accept => pickup, update order_tukarPoin
    if ($action === 'accept') {
        $stmt2 = $pdo->prepare("
            UPDATE order_sembako
               SET status = 'pickup'
             WHERE pickup_order_id = :orderId
               AND status = 'menunggu jemput sampah'
        ");
        $stmt2->execute(['orderId' => $orderId]);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Ambil tanggal hari ini untuk filter
$today = date('Y-m-d');

// 3. Ambil data orders dan items (hanya status pending & pickup untuk hari ini)
//    + LEFT JOIN ke order_sembako & sembako (untuk kolom Tukar Poin)
$stmt = $pdo->prepare("
    SELECT 
        o.id AS order_id,
        o.pickup_location,
        o.pickup_date,
        o.pickup_time,
        o.total_berat_sampah,
        o.total_amount,
        o.admin_fee,
        o.status,
        u.username,
        u.phone_number,

        -- Gabungkan semua sembako terkait
        GROUP_CONCAT(DISTINCT s.title SEPARATOR ', ') AS sembako_name,

        -- Data item (jika diperlukan)
        GROUP_CONCAT(DISTINCT CONCAT(oi.waste_type, ' (', oi.quantity, ' Kg)') SEPARATOR ', ') AS item_details
        
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    
    -- Link ke order_tukarPoin
    LEFT JOIN order_sembako os ON o.id = os.pickup_order_id
    LEFT JOIN sembako s ON os.sembako = s.id
    
    WHERE o.status IN ('pending', 'pickup')
      AND DATE(o.pickup_date) = :today
    
    GROUP BY o.id
    ORDER BY 
      CASE 
        WHEN o.pickup_time LIKE '%-%' 
        THEN SUBSTRING_INDEX(o.pickup_time, '-', 1)
        ELSE o.pickup_time 
      END ASC
");
$stmt->execute(['today' => $today]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kelompokkan items per order
$orderItems = [];
foreach ($orders as $order) {
    $oid = $order['order_id'];
    
    if (!isset($orderItems[$oid])) {
        $orderItems[$oid] = [
            'order_info' => [
                'id'                => $order['order_id'],
                'username'          => $order['username'],
                'phone_number'      => $order['phone_number'],
                'pickup_location'   => $order['pickup_location'],
                'pickup_date'       => $order['pickup_date'],
                'pickup_time'       => $order['pickup_time'], // Simpan string asli, contoh "10:00-12:00"
                'total_berat_sampah'=> $order['total_berat_sampah'],
                'total_amount'      => $order['total_amount'],
                'admin_fee'         => $order['admin_fee'],
                'status'            => $order['status'],
                'sembako_name'      => $order['sembako_name'] // Kolom tukar poin (nama sembako), jika ada
            ],
            'items' => []
        ];
    }
    
    if (!empty($order['item_id'])) { // Pastikan item_id tidak null atau kosong
        $orderItems[$oid]['items'][] = [
            'id'           => $order['item_id'],
            'waste_type'   => $order['waste_type'],
            'quantity'     => $order['quantity'],
            'price_per_kg' => $order['price_per_kg']
        ];
    }    
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Order Hari Ini</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">
<?php include 'staff_sidebar.php'; ?>

<div class="flex-1 mx-auto p-5">
    <header class="flex justify-between items-center mb-5">
        <div>
            <h1 class="text-3xl font-bold">Daftar Order Hari Ini</h1>
            <p class="text-gray-600 mt-1"><?php echo date('d F Y', strtotime($today)); ?></p>
        </div>
    </header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="table-auto w-full">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="px-4 py-2 border">Nama Pelanggan</th>
                    <th class="px-4 py-2 border">Nomor Telepon</th>
                    <th class="px-4 py-2 border">Lokasi</th>
                    <th class="px-4 py-2 border">Waktu Pickup</th>
                    <th class="px-4 py-2 border">Tukar Poin</th> <!-- Tambahan kolom -->
                    <th class="px-4 py-2 border">Total Berat (kg)</th>
                    <th class="px-4 py-2 border">Total Poin</th>
                    <th class="px-4 py-2 border">Status</th>
                    <th class="px-4 py-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orderItems)): ?>
                    <tr>
                        <td colspan="9" class="px-4 py-4 text-center text-gray-500">Tidak ada order untuk hari ini</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orderItems as $orderId => $orderData): ?>
                        <?php 
                            $info   = $orderData['order_info'];
                            $status = $info['status'];
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($info['username']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($info['phone_number']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($info['pickup_location']); ?></td>
                            <td class="px-4 py-2 border">
                                <?php echo htmlspecialchars($info['pickup_time']); ?> WIB
                            </td>
                            <!-- Kolom Tukar Poin (nama sembako) -->
                            <td class="px-4 py-2 border">
                                <?php echo $info['sembako_name'] ? htmlspecialchars($info['sembako_name']) : '-'; ?>
                            </td>

                            <td class="px-4 py-2 border">
                                <?php echo number_format($info['total_berat_sampah'], 2, ',', '.'); ?> Kg
                            </td>
                            <td class="px-4 py-2 border">
                                <?php echo number_format($info['total_amount'], 0, ',', '.'); ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 rounded-full text-sm
                                    <?php 
                                    switch($status) {
                                        case 'pending':
                                            echo 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'pickup':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'cancel':
                                            echo 'bg-red-100 text-red-800';
                                            break;
                                        case 'done':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                            break;
                                    }
                                    ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 border">
                                <!-- Kita tampilkan tombol berdasarkan status: 
                                     1) pending  => Terima, Tolak
                                     2) pickup   => Edit, Selesai
                                     3) cancel/done => tidak ada tombol
                                -->
                                <form method="POST" class="flex space-x-2 flex-wrap" id="form-<?php echo $orderId; ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                                    
                                    <?php if ($status === 'pending'): ?>
                                        <!-- Tampilkan tombol Terima dan Tolak -->
                                        <button type="button" 
                                                onclick="handleAction('accept', <?php echo $orderId; ?>)"
                                                class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                                            Terima
                                        </button>
                                        <button type="button"
                                                onclick="handleAction('reject', <?php echo $orderId; ?>)"
                                                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                                            Tolak
                                        </button>
                                    
                                    <?php elseif ($status === 'pickup'): ?>
                                        <!-- Tampilkan tombol Edit dan Selesai -->
                                        <button type="button" 
                                                onclick="openEditModal(<?php echo htmlspecialchars(json_encode($orderData), ENT_QUOTES, 'UTF-8'); ?>)"
                                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                            Edit
                                        </button>
                                        <button type="button"
                                                onclick="handleAction('done', <?php echo $orderId; ?>)"
                                                class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                                            Selesai
                                        </button>
                                    
                                    <?php else: ?>
                                        <!-- Status cancel atau done => tidak ada tombol -->
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden z-50">
    <div class="fixed inset-0 flex items-center justify-center">
        <div class="bg-white w-96 p-6 rounded-lg shadow-lg relative">
            <h2 class="text-lg font-bold mb-4">Edit Berat Per Item</h2>
            <form method="POST" id="editForm" onsubmit="return handleEditSubmit(event)">
                <input type="hidden" id="editOrderId" name="order_id">
                <div id="itemFields" class="space-y-4">
                    <!-- Item fields akan dimuat melalui JS -->
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Simpan</button>
                    <button type="button" onclick="closeEditModal()" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleAction(action, orderId) {
    let confirmMessage = '';
    
    switch(action) {
        case 'accept':
            confirmMessage = 'Apakah Anda yakin ingin menerima order ini?';
            break;
        case 'reject':
            confirmMessage = 'Apakah Anda yakin ingin menolak order ini?';
            break;
        case 'done':
            confirmMessage = 'Apakah Anda yakin order ini sudah selesai?';
            break;
    }

    if (confirm(confirmMessage)) {
        // Dapat menonaktifkan tombol di sini jika mau
        const form = document.getElementById(`form-${orderId}`);
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'action';
        input.value = action;
        form.appendChild(input);
        form.submit();
    }
}

function handleEditSubmit(event) {
    event.preventDefault();
    if (confirm('Apakah Anda yakin ingin menyimpan perubahan ini?')) {
        document.getElementById('editForm').submit();
    }
    return false;
}

function openEditModal(orderData) {
    // Reset form
    const editForm = document.getElementById('editForm');
    editForm.reset();
    
    // Set order ID
    document.getElementById('editOrderId').value = orderData.order_info.id;
    const itemFields = document.getElementById('itemFields');
    itemFields.innerHTML = '';
    
    // Generate form fields
    orderData.items.forEach(item => {
        const div = document.createElement('div');
        div.classList.add('flex', 'flex-col', 'space-y-2', 'mb-4');
        div.innerHTML = `
            <label class="font-medium">${item.waste_type} (Rp${Number(item.price_per_kg).toLocaleString('id-ID')}/kg)</label>
            <input type="number" 
                   name="items[${item.id}]" 
                   value="${item.quantity}"
                   min="0" 
                   step="0.1" 
                   class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                   required>
        `;
        itemFields.appendChild(div);
    });
    
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

</body>
</html>