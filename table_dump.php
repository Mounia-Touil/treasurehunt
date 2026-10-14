<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("Access denied. Admins only.");
}

$table = isset($_GET['table']) ? preg_replace("/[^a-zA-Z0-9_]/", "", $_GET['table']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$export = isset($_GET['export']) && $_GET['export'] == '1';

if (empty($table)) {
    die("No table selected.");
}

try {
    $sql = "SELECT * FROM `$table`";
    if (!empty($search)) {
        $columns = $conn->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_COLUMN);
        $searchParts = array_map(function($col) use ($search) {
            return "$col LIKE '%$search%'";
        }, $columns);
        $sql .= " WHERE " . implode(" OR ", $searchParts);
    }

    $stmt = $conn->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($export) {
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename={$table}_export.csv");
        $out = fopen('php://output', 'w');
        if (!empty($results)) {
            fputcsv($out, array_keys($results[0]));
            foreach ($results as $row) {
                fputcsv($out, $row);
            }
        }
        fclose($out);
        exit;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Table - <?= htmlspecialchars($table) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1> Dump of Table: <?= htmlspecialchars($table) ?></h1>
        <a href="AdminPanel.html">⬅️ Back to Admin Panel</a>
    </header>
    <main>
        <?php if (empty($results)): ?>
            <p>No results found.</p>
        <?php else: ?>
            <table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <?php foreach (array_keys($results[0]) as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <?php foreach ($row as $cell): ?>
                                <td><?= htmlspecialchars($cell) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 TreasureHunt Admin Tools</p>
    </footer>
</body>
</html>
