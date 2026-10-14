<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.html");
    exit();
}

$userID = $_SESSION['userid'];

$stmt = $conn->prepare("
    SELECT i.ItemID, i.Name, i.StartingPrice, i.Status
    FROM Watchlist w
    JOIN Items i ON w.ItemID = i.ItemID
    WHERE w.UserID = ?
");
$stmt->execute([$userID]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>My Watchlist</title></head>
<body>
<h2>⭐ My Watchlist</h2>

<?php if (count($items) === 0): ?>
  <p>Your watchlist is empty.</p>
<?php else: ?>
  <table border="1">
    <tr><th>Item</th><th>Status</th><th>Price</th></tr>
    <?php foreach ($items as $item): ?>
      <tr>
        <td><?= htmlspecialchars($item['Name']) ?></td>
        <td><?= $item['Status'] ?></td>
        <td>$<?= number_format($item['StartingPrice'], 2) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
<a href="UserDashboard.php">⬅️ Back</a>
</body>
</html>
