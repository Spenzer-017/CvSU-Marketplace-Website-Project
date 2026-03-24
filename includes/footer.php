</main> <!-- Main Closer -->

<footer>

    <div class="footer-top">

        <!-- Brand column -->
        <div class="footer-brand">
            <a href="/index.php" class="logo">CvSU <span style="color: #C0B87A;">Marketplace</span></a>
            <p>Your campus marketplace - buy, sell, and trade within the CvSU community.</p>
        </div>

        <!-- Quick links -->
        <div class="footer-col">
            <h5>Marketplace</h5>
            <ul>
                <li><a href="/browse.php">Browse Listings</a></li>
                <li><a href="/sell.php">Post an Item</a></li>
                <li><a href="/categories.php">Categories</a></li>
            </ul>
        </div>

        <!-- Account links -->
        <div class="footer-col">
            <h5>Account</h5>
            <ul>
                <li><a href="/login.php">Login</a></li>
                <li><a href="/signup.php">Sign Up</a></li>
                <li><a href="/profile.php">My Profile</a></li>
            </ul>
        </div>

        <!-- Info links -->
        <div class="footer-col">
            <h5>Info</h5>
            <ul>
                <li><a href="/about.php">About Us</a></li>
                <li><a href="/faq.php">FAQ</a></li>
                <li><a href="/contact.php">Contact</a></li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> CvSU Marketplace. Made by Thumbtack's Team.</p>
    </div>

</footer>

<?php
// Load page-specific JS
if (isset($activePage)) {
    if ($activePage === 'dashboard') {
        echo '<script src="/cvsu-marketplace/js/dashboard.js"></script>';
    } elseif ($activePage === 'browse') {
        echo '<script src="/cvsu-marketplace/js/browse.js"></script>';
    } elseif ($activePage === 'home') {
        echo '<script src="/cvsu-marketplace/js/home.js"></script>';
    } elseif ($activePage === 'cart') {
        echo '<script src="/cvsu-marketplace/js/cart.js"></script>';
    } elseif ($activePage === 'sell') {
        echo '<script src="/cvsu-marketplace/js/sell.js"></script>';
    } 
}
?>

</body>

</html>