<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login.']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Query untuk mengambil data alamat lengkap
    $query = "SELECT city, district, village, address FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($address) {
        // Buat alamat lengkap untuk ditampilkan
        $full_address = implode(', ', array_filter([$address['address'], $address['village'], $address['district'], $address['city']]));
        $address['full_address'] = $full_address;

        echo json_encode(['success' => true, 'address' => $address]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Alamat tidak ditemukan.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal memuat alamat.']);
}
?>
