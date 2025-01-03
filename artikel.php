<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>I-Trashy Artikel</title>
  <meta name="page-title" content="Artikel Edukasi">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['THICCCBOI'],
                    },
                },
            },
        };
    </script>
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
  <!-- Main Layout Container -->
  <div class="flex h-screen overflow-hidden">
      <!-- Sidebar -->
      <?php include 'sidebar.php'; ?>

      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
          <!-- Header -->
          <?php include 'header.php'; ?>

          <!-- Content Section -->
          <div class="flex flex-1 overflow-auto p-4">
            <!-- Left Section -->
            <div class="flex-1 p-6 space-y-4 overflow-auto ">
                <img src="assets/image/gambar2.png" alt="Main Article Image" class="w-full rounded-lg shadow-md">
                <p class="text-sm text-gray-600 text-right">Senin, 20 Januari 2024</p>
                <h1 class="text-2xl font-bold text-black">Yuk, Belajar Mengelola Sampah Sejak Dini Secara Mandiri</h1>
                <p class="text-black leading-relaxed">
                    Sampah, sering kali kita anggap sepele, padahal dampaknya terhadap lingkungan sangat besar. Coba bayangkan, sampah plastik yang kita buang hari ini akan tetap ada di bumi ini selama ratusan tahun ke depan. Oleh karena itu, penting bagi kita untuk belajar mengelola sampah sejak dini. Nah, artikel ini akan memberikan tips dan trik kekinian tentang bagaimana kita bisa mengelola sampah secara mandiri. Yuk, simak!
                </p>
                <h2 class="text-xl font-semibold text-black">Mengapa Harus Mulai dari Sekarang?</h2>
                <p class="text-black leading-relaxed">
                    Mengelola sampah bukan hanya tentang membuang sampah pada tempatnya, tetapi juga tentang mengurangi, menggunakan kembali, dan mendaur ulang. Dengan memulai sejak dini, kita bisa membuat kebiasaan baik yang akan berdampak besar bagi lingkungan di masa depan.
                </p>
                <ul class="list-decimal ml-6 text-black space-y-2">
                    <li><strong>Kurangi Penggunaan Plastik</strong> </li>
                     <p class="text-black">Bawa tas belanja sendiri dan pilih produk dengan kemasan minimal.</p>
                    <li><strong>Pisahkan Sampah Organik dan Anorganik</strong> </li>
                    <p>Ini mempermudah proses daur ulang.</p>
                    <li><strong>Daur Ulang Kreatif:</strong> </li>
                      <p>Ubah sampah menjadi barang berguna, seperti pot tanaman atau tempat penyimpanan.</p>
                </ul>
            </div>

            <!-- Right Section -->
            <div id="rightSection" class="ml-5 w-full h-fit lg:w-1/3 bg-white rounded-lg shadow-lg p-6 space-y-4 lg:block hidden sticky top-4">
              <h3 class="text-xl font-bold mb-4">Artikel Lainnya</h3>
              <div class="space-y-4">
                  <div class="bg-white rounded-lg shadow p-4 items-start space-x-4">
                      <img src="assets/image/gambar8.png" alt="Article Image" class="w-full h-fit object-cover rounded-lg">
                      <div class="flex-1">
                          <h4 class="font-semibold mt-2">Cara membuat kerajinan daur ulang dari barang bekas</h4>
                          <p class="text-xs text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                      </div>
                  </div>
                  <div class="bg-white rounded-lg shadow p-4 items-start space-x-4">
                      <img src="assets/image/gambar7.png" alt="Article Image" class="w-full h-fit object-cover rounded-lg">
                      <div class="flex-1">
                          <h4 class="font-semibold mt-2">Pupuk kompos untuk tanaman kesayangan Anda</h4>
                          <p class="text-xs text-gray-500 mt-1">Kompas.com • 02 Januari 2024</p>
                      </div>
                  </div>
                  <div class="bg-white rounded-lg shadow p-4 items-start space-x-4">
                      <img src="assets/image/gambar6.png" alt="Article Image"class="w-full h-fit object-cover rounded-lg">
                      <div class="flex-1">
                          <h4 class="font-semibold text-sm text-black">Bukan sulap bukan sihir, sampah bisa jadi listrik</h4>
                          <p class="text-xs text-gray-500 mt-1">Detik.com • 10 Januari 2024</p>
                      </div>
                  </div>
              </div>
          </div>
          

          <!-- Toggle Button -->
          <div class="absolute bottom-4 right-4 lg:hidden">
              <button id="toggleRightSection" class="bg-blue-500 text-white px-4 py-2 rounded-full shadow-lg">Lihat Artikel Lainnya</button>
          </div>
      </div>
  </div>

  <script>
    // JavaScript for Toggle Functionality
    document.getElementById('toggleRightSection').addEventListener('click', () => {
        const rightSection = document.getElementById('rightSection');
        rightSection.classList.toggle('hidden');
    });

    (async function initializePage() {
            async function initializeSidebar() {
                try {
                    const sidebarResponse = await fetch("sidebar.html");
                    const headerResponse = await fetch("header.html");
                    
                    if (sidebarResponse.ok && headerResponse.ok) {
                        document.getElementById("sidebar").innerHTML = await sidebarResponse.text();
                        document.getElementById("header").innerHTML = await headerResponse.text();
                        
                        // Initialize menu functionality
                        initializeMenu();
                    } else {
                        console.error("Error loading components");
                    }
                } catch (error) {
                    console.error("Error initializing components:", error);
                }
            }

            function initializeMenu() {
                const menuToggle = document.getElementById('menuToggle');
                const menuClose = document.getElementById('menuClose');
                const menu = document.getElementById('menu');
                const navLinks = document.querySelectorAll('.nav-link');
                const pageTitle = document.getElementById('pageTitle');
                const currentPageTitle = document.querySelector('[data-page-title]').dataset.pageTitle;

                // Mobile menu toggles
                if (menuToggle && menuClose && menu) {
                    menuToggle.addEventListener('click', () => menu.classList.remove('-translate-x-full'));
                    menuClose.addEventListener('click', () => menu.classList.add('-translate-x-full'));
                }

                // Set active menu item and initial page title
                navLinks.forEach(link => {
                    const linkText = link.querySelector('span').textContent;

                    if (linkText === currentPageTitle) {
                        link.classList.add('active');
                    }

                    link.addEventListener('click', () => {
                        // Set active state (without preventing navigation)
                        navLinks.forEach(l => l.classList.remove('active'));
                        link.classList.add('active');
                    });
                });

                // Update page title
                if (pageTitle) {
                    pageTitle.textContent = currentPageTitle === 'Dashboard' ? 'Halo, Atalaric👋' : currentPageTitle;
                }
            }


            await initializeSidebar();
        })();
  </script>
</body>
</html>
