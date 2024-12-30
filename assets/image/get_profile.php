<?php
session_start();
require 'config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Ambil data foto profil dari database
    $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['profile_picture']) {
        echo json_encode(['success' => true, 'profile_picture' => $user['profile_picture']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Foto profil belum tersedia']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
