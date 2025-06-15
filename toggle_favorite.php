<?php
session_start();
include('database.php');

$userId = $_SESSION['UserID'] ?? null;
$productId = $_POST['product_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$userId || !$productId) {
    http_response_code(400);
    echo 'Invalid request';
    exit;
}

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT IGNORE INTO favorites (UserID, ProductID, AddedAt) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
} elseif ($action === 'remove') {
    $stmt = $conn->prepare("DELETE FROM favorites WHERE UserID = ? AND ProductID = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
}
?>
