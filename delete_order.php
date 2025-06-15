<?php
include("database.php"); 

header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order_id'])) {
    echo json_encode(["success" => false, "error" => "Няма подадено ID."]);
    exit;
}

$orderId = intval($data['order_id']);

$conn->query("DELETE FROM orderitems WHERE OrderID = $orderId");

if ($conn->query("DELETE FROM orders WHERE OrderID = $orderId")) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}
?>
