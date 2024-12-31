<?php
session_start();
require 'config.php';

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Periksa apakah user login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User tidak login."]);
    exit();
}

// Ambil data dari request body
$data = json_decode(file_get_contents("php://input"), true);

$user_id = $_SESSION['user_id'];
$address = $data['address'] ?? null;
$city = $data['city'] ?? null;
$district = $data['district'] ?? null;
$village = $data['village'] ?? null;

// Validasi input
if (!$address || !$city || !$district || !$village) {
    echo json_encode(["success" => false, "message" => "Semua kolom wajib diisi."]);
    exit();
}

// Gabungkan data menjadi Alamat Lengkap
$full_address = "$address, $village, $district, $city";

try {
    // Cek apakah user sudah memiliki data
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $userExists = $stmt->fetchColumn();

    if ($userExists) {
        // Update data jika user sudah ada
        $stmt = $pdo->prepare("UPDATE users SET address = :address, city = :city, district = :district, village = :village, full_address = :full_address WHERE id = :user_id");
        $stmt->execute([
            ':address' => $address,
            ':city' => $city,
            ':district' => $district,
            ':village' => $village,
            ':full_address' => $full_address,
            ':user_id' => $user_id
        ]);
        echo json_encode(["success" => true, "message" => "Alamat berhasil diperbarui."]);
    } else {
        // Insert data baru jika user belum ada
        $stmt = $pdo->prepare("INSERT INTO users (id, address, city, district, village, full_address) VALUES (:user_id, :address, :city, :district, :village, :full_address)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':address' => $address,
            ':city' => $city,
            ':district' => $district,
            ':village' => $village,
            ':full_address' => $full_address
        ]);
        echo json_encode(["success" => true, "message" => "Alamat berhasil ditambahkan."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan alamat: " . $e->getMessage()]);
}
?>
