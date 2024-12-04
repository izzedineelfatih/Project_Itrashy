<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

require 'config.php'; // Include file konfigurasi database

// Ambil data dari request
$user_id = $_SESSION['user_id'];
$username = $_POST['username'] ?? '';
$business_type = $_POST['business-type'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

// Update data ke database
$sql = "UPDATE profiles SET username = :username, business_type = :business_type, email = :email, phone = :phone WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        'username' => $username,
        'business_type' => $business_type,
        'email' => $email,
        'phone' => $phone,
        'user_id' => $user_id,
    ]);
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . $e->getMessage()]);
}
?>
