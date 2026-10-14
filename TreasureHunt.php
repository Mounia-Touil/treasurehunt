<?php
session_start();
$isLoggedIn = isset($_SESSION['userid']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TreasureHunt - Home</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <img src="treasure-hunter.png" alt="Treasure Hunt Banner" class="banner">
    <div class="header-content">
        <div class="header-left">
            <img src="Logo.png" alt="Treasure Hunt Logo" class="logo">
            <h1>🏴‍☠️ TreasureHunt</h1>
        </div>
        <div id="cart-icon">
            <?php if ($isLoggedIn): ?>
                <a href="Cart.html">🛒 Cart (<span id="cart-count">0</span>)</a>
            <?php else: ?>
                <a href="#" onclick="checkLogin('Cart.html')">🛒 Cart</a>
            <?php endif; ?>
        </div>
    </div>

    <nav>
        <ul>
            <li><a href="TreasureHunt.php" class="nav-button active"> Home</a></li>
            <li><a href="UserDashboard.php" class="nav-button"> Profile</a></li>
            <li><a href="ViewItem.php" class="nav-button"> View Items</a></li>
            <li>
                <?php if ($isLoggedIn): ?>
                    <a href="SellItem.php" class="nav-button">Sell Item</a>
                <?php else: ?>x
                    <a href="#" class="nav-button" onclick="checkLogin('SellItem.php')">Sell Item</a>
                <?php endif; ?>
            </li>
            <li><a href="AdminLogin.html" class="nav-button"> Admin</a></li>
        
            <li><a href="Help.html" class="nav-button"> Help</a></li>
            <li><a href="Contact.html" class="nav-button">Contact</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="logout.php" class="nav-button"> Logout</a></li>
            <?php endif; ?>
            <div id="cart-icon">
            <li><a href="logout.php" class="nav-button">Logout</a></li>
        </ul>
    </nav>
</header>

<button id="scroll-top"></button>
<button id="dark-toggle"></button>

<div class="sidebar">
    <h3>Categories</h3>
    <ul>
        <li><a href="ViewItem.php?category=Electronics"> Electronics</a></li>
        <li><a href="ViewItem.php?category=Smartphones">Smartphones</a></li>
        <li><a href="ViewItem.php?category=Home &amp; Garden"> Home & Garden</a></li>
        <li><a href="ViewItem.php?category=Jewelry &amp; Watches"> Jewelry & Watches</a></li>
        <li><a href="ViewItem.php?category=Automobiles"> Automobiles</a></li>
        <li><a href="ViewItem.php?category=Collectibles"> Collectibles</a></li>
        <li><a href="ViewItem.php?category=Books"> Books</a></li>
        <li><a href="ViewItem.php?category=Toys &amp; Hobbies"> Toys & Hobbies</a></li>
        <li><a href="ViewItem.php?category=Sports Equipment">Sports Equipment</a></li>
        <li><a href="ViewItem.php?category=Health &amp; Beauty"> Health & Beauty</a></li>
        <li><a href="ViewItem.php?category=Music &amp; Instruments">Music & Instruments</a></li>
        <li><a href="ViewItem.php?category=Laptops"> Laptops</a></li>
        <li><a href="ViewItem.php?category=Fashion"> Fashion</a></li>
    </ul>
</div>


<div class="main-content">
    <section>
        <h2> Welcome to TreasureHunt</h2>
        <p>
            Your one-stop online auction platform! Browse exciting items, place bids, or list your own treasures for sale.
        </p>

        <?php if (!$isLoggedIn): ?>
        <ul style="line-height: 1.8;">
            <li><a href="register.php">Register</a> – Create your free account to start buying or selling.</li>
            <li><a href="login.php">Login</a> – Sign in to explore and participate in auctions.</li>
        </ul>
        <?php endif; ?>

        <ul style="line-height: 1.8;">
            <li><a href="ViewItem.php"> View Items</a> – Browse and bid on all available auctions.</li>
            <li><a href="#" onclick="checkLogin('SellItem.php'); return false;"> Sell an Item</a>  Post something valuable for auction.</li>
            <li><a href="Adminlogin.html"> Admin Access</a> – Admin reports and management tools.</li>
        </ul>
    </section>

    <section id="latest-items">
        <h3> Featured Items</h3>
        <div class="item">
            <p><strong>⌚ Apple Watch Ultra</strong> – Starting at $399</p>
        </div>
        <div class="item">
            <p><strong>👜 Vintage Leather Bag</strong> – Starting at $150</p>
        </div>
        <div class="item">
            <p><strong>💻 Gaming Laptop</strong> – Starting at $1200</p>
        </div>
    </section>
</div>

<footer>
    <p>&copy; 2025 TreasureHunt  | <a href="Contact.html">📞 Contact</a> | <a href="Help.html"> Help</a></p>
</footer>

<script>

document.getElementById("cart-count").innerText = localStorage.getItem("cartCount") || 0;


const scrollBtn = document.getElementById("scroll-top");
window.onscroll = function() {
    scrollBtn.style.display = (document.documentElement.scrollTop > 100) ? "block" : "none";
};
scrollBtn.onclick = function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};


const darkToggle = document.getElementById("dark-toggle");
darkToggle.onclick = function() {
    document.body.classList.toggle("dark-mode");
    darkToggle.textContent = document.body.classList.contains("dark-mode") ? "☀️" : "🌙";
};


function checkLogin(destination) {
    <?php if ($isLoggedIn): ?>
        window.location.href = destination;
    <?php else: ?>
        alert("To buy or sell, you need to register. It takes just 5 minutes!");
        window.location.href = 'Register.php';
    <?php endif; ?>
}
</script>

</body>
</html>
