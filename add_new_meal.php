<?php
include('database.php');
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'];
$weight = $data['weight'];
$price = $data['price'];
$description = $data['description'];
$day_id = $data['day_id'];

if (!$name || !$weight || !$price || !$day_id) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO meals (meal_name, weight, price, description) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdds", $name, $weight, $price, $description);
$stmt->execute();

$newMealId = $conn->insert_id;

$linkStmt = $conn->prepare("INSERT INTO meal_deliveries (day_id, meal_id, delivery_type_id) VALUES (?, ?, 1)");
$linkStmt->bind_param("ii", $day_id, $newMealId);
$linkStmt->execute();

echo json_encode(['success' => true]);
