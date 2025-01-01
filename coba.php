<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toggle Button Example</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function toggleButtons() {
      const editButton = document.getElementById('editButton');
      const cancelButton = document.getElementById('cancelButton');
      
      // Toggle visibility of buttons
      editButton.classList.toggle('hidden');
      cancelButton.classList.toggle('hidden');
    }
  </script>
</head>
<body class="flex justify-center items-center min-h-screen bg-gray-100">

  <!-- Container -->
  <div class="flex space-x-4">
    <!-- Tombol Edit -->
    <button id="editButton" onclick="toggleButtons()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
      Edit
    </button>

    <!-- Tombol Batal (Awalnya tersembunyi) -->
    <button id="cancelButton" onclick="toggleButtons()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 hidden">
      Batal
    </button>
  </div>

</body>
</html>
