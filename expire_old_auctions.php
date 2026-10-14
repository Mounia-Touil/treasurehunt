<?php
require 'db.php'; 

try {
    
    $sql = "SELECT ItemID FROM Items WHERE Status = 'Active' AND EndTime <= NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $expiredItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $expiredCount = 0;
    $soldCount = 0;

    foreach ($expiredItems as $item) {
        $itemId = $item['ItemID'];

        $bidSql = "SELECT UserID, BidAmount FROM Bids 
                   WHERE ItemID = :itemId 
                   ORDER BY BidAmount DESC LIMIT 1";
        $bidStmt = $conn->prepare($bidSql);
        $bidStmt->execute(['itemId' => $itemId]);
        $topBid = $bidStmt->fetch(PDO::FETCH_ASSOC);

        if ($topBid) {
           
            $updateSql = "UPDATE Items SET Status = 'Sold', WinnerID = :winner, FinalPrice = :price 
                          WHERE ItemID = :itemId";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->execute([
                'winner' => $topBid['UserID'],
                'price' => $topBid['BidAmount'],
                'itemId' => $itemId
            ]);
            $soldCount++;
        } else {
            
            $expireSql = "UPDATE Items SET Status = 'Expired' WHERE ItemID = :itemId";
            $expireStmt = $conn->prepare($expireSql);
            $expireStmt->execute(['itemId' => $itemId]);
            $expiredCount++;
        }
    }

    echo "<h3> $soldCount item(s) marked as SOLD.</h3>";
    echo "<h3>$expiredCount item(s) marked as EXPIRED (no bids).</h3>";
    echo "<a href='AdminPanel.php'>⬅️ Back to Admin Panel</a>";

} catch (PDOException $e) {
    echo " Error processing auctions: " . $e->getMessage();
}
?>
