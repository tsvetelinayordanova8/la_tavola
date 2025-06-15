<?php
include("database.php");

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Липсват параметри']);
    exit;
}

$orderId = intval($data['order_id']);
$status = $data['status'];

$allowedStatuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
if (!in_array($status, $allowedStatuses)) {
    echo json_encode(['success' => false, 'error' => 'Невалиден статус']);
    exit;
}

$sql = "UPDATE orders SET Status = ? WHERE OrderID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $orderId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Грешка при запис']);
}

$stmt->close();
$conn->close();
