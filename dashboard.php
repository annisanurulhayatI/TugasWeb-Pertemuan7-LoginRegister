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

foreach ($users as $user) {
    if ($user['id'] == $_SESSION['user_id']) {
        $currentUser = $user;
        break;
    }
}

if (!$currentUser) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$nama = htmlspecialchars($currentUser['nama'], ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($currentUser['email'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>

    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <main class="dashboard">

        <div class="dashboard__card">

            <div class="dashboard__icon">
                <i class="fi fi-rr-user"></i>
            </div>

            <p class="dashboard__welcome">Selamat datang</p>

            <h1><?= $nama ?></h1>

            <p class="dashboard__email">
                <?= $email ?>
            </p>

            <div class="dashboard__info">

                <div class="dashboard__item">
                    <div class="dashboard__item-icon">
                        <i class="fi fi-rr-user"></i>
                    </div>

                    <div>
                        <span>Nama Lengkap</span>
                        <strong><?= $nama ?></strong>
                    </div>
                </div>

                <div class="dashboard__item">
                    <div class="dashboard__item-icon">
                        <i class="fi fi-rr-envelope"></i>
                    </div>

                    <div>
                        <span>Email</span>
                        <strong><?= $email ?></strong>
                    </div>
                </div>

            </div>

            <div class="dashboard__actions">

                <a href="edit-profile.php" class="button">
                    <i class="fi fi-rr-edit"></i>
                    Edit Profile
                </a>

                <a href="logout.php" class="button button--outline">
                    <i class="fi fi-rr-sign-out-alt"></i>
                    Logout
                </a>

            </div>

        </div>

    </main>

</body>
</html>