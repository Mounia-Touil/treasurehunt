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

$categoryId = isset($_GET['categoryId']) ? intval($_GET['categoryId']) : 0;
$purchaseDate = isset($_GET['purchaseDate']) ? $_GET['purchaseDate'] : '';

if ($categoryId && $purchaseDate) {

    $stmt = $conn->prepare("
        SELECT 
            i.Name AS ItemName,
            i.Description,
            c.CategoryName,
            p.Price AS SoldPrice,
            p.PurchaseDate,
            u.FullName AS BuyerName,
            u.Email AS BuyerEmail,
            u.PhoneNumber
        FROM Purchase p
        JOIN Items i ON p.ItemID = i.ItemID
        JOIN Categories c ON i.CategoryID = c.CategoryID
        JOIN Users u ON p.UserID = u.UserID
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
                    <th>Category</th>
                    <th>Purchase Date</th>
                    <th>Sold Price</th>
                    <th>Buyer Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['ItemName']}</td>
                    <td>{$row['CategoryName']}</td>
                    <td>{$row['PurchaseDate']}</td>
                    <td>\${$row['SoldPrice']}</td>
                    <td>{$row['BuyerName']}</td>
                    <td>{$row['BuyerEmail']}</td>
                    <td>{$row['PhoneNumber']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No items found for that category and date.</p>";
    }

    $stmt->close();
} else {
    echo "<form method='GET'>
            <label for='categoryId'>Category ID:</label>
            <input type='number' name='categoryId' required><br><br>
            <label for='purchaseDate'>Purchase Date (YYYY-MM-DD):</label>
            <input type='date' name='purchaseDate' required><br><br>
            <input type='submit' value='Generate Report'>
          </form>";
}

$conn->close();
?>
