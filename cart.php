<?php
include("header.php");
include("database.php"); 

$userId = $_SESSION['UserID'] ?? null;

$userName = '';
if ($userId) {
    $stmt = $conn->prepare("SELECT PersonName FROM users WHERE UserID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $userName = $user['PersonName'] ?? '';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    $name = $_POST['personName'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $comment = $_POST['comment'];
    $status = 'Pending';

    $cartItems = $conn->prepare("SELECT * FROM cart_items WHERE UserID = ?");
    $cartItems->bind_param("i", $userId);
    $cartItems->execute();
    $result = $cartItems->get_result();
    $items = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($items)) {
        echo "<script>alert('Количката е празна!');</script>";
    } else {
        $total = 0;
        $orderProducts = [];

        foreach ($items as $item) {
            if ($item['ProductType'] === 'lunch') {
                $priceStmt = $conn->prepare("SELECT Price FROM lunch_menu_products WHERE LunchProductID = ?");
            } else {
                $priceStmt = $conn->prepare("SELECT Price FROM products WHERE ProductID = ?");
            }
            $priceStmt->bind_param("i", $item['ProductID']);
            $priceStmt->execute();
            $priceResult = $priceStmt->get_result();
            $prod = $priceResult->fetch_assoc();

            $lineTotal = $prod['Price'] * $item['Quantity'];
            $total += $lineTotal;

            $orderProducts[] = [
                'ProductID' => $item['ProductID'],
                'Quantity' => $item['Quantity'],
                'UnitPrice' => $prod['Price'],
                'ProductType' => $item['ProductType'] 
            ];
        }

        $insertOrder = $conn->prepare("INSERT INTO orders (CustomerID, TotalAmount, Status, DeliveryAddress, Phone, Comment) VALUES (?, ?, ?, ?, ?, ?)");
        $insertOrder->bind_param("idssss", $userId, $total, $status, $address, $phone, $comment);
        $insertOrder->execute();
        $orderId = $conn->insert_id;

        foreach ($orderProducts as $prod) {
            $insertItem = $conn->prepare("INSERT INTO orderitems (OrderID, ProductID, Quantity, UnitPrice, ProductType) VALUES (?, ?, ?, ?, ?)");
            $insertItem->bind_param("iiids", $orderId, $prod['ProductID'], $prod['Quantity'], $prod['UnitPrice'], $prod['ProductType']);
            $insertItem->execute();
        }

        $clearCart = $conn->prepare("DELETE FROM cart_items WHERE UserID = ?");
        $clearCart->bind_param("i", $userId);
        $clearCart->execute();

        echo "<script>location.href='order_success.php';</script>";
        exit;
    }
}


?>
<script>
    function showStep(step) {
        if (step === 1) {
            document.getElementById('step-2').style.display = 'none';
        } else if (step === 2) {
            document.getElementById('step-2').style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        showStep(1);
    });

    function changeQuantity(productId, delta) {
        fetch('update_quantity.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'update_quantity',
                productId: productId,
                delta: delta
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('qty-' + productId).textContent = data.newQuantity;

                    const priceElement = document.getElementById('price-' + productId);
                    if (priceElement) {
                        priceElement.textContent = data.newTotalPrice + ' лв.';
                    }
                    updateCartTotal();
                } else {
                    alert('Грешка: ' + (data.error || 'При обновяване на количката'));
                }


            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Грешка при свързване със сървъра');
            });

    }

    function removeFromCart(productId) {
        if (!confirm('Сигурни ли сте, че искате да премахнете този продукт?')) return;

        fetch('update_quantity.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'remove_item',
                productId: productId
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload(); 
                } else {
                    alert('Грешка: ' + (data.error || 'Неуспешно премахване на продукта'));
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Грешка при свързване със сървъра');
            });
    }
    function updateCartTotal() {
        let total = 0;
        document.querySelectorAll('[id^="price-"]').forEach(el => {
            const priceText = el.textContent.replace(' лв.', '').replace(',', '.');
            total += parseFloat(priceText);
        });
        document.getElementById('cart-total').textContent = total.toFixed(2) + ' лв.';
    }


</script>
<div class="container-cart">

    <div class="steps-wrapper-cart">

        <div id="step-1" class="card">

            <h2>Информация за поръчката</h2>

            <?php
            $stmt = $conn->prepare("
    SELECT c.CartItemID, c.ProductID, c.Quantity, c.ProductType, 
           CASE 
               WHEN c.ProductType = 'lunch' THEN l.ProductName 
               ELSE p.ProductName 
           END AS ProductName,
           CASE 
               WHEN c.ProductType = 'lunch' THEN l.Price 
               ELSE p.Price 
           END AS Price
    FROM cart_items c
    LEFT JOIN products p ON c.ProductID = p.ProductID AND c.ProductType = 'main'
    LEFT JOIN lunch_menu_products l ON c.ProductID = l.LunchProductID AND c.ProductType = 'lunch'
    WHERE c.UserID = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = $result->fetch_all(MYSQLI_ASSOC);

            if (count($products) === 0) {
                echo <<<HTML
                    <div class="empty_cart">
                        <img src="Sources/empty_cart.png" alt="empty cart" width="300vw">
                        <h3>Количката е празна</h3>
                        <p>Добавете желаните от Вас продукти </p>
                    </div>
                    HTML;
            } else {
                foreach ($products as $prod): ?>
                    <div class="product-row-cart">
                        <div>
                            <p class="cart-product-name"> <?= htmlspecialchars($prod['ProductName']) ?></p>
                        </div>
                        <div class="quantity-controls-cart">
                            <button type="button" onclick="changeQuantity(<?= $prod['ProductID'] ?>, -1)"
                                class="minus-cart">-</button>
                            <span id="qty-<?= $prod['ProductID'] ?>"><?= $prod['Quantity'] ?></span>
                            <button type="button" onclick="changeQuantity(<?= $prod['ProductID'] ?>, 1)"
                                class="plus-cart">+</button>
                        </div>
                        <div class="cart-price">
                            <p id="price-<?= $prod['ProductID'] ?>">
                                <?php
                                $totalPrice = $prod['Price'] * $prod['Quantity'];
                                echo number_format($totalPrice, 2) . ' лв.';
                                ?>
                            </p>
                        </div>
                        <div class="remove-cart-icon">
                            <div onclick="removeFromCart(<?= $prod['ProductID'] ?>)" class="btn-remove">
                                <i class="fa-solid fa-trash"></i>
                            </div>
                        </div>
                    </div>

                <?php endforeach;
                ?>
                <div class="cart-total">
                    <strong>Обща сума: <span id="cart-total">
                            <?php
                            $cartTotal = 0;
                            foreach ($products as $prod) {
                                $cartTotal += $prod['Price'] * $prod['Quantity'];
                            }
                            echo number_format($cartTotal, 2) . ' лв.';
                            ?>
                        </span></strong>
                </div>
                <?php
            }

            ?>

            <?php if (count($products) > 0): ?>
                <button onclick="showStep(2)" class="btn-next-cart">Напред</button>
            <?php endif; ?>
        </div>

        <div id="step-2" class="card" style="display:none;">

            <h2>Адрес за доставка</h2>

            <form method="POST" action="" id="orderForm">
                <label for="personName">Име</label>
                <input type="text" id="personName" name="personName" value="<?= htmlspecialchars($userName) ?>"
                    required>

                <label for="address">Адрес</label>
                <input type="text" id="address" name="address" required>

                <label for="phone">Телефон</label>
                <input type="tel" id="phone" name="phone" required>

                <label for="comment">Допълнителни коментари</label>
                <textarea id="comment" name="comment" rows="3"></textarea>

                <button type="submit" name="submit_order" class="btn-submit-cart">Поръчай</button>
            </form>
        </div>
    </div>
</div>
<?php
include("footer.php");
?>