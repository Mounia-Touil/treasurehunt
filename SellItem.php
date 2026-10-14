<?php
session_start();

if (!isset($_SESSION['userid'])) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Login Required - TreasureHunt</title>
        <link rel='stylesheet' href='style.css'>
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .modal {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background: rgba(0,0,0,0.6);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            }
            .modal-content {
                background: white;
                padding: 30px;
                border-radius: 10px;
                text-align: center;
                max-width: 400px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
            .modal-content h2 {
                margin-bottom: 20px;
            }
            .modal-content button {
                margin: 10px;
                padding: 10px 20px;
                font-size: 16px;
                cursor: pointer;
                border: none;
                border-radius: 5px;
                background-color: #007bff;
                color: white;
                transition: background 0.3s ease;
            }
            .modal-content button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class='modal'>
            <div class='modal-content'>
                <h2> To buy or sell, You need to register. It takes 5 minutes to do it and then you can conveniently buy/Sell. Have Fun.</h2>
                <p>Please choose an option:</p>
                <button onclick=\"location.href='login.html'\">Login</button>
                <button onclick=\"location.href='register.php'\">Register</button>
            </div>
        </div>
    </body>
    </html>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TreasureHunt - Sell Item</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <img src="treasure-hunter.png" alt="Treasure Hunt Banner" class="banner">
    <div class="header-content">
        <div class="header-left">
            <img src="Logo.png" alt="Treasure Hunt Logo" class="logo">
            <h1>TreasureHunt </h1>
        </div>
        <div id="cart-icon">
      <a href="logout.php"> Logout</a>
      <a href="MyListings.php"> My Listings</a>
    </div>
    </div>
    <nav>
        <ul>
            <li><a href="TreasureHunt.php" class="nav-button"> Home</a></li>
            <li><a href="UserDashboard.php" class="nav-button"> Profile</a></li>
            <li><a href="ViewItem.php" class="nav-button"> View Items</a></li>
            <li><a href="SellItem.php" class="nav-button active">Sell Item</a></li>
            <li><a href="my_bids.php" class="nav-button"> My Bids</a></li>
            <li><a href="AdminLogin.html" class="nav-button"> Admin</a></li>
            <li><a href="Help.html" class="nav-button"> Help</a></li>
            <li><a href="Contact.html" class="nav-button">Contact</a></li>
            <li><a href="logout.php" class="nav-button">Logout</a></li>
        </ul>
    </nav>
</header>

<button id="scroll-top"></button>
<button id="dark-toggle"></button>

<div class="sidebar">
    <h3> Categories</h3>
    <ul>
        <li><a href="SellItem.php?category=1"> Electronics</a></li>
        <li><a href="SellItem.php?category=2"> Smartphones</a></li>
        <li><a href="SellItem.php?category=3"> Home & Garden</a></li>
        <li><a href="SellItem.php?category=4"> Jewelry & Watches</a></li>
        <li><a href="SellItem.php?category=5"> Automobiles</a></li>
        <li><a href="SellItem.php?category=6"> Collectibles</a></li>
        <li><a href="SellItem.php?category=7"> Books</a></li>
        <li><a href="SellItem.php?category=8"> Toys & Hobbies</a></li>
        <li><a href="SellItem.php?category=9"> Sports Equipment</a></li>
        <li><a href="SellItem.php?category=10"> Health & Beauty</a></li>
        <li><a href="SellItem.php?category=11"> Music & Instruments</a></li>
        <li><a href="SellItem.php?category=12"> Laptops</a></li>
        <li><a href="SellItem.php?category=13"> Fashion</a></li>
    </ul>
</div>

<div class="main-content">
    <section>
        <h2> Sell Your Item</h2>
        <form action="ProcessSellItem.php" method="POST" enctype="multipart/form-data">
            <label for="itemName">Item Name:</label><br>
            <input type="text" id="itemName" name="itemName" required><br><br>

            <label for="category">Category:</label><br>
            <select id="category" name="category" required>
                <option value="">-- Select Category --</option>
                <option value="1">Electronics</option>
                <option value="2">Smartphones</option>
                <option value="3">Home & Garden</option>
                <option value="4">Jewelry & Watches</option>
                <option value="5">Automobiles</option>
                <option value="6">Collectibles</option>
                <option value="7">Books</option>
                <option value="8">Toys & Hobbies</option>
                <option value="9">Sports Equipment</option>
                <option value="10">Health & Beauty</option>
                <option value="11">Music & Instruments</option>
                <option value="12">Laptops</option>
                <option value="13">Fashion</option>
            </select><br><br>

            <label for="price">Starting Price ($):</label><br>
            <input type="number" id="price" name="price" step="0.01" required><br><br>

            <label for="duration">Auction Duration (in hours):</label><br>
            <input type="number" name="duration" id="duration" required min="1"><br><br>

            <label for="description">Description:</label><br>
            <textarea id="description" name="description" rows="4" required></textarea><br><br>

            <label for="image">Item Image (optional):</label><br>
            <input type="file" id="image" name="image"><br><br>

            <button type="submit" class="nav-button">List Item for Auction</button>
        </form>
    </section>
</div>

<footer>
    <p>&copy; 2025 TreasureHunt | <a href="Contact.html"> Contact</a> | <a href="Help.html"> Help</a></p>
</footer>

<script>
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
    darkToggle.textContent = document.body.classList.contains("dark-mode") ? "☀️" : "";
};
</script>

</body>
</html>
