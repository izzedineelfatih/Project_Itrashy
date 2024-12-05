<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Ambil data dari request
$user_id = $_SESSION['user_id'];
$name = $_POST['nameInput'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$description = $_POST['descriptionInput'] ?? '';
$profile_picture = null;

// Proses upload foto profil jika ada
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'assets/uploads/';
    $fileName = $user_id . '_' . basename($_FILES['profile_picture']['name']);
    $uploadFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
        $profile_picture = $uploadFile;
    }
}

try {
    // Update tabel `users` untuk nama dan email
    $stmt = $pdo->prepare("UPDATE users SET username = :name, email = :email WHERE id = :user_id");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':user_id' => $user_id,
    ]);

    // Update tabel `profiles` untuk nomor telepon, deskripsi, dan foto profil
    $sql = "UPDATE profiles 
            SET phone = :phone, description = :description" .
            ($profile_picture ? ", profile_picture = :profile_picture" : "") . 
            " WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);

    $params = [
        ':phone' => $phone,
        ':description' => $description,
        ':user_id' => $user_id,
    ];

    if ($profile_picture) {
        $params[':profile_picture'] = $profile_picture;
    }

    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
