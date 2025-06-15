<?php
include("header.php");

if (!isset($_SESSION["UserID"])) {
    header("Location: login_page.php");
    exit();
}

include("database.php");

$statusTranslations = [
    'Pending' => 'Обработва се',
    'Shipped' => 'Изпратена',
    'Delivered' => 'Доставена',
    'Cancelled' => 'Отказана',
];
$userID = $_SESSION["UserID"];
?>

<div class="orders-container">
    <aside class="orders-sidebar">
        <a href="my_orders.php"><i class="fas fa-box"></i> Моите поръчки</a>
        <a href="favorites.php"><i class="fas fa-heart"></i> Любими продукти</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Настройки</a>
        <?php if ($_SESSION["usertype"] == '2'): ?>
            <a href="admin_panel.php">
                <i class="fas fa-tools"></i>
                <span>Административен панел</span>
            </a>
        <?php endif; ?>
    </aside>

    <main class="orders">
        <h2>Моите поръчки</h2>
        <?php
        $query = "SELECT * FROM orders WHERE CustomerID = ? ORDER BY OrderDate DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $orders = $stmt->get_result();

        if ($orders->num_rows === 0) {
            echo "<p class='favorites-empty-message'>Нямате поръчки.</p>";
        } else {
            while ($order = $orders->fetch_assoc()) {
                $orderID = $order['OrderID'];

                $productQuery = "
                SELECT oi.Quantity, oi.UnitPrice, oi.ProductType, 
                       p.ProductName AS MainProductName, p.PhotoSource AS MainPhoto,
                       lp.ProductName AS LunchProductName, lp.PhotoSource AS LunchPhoto
                FROM orderitems oi
                LEFT JOIN products p ON oi.ProductID = p.ProductID AND oi.ProductType = 'main'
                LEFT JOIN lunch_menu_products lp ON oi.ProductID = lp.LunchProductID AND oi.ProductType = 'lunch'
                WHERE oi.OrderID = ?
            ";

                $pstmt = $conn->prepare($productQuery);
                $pstmt->bind_param("i", $orderID);
                $pstmt->execute();
                $products = $pstmt->get_result();

                $totalAmount = 0;
                $productHTML = "";

                while ($product = $products->fetch_assoc()) {
                    // Определяме име и снимка според типа
                    if ($product['ProductType'] == 'main') {
                        $productName = $product['MainProductName'];
                        $photoSource = $product['MainPhoto'];
                    } elseif ($product['ProductType'] == 'lunch') {
                        $productName = $product['LunchProductName'];
                        $photoSource = $product['LunchPhoto'];
                    } else {
                        $productName = "Неизвестен продукт";
                        $photoSource = "";
                    }

                    $productTotal = $product['Quantity'] * $product['UnitPrice'];
                    $totalAmount += $productTotal;

                    $productHTML .= "<div class='order-product-card'>";
                    $productHTML .= "<img src='{$photoSource}' alt='" . htmlspecialchars($productName) . "' class='product-img'>";
                    $productHTML .= "<div class='product-info'>";
                    $productHTML .= "<h4>" . htmlspecialchars($productName) . "</h4>";
                    $productHTML .= "<p>Брой: {$product['Quantity']}</p>";
                    $productHTML .= "<p>Цена за брой: " . number_format($product['UnitPrice'], 2, ',', ' ') . " лв.</p>";
                    $productHTML .= "<p><strong>Общо: " . number_format($productTotal, 2, ',', ' ') . " лв.</strong></p>";
                    $productHTML .= "</div></div>";
                }

                echo "<div class='order-box'>";
                echo "<p><i class='fas fa-hashtag'></i> Номер на поръчка: <strong>{$orderID}</strong></p>";
                echo "<p><i class='fas fa-money-bill'></i> Дължима сума: <strong>" . number_format($totalAmount, 2, ',', ' ') . " лв.</strong></p>";
                echo "<p><i class='fas fa-calendar-alt'></i> Дата на поръчка: <strong>{$order['OrderDate']}</strong></p>";
                $translatedStatus = $statusTranslations[$order['Status']] ?? $order['Status'];
                echo "<p><i class='fas fa-info-circle'></i> Статус: <strong>{$translatedStatus}</strong></p>";


                echo "<div class='order-products-container'>";
                echo $productHTML;
                echo "</div>";
                echo "</div>";
            }
        }
        ?>
    </main>
</div>
<?php
include("footer.php");
?>