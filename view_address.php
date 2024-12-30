<?php
session_start();
require 'config.php';

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Periksa apakah user login
if (!isset($_SESSION['user_id'])) {
    die("User tidak login.");
}

$user_id = $_SESSION['user_id'];

try {
    // Ambil data full_address dari tabel users
    $stmt = $pdo->prepare("SELECT full_address FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $addressData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika data tidak ditemukan
    if (!$addressData || empty($addressData['full_address'])) {
        $addressData = [
            'full_address' => 'Alamat belum tersedia.'
        ];
    }
} catch (PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}
?>
