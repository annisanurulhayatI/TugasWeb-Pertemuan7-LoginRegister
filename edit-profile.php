<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$file = 'users.json';
$users = [];

if (file_exists($file)) {
    $json = file_get_contents($file);
    $users = json_decode($json, true);

    if (!is_array($users)) {
        $users = [];
    }
}

$currentUser = null;
$currentIndex = null;

foreach ($users as $index => $user) {
    if ($user['id'] == $_SESSION['user_id']) {
        $currentUser = $user;
        $currentIndex = $index;
        break;
    }
}

if (!$currentUser) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

$namaValue = $currentUser['nama'];
$emailValue = $currentUser['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $namaValue = $nama;
    $emailValue = $email;

    if (empty($nama) || empty($email)) {
        $error = 'Nama dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $emailTerdaftar = false;

        foreach ($users as $index => $user) {
            if (
                $index != $currentIndex &&
                strtolower($user['email']) === strtolower($email)
            ) {
                $emailTerdaftar = true;
                break;
            }
        }

        if ($emailTerdaftar) {
            $error = 'Email sudah digunakan oleh akun lain.';
        } else {
            $users[$currentIndex]['nama'] = $nama;
            $users[$currentIndex]['email'] = $email;

            file_put_contents(
                $file,
                json_encode($users, JSON_PRETTY_PRINT),
                LOCK_EX
            );

            $success = 'Profil berhasil diperbarui.';

            $namaValue = $nama;
            $emailValue = $email;
        }
    }
}

$namaValue = htmlspecialchars($namaValue, ENT_QUOTES, 'UTF-8');
$emailValue = htmlspecialchars($emailValue, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Profile</title>

    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <main class="auth">

        <div class="auth__card">

            <div class="auth__icon">
                <i class="fi fi-rr-edit"></i>
            </div>

            <h1>Edit Profile</h1>

            <p class="auth__subtitle">
                Perbarui informasi akun kamu
            </p>

            <?php if ($error): ?>
                <div class="message message--error">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="message message--success">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        value="<?= $namaValue ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Masukkan email"
                        value="<?= $emailValue ?>"
                        required
                    >
                </div>

                <button type="submit" class="button">
                    <i class="fi fi-rr-check"></i>
                    Simpan Perubahan
                </button>

            </form>

            <p class="auth__footer">
                <a href="dashboard.php">Kembali ke Dashboard</a>
            </p>

        </div>

    </main>

</body>
</html>