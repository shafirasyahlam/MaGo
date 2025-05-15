<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../user/dashboard.php');
    exit();
}

// Ambil semua data mahasiswa
$stmt = $pdo->query("SELECT m.*, u.username FROM mahasiswa m JOIN users u ON m.user_id = u.id");
$mahasiswa = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Atau path relatif ke root project -->
    <link rel="stylesheet" href="<?php echo $_SERVER['DOCUMENT_ROOT']; ?>/mahasiswa-app/assets/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (Admin)</p>
        
        <div class="dashboard-section">
            <h2>Daftar Mahasiswa</h2>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Tanggal Lahir</th>
                        <th>Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mahasiswa as $key => $m): ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= htmlspecialchars($m['username']) ?></td>
                        <td><?= htmlspecialchars($m['nama']) ?></td>
                        <td><?= htmlspecialchars($m['nim']) ?></td>
                        <td><?= htmlspecialchars($m['tanggal_lahir']) ?></td>
                        <td>
                            <?php if ($m['latitude'] && $m['longitude']): ?>
                                <a href="https://maps.google.com/?q=<?= $m['latitude'] ?>,<?= $m['longitude'] ?>" target="_blank">Lihat Peta</a>
                            <?php else: ?>
                                Tidak ada data
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="view_user.php?id=<?= $m['id'] ?>" class="btn btn-view">Detail</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>