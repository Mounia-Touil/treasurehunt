<?php
require 'db.php';

$sql = "SELECT * FROM Items WHERE EndTime < NOW() AND Status = 'Active'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
    $itemID = $item['ItemID'];

    $bidStmt = $conn->prepare("SELECT UserID, BidAmount FROM Bids WHERE ItemID = ? ORDER BY BidAmount DESC LIMIT 1");
    $bidStmt->execute([$itemID]);
    $highest = $bidStmt->fetch(PDO::FETCH_ASSOC);

    if ($highest) {
        $winnerID = $highest['UserID'];
        $finalPrice = $highest['BidAmount'];

        $updateStmt = $conn->prepare("UPDATE Items SET Status = 'Sold', WinnerID = ?, FinalPrice = ? WHERE ItemID = ?");
        $updateStmt->execute([$winnerID, $finalPrice, $itemID]);


        $sellerID = $item['UserID'];
        $itemName = htmlspecialchars($item['Name'], ENT_QUOTES);

        $msgWinner = "🎉 You won the auction for '$itemName' at \$$finalPrice!";
        $msgSeller = "🎉 Your item '$itemName' was sold for \$$finalPrice!";

        $conn->prepare("INSERT INTO Notifications (UserID, Message) VALUES (?, ?)")
             ->execute([$winnerID, $msgWinner]);

        $conn->prepare("INSERT INTO Notifications (UserID, Message) VALUES (?, ?)")
             ->execute([$sellerID, $msgSeller]);
    } else {
        $conn->prepare("UPDATE Items SET Status = 'Expired' WHERE ItemID = ?")->execute([$itemID]);

        $sellerID = $item['UserID'];
        $itemName = htmlspecialchars($item['Name'], ENT_QUOTES);
        $msg = " Your item '$itemName' expired with no bids.";

        $conn->prepare("INSERT INTO Notifications (UserID, Message) VALUES (?, ?)")
             ->execute([$sellerID, $msg]);
    }
}
?>
