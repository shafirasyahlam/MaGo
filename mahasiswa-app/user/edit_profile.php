<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Hanya user yang bisa akses
if ($_SESSION['role'] !== 'user') {
    header('Location: ../admin/dashboard.php');
    exit();
}


$errors = [];
$success = false;

// Ambil data mahasiswa user ini
$stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$mahasiswa = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $nim = trim($_POST['nim']);
    $tanggal_lahir = trim($_POST['tanggal_lahir']);
    $alamat = trim($_POST['alamat']);
    $telepon = trim($_POST['telepon']);
    $kesukaan = trim($_POST['kesukaan']);
    
    // Validasi
    if (empty($nama)) $errors['nama'] = 'Nama harus diisi';
    if (empty($nim)) $errors['nim'] = 'NIM harus diisi';
    if (empty($tanggal_lahir)) $errors['tanggal_lahir'] = 'Tanggal lahir harus diisi';
    if (empty($alamat)) $errors['alamat'] = 'Alamat harus diisi';
    if (empty($telepon)) $errors['telepon'] = 'Telepon harus diisi';
    
    if (empty($errors)) {
        if ($mahasiswa) {
            // Update data
            $stmt = $pdo->prepare("UPDATE mahasiswa SET nama = ?, nim = ?, tanggal_lahir = ?, alamat = ?, telepon = ?, kesukaan = ? WHERE user_id = ?");
            $stmt->execute([$nama, $nim, $tanggal_lahir, $alamat, $telepon, $kesukaan, $_SESSION['user_id']]);
        } else {
            // Insert data baru
            $stmt = $pdo->prepare("INSERT INTO mahasiswa (user_id, nama, nim, tanggal_lahir, alamat, telepon, kesukaan) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $nama, $nim, $tanggal_lahir, $alamat, $telepon, $kesukaan]);
        }
        $success = true;
        header('Location: dashboard.php?updated=1');
        exit();
    }
}

if (isset($_GET['location_saved'])) {
    echo '<div class="alert success">Lokasi berhasil diperbarui!</div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Edit Profil Mahasiswa</h1>
        
        <?php if ($success): ?>
            <div class="alert success">Profil berhasil diperbarui!</div>
        <?php endif; ?>
        
        <form action="edit_profile.php" method="post">
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($mahasiswa['nama'] ?? '') ?>" required>
                <?php if (isset($errors['nama'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['nama']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="nim">NIM</label>
                <input type="text" id="nim" name="nim" value="<?= htmlspecialchars($mahasiswa['nim'] ?? '') ?>" required>
                <?php if (isset($errors['nim'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['nim']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= htmlspecialchars($mahasiswa['tanggal_lahir'] ?? '') ?>" required>
                <?php if (isset($errors['tanggal_lahir'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['tanggal_lahir']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" required><?= htmlspecialchars($mahasiswa['alamat'] ?? '') ?></textarea>
                <?php if (isset($errors['alamat'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['alamat']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="telepon">Telepon</label>
                <input type="tel" id="telepon" name="telepon" value="<?= htmlspecialchars($mahasiswa['telepon'] ?? '') ?>" required>
                <?php if (isset($errors['telepon'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['telepon']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="kesukaan">Kesukaan</label>
                <input type="text" id="kesukaan" name="kesukaan" value="<?= htmlspecialchars($mahasiswa['kesukaan'] ?? '') ?>">
            </div>
            
            <button type="submit" class="btn">Simpan Perubahan</button>
            <a href="dashboard.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>