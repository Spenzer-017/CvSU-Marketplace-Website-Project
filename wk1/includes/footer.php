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
                <li><a href="browse.php">Browse Listings</a></li>
                <li><a href="sell.php">Post an Item</a></li>
                <li><a href="transactions.php">Transactions</a></li>
            </ul>
        </div>

        <!-- Account links -->
        <div class="footer-col">
            <h5>Account</h5>
            <ul>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
                <li><a href="profile.php">My Profile</a></li>
            </ul>
        </div>

        <!-- Info links -->
        <div class="footer-col">
            <h5>Info</h5>
            <ul>
                <li><a href="about.php">About Us</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> CvSU Marketplace. Made by Thumbtack's Team.</p>
    </div>

</footer>

<!-- Move Later To A JS Script File -->
<script>
    const menuToggle = document.querySelector(".menu-toggle");
    const nav = document.querySelector("nav");

    let darkmode = localStorage.getItem("darkmode");
    const themeSwitch = document.getElementById("theme-switch");

    // Darkmode Enable Function
    const enableDarkmode = () => {
        document.body.classList.add("darkmode");
        localStorage.setItem("darkmode", "active");
    }

    // Darkmode Disable Function
    const disableDarkmode = () => {
        document.body.classList.remove("darkmode");
        localStorage.setItem("darkmode", null);
    }

    // Menu Toggle Event
    menuToggle.addEventListener("click", () => {
        nav.classList.toggle("open");
    });

    if (darkmode === "active") enableDarkmode();

    //Theme Swtich Event
    themeSwitch.addEventListener("click", () => {
        darkmode = localStorage.getItem("darkmode");
        darkmode !== "active" ? enableDarkmode() : disableDarkmode();
    });
</script>

</body>

</html>