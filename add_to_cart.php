<?php
session_start();
include('database.php');

if (!isset($_SESSION['UserID'])) {
    
    echo "not_logged_in";
    exit;
}

$userId = $_SESSION['UserID'] ?? null;
$productId = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;
$productType = $_POST['product_type'] ?? 'main'; 

if (!$userId || !$productId || !in_array($productType, ['main', 'lunch'])) {
    http_response_code(400);
    echo 'Invalid request';
    exit;
}

$stmt = $conn->prepare("SELECT Quantity FROM cart_items WHERE UserID = ? AND ProductID = ? AND ProductType = ?");
$stmt->bind_param("iis", $userId, $productId, $productType);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($currentQuantity);
    $stmt->fetch();
    $newQuantity = $currentQuantity + $quantity;

    $updateStmt = $conn->prepare("UPDATE cart_items SET Quantity = ?, DateAdded = NOW() WHERE UserID = ? AND ProductID = ? AND ProductType = ?");
    $updateStmt->bind_param("iiis", $newQuantity, $userId, $productId, $productType);
    $updateStmt->execute();
    $updateStmt->close();

    echo "Актуализирано количество: $newQuantity";
} else {
    $insertStmt = $conn->prepare("INSERT INTO cart_items (UserID, ProductID, Quantity, DateAdded, ProductType) VALUES (?, ?, ?, NOW(), ?)");
    $insertStmt->bind_param("iiis", $userId, $productId, $quantity, $productType);
    $insertStmt->execute();
    $insertStmt->close();

    echo "Добавен продукт.";
}

$stmt->close();
$conn->close();
?>
