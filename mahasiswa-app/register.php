<?php
require_once 'config/database.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validasi
    if (empty($username)) $errors['username'] = 'Username harus diisi';
    if (empty($email)) $errors['email'] = 'Email harus diisi';
    if (empty($password)) $errors['password'] = 'Password harus diisi';
    if ($password !== $confirm_password) $errors['confirm_password'] = 'Password tidak cocok';
    
    // Cek username/email sudah ada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetchColumn() > 0) {
        $errors['general'] = 'Username atau email sudah terdaftar';
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password])) {
            header('Location: login.php?registered=1');
            exit();
        } else {
            $errors['general'] = 'Gagal mendaftar. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Aplikasi Mahasiswa</title>
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Atau path relatif ke root project -->
    <link rel="stylesheet" href="<?php echo $_SERVER['DOCUMENT_ROOT']; ?>/mahasiswa-app/assets/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Register</h1>
        
        <?php if (isset($errors['general'])): ?>
            <div class="alert error"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>
        
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <?php if (isset($errors['username'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['username']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <?php if (isset($errors['password'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['password']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['confirm_password']) ?></span>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>
        
        <p class="register-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>