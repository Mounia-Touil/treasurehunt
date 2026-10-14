<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.html");
    exit;
}

$userID = $_SESSION['userid'];
$itemID = $_GET['itemID'] ?? null;
if (!$itemID) {
    echo " Invalid item ID.";
    exit;
}

$stmt = $conn->prepare("SELECT i.*, c.CategoryName FROM Items i JOIN Categories c ON i.CategoryID = c.CategoryID WHERE i.ItemID = ?");
$stmt->execute([$itemID]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) {
    echo " Item not found.";
    exit;
}

$maxStmt = $conn->prepare("SELECT MAX(BidAmount) AS HighestBid FROM Bids WHERE ItemID = ?");
$maxStmt->execute([$itemID]);
$highestBid = $maxStmt->fetchColumn();
$minBid = max($item['StartingPrice'], $highestBid ?: 0);


$userStmt = $conn->prepare("SELECT FullName, ShippingAddress FROM Users WHERE UserID = ?");
$userStmt->execute([$userID]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $user = [
        'FullName' => '',
        'ShippingAddress' => ''
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Place Bid - <?= htmlspecialchars($item['Name']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2> Place Bid for: <?= htmlspecialchars($item['Name']) ?></h2>

<img src="<?= htmlspecialchars($item['ImageURL'] ?: 'placeholder.jpg') ?>" style="max-width: 300px;"><br><br>

<p><strong>Description:</strong> <?= htmlspecialchars($item['Description']) ?></p>
<p><strong>Starting Price:</strong> $<?= number_format($item['StartingPrice'], 2) ?></p>
<p><strong>Current Highest Bid:</strong> $<?= number_format($highestBid ?: $item['StartingPrice'], 2) ?></p>
<p><strong>Auction Ends:</strong> <?= htmlspecialchars($item['EndTime']) ?></p>

<form action="submit_bid.php" method="POST">
    <input type="hidden" name="itemID" value="<?= $item['ItemID'] ?>">

    <label>Your Full Name:</label><br>
    <input type="text" name="fullName" value="<?= htmlspecialchars($user['FullName']) ?>" required><br><br>

    <label>Shipping Address:</label><br>
    <textarea name="shippingAddress" required><?= htmlspecialchars($user['ShippingAddress']) ?></textarea><br><br>

    <label>Your Bid Amount ($):</label><br>
    <input type="number" name="bidAmount" step="0.01" min="<?= $minBid + 0.01 ?>" required><br><br>

    <label><input type="checkbox" name="saveAddress"> Save this address for future bids</label><br><br>

    <button type="submit">Submit Bid</button>
</form>

</body>
</html>
