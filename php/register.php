<?php
require 'db.php';  // include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form inputs
    $fullname = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $creditcard = $_POST['credit'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash the password

    try {
        // Insert into database
//         echo "<pre>";
// print_r($_POST);
// echo "</pre>";

        $stmt = $conn->prepare("INSERT INTO Users (FullName, ShippingAddress, PhoneNumber, CreditCardInfo, Email, Username, Password, IsAdmin)
                                VALUES (?, ?, ?, ?, ?, ?, ?, 0)");

        $stmt->execute([$fullname, $address, $phone, $creditcard, $email, $username, $password]);

        echo "<p style='color:green;'>Registration successful!</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>
