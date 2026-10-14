<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('You must be logged in to buy.'); window.location.href='login.html';</script>";
    exit();
}

$userID = $_SESSION['userid'];
$itemID = $_POST['itemID'] ?? null;
$fullName = trim($_POST['fullName'] ?? '');
$shippingAddress = trim($_POST['shippingAddress'] ?? '');
$saveAddress = isset($_POST['saveAddress']);

if (!$itemID || !$fullName || !$shippingAddress) {
    echo "<script>alert('Please fill out all required fields.'); history.back();</script>";
    exit();
}


$stmt = $conn->prepare("SELECT StartingPrice, Status FROM Items WHERE ItemID = ?");
$stmt->execute([$itemID]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "<script>alert('Item not found.'); window.location.href='ViewItem.php';</script>";
    exit();
}
if ($item['Status'] !== 'Active') {
    echo "<script>alert('This item is no longer available.'); window.location.href='ViewItem.php';</script>";
    exit();
}

$price = $item['StartingPrice'];

try {
    $conn->beginTransaction();

    
    $insert = $conn->prepare("INSERT INTO Purchase (UserID, ItemID, Price, PurchaseDate) VALUES (?, ?, ?, NOW())");
    $insert->execute([$userID, $itemID, $price]);

    
    $update = $conn->prepare("UPDATE Items SET Status = 'Sold', WinnerID = ?, FinalPrice = ? WHERE ItemID = ?");
    $update->execute([$userID, $price, $itemID]);

    
    if ($saveAddress) {
        $updateUser = $conn->prepare("UPDATE Users SET FullName = ?, ShippingAddress = ? WHERE UserID = ?");
        $updateUser->execute([$fullName, $shippingAddress, $userID]);
    }

   
    $infoStmt = $conn->prepare("SELECT Name, UserID FROM Items WHERE ItemID = ?");
    $infoStmt->execute([$itemID]);
    $itemInfo = $infoStmt->fetch(PDO::FETCH_ASSOC);

    $buyerMsg = "🎉 You purchased '{$itemInfo['Name']}' for $$price!";
    $sellerMsg = "💰 Your item '{$itemInfo['Name']}' was sold for $$price!";

    $notifStmt = $conn->prepare("INSERT INTO Notifications (UserID, Message, IsRead, CreatedAt) VALUES (?, ?, 0, NOW())");
    $notifStmt->execute([$userID, $buyerMsg]);
    $notifStmt->execute([$itemInfo['UserID'], $sellerMsg]);

    $conn->commit();

    echo "<script>alert(' Purchase successful!'); window.location.href='MyPurchase.php';</script>";
} catch (Exception $e) {
    $conn->rollBack();
    file_put_contents("buy_error.log", $e->getMessage() . "\n", FILE_APPEND);
    echo "<script>alert(' Purchase failed. Please try again.'); window.location.href='ViewItem.php';</script>";
}
?>
