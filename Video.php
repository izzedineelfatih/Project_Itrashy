<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require 'config.php';
// Ambil data video berdasarkan ID dari URL
$video = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$video) {
        echo "Video tidak ditemukan!";
        exit();
    }
} else {
    echo "ID video tidak valid!";
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
  <div class="flex h-screen overflow-hidden">
      <?php include 'sidebar.php'; ?>
      <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
          <?php include 'header.php'; ?>
          <div class="flex flex-1 overflow-auto p-4">
              <div class="flex-1 p-6 space-y-4 overflow-auto">
                  <div id="video-container" class="relative w-full h-fit">
                      <!-- Display the first video dynamically -->
                      <?php if (!empty($video)) : ?>
                        <video id="video" class="w-full h-64 object-cover" controls>
    <source src="uploads/videoedukasi.mp4?= $video['video_url'] ?>" type="video/mp4">
    Your browser does not support the video tag.
</video>

                          <div class="p-6">
                              <h2 class="text-xl font-bold"><?= htmlspecialchars($video['title']) ?></h2>
                              <p class="text-gray-500 mt-2"><?= htmlspecialchars($video['content']) ?></p>
                          </div>
                      <?php else : ?>
                          <p>No videos available.</p>
                      <?php endif; ?>
                  </div>
              </div>
             
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
