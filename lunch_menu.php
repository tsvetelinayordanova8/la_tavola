<?php

include("header.php");
date_default_timezone_set('Europe/Sofia');
$currentHour = (int) date('H');
$isLunchActive = ($currentHour >= 10 && $currentHour < 15);

$query = "SELECT c.CategoryName AS Category, l.LunchProductID, l.ProductName, l.Description, l.Price
          FROM lunch_menu_products l
          JOIN categories c ON l.CategoryID = c.CategoryID
          WHERE l.IsActive = 1
          ORDER BY c.CategoryID, l.ProductName";

$result = $conn->query($query);

$menu = [];

while ($row = $result->fetch_assoc()) {
    $category = $row['Category'];
    if (!isset($menu[$category])) {
        $menu[$category] = [];
    }
    $menu[$category][] = $row;
}
?>
<div <?php if (!$isLunchActive)
    echo 'class="blurred"'; ?>>
    <div class="lunch-wrapper">
        <div class="lunch-menu-container">
            <img src="Sources/Logo1.png" alt="logo" width="200vw">
            <div class="lunch-menu-title">Обедно меню </div>

            <?php foreach ($menu as $category => $products): ?>
                <div class="lunch-category-title"><?= htmlspecialchars($category) ?></div>

                <?php foreach ($products as $row): ?>
                    <div class="lunch-product">
                        <div class="lunch-product-info">
                            <div class="lunch-product-name"><?= htmlspecialchars($row["ProductName"]) ?></div>
                            <div class="lunch-product-description"><?= htmlspecialchars($row["Description"]) ?></div>
                        </div>
                        <div class="lunch-product-right">
                            <span class="lunch-product-price"><?= number_format($row["Price"], 2) ?> лв.</span>
                            <button class="lunch-order-button"
                                onclick="addToCart(<?= $row['LunchProductID'] ?>)">Поръчай</button>


                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endforeach; ?>
        </div>
    </div>
    <?php if (!$isLunchActive): ?>
        <div class="blur-overlay">

            <div class="message-box">
                <strong>Обедното меню е активно от 10:00 до 15:00 часа.</strong><br>
                Моля, върнете се в този времеви диапазон.
                <button class="message-btn" onclick="closeMessage()">Ок</button>
            </div>
        </div>
    <?php endif; ?>
</div>
<script>

    function addToCart(productId) {
        const formData = new FormData();
        formData.append("product_id", productId);
        formData.append("product_type", "lunch");
        formData.append("quantity", 1);

        fetch("add_to_cart.php", {
            method: "POST",
            body: formData
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error("HTTP статус: " + res.status);
                }
                return res.text();
            })
            .then(data => {
                const response = data.trim();
                if (response === "not_logged_in") {
                    alert("Не сте влезли в профила си!");
                } else {
                    alert("Продуктът беше успешно добавен в количката!");
                    console.log("Отговор от сървъра:", response);
                }
            })
            .catch(error => {
                console.error("Грешка при заявката:", error);
                alert("Възникна грешка при добавяне в кошницата.");
            });
    }

    function closeMessage() {
        const overlay = document.querySelector('.blur-overlay');
        const mainContent = document.querySelector('.main-content');
        if (overlay) overlay.style.display = 'none';
        if (mainContent) mainContent.classList.remove('blurred');
    }
</script>

<?php

include("footer.php");
?>