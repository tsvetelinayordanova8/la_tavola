<?php
header('Content-Type: application/json');
include("database.php"); 

if (!isset($_GET['order_id'])) {
    echo json_encode(['error' => 'Липсва ID на поръчката.']);
    exit;
}

$orderId = intval($_GET['order_id']);

$sql = "
    SELECT 
        oi.ProductID,
        oi.Quantity,
        oi.UnitPrice,
        oi.ProductType,
        CASE 
            WHEN oi.ProductType = 'main' THEN p.ProductName
            WHEN oi.ProductType = 'lunch' THEN l.ProductName
            ELSE 'Неизвестен продукт'
        END AS ProductName,
        CASE 
            WHEN oi.ProductType = 'main' THEN p.PhotoSource
            WHEN oi.ProductType = 'lunch' THEN l.PhotoSource
            ELSE ''
        END AS PhotoSource
    FROM orderitems oi
    LEFT JOIN products p ON oi.ProductID = p.ProductID AND oi.ProductType = 'main'
    LEFT JOIN lunch_menu_products l ON oi.ProductID = l.LunchProductID AND oi.ProductType = 'lunch'
    WHERE oi.OrderID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $row['UnitPrice'] = (float) $row['UnitPrice'];
    $row['Quantity'] = (int) $row['Quantity'];
    $items[] = $row;
}

echo json_encode($items);

$stmt->close();
$conn->close();
?>