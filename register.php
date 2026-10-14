<?php
require 'db.php'; // PDO $conn

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname   = trim($_POST['fullname']);
    $address    = trim($_POST['address']);
    $phone      = trim($_POST['phonenumber']);
    $creditcard = trim($_POST['creditcard']);
    $email      = trim($_POST['email']);
    $username   = trim($_POST['username']);
    $password_raw = $_POST['password'];

    if (empty($fullname) || empty($address) || empty($phone) || empty($creditcard) ||
        empty($email) || empty($username) || empty($password_raw)) {
        echo "<script>alert(' All fields are required.'); window.location.href='register.php';</script>";
        exit;
    }

    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ? OR Email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('⚠️ Username or Email already exists.'); window.location.href='register.php';</script>";
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO Users 
            (FullName, ShippingAddress, PhoneNumber, CreditCardInfo, Email, Username, Password, IsAdmin) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->execute([$fullname, $address, $phone, $creditcard, $email, $username, $password]);

        echo "<script>
            alert(' Registration successful! Redirecting...');
            window.location.href = 'login.html';
        </script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert(' Database error: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TreasureHunt - Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <img src="treasure-hunter.png" alt="Treasure Hunt Banner" class="banner">
    <div class="header-content">
        <div class="header-left">
            <img src="logo.png" alt="Logo" class="logo">
            <h1>TreasureHunt Auction System</h1>
        </div>
        <div id="cart-icon">
            <a href="Cart.html">🛒 Cart</a>
        </div>
    </div>
    <nav>
        <ul>
            <li><a href="TreasureHunt.php" class="nav-button">Home</a></li>
            <li><a href="register.php" class="nav-button active">Register</a></li>
            <li><a href="login.html" class="nav-button">Login</a></li>
            <li><a href="ViewItem.php" class="nav-button">View Items</a></li>
            <li><a href="SellItem.php" class="nav-button">Sell Item</a></li>
            <li><a href="AdminLogin.html" class="nav-button">Admin Login</a></li>
            <li><a href="Help.html" class="nav-button">Help</a></li>
            <li><a href="Contact.html" class="nav-button">Contact</a></li>
        </ul>
    </nav>
</header>

<div class="sidebar">
    <h3>Categories</h3>
    <ul>
        <li><a href="ViewItem.html#electronics">Electronics</a></li>
        <li><a href="ViewItem.html#fashion">Fashion</a></li>
        <li><a href="ViewItem.html#automobiles">Automobiles</a></li>
        <li><a href="ViewItem.html#home">Home & Kitchen</a></li>
        <li><a href="ViewItem.html#books">Books</a></li>
        <li><a href="ViewItem.html#sports">Sports & Outdoors</a></li>
        <li><a href="ViewItem.html#health">Health & Beauty</a></li>
        <li><a href="ViewItem.html#toys">Toys & Games</a></li>
        <li><a href="ViewItem.html#music">Music & Instruments</a></li>
        <li><a href="ViewItem.html#collectibles">Collectibles & Art</a></li>
    </ul>
</div>

<div class="main-content">
    <section>
        <h2>Register</h2>
        <form action="register.php" method="POST">
            <label>Full Name:</label><br>
            <input type="text" name="fullname" required><br><br>

            <label>Address:</label><br>
            <input type="text" name="address" required><br><br>

            <label>Phone Number:</label><br>
            <input type="tel" name="phonenumber" required><br><br>

            <label>Credit Card Number:</label><br>
            <input type="text" name="creditcard" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>

            <label>Username:</label><br>
            <input type="text" name="username" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br><br>

            <button type="submit">Register</button>
        </form>
    </section>
</div>

<footer>
    <p>&copy; 2025 TreasureHunt Auction System</p>
</footer>

</body>
</html>
