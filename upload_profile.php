<?php
session_start();
require 'config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'assets/image/'; // Direktori penyimpanan gambar

    // Cek apakah direktori sudah ada, jika belum buat
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Nama file gambar (gunakan user_id dan waktu untuk memastikan nama unik)
    $fileName = $user_id . '_' . time() . '_' . basename($_FILES['profile_picture']['name']);
    $uploadFile = $uploadDir . $fileName;

    // Cek apakah file berhasil dipindahkan
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
        try {
            // Update foto profil di database
            $stmt = $pdo->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :user_id");
            $stmt->execute([
                ':profile_picture' => $uploadFile,
                ':user_id' => $user_id,
            ]);

            echo json_encode(['success' => true, 'message' => 'Foto profil berhasil diupload!', 'profile_picture' => $uploadFile]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload gambar']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload']);
}
?>
