<?php
session_start();
include("database.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents('php://input'), true);

// General values
$dayId = $data['day_id'] ?? null;
$mealId = $data['meal_id'] ?? null;
$name = $data['name'] ?? null;
$weight = $data['weight'] ?? null;
$price = $data['price'] ?? null;
$description = $data['description'] ?? null;
$category = $data['category'] ?? null; 

if (!$dayId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing day ID']);
    exit;
}

if ($mealId) {
    $checkLinkStmt = $conn->prepare("SELECT id FROM meal_schedule WHERE day_id = ? AND meal_id = ?");
    $checkLinkStmt->bind_param("ii", $dayId, $mealId);
    $checkLinkStmt->execute();
    $checkLinkStmt->store_result();

    if ($checkLinkStmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Meal already added to this day']);
    } else {
        $stmt = $conn->prepare("INSERT INTO meal_schedule (day_id, meal_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $dayId, $mealId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error linking existing meal']);
        }
        $stmt->close();
    }
    exit;
}

if (!$name || !$weight || !$price || !$category) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields for new meal']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM meals WHERE meal_name = ? AND weight = ? AND price = ? AND description = ? AND category = ?");
$stmt->bind_param('sddss', $name, $weight, $price, $description, $category);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($mealId);
    $stmt->fetch();
    $stmt->close();
} else {
    $stmt->close();
    $insertMeal = $conn->prepare("INSERT INTO meals (meal_name, weight, price, description, category) VALUES (?, ?, ?, ?, ?)");    
    $insertMeal->bind_param('sddss', $name, $weight, $price, $description, $category);
    if (!$insertMeal->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to insert new meal']);
        exit;
    }
    $mealId = $insertMeal->insert_id;
    $insertMeal->close();
}

$linkCheck = $conn->prepare("SELECT id FROM meal_schedule WHERE day_id = ? AND meal_id = ?");
$linkCheck->bind_param("ii", $dayId, $mealId);
$linkCheck->execute();
$linkCheck->store_result();

if ($linkCheck->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Meal already linked to this day']);
    $linkCheck->close();
    exit;
}

$link = $conn->prepare("INSERT INTO meal_schedule (day_id, meal_id) VALUES (?, ?)");
$link->bind_param("ii", $dayId, $mealId);
if ($link->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to link meal to day']);
}
$link->close();
?>
