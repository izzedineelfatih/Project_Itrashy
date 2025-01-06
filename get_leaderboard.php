<?php
// Include koneksi database
require('config.php');

// Query SQL untuk mendapatkan data leaderboard
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

// Eksekusi query
$stmt = $pdo->query($query);
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
