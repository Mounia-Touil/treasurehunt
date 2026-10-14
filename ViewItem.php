<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

$isGuest = !isset($_SESSION['userid']);
$categoryFilter = $_GET['category'] ?? null;

$sql = "
    SELECT i.*, c.CategoryName,
           (SELECT MAX(BidAmount) FROM Bids b WHERE b.ItemID = i.ItemID) AS HighestBid
    FROM Items i 
    JOIN Categories c ON i.CategoryID = c.CategoryID 
    WHERE i.Status = 'Active' AND i.EndTime > NOW()"
;

if ($categoryFilter) {
    $sql .= " AND c.CategoryName = :category ORDER BY i.EndTime ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':category', $categoryFilter);
    $stmt->execute();
} else {
    $sql .= " ORDER BY i.EndTime ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
}

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$grouped = [];
foreach ($items as $item) {
    $grouped[$item['CategoryName']][] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>TreasureHunt - View Items</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .badge {
      padding: 3px 8px;
      border-radius: 8px;
      font-size: 0.8em;
      margin-left: 5px;
      font-weight: bold;
      display: inline-block;
    }
    .badge.active {
      background-color: #d4edda;
      color: #155724;
    }
    .countdown {
      font-weight: bold;
      color: #444;
    }
  </style>
</head>
<body>

<header>
  <img src="treasure-hunter.png" alt="Treasure Hunt Banner" class="banner">
  <div class="header-content">
    <div class="header-left">
      <img src="Logo.png" alt="Treasure Hunt Logo" class="logo">
      <h1>TreasureHunt Auction System</h1>
    </div>
    <div id="cart-icon">
      <a href="logout.php"> Logout</a>
      <a href="MyListings.php"> My Listings</a>
    </div>
  </div>
  <nav>
    <ul>
      <li><a href="index.html" class="nav-button"> Home</a></li>
      <li><a href="UserDashboard.php" class="nav-button"> Profile</a></li>
      <li><a href="ViewItem.php" class="nav-button active"> View Items</a></li>
      <li><a href="SellItem.php" class="nav-button"> Sell Item</a></li>
      <li><a href="AdminLogin.html" class="nav-button"> Admin Login</a></li>
      <li><a href="Help.html" class="nav-button"> Help</a></li>
      <li><a href="Contact.html" class="nav-button"> Contact</a></li>
    </ul>
  </nav>
</header>

<button id="scroll-top"></button>
<button id="dark-toggle"></button>

<div class="sidebar">
  <h3> Categories</h3>
  <ul>
    <li><a href="ViewItem.php?category=Electronics"> Electronics</a></li>
    <li><a href="ViewItem.php?category=Smartphones"> Smartphones</a></li>
    <li><a href="ViewItem.php?category=Home%20%26%20Garden">Home & Garden</a>
    <li><a href="ViewItem.php?category=Jewelry%20%26%20Watches"> Jewelry & Watches</a></li>
    <li><a href="ViewItem.php?category=Automobiles"> Automobiles</a></li>
    <li><a href="ViewItem.php?category=Collectibles"> Collectibles</a></li>
    <li><a href="ViewItem.php?category=Books">Books</a></li>
    <li><a href="ViewItem.php?category=Toys%20%26%20Hobbies"> Toys & Hobbies</a></li>
    <li><a href="ViewItem.php?category=Sports%20%26%20Equipment"> Sports Equipment</a></li>
    <li><a href="ViewItem.php?category=Health%20%26%20Beauty"> Health & Beauty</a></li>
    <li><a href="ViewItem.php?category=Music%20%26%20Instruments"> Music & Instruments</a></li>
    <li><a href="ViewItem.php?category=Laptops"> Laptops</a></li>
    <li><a href="ViewItem.php?category=Fashion"> Fashion</a></li>
  </ul>
</div>

<div class="main-content">
  <h2>Live Auction Items</h2>

  <?php if ($categoryFilter): ?>
    <h3>Filtered by Category: <?= htmlspecialchars($categoryFilter) ?></h3>
  <?php endif; ?>

  <?php foreach ($grouped as $category => $items): ?>
  <section>
    <h3><?= htmlspecialchars($category) ?></h3>
    <div class="item-container">
      <?php foreach ($items as $item): ?>
        <div class="item-card">
          <img src="<?= htmlspecialchars($item['ImageURL']) ?>" alt="Item" class="item-img">
          <h4><?= htmlspecialchars($item['Name']) ?></h4>
          <p><?= htmlspecialchars($item['Description']) ?></p>
          <p><strong>Starting Price:</strong> $<?= number_format($item['StartingPrice'], 2) ?></p>
          <p><strong>Current Highest Bid:</strong> 
            <?= $item['HighestBid'] !== null ? '$' . number_format($item['HighestBid'], 2) : 'No bids yet' ?>
          </p>
          <p>
            <strong>Status:</strong> <span class="badge active"> Active</span><br>
            <span class="countdown" data-end="<?= $item['EndTime'] ?>"></span>
          </p>
          <p><a href="ViewDetails.php?itemID=<?= $item['ItemID'] ?>">🔍 View Details</a></p>

          <div class="action-buttons">
  <?php if ($isGuest): ?>
    <button onclick="showGuestAlert()">Place Bid</button>
    <button onclick="showGuestAlert()"> Buy Now</button>
    <button onclick="showGuestAlert()"> Watchlist</button>
    <button onclick="showGuestAlert()">Offer</button>
  <?php else: ?>
    <a href="BidForm.php?itemID=<?= $item['ItemID'] ?>">
    <button>Place Bid</button>
</a>

    <button onclick="buyNow(<?= $item['ItemID'] ?>)"> Buy Now</button>
    <button onclick="addToWatchlist(<?= $item['ItemID'] ?>)">Watchlist</button>
    <button onclick="makeOffer(<?= $item['ItemID'] ?>)">Offer</button>
  <?php endif; ?>
</div>

        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endforeach; ?>
</div>

<footer>
  <p>&copy; 2025 TreasureHunt Auction System | <a href="Contact.html">📞 Contact</a> | <a href="Help.html">❓ Help</a></p>
</footer>

<script>
function showGuestAlert() {
  alert(" To buy or sell, You need to register. It takes 5 minutes to do it and then you can conveniently buy/Sell. Have Fun.");
}

function updateCountdowns() {
  const countdowns = document.querySelectorAll('.countdown');
  countdowns.forEach(span => {
    const endTime = new Date(span.dataset.end).getTime();
    const now = new Date().getTime();
    const diff = endTime - now;

    if (diff <= 0) {
      span.textContent = " Auction Ended";
      span.style.color = "red";
      return;
    }

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    span.textContent = `⏳ Ends in ${hours}h ${minutes}m ${seconds}s`;
  });
}
setInterval(updateCountdowns, 1000);
updateCountdowns();

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
  darkToggle.textContent = document.body.classList.contains("dark-mode") ? "☀️" : "🌙";
};

function placeBid(event, itemID, minPrice) {
  event.preventDefault();
  const form = event.target;
  const bidAmount = parseFloat(form.bidAmount.value);

  if (isNaN(bidAmount) || bidAmount <= minPrice) {
    alert(" Your bid must be greater than $" + minPrice.toFixed(2));
    return false;
  }

  fetch('place_bid.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `itemID=${itemID}&bidAmount=${bidAmount}`
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if (data.success) location.reload();
  });
}

function buyNow(itemID) {
  fetch('buy_now.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: `itemID=${itemID}`
})
.then(res => res.text())
.then(html => document.write(html));

}
function addToWatchlist(itemID) {
  fetch('add_to_watchlist.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `itemID=${itemID}`
  })
  .then(res => res.text())
  .then(html => {
    document.write(html);
  });
}

function makeOffer(itemID) {
  const offerAmount = prompt("Enter your offer amount:");
  if (!offerAmount || isNaN(offerAmount) || parseFloat(offerAmount) <= 0) {
    alert(" Invalid offer.");
    return;
  }

  fetch('make_offer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `itemID=${itemID}&offerAmount=${offerAmount}`
  })
  .then(res => res.text())
  .then(html => {
    document.write(html);
  });
}
</script>

</body>
</html>
