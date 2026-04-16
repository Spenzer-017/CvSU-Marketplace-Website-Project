</main> <!-- Main Closer -->

<footer>

    <div class="footer-top">

        <!-- Brand column -->
        <div class="footer-brand">
            <a href="index.php" class="logo">CvSU <span style="color: #C0B87A;">Marketplace</span></a>
            <p>Your campus marketplace - buy & sell within the CvSU community.</p>
        </div>

        <!-- Quick links -->
        <div class="footer-col">
            <h5>Marketplace</h5>
            <ul>
                <li><a <?= ($user) ? 'href="browse.php"' : 'href="login.php"' ?>>Browse Listings</a></li>
                <li><a <?= ($user) ? 'href="sell.php"' : 'href="login.php"' ?>>Post an Item</a></li>
                <li><a <?= ($user) ? 'href="transactions.php"' : 'href="login.php"' ?>>Transactions</a></li>
            </ul>
        </div>

        <!-- Account links -->
        <div class="footer-col">
            <h5>Account</h5>
            <ul>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
                <li><a <?= ($user) ? 'href="profile.php"' : 'href="login.php"' ?>>My Profile</a></li>
            </ul>
        </div>

        <!-- Info links -->
        <div class="footer-col">
            <h5>Info</h5>
            <ul>
                <li><a href="info.php#about-us-section">About Us</a></li>
                <li><a href="info.php#faq-section">FAQ</a></li>
                <li><a href="info.php#contact-section">Contact</a></li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> CvSU Marketplace. Made by Thumbtack's Team.</p>
    </div>

</footer>

<?php
// Load page-specific JS (Uncomment if decided to put embedded scripts in external JS files)
// if (isset($activePage)) {
//     if ($activePage === 'dashboard') {
//         echo '<script src="js/dashboard.js"></script>';
//     } elseif ($activePage === 'browse') {
//         echo '<script src="js/browse.js"></script>';
//     } elseif ($activePage === 'home') {
//         echo '<script src="js/home.js"></script>';
//     } elseif ($activePage === 'transactions') {
//         echo '<script src="js/transactions.js"></script>';
//     } elseif ($activePage === 'sell') {
//         echo '<script src="js/sell.js"></script>';
//     } 
// }
?>

</body>

</html>