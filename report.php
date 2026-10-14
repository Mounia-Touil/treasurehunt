<?php include 'session_check.php'; ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "treasurehunt");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$categories = [];
$cat_result = $conn->query("SELECT CategoryID, CategoryName FROM Categories ORDER BY CategoryName");
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}

$reportType = $_GET['report_type'] ?? '';
$categoryId = $_GET['category_id'] ?? '';
$purchaseDate = $_GET['purchase_date'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>TreasureHunt - Reports</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        select, input[type="date"], input[type="submit"] { margin: 10px; padding: 5px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .hidden { display: none; }
    </style>
</head>
<body>

<h2> Admin Reports</h2>

<form method="get">
    <label>Select Report Type:</label>
    <select name="report_type" id="reportType" onchange="toggleFilters()">
        <option value="">-- Select --</option>
        <option value="on_sale" <?= ($reportType == 'on_sale') ? 'selected' : '' ?>>Items Currently on Sale</option>
        <option value="purchases" <?= ($reportType == 'purchases') ? 'selected' : '' ?>>Purchase Report (By Date & Category)</option>
    </select>

    <div id="purchaseFilters" class="<?= ($reportType == 'purchases') ? '' : 'hidden' ?>">
        <label for="category_id">Category:</label>
        <select name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['CategoryID'] ?>" <?= ($cat['CategoryID'] == $categoryId) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['CategoryName']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="purchase_date">Date:</label>
        <input type="date" name="purchase_date" value="<?= htmlspecialchars($purchaseDate) ?>">
    </div>

    <input type="submit" value="View Report">
</form>
<hr>

<?php
if ($reportType === 'on_sale') {
    
    $sql = "SELECT 
            i.Name AS ItemName,
            c.CategoryName,
            i.Description,
            i.StartingPrice AS StartPrice,
            i.StartTime,
            i.EndTime,
            u.FullName AS Seller
        FROM Items i
        JOIN Users u ON i.UserID = u.UserID
        JOIN Categories c ON i.CategoryID = c.CategoryID
        WHERE i.Status = 'Active' AND i.EndTime > NOW()
        ORDER BY i.EndTime ASC";

    $result = $conn->query($sql);

    echo "<h3> Items Currently on Sale</h3>";
    if ($result->num_rows > 0) {
        echo "<table>
                <tr><th>Item</th><th>Category</th><th>Description</th><th>Price</th><th>Seller</th><th>Start</th><th>End</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['ItemName']}</td>
                    <td>{$row['CategoryName']}</td>
                    <td>{$row['Description']}</td>
                    <td>\${$row['StartPrice']}</td> <!-- this still works because we're aliasing StartingPrice AS StartPrice -->
                    <td>{$row['Seller']}</td>
                    <td>{$row['StartTime']}</td>
                    <td>{$row['EndTime']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No items currently on sale.</p>";
    }
}
elseif ($reportType === 'purchases' && $categoryId && $purchaseDate) {
   
    $sql = "SELECT 
                i.Name AS ItemName,
                c.CategoryName,
                p.Price AS SoldPrice,
                u.FullName AS BuyerName,
                u.Email,
                u.PhoneNumber,
                p.PurchaseDate
            FROM Purchase p
            JOIN Items i ON p.ItemID = i.ItemID
            JOIN Users u ON p.UserID = u.UserID
            JOIN Categories c ON i.CategoryID = c.CategoryID
            WHERE DATE(p.PurchaseDate) = ?
              AND c.CategoryID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $purchaseDate, $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3> Purchase Report for " . htmlspecialchars($purchaseDate) . "</h3>";
    if ($result->num_rows > 0) {
        echo "<table>
                <tr><th>Item</th><th>Category</th><th>Sold Price</th><th>Buyer</th><th>Email</th><th>Phone</th><th>Date</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['ItemName']}</td>
                    <td>{$row['CategoryName']}</td>
                    <td>\${$row['SoldPrice']}</td>
                    <td>{$row['BuyerName']}</td>
                    <td>{$row['Email']}</td>
                    <td>{$row['PhoneNumber']}</td>
                    <td>{$row['PurchaseDate']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No purchases found for that category and date.</p>";
    }
}
?>

<script>
function toggleFilters() {
    const reportType = document.getElementById("reportType").value;
    const filterDiv = document.getElementById("purchaseFilters");
    if (reportType === "purchases") {
        filterDiv.classList.remove("hidden");
    } else {
        filterDiv.classList.add("hidden");
    }
}
</script>

</body>
</html>
