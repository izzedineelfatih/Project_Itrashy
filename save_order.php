<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Mulai transaksi
        $pdo->beginTransaction();
        
        // Insert ke tabel orders
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, pickup_location, pickup_date, pickup_time, total_amount, admin_fee, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $data['location'],
            $data['pickup_date'],
            $data['pickup_time'],
            $data['total'],
            $data['admin_fee']
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // Insert ke tabel order_items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, waste_type, quantity, price_per_kg) VALUES (?, ?, ?, ?)");
        
        foreach ($data['items'] as $item) {
            $stmt->execute([
                $orderId,
                $item['name'],
                $item['quantity'],
                $item['price']
            ]);
        }
        
        // Commit transaksi
        $pdo->commit();
        
        echo json_encode(['success' => true, 'order_id' => $orderId]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>