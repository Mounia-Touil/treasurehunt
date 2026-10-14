<?php
session_start();
require 'db.php';

$isGuest = !isset($_SESSION['userid']);
$itemID = intval($_GET['itemID'] ?? 0);

if ($itemID === 0) {
    echo "<script>alert('Invalid item ID.'); window.location.href='ViewItem.php';</script>";
    exit();
}

$stmt = $conn->prepare("SELECT i.*, c.CategoryName,
    (SELECT MAX(BidAmount) FROM Bids WHERE ItemID = i.ItemID) AS HighestBid
    FROM Items i 
    JOIN Categories c ON i.CategoryID = c.CategoryID
    WHERE i.ItemID = ?");
$stmt->execute([$itemID]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "<script>alert('Item not found.'); window.location.href='ViewItem.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Item Details - <?= htmlspecialchars($item['Name']) ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    .countdown { font-weight: bold; color: #d9534f; }
    .item-details { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
    .item-details img { width: 100%; max-width: 300px; display: block; margin-bottom: 10px; }
  </style>
</head>
<body>
<header>
  <h1> Item Details</h1>
  <a href="ViewItem.php">🔙 Back to Items</a>
</header>

<main>
  <div class="item-details">
    <h2><?= htmlspecialchars($item['Name']) ?></h2>
    <img src="<?= htmlspecialchars($item['ImageURL']) ?>" alt="Item Image">
    <p><strong>Category:</strong> <?= htmlspecialchars($item['CategoryName']) ?></p>
    <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($item['Description'])) ?></p>
    <p><strong>Starting Price:</strong> $<?= number_format($item['StartingPrice'], 2) ?></p>
    <p><strong>Start Time:</strong> <?= htmlspecialchars($item['StartTime']) ?></p>
    <p><strong>End Time:</strong> <?= htmlspecialchars($item['EndTime']) ?></p>
    <p><strong>⏳ Time Remaining:</strong> <span class="countdown" data-end="<?= $item['EndTime'] ?>"></span></p>
    <p><strong>Current Highest Bid:</strong>
      <?= $item['HighestBid'] ? '$' . number_format($item['HighestBid'], 2) : 'No bids yet' ?></p><br>

    <?php if ($isGuest): ?>
      <button onclick="alert('To place a bid, please register or log in.')">Register to Bid</button>
    <?php else: ?>
      <form action="place_bid.php" method="POST" onsubmit="return validateBid()">
        <input type="hidden" name="itemID" value="<?= $itemID ?>">
        <label for="bidAmount">Your Bid ($):</label><br>
        <input type="number" name="bidAmount" id="bidAmount" step="0.01" required autofocus><br><br>
        <button type="submit">Place Bid</button>
      </form>
    <?php endif; ?>
  </div>
</main>

<footer>
  <p>&copy; 2025 TreasureHunt Auction System</p>
</footer>

<script>
function updateCountdowns() {
  const span = document.querySelector(".countdown");
  const endTime = new Date(span.dataset.end).getTime();
  const now = new Date().getTime();
  const diff = endTime - now;

  if (diff <= 0) {
    span.textContent = " Auction Ended";
    return;
  }

  const hours = Math.floor(diff / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);
  span.textContent = `${hours}h ${minutes}m ${seconds}s`;
}
setInterval(updateCountdowns, 1000);
updateCountdowns();

function validateBid() {
  const bid = parseFloat(document.getElementById("bidAmount").value);
  if (isNaN(bid) || bid <= 0) {
    alert("Please enter a valid bid amount.");
    return false;
  }
  return true;
}
</script>
</body>
</html>
