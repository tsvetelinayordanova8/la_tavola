<?php
include("header.php");
?>
<?php
if (isset($_SESSION['email']) && $_SESSION['usertype'] == '2') {
    ?>
    <div class="admin-container">
        <aside class="orders-sidebar">
            <div>
                <a href="my_orders.php"><i class="fas fa-box"></i> Моите поръчки</a>
                <a href="favorites.php"><i class="fas fa-heart"></i> Любими продукти</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Настройки</a>
                <?php if ($_SESSION["usertype"] == '2'): ?>
                    <a href="admin_panel.php">
                        <i class="fas fa-tools"></i>
                        <span>Административен панел</span>
                    </a>
                <?php endif; ?>
            </div>
        </aside>
        <main class="administrative">
            <div class="admin-sections">
                <a href="add_product.php" class="admin-box">
                    <i class="fa-solid fa-plus"></i>
                    <span>Създаване на нов продукт</span>
                </a>
                <a href="update_products.php" class="admin-box">
                    <i class="fa-solid fa-pencil"></i>
                    <span>Редактиране на продукти</span>
                </a>
                <a href="seasonal_products.php" class="admin-box">
                    <i class="fa-solid fa-clipboard"></i>
                    <span>Редактиране на сезонни предложения</span>
                </a>
                <a href="lunch-menu-admin.php" class="admin-box">
                    <i class="fa-solid fa-utensils"></i>
                    <span>Обедно меню</span>
                </a>
                <a href="users.php" class="admin-box">
                    <i class="fa-solid fa-users"></i>
                    <span>Потребители</span>
                </a>
                <a href="orders.php" class="admin-box">
                    <i class="fa-solid fa-basket-shopping"></i>
                    <span>Поръчки</span>
                </a>

            </div>
            <?php
        } else {
            echo "<p class='no-permission-text'>Нямате права за тази страница!</p";
        }
        ?>
    </main>

</div>

<?php
include("footer.php");
?>