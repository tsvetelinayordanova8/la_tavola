<?php
$servername = "localhost";
$usernamedb = "root"; 
$password = ""; 
$dbname = "la_tavola_db";


$conn = new mysqli($servername, $usernamedb, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>