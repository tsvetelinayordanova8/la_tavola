<?php
include("database.php");
session_start();
header('Content-Type: application/json');

$userId = $_SESSION['UserID'] ?? null;

if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = intval($_POST['productId'] ?? 0);

    if ($action === 'update_quantity') {
        $delta = intval($_POST['delta'] ?? 0);

        $stmt = $conn->prepare("SELECT Quantity FROM cart_items WHERE UserID = ? AND ProductID = ?");
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();

        if ($item) {
            $newQty = max(1, $item['Quantity'] + $delta);

            $update = $conn->prepare("UPDATE cart_items SET Quantity = ? WHERE UserID = ? AND ProductID = ?");
            $update->bind_param("iii", $newQty, $userId, $productId);
            $update->execute();

            $priceStmt = $conn->prepare("SELECT Price FROM products WHERE ProductID = ?");
            $priceStmt->bind_param("i", $productId);
            $priceStmt->execute();
            $priceResult = $priceStmt->get_result();
            $product = $priceResult->fetch_assoc();

            $newTotalPrice = $newQty * $product['Price'];

            echo json_encode([
                'success' => true,
                'newQuantity' => $newQty,
                'newTotalPrice' => number_format($newTotalPrice, 2)
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Item not found']);
        }
        exit;
    }

    if ($action === 'remove_item') {
        $del = $conn->prepare("DELETE FROM cart_items WHERE UserID = ? AND ProductID = ?");
        $del->bind_param("ii", $userId, $productId);
        $success = $del->execute();
        echo json_encode(['success' => $success]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;
