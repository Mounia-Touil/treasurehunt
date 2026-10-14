test_admin_login.php
<?php

$enteredPassword = 'adminpass';  
$storedHash = '$2y$10$8V72fJj7lz51Wx3u2z1wtO.Njuhb.VLaBj.YW1J0SkPWXLPmQlVPy';  

echo "<h3>Testing Password Verification</h3>";

if (password_verify($enteredPassword, $storedHash)) {
    echo "<p style='color: green;'> Password is correct</p>";
} else {
    echo "<p style='color: red;'> Password is incorrect</p>";
}
?>
