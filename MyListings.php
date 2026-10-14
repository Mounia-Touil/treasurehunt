<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.html");
    exit();
}
$userID = $_SESSION['userid']; 
$query = "
SELECT i.ItemID, i.Name, i.StartingPrice, i.EndTime, i.Status,
       (SELECT MAX(BidAmount) FROM Bids WHERE ItemID = i.ItemID) AS HighestBid,
       c.CategoryName
FROM Items i
JOIN Categories c ON i.CategoryID = c.CategoryID
WHERE i.UserID = ?
ORDER BY i.EndTime DESC";

$stmt = $conn->prepare($query);
$stmt->execute([$userID]);
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Listings - TreasureHunt</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <img src="treasure-hunter.png" alt="Treasure Hunt Banner" class="banner">
  <div class="header-content">
    <div class="header-left">
      <img src="Logo.png" alt="Logo" class="logo">
      <h1> My Listings</h1>
    </div>
    <div id="cart-icon">
      <a href="UserDashboard.php">🏠 Dashboard</a>
    </div>
  </div>
  <nav>
    <ul>
      <li><a href="TreasureHunt.php" class="nav-button">Home</a></li>
      <li><a href="ViewItem.php" class="nav-button">Browse Items</a></li>
      <li><a href="add_item.php" class="nav-button">➕ Add Item</a></li>
      <li><a href="logout.php" class="nav-button">🚪 Logout</a></li>
    </ul>
  </nav>
</header>

<main class="main-content">
  <h2> Your Listed Items</h2>
  <?php if (empty($listings)): ?>
    <p>You have not listed any items yet. <a href="add_item.php">Add one now</a>!</p>
  <?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0">
      <tr>
        <th>Item Name</th>
        <th>Category</th>
        <th>Current Price</th>
        <th>Status</th>
        <th>End Time</th>
      </tr>
      <?php foreach ($listings as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['Name']) ?></td>
          <td><?= htmlspecialchars($item['CategoryName']) ?></td>
          <td>$<?= number_format($item['HighestBid'] ?? $item['StartingPrice'], 2) ?></td>
          <td><?= htmlspecialchars($item['Status']) ?></td>
          <td><?= htmlspecialchars($item['EndTime']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</main>

<footer>
  <p>&copy; 2025 TreasureHunt Auction System | <a href="Contact.html">📞 Contact</a> | <a href="Help.html"> Help</a></p>
</footer>

</body>
</html>
