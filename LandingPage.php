<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-black text-white">
    <div class="relative w-full h-screen bg-cover bg-center" style="background-image: url('your-background-image.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        <nav class="flex justify-between items-center p-5">
            <div class="logo">
                <img src="logo.png" alt="I-Trashy" class="w-12">
            </div>
            <div>
                <a href="#" class="text-white mx-3 hover:underline">Beranda</a>
                <a href="#" class="text-white mx-3 hover:underline">Layanan</a>
                <a href="#" class="text-white mx-3 hover:underline">Tentang Kami</a>
                <a href="#" class="text-white mx-3 hover:underline">Kontak</a>
                <a href="#" class="text-white mx-3 hover:underline">FAQ</a>
                <button class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Sign in</button>
            </div>
        </nav>
        <div class="absolute inset-0 flex items-center justify-center text-center">
            <div>
                <h1 class="text-3xl mb-5">Kumpulkan Dan Pilah Sampahmu<br>Biar Kami Yang Jemput & Olah</h1>
                <p class="text-lg mb-8">Mari kelola sampah kita sejak dini.</p>
                <a href="login.php" class="bg-green-500 text-white py-3 px-6 rounded hover:bg-green-600 text-lg">Coba Sekarang</a>
            </div>
        </div>
    </div>
</body>
</html>