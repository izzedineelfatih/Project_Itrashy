<?php
require('config.php');

// Query untuk mendapatkan data leaderboard
$query = "
    SELECT 
        u.id, 
        u.username, 
        u.profile_picture, 
        COALESCE(SUM(o.total_berat_sampah), 0) AS total_weight
    FROM 
        users u
    LEFT JOIN 
        orders o ON u.id = o.user_id
    GROUP BY 
        u.id
    ORDER BY 
        total_weight DESC
    LIMIT 10
";

// Eksekusi query dengan error handling
try {
    $stmt = $pdo->query($query);
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $leaderboard = []; // Pastikan $leaderboard tetap didefinisikan
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 text-center border-b pb-4">🏆 Leaderboard</h1>
        <div class="mt-4 space-y-4">
            <?php if (!empty($leaderboard)): ?>
                <?php foreach ($leaderboard as $index => $user): ?>
                    <div class="flex items-center bg-gray-50 p-4 rounded-lg shadow-sm hover:bg-blue-50 transition">
                        <div class="flex items-center justify-center bg-blue-500 text-white font-bold text-xl w-12 h-12 rounded-full">
                            <?= $index + 1; ?>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <img src="<?= htmlspecialchars($user['profile_picture']); ?>" alt="Avatar" class="w-14 h-14 rounded-full border border-gray-300">
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-800"><?= htmlspecialchars($user['username']); ?></h3>
                            <p class="text-sm text-gray-500">Total Berat: <strong><?= htmlspecialchars($user['total_weight']); ?> Kg</strong></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500 text-center">Belum ada data untuk ditampilkan.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
