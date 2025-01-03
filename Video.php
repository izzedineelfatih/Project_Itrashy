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
  <title>I-Trashy Video</title>
  <meta name="page-title" content="Video Edukasi">
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
              <!-- Left Section for Video -->
              <div class="flex-1 p-6 space-y-4 overflow-auto">
                  <div class=" overflow-hidden">
                      <!-- Video Thumbnail and Video -->
                      <div id="video-container" class="relative w-full h-fit">
                          <img id="video-thumbnail" src="assets/image/gambar4.png" alt="Video Thumbnail" class="w-full h-full cursor-pointer">
                          <video id="video" class="hidden w-full h-64 object-cover" controls>
                              <source src="path/to/your-video.mp4" type="video/mp4">
                              <source src="path/to/your-video.webm" type="video/webm">
                              Your browser does not support the video tag.
                          </video>
                      </div>
                      <div class="p-6">
                          <h2 class="text-xl font-bold">Tips Memilah Sampah Tanpa Ribet yang Bisa Dilakukan di Rumah Masing-Masing</h2>
                          <p class="text-gray-500 mt-2">Halo teman-teman! 👋 Sampah yang tidak terkelola dengan baik bisa berdampak buruk bagi lingkungan kita...</p>
                      </div>
                  </div>
              </div>

              <!-- Right Section for Other Videos -->
              <div id="rightSection" class="ml-5 w-full h-fit lg:w-1/3 bg-white rounded-lg shadow-lg p-6 space-y-4 lg:block hidden sticky top-4">
                  <h3 class="text-xl font-bold mb-4">Video Lainnya</h3>
                  <div class="space-y-4">
                      <!-- Video Card 1 -->
                      <div class="bg-white rounded-lg shadow p-4 items-start space-x-4">
                          <img src="assets/image/gambar6.png" alt="Video Thumbnail 1" class="w-full h-fit object-cover rounded-md">
                          <div>
                              <h4 class="font-semibold mt-2">Berapa lama sampah plastik dapat terurai? Yuk, cari tau</h4>
                              <p class="text-xs text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                          </div>
                      </div>
                      <!-- Video Card 2 -->
                      <div class="bg-white rounded-lg shadow p-4 items-start space-x-4">
                          <img src="assets/image/gambar5.png" alt="Video Thumbnail 2" class="w-full h-fit object-cover rounded-md">
                          <div>
                              <h4 class="font-semibold mt-2">Gerakan bersih - bersih pantai di Indonesia</h4>
                              <p class="text-xs text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                          </div>
                      </div>
                      <!-- Video Card 3 -->
                      <div class="bg-white rounded-lg shadow p-4 items-start space-x-4">
                          <img src="assets/image/gambar4.png" alt="Video Thumbnail 3" class="w-full h-fit object-cover rounded-md">
                          <div>
                              <h4 class="font-semibold mt-2">Kisah perjuangan pengumpul sampah plastik</h4>
                              <p class="text-xs text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Toggle Button -->
      <div class="absolute bottom-4 right-4 lg:hidden">
          <button id="toggleRightSection" class="bg-blue-500 text-white px-4 py-2 rounded-full shadow-lg">Video Lainnya</button>
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
