<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validasi data
        if (empty($data['location']) || empty($data['pickup_date']) || empty($data['pickup_time']) || empty($data['items'])) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }

        // Format tanggal dan waktu
        $pickupDate = date('Y-m-d', strtotime($data['pickup_date']));
        
        // Gunakan pickup_time langsung dari dropdown tanpa diformat ulang
        $pickupTime = $data['pickup_time']; // Format sudah sesuai: "HH:mm-HH:mm"

        $userId = $_SESSION['user_id'];

        // Hitung total berat sampah dan total poin sebelum admin fee
        $totalBeratSampah = 0;
        $totalPointsBeforeFee = 0;

        // Mulai transaksi
        $pdo->beginTransaction();
        
        // Hitung total points
        foreach ($data['items'] as $item) {
            $totalBeratSampah += $item['quantity'];
            $totalPointsBeforeFee += $item['quantity'] * $item['price'];
        }
        
        // Hitung admin fee 20%
        $adminFee = $totalPointsBeforeFee * 0.20;
        // Hitung total amount setelah dipotong admin fee
        $finalTotalAmount = $totalPointsBeforeFee - $adminFee;
        
        // Insert ke tabel orders
        $stmt = $pdo->prepare("
            INSERT INTO orders (
                user_id, 
                pickup_location, 
                pickup_date, 
                pickup_time, 
                total_amount, 
                admin_fee, 
                status, 
                created_at, 
                total_berat_sampah
            ) VALUES (
                ?, ?, ?, ?, ?, ?, 'pending', NOW(), ?
            )
        ");
        
        $stmt->execute([
            $userId,
            $data['location'],
            $pickupDate,
            $pickupTime,  // Menggunakan format waktu langsung dari dropdown
            $finalTotalAmount,
            $adminFee,
            $totalBeratSampah
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // Insert items
        $stmt = $pdo->prepare("
            INSERT INTO order_items (
                order_id, 
                waste_type, 
                quantity, 
                price_per_kg
            ) VALUES (?, ?, ?, ?)
        ");

        foreach ($data['items'] as $item) {
            $stmt->execute([
                $orderId, 
                $item['name'], 
                $item['quantity'], 
                $item['price']
            ]);
        }

        // Update sembako orders yang menunggu pengiriman
        $stmt = $pdo->prepare("
            UPDATE order_sembako 
            SET 
                pickup_order_id = ?,
                pickup_date = ?,
                pickup_time = ?,
                pickup_location = ?,
                status = 'menunggu jemput sampah'
            WHERE 
                user_id = ? 
                AND status = 'menunggu jemput sampah'
                AND pickup_order_id IS NULL
        ");

        $stmt->execute([
            $orderId,
            $pickupDate,
            $pickupTime,  // Menggunakan format waktu langsung dari dropdown
            $data['location'],
            $userId
        ]);

        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'order_id' => $orderId,
            'total_points_before_fee' => $totalPointsBeforeFee,
            'admin_fee' => $adminFee,
            'final_total' => $finalTotalAmount,
            'pickup_details' => [
                'date' => $pickupDate,
                'time' => $pickupTime,
                'formatted_date' => date('d F Y', strtotime($pickupDate))
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error in save_order.php: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>