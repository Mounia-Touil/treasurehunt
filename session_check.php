session_check.php
<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: Admin.html");
    exit();
}
?>
