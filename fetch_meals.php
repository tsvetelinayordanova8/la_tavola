<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include('database.php'); 

if (isset($_GET['day_id'])) {
    $day_id = intval($_GET['day_id']);

    $query = "
        SELECT m.id, m.meal_name, m.weight, m.price, m.description
        FROM user_meals um
        JOIN meals m ON um.meal_id = m.id
        WHERE um.day_id = ?
    ";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $day_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $meals = [];
        while ($row = $result->fetch_assoc()) {
            $meals[] = $row;
        }

        echo json_encode($meals, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["error" => "Query preparation failed"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Missing day_id parameter"]);
}
?>
