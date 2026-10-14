<?php
$hash = password_hash('adminpass', PASSWORD_DEFAULT);
echo "Hashed password: " . $hash;
?>
