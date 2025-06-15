<?php
include('database.php');
$data = json_decode(file_get_contents('php://input'), true);

$meal_id = $data['meal_id'];
$day_id = $data['day_id'];

if (!$meal_id || !$day_id) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO meal_deliveries (day_id, meal_id, delivery_type_id) VALUES (?, ?, 1)");
$stmt->bind_param("ii", $day_id, $meal_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}
