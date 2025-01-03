<?php
session_start();
require 'config.php';

// Pastikan hanya driver yang bisa mengakses
if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil data orders dan items (hanya status done dan cancel)
$stmt = $pdo->query("
    SELECT 
        o.id AS order_id,
        o.pickup_location,
        o.pickup_date,
        o.pickup_time,
        o.total_berat_sampah,
        o.total_amount,
        o.admin_fee,
        o.status,
        o.created_at,
        u.username,
        u.phone_number,
        oi.id AS item_id,
        oi.waste_type,
        oi.quantity,
        oi.price_per_kg
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.status IN ('done', 'cancel')
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kelompokkan items per order
$orderItems = [];
foreach ($orders as $order) {
    if (!isset($orderItems[$order['order_id']])) {
        $orderItems[$order['order_id']] = [
            'order_info' => [
                'id' => $order['order_id'],
                'username' => $order['username'],
                'phone_number' => $order['phone_number'],
                'pickup_location' => $order['pickup_location'],
                'pickup_datetime' => $order['pickup_date'] . ' ' . $order['pickup_time'],
                'total_berat_sampah' => $order['total_berat_sampah'],
                'total_amount' => $order['total_amount'],
                'admin_fee' => $order['admin_fee'],
                'status' => $order['status'],
                'created_at' => $order['created_at']
            ],
            'items' => []
        ];
    }
    if ($order['item_id']) {
        $orderItems[$order['order_id']]['items'][] = [
            'id' => $order['item_id'],
            'waste_type' => $order['waste_type'],
            'quantity' => $order['quantity'],
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
    <title>Riwayat Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">
<?php include 'staff_sidebar.php'; ?>

<div class="flex-1 mx-auto p-5">
    <header class="flex justify-between items-center mb-5">
        <h1 class="text-3xl font-bold">Riwayat Order</h1>
    </header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="table-auto w-full">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="px-4 py-2 border">Nama Pelanggan</th>
                    <th class="px-4 py-2 border">Nomor Telepon</th>
                    <th class="px-4 py-2 border">Lokasi</th>
                    <th class="px-4 py-2 border">Tanggal dan Waktu Pickup</th>
                    <th class="px-4 py-2 border">Total Berat (kg)</th>
                    <th class="px-4 py-2 border">Total Poin</th>
                    <th class="px-4 py-2 border">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orderItems)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">Tidak ada riwayat order</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orderItems as $orderId => $orderData): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($orderData['order_info']['username']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($orderData['order_info']['phone_number']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($orderData['order_info']['pickup_location']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($orderData['order_info']['pickup_datetime']); ?></td>
                            <td class="px-4 py-2 border"><?php echo number_format($orderData['order_info']['total_berat_sampah'], 2, ',', '.'); ?> Kg</td>
                            <td class="px-4 py-2 border"><?php echo number_format($orderData['order_info']['total_amount'], 0, ',', '.'); ?></td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 rounded-full text-sm
                                    <?php echo $orderData['order_info']['status'] === 'done' 
                                        ? 'bg-green-100 text-green-800' 
                                        : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo htmlspecialchars($orderData['order_info']['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>