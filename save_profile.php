<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

require 'config.php';

// Ambil data dari request
$user_id = $_SESSION['user_id'];
$username = $_POST['nameInput'] ?? '';
$description = $_POST['descriptionInput'] ?? '';

// Path default untuk gambar profil
$profile_picture = null;

// Proses unggah file jika ada
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = 'assets/uploads/';
    $fileName = $user_id . '_' . basename($_FILES['profile_picture']['name']);
    $uploadFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
        $profile_picture = $uploadFile;
    }
}

// Update database
$sql = "UPDATE profiles 
        SET username = :username, description = :description" . 
        ($profile_picture ? ", profile_picture = :profile_picture" : "") . 
        " WHERE user_id = :user_id";

$stmt = $pdo->prepare($sql);
$params = [
    'username' => $username,
    'description' => $description,
    'user_id' => $user_id
];

if ($profile_picture) {
    $params['profile_picture'] = $profile_picture;
}

try {
    $stmt->execute($params);
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . $e->getMessage()]);
}
?>
