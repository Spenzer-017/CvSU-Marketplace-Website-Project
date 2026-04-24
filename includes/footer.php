</main> <!-- Main Closer -->

<footer>

    <div class="footer-top">

        <!-- Brand column -->
        <div class="footer-brand">
            <a href="index.php" class="logo">
                <span>Kabsu<span class="logo-accent">hayan</span></span>
            </a>
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

<!-- Confirmation Modals -->
<div class="modal-overlay" id="confirmModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-box">
 
    <div class="modal-icon" id="modalIcon">
      <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M109-120q-11 0-20-5.5T75-140q-5-9-5.5-19.5T75-180l370-640q6-10 15.5-15t19.5-5q10 0 19.5 5t15.5 15l370 640q6 10 5.5 20.5T885-140q-5 9-14 14.5t-20 5.5H109Zm69-80h604L480-720 178-200Zm330.5-51.5Q520-263 520-280t-11.5-28.5Q497-320 480-320t-28.5 11.5Q440-297 440-280t11.5 28.5Q463-240 480-240t28.5-11.5Zm0-120Q520-383 520-400v-120q0-17-11.5-28.5T480-560q-17 0-28.5 11.5T440-520v120q0 17 11.5 28.5T480-360q17 0 28.5-11.5ZM480-460Z"/></svg>
    </div>
 
    <div class="modal-title" id="modalTitle">Are you sure?</div>
    <div class="modal-message" id="modalMessage"></div>
 
    <div class="modal-actions">
      <button class="modal-btn-cancel" id="modalCancel">Cancel</button>
      <button class="modal-btn-confirm" id="modalConfirm">Confirm</button>
    </div>
 
  </div>
</div>

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

<script>
    (function () {
    const overlay = document.getElementById('confirmModal');
    const msgEl = document.getElementById('modalMessage');
    const iconEl = document.getElementById('modalIcon');
    const btnConfirm = document.getElementById('modalConfirm');
    const btnCancel = document.getElementById('modalCancel');
    
    let pendingAction = null;
    
    function openModal(message, onConfirm, isGreen) {
        msgEl.textContent = message;
        pendingAction = onConfirm;
    
        if (isGreen) {
        btnConfirm.classList.add('modal-btn-green');
        iconEl.classList.add('modal-icon-green');
        } else {
        btnConfirm.classList.remove('modal-btn-green');
        iconEl.classList.remove('modal-icon-green');
        }
    
        overlay.classList.add('modal-open');
        btnCancel.focus();
    }
    
    function closeModal() {
        overlay.classList.remove('modal-open');
        pendingAction = null;
    }
    
    btnConfirm.addEventListener('click', function () {
        const action = pendingAction;
        closeModal();
        if (action) action();
    });
    
    btnCancel.addEventListener('click', closeModal);
    
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
    });
    
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('modal-open')) closeModal();
    });
    
    // Intercepts submit, shows modal, submits for real only on Confirm.
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const msg  = form.dataset.confirm;
        if (!msg) return;
    
        e.preventDefault();
        const isGreen = 'confirmGreen' in form.dataset;
    
        openModal(msg, function () {
        delete form.dataset.confirm;
        form.submit();
        }, isGreen);
    });
    
    // Intercepts click, shows modal, re-fires click only on Confirm.
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-confirm]');
        if (!btn) return;
    
        e.preventDefault();
        const msg     = btn.dataset.confirm;
        const isGreen = 'confirmGreen' in btn.dataset;
    
        openModal(msg, function () {
        delete btn.dataset.confirm;
        btn.click();
        }, isGreen);
    });
    
    })();
</script>

</body>

</html>