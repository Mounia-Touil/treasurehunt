<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.html");
    exit();
}

$itemID = $_GET['itemID'] ?? null;
if (!$itemID || !is_numeric($itemID)) {
    echo "Invalid item.";
    exit();
}


$stmt = $conn->prepare("SELECT i.Name, i.ImageURL, i.Description, i.StartingPrice, u.ShippingAddress FROM Items i JOIN Users u ON i.UserID = u.UserID WHERE i.ItemID = ?");
$stmt->execute([$itemID]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "Item not found.";
    exit();
}


$userStmt = $conn->prepare("SELECT FullName, ShippingAddress FROM Users WHERE UserID = ?");
$userStmt->execute([$_SESSION['userid']]);
$buyer = $userStmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirm Purchase</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <h1>🛒 Confirm Your Purchase</h1>
</header>

<main class="main-content">
  <section>
    <h2><?= htmlspecialchars($item['Name']) ?></h2>
    <img src="<?= htmlspecialchars($item['ImageURL'] ?: 'placeholder.jpg') ?>" style="max-width: 300px;">
    <p><strong>Description:</strong> <?= htmlspecialchars($item['Description']) ?></p>
    <p><strong>Price:</strong> $<?= number_format($item['StartingPrice'], 2) ?></p>
    <p><strong>Note:</strong> You will need to manually confirm payment with the seller after purchase.</p>

    <form action="buy_now.php" method="POST">
      <input type="hidden" name="itemID" value="<?= $itemID ?>">

      <label>Your Name:</label><br>
      <input type="text" name="fullName" value="<?= htmlspecialchars($buyer['FullName']) ?>" required><br><br>

      <label>Shipping Address:</label><br>
      <textarea name="shippingAddress" required><?= htmlspecialchars($buyer['ShippingAddress']) ?></textarea><br><br>

      <label>Credit Card (Mock):</label><br>
      <input type="text" placeholder="XXXX-XXXX-XXXX-1234" disabled><br><br>

      <label><input type="checkbox" name="saveAddress"> Save this address for future purchases</label><br><br>

      <button type="submit"> Confirm Purchase</button>
    </form>
  </section>
</main>

</body>
</html>
