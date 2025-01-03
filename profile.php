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
$query = "SELECT username, email, phone_number, profile_picture, description,address FROM users WHERE id = :id";


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
    <meta name="page-title" content="Profile">
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
    <img id="profilePic" src="<?php echo htmlspecialchars($profile['profile_picture'] ?? 'assets/icon/user.png'); ?>" 
         alt="Profile Picture" class="w-24 h-24 md:w-28 md:h-28 rounded-full cursor-pointer">
    
    <!-- Ikon Kamera, hanya ditampilkan ketika gambar profil diklik -->
    <img src="assets/icon/camera.png" class="absolute bottom-0 right-0 h-5 cursor-pointer hidden" id="cameraIcon" alt="Camera Icon">
    
    <!-- Input untuk memilih gambar baru -->
    <input type="file" id="fileInput" class="hidden" accept="image/*">
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
                        <button id="cancelButton" class="flex items-center space-x-2 px-4 py-2 bg-red-500 text-white font-semibold rounded-full shadow-sm border border-gray-300 hover:bg-red-600 hidden">
                        <span>Batal</span>
                        <img src="assets/icon/forbidden.png" alt="cancel" class="w-4 opacity-85">
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
                   
                    </div>
                </div>
                
                <div class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6 md:p-8">
    <!-- Form Header -->
    <h2 class="text-xl font-bold mb-6">Alamat</h2>

    <!-- Form Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Kolom Kiri -->
        <div class="space-y-4">
            <!-- Pilihan Kota -->
            <div class="flex items-center">
                <label for="city" class="w-1/3 text-gray-700 font-medium">Kota:</label>
                <select class="w-2/3 p-2 border rounded-lg cursor-pointer" id="city" required>
                    <option value="" >Pilih Kota/Kabupaten</option>
                    <option value="Bandung" <?php echo isset($addressData['city']) && $addressData['city'] == 'Bandung' ? 'selected' : ''; ?>>Kota Bandung</option>
                    <option value="Bandung Kabupaten" <?php echo isset($addressData['city']) && $addressData['city'] == 'Bandung Kabupaten' ? 'selected' : ''; ?>>Kabupaten Bandung</option>
                    <option value="Bandung Barat Kabupaten" <?php echo isset($addressData['city']) && $addressData['city'] == 'Bandung Barat Kabupaten' ? 'selected' : ''; ?>>Kabupaten Bandung Barat</option>
                </select>
            </div>

            <!-- Pilihan Kecamatan -->
            <div class="flex items-center">
                <label for="district" class="w-1/3 text-gray-700 font-medium">Kecamatan:</label>
                <select class="w-2/3 p-2 border rounded-lg" id="district" required disabled>
                    <option value="">Pilih Kecamatan</option>
                </select>
            </div>

            <!-- Pilihan Desa -->
            <div class="flex items-center">
                <label for="village" class="w-1/3 text-gray-700 font-medium">Desa:</label>
                <select class="w-2/3 p-2 border rounded-lg" id="village" required disabled>
                    <option value="">Pilih Desa</option>
                </select>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="space-y-4">
            <!-- Alamat Jalan -->
            <div class="flex items-center">
                <label for="address" class="w-1/3 text-gray-700 font-medium">Alamat Jalan:</label>
                <input type="text" id="address" class="w-2/3 p-2 border rounded-lg" placeholder="Masukkan nama jalan"  required>
            </div>
        </div>

        <!-- Alamat Lengkap -->
        <div>
        <label class="block text-sm font-medium text-gray-700">Alamat Lengkap:</label>
    <p id="fullAddress" class="mt-1 p-2 w-full border rounded-lg bg-gray-50 text-gray-900">
        Alamat belum tersedia.
    </p>


    </div>

    <!-- Tombol -->
      
    
</div>
<button id="saveButton" class="w-full mt-6 px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-sm hover:bg-blue-600 hidden">
            Simpan
        </button>
    
                       
                    </div>
                </>
            </div>
        </div>
    </div>
    <script>
// Data Kecamatan dan Desa

const regionData = {
    "Bandung": {
        "Cidadap": ["Desa Cidadap 1", "Desa Cidadap 2"],
        "Cibeunying": ["Desa Cibeunying 1", "Desa Cibeunying 2"],
        "Sumur Bandung": ["Desa Sumur Bandung 1", "Desa Sumur Bandung 2"]
    },
    "Bandung Kabupaten": {
        "Cicalengka": ["Desa Cicalengka 1", "Desa Cicalengka 2"],
        "Majalaya": ["Desa Majalaya 1", "Desa Majalaya 2"],
        "Soreang": ["Desa Soreang 1", "Desa Soreang 2"],
        "Kecamatan Bojongsoang": ["Desa Bojongsoang", "Desa Lengkong", "Desa Tegalluar"]
    },
    "Bandung Barat Kabupaten": {
        "Ngamprah": ["Desa Ngamprah 1", "Desa Ngamprah 2"],
        "Cimahi Selatan": ["Desa Cimahi Selatan 1", "Desa Cimahi Selatan 2"],
        "Padalarang": ["Desa Padalarang 1", "Desa Padalarang 2"]
    }
};



// Update dropdown Kecamatan berdasarkan Kota yang dipilih
document.getElementById('city').addEventListener('change', function () {
    const city = this.value;
    const districtSelect = document.getElementById('district');
    const villageSelect = document.getElementById('village');

    // Kosongkan dropdown
    districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
    villageSelect.innerHTML = '<option value="">Pilih Desa</option>';
    villageSelect.disabled = true;

    if (city && regionData[city]) {
        Object.keys(regionData[city]).forEach(district => {
            const option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        });
        districtSelect.disabled = false;
    } else {
        districtSelect.disabled = true;
    }
});

// Update dropdown Desa berdasarkan Kecamatan yang dipilih
document.getElementById('district').addEventListener('change', function () {
    const city = document.getElementById('city').value;
    const district = this.value;
    const villageSelect = document.getElementById('village');

    // Kosongkan dropdown Desa
    villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

    if (district && regionData[city][district]) {
        regionData[city][district].forEach(village => {
            const option = document.createElement('option');
            option.value = village;
            option.textContent = village;
            villageSelect.appendChild(option);
        });
        villageSelect.disabled = false;
    } else {
        villageSelect.disabled = true;
    }
});
document.addEventListener("DOMContentLoaded", function () {
    // Load data alamat dari server
    function loadAddress() {
        fetch("view_address.php")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const address = data.address;

                    // Isi form dengan data yang diterima
                    document.getElementById("city").value = address.city;
                    document.getElementById("district").value = address.district;
                    document.getElementById("village").value = address.village;
                    document.getElementById("address").value = address.address;
                    document.getElementById("fullAddress").textContent = address.full_address || "Alamat belum tersedia.";
                } else {
                    console.error("Gagal memuat alamat:", data.message);
                }
            })
            .catch(error => console.error("Terjadi kesalahan:", error));
    }

    // Simpan data alamat ke server
    document.getElementById("saveButton").addEventListener("click", function () {
        const city = document.getElementById("city").value;
        const district = document.getElementById("district").value;
        const village = document.getElementById("village").value;
        const address = document.getElementById("address").value;

        if (!city || !district || !village || !address) {
            alert("Semua kolom alamat wajib diisi!");
            return;
        }
 // Kirim data ke server menggunakan Fetch API
 fetch("process_address.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ city, district, village, address })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Alamat berhasil disimpan!");
                location.reload(); // Reload halaman untuk memperbarui "Alamat Lengkap"
            } else {
                alert("Gagal menyimpan alamat: " + data.message);
            }
        })
        .catch(error => {
            console.error("Terjadi kesalahan:", error);
        });
});
    // Panggil fungsi untuk load data alamat
    loadAddress();
});



    // Mengaktifkan input file ketika ikon kamera diklik
    document.getElementById('cameraIcon').addEventListener('click', function() {
        document.getElementById('fileInput').click(); // Memicu file input untuk memilih gambar
    });

    // Menangani proses upload gambar saat pengguna memilih file
    document.getElementById('fileInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            // Membaca gambar dan menampilkannya di profil
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePic').src = e.target.result; // Menampilkan gambar baru
            };
            reader.readAsDataURL(file);

            // Kirim gambar ke server untuk disimpan (menggunakan AJAX atau form submission)
            const formData = new FormData();
            formData.append('profile_picture', file);

            fetch('upload_profile.php', { // Menggunakan file PHP yang telah dibuat sebelumnya
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Foto profil berhasil diupdate');
                } else {
                    alert('Gagal mengupdate foto profil');
                }
            })
            .catch(error => {
                console.error('Terjadi kesalahan:', error);
            });
        }
    });
    
        document.addEventListener("DOMContentLoaded", function () {
            const editButton = document.getElementById("editButton");
            const saveButton = document.getElementById("saveButton");
            const cancelButton = document.getElementById("cancelButton");
            const inputs = document.querySelectorAll("input");
            
            
                // Fungsi untuk membatalkan edit
      cancelButton.addEventListener("click", function () {
        inputs.forEach(input => {
          input.setAttribute("readonly", "true");
          input.classList.remove("border-blue-500");
        });
        editButton.classList.remove("hidden");
        saveButton.classList.add("hidden");
        cancelButton.classList.add("hidden");
        cameraIcon.classList.add("hidden");
      });

            // Fungsi untuk mengaktifkan mode edit
            editButton.addEventListener("click", function () {
                inputs.forEach(input => {
                    input.removeAttribute("readonly");
                    input.classList.add("border-blue-500");
                });
                editButton.classList.add("hidden");
                saveButton.classList.remove("hidden");
                cancelButton.classList.remove("hidden");
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

             const profilePicInput = document.getElementById("profile_picture");
        if (profilePicInput && profilePicInput.files[0]) {
            formData.append("profile_picture", profilePicInput.files[0]);
        }
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
