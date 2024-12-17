<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Ambil data dari request
$user_id = $_SESSION['user_id'];
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$description = $_POST['description'] ?? '';
$profile_picture = null;

// Proses upload foto profil jika ada
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'assets/uploads/'; // Direktori penyimpanan
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Buat direktori jika belum ada
    }

    $fileName = $user_id . '_' . time() . '_' . basename($_FILES['profile_picture']['name']);
    $uploadFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
        $profile_picture = $uploadFile;
    }
}

try {
    // Query dasar untuk update tabel `users`
    $sql = "UPDATE users SET username = :username, email = :email, phone_number = :phone_number, description = :description";

    // Jika ada foto profil yang diupload, tambahkan kolom `profile_picture` ke query
    if ($profile_picture) {
        $sql .= ", profile_picture = :profile_picture";
    }

    $sql .= " WHERE id = :user_id";

    // Persiapkan query
    $stmt = $pdo->prepare($sql);

    // Parameter untuk query
    $params = [
        ':username' => $username,
        ':email' => $email,
        ':phone_number' => $phone_number,
        ':description' => $description,
        ':user_id' => $user_id,
    ];

    if ($profile_picture) {
        $params[':profile_picture'] = $profile_picture;
    }

    // Eksekusi query
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
