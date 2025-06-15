<?php
header('Content-Type: application/json');

include('database.php'); 

try {
    $result = $conn->query("SELECT id, meal_name AS name FROM meals ORDER BY meal_name ASC");

    if ($result) {
        $meals = $result->fetch_all(MYSQLI_ASSOC); 
        echo json_encode($meals);
    } else {
        throw new Exception('Query failed');
    }
} catch (Exception $e) {
    http_response_code(500); 
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
