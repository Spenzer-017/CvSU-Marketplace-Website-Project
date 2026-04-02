<?php 
session_start();
$activePage = "home"; 
include "includes/header.php"; 
//unset($_SESSION['user_id']); // Test for guest navigation
$_SESSION['user_id'] = 1; // Test for logged-in user navigation
?>

<h1>
    <center>Wow A Darkmode Feature!</center>
</h1>

<?php include "includes/footer.php"; ?>