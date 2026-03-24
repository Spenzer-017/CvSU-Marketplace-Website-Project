<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CvSU Marketplace</title>
    <link rel="stylesheet" href="/cvsu-marketplace/css/style.css?v=<?php echo time(); ?>" /> <!--Remove script at deployment -->
    <script type="text/javascript" src="/cvsu-marketplace/js/includes.js" defer></script>
</head>

<body>

    <header>

        <!-- Logo -->
        <a href="/index.php" class="logo">
            CvSU <span>Marketplace</span>
        </a>

        <!-- Navigation links -->
        <nav>
            <!-- Logged In -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/cvsu-marketplace/index.php" <?= ($activePage ?? '') === 'home' ? 'class="active"' : '' ?>>Home</a>
                <a href="/cvsu-marketplace/dashboard.php" <?= ($activePage ?? '') === 'dashboard' ? 'class="active"' : '' ?>>Dashboard</a>
                <a href="/cvsu-marketplace/browse.php" <?= ($activePage ?? '') === 'browse' ? 'class="active"' : '' ?>>Browse</a>
                <a href="/cvsu-marketplace/sell.php" <?= ($activePage ?? '') === 'sell' ? 'class="active"' : '' ?>>Sell</a>
                <a href="/cvsu-marketplace/cart.php" <?= ($activePage ?? '') === 'cart' ? 'class="active"' : '' ?>>Cart</a>
                <a href="/cvsu-marketplace/profile.php" <?= ($activePage ?? '') === 'profile' ? 'class="active"' : '' ?>>Profile</a>
                <a href="/cvsu-marketplace/logout.php" class="btn-login">Logout</a>

                <!-- Guest -->
            <?php else: ?>
                <a href="/cvsu-marketplace/index.php" <?= ($activePage ?? '') === 'home' ? 'class="active"' : '' ?>>Home</a>
                <a href="/cvsu-marketplace/login.php" class="btn-login">Login</a>
                <a href="/cvsu-marketplace/signup.php" class="btn-signup">Sign Up</a>
            <?php endif; ?>
        </nav>

        <!-- Theme Switch -->
        <button id="theme-switch">
            <img src="/cvsu-marketplace/assets/svg/darkmode-icon.svg" alt="darkmode-icon">
            <img src="/cvsu-marketplace/assets/svg/lightmode-icon.svg" alt="lightmode-icon">
        </button>

        <!-- Hamburger menu toggle (For mobile) -->
        <div class="menu-toggle">
            <div></div>
            <div></div>
            <div></div>
        </div>

    </header>

    <!-- Main starter -->
    <main>