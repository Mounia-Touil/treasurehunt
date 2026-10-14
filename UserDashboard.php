<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include_once("process_ended_auctions.php");

if (!isset($_SESSION['userid'])) {
    header("Location: login.html");
    exit();
}
require 'db.php';

$stmt = $conn->prepare("SELECT FullName, Email, ShippingAddress, PhoneNumber, RegistrationDate, UserID FROM Users WHERE UserID = ?");
$stmt->execute([$_SESSION['userid']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p style='color:red;'> User not found in database.</p>";
    exit();
}
$userID = $user['UserID'];

$notifStmt = $conn->prepare("SELECT NotificationID, Message, CreatedAt FROM Notifications WHERE UserID = ? AND IsRead = 0 ORDER BY CreatedAt DESC");
$notifStmt->execute([$userID]);
$notifications = $notifStmt->fetchAll(PDO::FETCH_ASSOC);


$countStmt = $conn->prepare("SELECT COUNT(*) FROM Items WHERE UserID = ?");
$countStmt->execute([$userID]);
$itemCount = $countStmt->fetchColumn();

$bidCount = 0;
$purchaseCount = 0;

try {
    $stmtBids = $conn->prepare("SELECT COUNT(DISTINCT ItemID) FROM Bids WHERE UserID = ?");
    $stmtBids->execute([$userID]);
    $bidCount = $stmtBids->fetchColumn();
} catch (PDOException $e) {
    $bidCount = 0;
}

try {
    $stmtPurchases = $conn->prepare("SELECT COUNT(*) FROM Purchases WHERE UserID = ?");
    $stmtPurchases->execute([$userID]);
    $purchaseCount = $stmtPurchases->fetchColumn();
} catch (PDOException $e) {
    $purchaseCount = 0;
}

$soldStmt = $conn->prepare("SELECT Name, FinalPrice, WinnerID FROM Items WHERE Status = 'Sold' AND UserID = ?");
$soldStmt->execute([$userID]);
$soldItems = $soldStmt->fetchAll(PDO::FETCH_ASSOC);

$wonStmt = $conn->prepare("SELECT Name, FinalPrice FROM Items WHERE Status = 'Sold' AND WinnerID = ?");
$wonStmt->execute([$userID]);
$wonItems = $wonStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - TreasureHunt</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <img src="treasure-hunter.png" alt="Treasure Hunt Banner" class="banner">
  <div class="header-content">
    <div class="header-left">
      <img src="Logo.png" alt="Logo" class="logo">
      <h1>Welcome, <?php echo htmlspecialchars($user['FullName'] ?? 'User'); ?>!</h1>
    </div>
    <div id="cart-icon">
      <a href="logout.php"> Logout</a>
      <a href="MyListings.php">My Listings</a>
    </div>
  </div>
  <nav>
    <ul>
      <li><a href="TreasureHunt.php" class="nav-button"> Home</a></li>
      <li><a href="ViewItem.php" class="nav-button"> View Items</a></li>
            <li><a href="SellItem.php" class="nav-button active"> Sell Item</a></li>
      <li><a href="add_item.php" class="nav-button"> Add Item</a></li>
      <li><a href="my_bids.php" class="nav-button"> My Bids</a></li>
      <li><a href="MyListings.php" class="nav-button"> My Listings (<?= $itemCount ?>)</a></li>
      <li><a href="Help.html" class="nav-button"> Help</a></li>
    </ul>
  </nav>
</header>

<main class="main-content">
  <section>
    <h2>👤 Profile Overview</h2>
    <p><strong>Full Name:</strong> <?= htmlspecialchars($user['FullName']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
    <p><strong>Shipping Address:</strong> <?= htmlspecialchars($user['ShippingAddress']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['PhoneNumber']) ?></p>
    <p><strong>Member Since:</strong> <?= htmlspecialchars($user['RegistrationDate']) ?></p>
  </section>

  <section>
    <h3>My Activity</h3>
    <ul>
      <li><strong>Items Listed:</strong> <?= $itemCount ?> (<a href="MyListings.php">View</a>)</li>
      <li><strong>Bids Placed:</strong> <?= $bidCount ?> (<a href="my_bids.php">View</a>)</li>
      <li><strong>Items Won / Purchased:</strong> <?= $purchaseCount ?> (<a href="MyPurchase.php">View All</a>)</li>

    </ul>
  </section>
  <section>
  <h4> Notifications</h3>
  <?php if (count($notifications) > 0): ?>
    <ul>
      <?php foreach ($notifications as $note): ?>
        <li>
          <?= htmlspecialchars($note['Message']) ?>
          <br><small><?= $note['CreatedAt'] ?></small>
        </li>
      <?php endforeach; ?>
    </ul>
    <form method="post" action="mark_notifications_read.php">
      <button type="submit">Mark All as Read</button>
    </form>
  <?php else: ?>
    <p>You have no new notifications.</p>
  <?php endif; ?>
</section>


  <section>
    <h5> Items You've Sold</h3>
    <?php if (count($soldItems) > 0): ?>
      <ul>
        <?php foreach ($soldItems as $item): ?>
          <li><?= htmlspecialchars($item['Name']) ?> — Sold for $<?= $item['FinalPrice'] ?> to User #<?= $item['WinnerID'] ?></li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>You haven’t sold any items yet.</p>
    <?php endif; ?>
  </section>

  <section>
    <h6> Items You've Won</h3>
    <?php if (count($wonItems) > 0): ?>
      <ul>
        <?php foreach ($wonItems as $item): ?>
          <li><?= htmlspecialchars($item['Name']) ?> — You won it for $<?= $item['FinalPrice'] ?></li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>You haven’t won any auctions yet.</p>
    <?php endif; ?>
  </section>

  <section>
    <h7>⚙️ Settings</h3>
    <p><a href="UpdateProfile.php">Update Profile</a> | <a href="ChangePassword.php">Change Password</a></p>
  </section>
</main>

<footer>
  <p>&copy; 2025 TreasureHunt | <a href="Contact.html">📞 Contact</a> | <a href="Help.html"> Help</a></p>
</footer>

</body>
</html>
