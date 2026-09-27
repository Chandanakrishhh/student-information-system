<?php
include("../config/db_connect.php");

$admin_password = password_hash("admin123", PASSWORD_DEFAULT);
$faculty_password = password_hash("faculty123", PASSWORD_DEFAULT);

// Admin account
//mysqli_query($conn, "INSERT INTO users (username, password, role, faculty_id) 
   // VALUES ('admin', '$admin_password', 'admin', NULL)");

// Faculty account - make sure faculty_id 1 actually exists in your 'faculty' table
mysqli_query($conn, "INSERT INTO users (username, password, role, faculty_id) 
    VALUES ('faculty1', '$faculty_password', 'faculty', 101)");

echo "Users created successfully!";
?>