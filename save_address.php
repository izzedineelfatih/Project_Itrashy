<?php
session_start();
require 'config.php';

// Menampilkan error (untuk debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Pengguna belum login.']);
    exit();
}

// Koneksi database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=itrashy", "root", ""); // Sesuaikan nama database, username, dan password
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . $e->getMessage()]);
    exit();
}

// Ambil ID pengguna dari sesi
$user_id = $_SESSION['user_id'];

// Ambil data dari request
$data = json_decode(file_get_contents("php://input"), true);

// Validasi data
if (empty($data['city']) || empty($data['district']) || empty($data['village']) || empty($data['street'])) {
    echo json_encode(['success' => false, 'message' => 'Semua field harus diisi.']);
    exit();
}

$city = htmlspecialchars($data['city']);
$district = htmlspecialchars($data['district']);
$village = htmlspecialchars($data['village']);
$street = htmlspecialchars($data['street']);

// Simpan data ke database
try {
    $query = "UPDATE users SET city = :city, district = :district, village = :village, address = :street WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':district', $district);
    $stmt->bindParam(':village', $village);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Alamat berhasil disimpan.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan alamat.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Kesalahan: ' . $e->getMessage()]);
}
?>
