<?php include 'session_check.php'; ?>

<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "treasurehunt";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$categories = [];
$cat_result = $conn->query("SELECT CategoryID, CategoryName FROM Categories");
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}

$categoryId = isset($_GET['categoryId']) ? intval($_GET['categoryId']) : 0;
$purchaseDate = isset($_GET['purchaseDate']) ? $_GET['purchaseDate'] : '';

if ($categoryId && $purchaseDate) {
    $stmt = $conn->prepare("
        SELECT 
            i.Name AS ItemName,
            i.Description,
            c.CategoryName,
            b.BidAmount AS SoldPrice,
            p.PurchaseDate,
            u.FullName AS BuyerName,
            u.Email AS BuyerEmail,
            u.PhoneNumber
        FROM Purchase p
        JOIN Bids b ON p.BidID = b.BidID
        JOIN Items i ON b.ItemID = i.ItemID
        JOIN Categories c ON i.CategoryID = c.CategoryID
        JOIN Users u ON b.UserID = u.UserID
        WHERE DATE(p.PurchaseDate) = ?
          AND c.CategoryID = ?
    ");

    $stmt->bind_param("si", $purchaseDate, $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Items Purchased on $purchaseDate in Selected Category</h2>";

    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Purchase Date</th>
                    <th>Sold Price</th>
                    <th>Buyer Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['ItemName']) . "</td>
                    <td>" . htmlspecialchars($row['Description']) . "</td>
                    <td>" . htmlspecialchars($row['CategoryName']) . "</td>
                    <td>" . htmlspecialchars($row['PurchaseDate']) . "</td>
                    <td>$" . number_format($row['SoldPrice'], 2) . "</td>
                    <td>" . htmlspecialchars($row['BuyerName']) . "</td>
                    <td>" . htmlspecialchars($row['BuyerEmail']) . "</td>
                    <td>" . htmlspecialchars($row['PhoneNumber']) . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No items found for that category and date.</p>";
    }

    $stmt->close();
}


?>

<form method="GET">
    <label for="categoryId">Select Category:</label>
    <select name="categoryId" required>
        <option value="">-- Choose Category --</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['CategoryID'] ?>" <?= ($cat['CategoryID'] == $categoryId ? 'selected' : '') ?>>
                <?= htmlspecialchars($cat['CategoryName']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="purchaseDate">Purchase Date:</label>
    <input type="date" name="purchaseDate" value="<?= htmlspecialchars($purchaseDate) ?>" required><br><br>

    <input type="submit" value="Generate Report">
</form>

<?php
$conn->close();
?>
