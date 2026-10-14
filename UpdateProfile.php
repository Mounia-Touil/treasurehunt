<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.html");
    exit();
}

$userID = $_SESSION['userid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullname'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE Users SET FullName = ?, Email = ?, ShippingAddress = ?, PhoneNumber = ? WHERE UserID = ?");
    $stmt->execute([$fullName, $email, $address, $phone, $userID]);

    echo "<script>alert(' Profile updated successfully!'); window.location.href='UserDashboard.php';</script>";
    exit();
}

$stmt = $conn->prepare("SELECT FullName, Email, ShippingAddress, PhoneNumber FROM Users WHERE UserID = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<form method="POST">
  <h2>Update Profile</h2>
  <label>Full Name:</label><br>
  <input type="text" name="fullname" value="<?= htmlspecialchars($user['FullName']) ?>" required><br><br>

  <label>Email:</label><br>
  <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required><br><br>

  <label>Shipping Address:</label><br>
  <textarea name="address" required><?= htmlspecialchars($user['ShippingAddress']) ?></textarea><br><br>

  <label>Phone Number:</label><br>
  <input type="text" name="phone" value="<?= htmlspecialchars($user['PhoneNumber']) ?>" required><br><br>

  <button type="submit">Update Profile</button>
</form>
