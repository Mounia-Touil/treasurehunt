<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    echo "Session not set.";
    exit();
}
$userID = $_SESSION['userid'];
try {  
    $buyNowSql = "
        SELECT i.Name AS ItemName, p.Price AS FinalPrice, 'Buy Now' AS Type, p.PurchaseDate AS Date
        FROM Purchase p
        JOIN Items i ON p.ItemID = i.ItemID
        WHERE p.UserID = ?
    ";
    $buyStmt = $conn->prepare($buyNowSql);
    $buyStmt->execute([$userID]);
    $buyNow = $buyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error in Buy Now query: " . $e->getMessage();
    exit();
}
try {
    $auctionSql = "
        SELECT Name AS ItemName, FinalPrice, 'Auction' AS Type, EndTime AS Date
        FROM Items
        WHERE WinnerID = ? AND Status = 'Sold'
    ";
    $auctionStmt = $conn->prepare($auctionSql);
    $auctionStmt->execute([$userID]);
    $auctionWins = $auctionStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error in Auction Wins query: " . $e->getMessage();
    exit();
}

$allPurchase = array_merge($buyNow, $auctionWins);

usort($allPurchase, function ($a, $b) {
    return strtotime($b['Date']) - strtotime($a['Date']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Purchase - TreasureHunt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <img src="treasure-hunter.png" alt="Treasure Hunt Banner" class="banner">
    <div class="header-content">
        <div class="header-left">
            <img src="Logo.png" alt="Logo" class="logo">
            <h1>My Purchase</h1>
        </div>
        <div id="cart-icon">
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>
    <nav>
        <ul>
            <li><a href="UserDashboard.php" class="nav-button">Dashboard</a></li>
            <li><a href="ViewItem.php" class="nav-button">Browse Items</a></li>
            <li><a href="add_item.php" class="nav-button">Add Item</a></li>
        </ul>
    </nav>
</header>

<main class="main-content">
    <h2> All Purchase (Buy Now + Auction Wins)</h2>

    <?php if (count($allPurchase) === 0): ?>
        <p>You haven’t purchased or won any items yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Final Price ($)</th>
                    <th>Type</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allPurchase as $purchase): ?>
                    <tr>
                        <td><?= htmlspecialchars($purchase['ItemName']) ?></td>
                        <td>$<?= number_format($purchase['FinalPrice'], 2) ?></td>
                        <td><?= $purchase['Type'] ?></td>
                        <td><?= htmlspecialchars($purchase['Date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="UserDashboard.php" class="nav-button">⬅️ Back to Dashboard</a>
</main>

<footer>
    <p>&copy; 2025 TreasureHunt Auction System | <a href="Contact.html">📞 Contact</a> | <a href="Help.html">❓ Help</a></p>
</footer>
</body>
</html>
