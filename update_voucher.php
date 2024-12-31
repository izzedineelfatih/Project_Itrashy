<?php
include 'config.php';

// Ambil data dari form
$id = $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];
$points = $_POST['points'];

// Cek apakah ada file gambar baru yang diupload
if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image'];
    $imageName = time() . '_' . $image['name'];
    move_uploaded_file($image['tmp_name'], 'assets/image/' . $imageName);
} else {
    // Jika tidak ada file baru, gunakan gambar lama
    $stmt = $pdo->prepare("SELECT image FROM voucher WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    $imageName = $row['image'];
}

// Update data di database
$stmt = $pdo->prepare("UPDATE voucher SET title = ?, description = ?, points = ?, image = ? WHERE id = ?");
$stmt->execute([$title, $description, $points, $imageName, $id]);

// Redirect kembali ke katalog voucher
header('Location: katalog_voucher.php');
exit();
?>
