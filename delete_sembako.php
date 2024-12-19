<?php
include 'config.php';

$id = $_GET['id'];

// Hapus data sembako
$stmt = $pdo->prepare("DELETE FROM sembako WHERE id = ?");
$stmt->execute([$id]);

header('Location: katalog_sembako.php');
exit();
?>
