<?php
session_start();
include("database.php");

$day_id = isset($_GET['day_id']) ? $_GET['day_id'] : null;

if (empty($day_id) || !is_numeric($day_id)) {
    die(json_encode(['error' => 'Invalid or missing day_id']));
}

$sql = "
    SELECT m.id, m.meal_name, m.price, m.weight, m.description
    FROM meal_schedule ms
    JOIN meals m ON ms.meal_id = m.id
    WHERE ms.day_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $day_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $meals = [];
    while ($row = $result->fetch_assoc()) {
        $meals[] = [
            'id' =>$row['id'],
            'meal_name' => $row['meal_name'],
            'price' => $row['price'],
            'weight' => $row['weight'],
            'description' => $row['description']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($meals);
} else {
    echo json_encode(['error' => 'No meals found for the selected day']);
}

$stmt->close();
$conn->close();
?>
