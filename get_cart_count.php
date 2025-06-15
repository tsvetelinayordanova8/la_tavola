<?php
session_start();
include("database.php");

$userID = $_SESSION['UserID'] ?? null;

if (!$userID) {
    echo 0;
    exit();
}

$sql = "SELECT SUM(Quantity) as total FROM cart_items WHERE UserID = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "SQL error: " . $conn->error;
    exit();
}

$stmt->bind_param("i", $userID);
$stmt->execute();

$stmt->bind_result($total);
$stmt->fetch();

echo $total ?? 0;

$stmt->close();
$conn->close();
