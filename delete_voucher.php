<?php
include 'config.php';

$id = $_GET['id'];

// Hapus data voucher
$stmt = $pdo->prepare("DELETE FROM voucher WHERE id = ?");
$stmt->execute([$id]);

header('Location: katalog_voucher.php');
exit();
?>
