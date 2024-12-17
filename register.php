<?php
session_start();
require_once 'config.php';

// Cek jika role belum dipilih
if (!isset($_SESSION['selected_role'])) {
    header('Location: role.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $errors = [];

    // Validasi username
    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Username harus antara 3-50 karakter";
    }
    if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $errors[] = "Username hanya boleh berisi huruf, angka, dan underscore";
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    // Cek email sudah terdaftar
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Email sudah terdaftar";
    }

    // Validasi nomor telepon
    if (!preg_match("/^[0-9]{10,15}$/", $phone_number)) {
        $errors[] = "Nomor telepon harus berupa angka dan 10-15 karakter";
    }

    // Validasi password
    if (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Password tidak cocok";
    }

    // Jika tidak ada error, proses pendaftaran
    if (empty($errors)) {
        try {
            // Mulai transaksi
            $pdo->beginTransaction();

            // Get role_id
            $stmt = $pdo->prepare("SELECT id FROM roles WHERE role_name = ?");
            $stmt->execute([$_SESSION['selected_role']]);
            $role = $stmt->fetch();

            if (!$role) {
                throw new Exception("Role tidak valid");
            }

            // Tentukan deskripsi berdasarkan role
            $description = ($_SESSION['selected_role'] == 'Bisnis') ? 'Pengguna Bisnis' : 'Pengguna Individu';

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert data ke tabel `users`
            $stmt = $pdo->prepare("INSERT INTO users (username, email, phone_number, password, role_id, description) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $phone_number, $hashedPassword, $role['id'], $description]);

            $pdo->commit();

            $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
            header('Location: login.php');
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-white">
    <div class="flex flex-col md:flex-row min-h-screen">
        <div class="relative w-full md:w-1/2">
            <div class="relative w-full h-64 md:h-full p-4">
                <a href="role.php" class="absolute top-8 left-10 h-4 lg:top-12 lg:left-14">
                    <img src="assets/icon/back icon.png" alt="Back" class="w-6 h-6">
                </a>
                <img src="assets/image/orang buang sampah.jpg" alt="Background" class="w-full h-full object-cover rounded-xl">
                
                <div class="absolute bottom-10 left-10 lg:bottom-20 lg:left-14">
                    <div class="flex items-center mb-2">
                        <img src="assets/image/Logo Itrashy.png" alt="I-Trashy Logo" class="w-6 lg:w-12">
                    </div>
                    <h1 class="md:text-2xl font-bold text-white/80 mb-2">I-Trashy.</h1>
                    <p class="text-sm text-white/80 pr-20 lg:pr-0">Solusi pengelolaan sampah untuk rumah tangga dan bisnis</p>
                </div>
            </div>
        </div>

        <div class="flex-1 p-6 md:p-12 flex flex-col items-center justify-center">
            <div class="max-w-md mx-auto w-full">
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-bold pb-2">Welcome!</h1>
                    <p class="text-gray-400">Yuk daftar! Bersama wujudkan lingkungan hijau dan bersih</p>
                </div>

                <form method="POST" class="space-y-4">
                    <div>
                        <label for="username" class="text-gray-600">Username</label>
                        <input type="text" id="username" name="username" placeholder="Masukkan Username" required 
                            class="w-full px-4 py-2 rounded-xl bg-[#f5f7fa] mt-2"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>

                    <div>
                        <label for="email" class="text-gray-600">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan Email" required
                            class="w-full px-4 py-2 rounded-xl bg-[#f5f7fa] mt-2"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div>
                    <label for="phone_number" class="text-gray-600">Nomor Telepon</label>
                    <input type="text" id="phone_number" name="phone_number" placeholder="Masukkan Nomor Telepon" required
                    class="w-full px-4 py-2 rounded-xl bg-[#f5f7fa] mt-2"
                    value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">
                    <span id="phone-warning" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="relative">
                        <label for="password" class="text-gray-600">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan Password" required
                            class="w-full px-4 py-2 rounded-xl bg-[#f5f7fa] mt-2">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-6 pt-4 opacity-50">
                            <img src="assets/icon/eye icon.png" alt="eye" class="w-6">
                        </button>
                    </div>

                    <div class="relative">
                        <label for="confirmPassword" class="text-gray-600">Konfirmasi Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Ulangi Password" required
                            class="w-full px-4 py-2 rounded-xl bg-[#f5f7fa] mt-2">
                        <button type="button" onclick="togglePassword('confirmPassword')" class="absolute right-6 pt-4 opacity-50">
                            <img src="assets/icon/eye icon.png" alt="eye" class="w-6">
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="terms" name="terms" class="rounded border-gray-300" required>
                        <label for="terms" class="text-gray-600 text-sm">Saya setuju dengan <a href="#" class="text-blue-500">Ketentuan Penggunaan</a></label>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="w-full bg-[#1f6feb] text-white py-2 rounded-xl">Daftar</button>
                    </div>

                    <div class="text-center text-sm mt-4">
                        <p>Sudah punya akun? <a href="login.php" class="text-[#1f6feb]">Masuk di sini</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        
        document.addEventListener("DOMContentLoaded", () => {
        const phone_numberInput = document.getElementById("phone_number");
        const phone_numberWarning = document.getElementById("phone-warning");

        phone_numberInput.addEventListener("input", () => {
            const phone_numberValue = phone_numberInput.value;

            // Jika input bukan angka, hapus karakter terakhir dan tampilkan peringatan
            if (!/^\d*$/.test(phone_numberValue)) {
                phone_numberInput.value = phone_numberValue.slice(0, -1);
                phone_numberWarning.textContent = "Nomor telepon hanya boleh berupa angka!";
            } else {
                phone_numberWarning.textContent = ""; // Hapus peringatan jika input valid
            }
        });
    });

    </script>
</body>
</html>