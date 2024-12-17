<?php
session_start();
require 'config.php';

// Menampilkan error (untuk debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Koneksi Database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=itrashy", "root", ""); // Sesuaikan nama database, username, dan password
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Ambil data pengguna berdasarkan ID sesi
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, phone_number, profile_picture, description FROM users WHERE id = :id";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika data tidak ditemukan
if (!$profile) {
    die("Data pengguna tidak ditemukan.");
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .nav-link {
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #3b82f6;
        }
        .nav-link.active {
            background-color: #3968DA;
            color: white;
            margin-left: -20px;
            padding-left: 30px; 
            margin-right: 10px;
            padding-top: 10px;
            padding-bottom: 10px;
            font-weight: 500;
            border-radius: 0 15px 15px 0;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body class="bg-[#f5f6fb] font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-y-auto min-h-screen lg:ml-0">
            <!-- Header -->
            <?php include 'header.php'; ?>
            <div class="min-h-screen flex flex-col py-10 px-4 lg:px-10 gap-6">
                <!-- Profile Section -->
                <div class="w-full max-w-4xl p-6 md:p-8 flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4 bg-white rounded-lg">
                    <!-- Profile Picture -->
                    <div class="relative flex-shrink-0">
    <img id="profilePic" src="<?php echo htmlspecialchars($profile['profile_picture'] ?? 'assets/image/default_profile.jpg'); ?>" 
         alt="Profile Picture" class="w-24 h-24 md:w-28 md:h-28 rounded-full">
    <div id="cameraIcon" class="absolute bottom-0 right-0 bg-black p-2 rounded-full cursor-pointer">
        <i class="fas fa-camera text-white"></i>
    </div>
</div>
                
                    <!-- Profile Text -->
                    <div class="flex-1 flex flex-col justify-center">
    <h1 class="text-lg md:text-xl font-bold flex items-center space-x-2">
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($profile['username']); ?>" readonly
               class="border-none bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
    </h1>
    <p class="text-gray-500 text-sm md:text-base">
    <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($profile['description'] ?? 'Belum ada deskripsi.'); ?>" readonly
           class="border-none bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
</p>

                    </div>
                
                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <button id="editButton" class="flex items-center space-x-2 px-4 py-2 bg-white text-gray-500 font-semibold rounded-full shadow-sm border border-gray-300 hover:bg-blue-100">
                            <span>Edit</span>
                            <img src="assets/icon/edit.png" alt="edit" class="w-5 opacity-65">
                        </button>
                        <a href="logout.php">
                            <button class="flex items-center space-x-2 px-4 py-2 bg-white text-red-600 font-semibold rounded-lg shadow-sm hover:bg-red-100">
                                <i class="fas fa-right-from-bracket text-red-600"></i>
                                <span>Keluar</span>
                            </button>
                        </a>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="w-full max-w-4xl flex flex-col items-start">
                    <div class="w-full bg-white rounded-lg shadow-lg p-6 md:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <input type="text" id="username" class="mt-1 p-2 w-full border rounded-lg bg-gray-50" 
                                value="<?php echo htmlspecialchars($profile['username']); ?>" readonly>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" class="mt-1 p-2 w-full border rounded-lg bg-gray-50" 
                                value="<?php echo htmlspecialchars($profile['email']); ?>" readonly>
                            </div>
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700">No. Handphone</label>
                                <input type="tel" id="phone_number" class="mt-1 p-2 w-full border rounded-lg bg-gray-50" 
                                value="<?php echo htmlspecialchars($profile['phone_number']); ?>" readonly>
                            </div>
                        </div>
                        <button id="saveButton" class="w-full mt-6 px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-sm hover:bg-blue-600 hidden">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const editButton = document.getElementById("editButton");
            const saveButton = document.getElementById("saveButton");
            const inputs = document.querySelectorAll("input");
            const cameraIcon = document.getElementById("cameraIcon");
            const nameIcon = document.getElementById("nameIcon");

            // Fungsi untuk mengaktifkan mode edit
            editButton.addEventListener("click", function () {
                inputs.forEach(input => {
                    input.removeAttribute("readonly");
                    input.classList.add("border-blue-500");
                });
                editButton.classList.add("hidden");
                saveButton.classList.remove("hidden");
                cameraIcon.classList.remove("hidden");
                nameIcon.classList.remove("hidden");
            });

            // Fungsi untuk menyimpan perubahan
            saveButton.addEventListener("click", function () {
                inputs.forEach(input => {
                    input.setAttribute("readonly", true);
                    input.classList.remove("border-blue-500");
                });
                saveButton.classList.add("hidden");
                editButton.classList.remove("hidden");
                cameraIcon.classList.add("hidden");
                nameIcon.classList.add("hidden");
                alert("Perubahan berhasil disimpan!");
            });

            // Mengganti foto profil
            cameraIcon.addEventListener("click", function() {
                const fileInput = document.createElement("input");
                fileInput.type = "file";
                fileInput.accept = "image/*";
                fileInput.onchange = function() {
                    const file = fileInput.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById("profilePic").src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                };
                fileInput.click();
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
    const editButton = document.getElementById("editButton");
    const saveButton = document.getElementById("saveButton");
    const inputs = document.querySelectorAll("input");

    editButton.addEventListener("click", function () {
        inputs.forEach(input => {
            input.removeAttribute("readonly");
            input.classList.add("border-blue-500");
        });
        editButton.classList.add("hidden");
        saveButton.classList.remove("hidden");
    });

    saveButton.addEventListener("click", function () {
        const formData = new FormData();
        inputs.forEach(input => {
            formData.append(input.id, input.value);
        });

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "save_profile.php", true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert(response.message);
                    inputs.forEach(input => {
                        input.setAttribute("readonly", true);
                        input.classList.remove("border-blue-500");
                    });
                    saveButton.classList.add("hidden");
                    editButton.classList.remove("hidden");
                } else {
                    alert("Error: " + response.message);
                }
            } else {
                alert("Server error: " + xhr.status);
            }
        };
        xhr.send(formData);
    });
});

    </script>
</body>
</html>
