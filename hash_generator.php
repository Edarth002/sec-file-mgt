<?php
$password = 'abalujoshua';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash for '$password':</h2>";
echo "<p>Copy this entire string and use it in your SQL UPDATE query:</p>";
echo "<h3>" . $hashed_password . "</h3>";
?>