<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('Please log in first'); window.location.href='login.html';</script>";
    exit();
}

$userID = $_SESSION['userid'];

try {
    $stmt = $conn->prepare("
        SELECT i.Name, i.Status, MAX(b.BidAmount) AS YourBid
        FROM Bids b
        JOIN Items i ON b.ItemID = i.ItemID
        WHERE b.UserID = ?
        GROUP BY i.ItemID, i.Name, i.Status
        ORDER BY i.ItemID DESC
    ");
    $stmt->execute([$userID]);
    $bids = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Bids - TreasureHunt</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1> My Bids</h1>
    <a href="UserDashboard.php">⬅️ Back to Dashboard</a>
</header>

<main class="main-content">
    <?php if (count($bids) > 0): ?>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Your Highest Bid</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bids as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Name']) ?></td>
                        <td><?= htmlspecialchars($row['Status']) ?></td>
                        <td>$<?= number_format($row['YourBid'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You haven’t placed any bids yet.</p>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2025 TreasureHunt Auction System</p>
</footer>
</body>
</html>
