<?php
/**
 * Password Hash Generator
 * Run this file to generate a password hash for admin login
 * Delete this file after use!
 */

// Change this to your desired password
$password = "YourNewPassword123";

// Generate hash
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password Hash Generator\n";
echo "=======================\n\n";
echo "Password: $password\n";
echo "Hash: $hash\n\n";
echo "SQL Query to update admin password:\n";
echo "UPDATE users SET password = '$hash' WHERE role = 'admin';\n\n";
echo "⚠️ DELETE THIS FILE AFTER USE!\n";
?>
