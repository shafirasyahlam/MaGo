<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Hanya user yang bisa akses
if ($_SESSION['role'] !== 'user') {
    header('Location: ../admin/dashboard.php');
    exit();
}

// Ambil data mahasiswa user ini
$stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$mahasiswa = $stmt->fetch();

// Jika belum ada data, redirect ke edit profile
if (!$mahasiswa) {
    header('Location: edit_profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>User Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></p>
        
        <div class="profile-section">
            <h2>Profil Mahasiswa</h2>
            
            <div class="profile-info">
                <p><strong>Nama:</strong> <?= htmlspecialchars($mahasiswa['nama']) ?></p>
                <p><strong>NIM:</strong> <?= htmlspecialchars($mahasiswa['nim']) ?></p>
                <p><strong>Tanggal Lahir:</strong> <?= htmlspecialchars($mahasiswa['tanggal_lahir']) ?></p>
                <p><strong>Alamat:</strong> <?= htmlspecialchars($mahasiswa['alamat']) ?></p>
                <p><strong>Telepon:</strong> <?= htmlspecialchars($mahasiswa['telepon']) ?></p>
                <p><strong>Kesukaan:</strong> <?= htmlspecialchars($mahasiswa['kesukaan']) ?></p>
                
                    <!-- ... other fields ... -->
                    <?php if (!empty($mahasiswa['city'])): ?>
                        <p><strong>Location:</strong> 
                            <?= htmlspecialchars($mahasiswa['city']) ?>, 
                            <?= htmlspecialchars($mahasiswa['region']) ?>, 
                            <?= htmlspecialchars($mahasiswa['country']) ?>
                        </p>
                    <?php else: ?>
                        <p><strong>Location:</strong> Not specified yet. 
                            <a href="location.php">Set your location</a>
                        </p>
                    <?php endif; ?>

            </div>
            
            <div class="action-buttons">
                <a href="edit_profile.php" class="btn btn-edit">Edit Profil</a>
                <a href="location.php" class="btn btn-location">Update Lokasi</a>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>