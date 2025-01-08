<?php
session_start(); // Memastikan sesi dimulai

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

// Menghubungkan ke database
require 'config.php';

// Ambil ID notifikasi dari permintaan GET
$notif_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Pastikan ID valid
if ($notif_id > 0) {
    try {
        // Hapus notifikasi dari database
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        $stmt->execute([$notif_id, $_SESSION['user_id']]); // Pastikan hanya menghapus milik pengguna yang login

        // Cek apakah notifikasi berhasil dihapus
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Notifikasi tidak ditemukan atau sudah dihapus.']);
        }
    } catch (PDOException $e) {
        // Tangani kesalahan database
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus notifikasi.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID notifikasi tidak valid.']);
}
?>

<script>
    fetch(`delete_notification.php?id=${notifId}`)
    .then(response => response.json()) // Parse JSON
    .then(data => {
        if (data.success) {
            notifItem.remove();
            updateNotifBadge();
        } else {
            console.error('Gagal menghapus notifikasi:', data.message);
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
</script>