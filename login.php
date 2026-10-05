<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

$emailCookie = $_COOKIE['remember_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $file = 'users.json';
        $users = [];

        if (file_exists($file)) {
            $json = file_get_contents($file);
            $users = json_decode($json, true);

            if (!is_array($users)) {
                $users = [];
            }
        }

        $userFound = null;

        foreach ($users as $user) {
            if (
                strtolower($user['email']) === strtolower($email) &&
                password_verify($password, $user['password'])
            ) {
                $userFound = $user;
                break;
            }
        }

        if ($userFound) {
            $_SESSION['user_id'] = $userFound['id'];

            if ($remember) {
                setcookie(
                    'remember_email',
                    htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
                    time() + (30 * 24 * 60 * 60),
                    '/'
                );
            } else {
                setcookie(
                    'remember_email',
                    '',
                    time() - 3600,
                    '/'
                );
            }

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}

$emailValue = htmlspecialchars(
    $_POST['email'] ?? $emailCookie,
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <main class="auth">

        <div class="auth__card">

            <div class="auth__icon">
                <i class="fi fi-rr-lock"></i>
            </div>

            <h1>Selamat Datang</h1>

            <p class="auth__subtitle">
                Login untuk melanjutkan
            </p>

            <?php if ($error): ?>
                <div class="message message--error">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

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

                <div class="form-group">
                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <div class="remember">
                    <label for="remember">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                        >
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="button">
                    <i class="fi fi-rr-sign-in-alt"></i>
                    Login
                </button>

            </form>

            <p class="auth__footer">
                Belum punya akun?
                <a href="register.php">Daftar</a>
            </p>

        </div>

    </main>

</body>
</html>