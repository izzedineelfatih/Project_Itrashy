<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy - Lupa Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Left Side -->
        <div class="flex-1 bg-cover bg-center relative text-white p-8" style="background-image: url('assets/image/orang buang sampah.jpg')">
            <a href="#" class="absolute top-8 left-8">
                <img src="assets/icon/back icon.png" alt="Back" class="w-6 h-6">
            </a>
            <div class="absolute bottom-20 max-w-xs">
                <img src="assets/image/Logo Itrashy.png" alt="I-Trashy Logo" class="w-16 h-20 mb-4">
                <h1 class="text-2xl font-bold">I-Trashy.</h1>
                <p class="text-sm font-light leading-relaxed">Solusi pengelolaan sampah untuk rumah tangga dan bisnis</p>
            </div>
        </div>
        <!-- Right Side -->
        <div class="flex-1 bg-gray-50 flex flex-col justify-center p-8">
            <div class="max-w-md mx-auto">
                <h2 class="text-2xl font-bold mb-4">Lupa Password<br>
                    <span class="text-base font-normal">Masukkan email Anda untuk memverifikasi akun</span>
                </h2>
                <form>
                    <div class="mb-4">
                        <input type="email" placeholder="Email" required class="w-full px-4 py-2 rounded-lg border border-gray-300">
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition">Lanjut</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>