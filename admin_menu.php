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
  <div class="admin-aside">
    <a href="add_product.php">
      <i class="fa-solid fa-plus"></i>
      Създаване на нов продукт
    </a>
    <a href="update_products.php">
      <i class="fa-solid fa-pencil"></i>
      Редактиране на продукти
    </a>
    <a href="seasonal_products.php">
      <i class="fa-solid fa-clipboard"></i>
      Редактиране на сезонни предложения
    </a>
    <a href="lunch-menu-admin.php">
      <i class="fa-solid fa-utensils"></i>
      Обедно меню
    </a>
    <a href="users.php">
      <i class="fa-solid fa-users"></i>
      Потребители
    </a>
    <a href="orders.php">
      <i class="fa-solid fa-basket-shopping"></i>
      Поръчки
    </a>
  </div>
</aside>