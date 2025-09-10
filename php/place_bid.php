<?php
session_start();
include "db.php";  // Make sure this includes $conn

if (!isset($_SESSION['userid'])) {
    die("Please log in to place a bid.");
}

$itemID = $_POST['itemID'];
$bidAmount = $_POST['bidAmount'];
$bidderID = $_SESSION['userid'];

// ✅ Check if auction is still open
$endQuery = $conn->prepare("SELECT EndTime FROM Items WHERE ItemID = ?");
$endQuery->bind_param("i", $itemID);
$endQuery->execute();
$endResult = $endQuery->get_result();
if ($endResult->num_rows === 0) {
    die("Item not found.");
}
$endTime = $endResult->fetch_assoc()['EndTime'];

if (new DateTime() > new DateTime($endTime)) {
    die("Bidding has ended for this item.");
}

// ✅ Check current highest bid
$maxBidQuery = $conn->prepare("SELECT MAX(BidAmount) AS max FROM Bids WHERE ItemID = ?");
$maxBidQuery->bind_param("i", $itemID);
$maxBidQuery->execute();
$maxResult = $maxBidQuery->get_result();
$maxBid = $maxResult->fetch_assoc()['max'];
$maxBid = $maxBid ?? 0; // if no previous bids

if ($bidAmount <= $maxBid) {
    die("Your bid must be higher than the current highest bid ($maxBid).");
}

// ✅ Insert new bid
$insert = $conn->prepare("INSERT INTO Bids (ItemID, BidderID, BidAmount, BidTime) VALUES (?, ?, ?, NOW())");
$insert->bind_param("iid", $itemID, $bidderID, $bidAmount);

if ($insert->execute()) {
    echo "<script>alert('✅ Bid placed successfully!'); window.history.back();</script>";
} else {
    echo "<script>alert('❌ Failed to place bid.'); window.history.back();</script>";
}
?>
