export_csv.php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "treasurehunt";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = $_GET['table'] ?? '';
$allowed = ['Users', 'Items', 'Purchase', 'Categories', 'Bids', 'Admins'];

if (!in_array($table, $allowed)) {
    die("Invalid table.");
}

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename={$table}_export.csv");

$output = fopen("php://output", "w");

$result = $conn->query("SELECT * FROM `$table`");

if ($result && $result->num_rows > 0) {
    
    $fields = $result->fetch_fields();
    $headers = array_map(fn($f) => $f->name, $fields);
    fputcsv($output, $headers);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
$conn->close();
exit();
?>
