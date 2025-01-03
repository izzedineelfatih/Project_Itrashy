<?php
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validasi input
    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong";
    }

    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong";
    }

    // Jika tidak ada error, lakukan proses login
    if (empty($errors)) {
        try {
            // Cari staff berdasarkan email
            $stmt = $pdo->prepare("SELECT * FROM staff WHERE email = ?");
            $stmt->execute([$email]);
            $staff = $stmt->fetch();

            // Verifikasi password
            if ($staff && password_verify($password, $staff['password'])) {
                // Login berhasil
                session_start();
                $_SESSION['staff_id'] = $staff['id'];
                $_SESSION['staff_email'] = $staff['email'];
                $_SESSION['staff_username'] = $staff['username']; // Menyimpan username
                $_SESSION['staff_role'] = $staff['role']; // Menyimpan peran (admin/driver)

                // Redirect berdasarkan peran (admin atau driver)
                if ($staff['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: driver_dashboard.php");
                }
                exit();
            } else {
                $errors[] = "Email atau password salah";
            }
        } catch (PDOException $e) {
            $errors[] = "Kesalahan login: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy - Staff Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-white">
    <div class="flex flex-col md:flex-row min-h-screen">
        <div class="relative w-full md:w-1/2">
            <div class="relative w-full h-64 md:h-full p-4">
                <a href="staff_register.php" class="absolute top-8 left-10 h-4 lg:top-12 lg:left-14">
                    <img src="assets/icon/back icon.png" alt="Back" class="w-6 h-6">
                </a>
                <img src="assets/image/orang buang sampah.jpg" alt="Background" class="w-full h-full object-cover rounded-xl">
                
                <div class="absolute bottom-10 left-10 lg:bottom-20 lg:left-14">
                    <div class="flex items-center mb-2">
                        <img src="assets/image/Logo Itrashy.png" alt="I-Trashy Logo" class="w-8 lg:w-12">
                    </div>
                    <h1 class="md:text-2xl font-bold text-white/80 mb-2">I-Trashy Staff.</h1>
                    <p class="text-sm text-white/80 pr-20 lg:pr-0">Panel Login Staff</p>
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
                    <h1 class="text-2xl font-bold pb-2">Staff Login</h1>
                    <p class="text-gray-400">Masuk ke panel I-Trashy</p>
                </div>

                <form method="POST" class="space-y-4">
                    <div>
                        <label for="email" class="text-gray-600">Email Staff</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan Email" required
                            class="w-full px-4 py-2 rounded-xl bg-[#f5f7fa] mt-2"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="relative">
                        <label for="password" class="text-gray-600">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan Password" required
                            class="w-full px-4 py-2 rounded-xl bg-[#f5f7fa] mt-2">
                    </div>

                    <div class="pt-8">
                        <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            Login
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-gray-500 mt-4">Belum punya akun? <a href="staff_register.php" class="text-blue-600">Daftar</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>