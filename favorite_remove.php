<?php
session_start();
require 'database.php';

if (!isset($_SESSION['UserID']) || !isset($_GET['product_id'])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['UserID'];
$productID = intval($_GET['product_id']);

$stmt = $conn->prepare("DELETE FROM Favorites WHERE UserID = ? AND ProductID = ?");
$stmt->bind_param("ii", $userID, $productID);
$stmt->execute();

header("Location: favorites.php");
exit;
?>
