<?php
session_start();
require 'config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['delete'])) {
    try {
        // Ambil path foto profil dari database
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['profile_picture']) {
            // Hapus file gambar dari server
            $filePath = $user['profile_picture'];
            if (file_exists($filePath)) {
                unlink($filePath); // Hapus file gambar
            }

            // Hapus gambar dari database
            $stmt = $pdo->prepare("UPDATE users SET profile_picture = NULL WHERE id = :user_id");
            $stmt->execute([':user_id' => $user_id]);

            echo json_encode(['success' => true, 'message' => 'Foto profil berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tidak ada foto profil untuk dihapus']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid']);
}
?>
