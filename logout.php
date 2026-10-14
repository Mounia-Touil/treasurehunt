<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TreasureHunt - Logout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <img src="treasure-hunter.png" alt="Treasure Hunt Banner" class="banner">
    <div class="header-content">
        <div class="header-left">
            <img src="Logo.png" alt="Treasure Hunt Logo" class="logo">
            <h1>TreasureHunt Auction System</h1>
        </div>
    </div>
</header>

<div class="main-content">
    <h2> Logging you out...</h2>
</div>

<footer>
    <p>&copy; 2025 TreasureHunt | <a href="Contact.html">Contact</a> | <a href="Help.html">Help</a></p>
</footer>

<script>

localStorage.removeItem("isLoggedIn");
localStorage.removeItem("cartItems");
localStorage.setItem("cartCount", "0");


setTimeout(() => {
    window.location.href = "index.html"; // or "TreasureHunt.php" if using PHP homepage
}, 2000);
</script>

</body>
</html>
