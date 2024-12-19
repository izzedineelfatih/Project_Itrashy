<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $points = $_POST['points'];
    
    // Upload file gambar
    $image = $_FILES['image'];
    $imageName = time() . '_' . $image['name'];
    move_uploaded_file($image['tmp_name'], 'assets/image/' . $imageName);

    // Masukkan data ke database
    $stmt = $pdo->prepare("INSERT INTO voucher (title, description, points, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $description, $points, $imageName]);

    header('Location: katalog_voucher.php');
    exit();
}
?>
