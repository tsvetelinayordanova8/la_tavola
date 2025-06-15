<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product_id'])) {
  $prodId = (int) $_POST['add_product_id'];
  $stmt = $conn->prepare("INSERT IGNORE INTO seasonal_products (productID) VALUES (?)");
  $stmt->bind_param("i", $prodId);
  $stmt->execute();
  header("Location: seasonal_products.php");
  exit;
}

if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  $stmt = $conn->prepare("DELETE FROM seasonal_products WHERE productID = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: seasonal_products.php");
  exit;
}

$allProducts = $conn->query("SELECT ProductID, ProductName FROM products")->fetch_all(MYSQLI_ASSOC);

$seasonals = $conn->query("
    SELECT sp.productID, p.ProductName, p.Price
    FROM seasonal_products sp
    JOIN products p ON sp.productID = p.ProductID
")->fetch_all(MYSQLI_ASSOC);

include "header.php";
?>
<?php
if (isset($_SESSION['email']) && $_SESSION['usertype'] == '2') {
  ?>
  <div class="seasonal-products-container">
    <?php include("admin_menu.php") ?>
    <main class="update-products">
      <h2>Сезонни продукти</h2>
      <button class="seasonal-add-btn" onclick="openAddModal()">Добави сезонен продукт</button>

      <table class="styled-table">
        <thead>
          <tr>
            <th>ProductID</th>
            <th>Име</th>
            <th>Цена</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($seasonals as $row): ?>
            <tr>
              <td><?= $row['productID'] ?></td>
              <td><?= htmlspecialchars($row['ProductName']) ?></td>
              <td><?= number_format($row['Price'], 2) ?> лв</td>
              <td>
                <button class="delete-btn"
                  onclick="if(confirm('Сигурни ли сте?')){ window.location='seasonal_products.php?delete=<?= $row['productID'] ?>' }">
                  Изтрий
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div id="seasonal-addModal" class="seasonal-modal">
        <div class="seasonal-modal-content">
          <span class="seasonal-close" onclick="closeAddModal()">×</span>
          <h3>Добави сезонен продукт</h3>
          <form method="POST" id="seasonal-addForm">
            <label class="seasonal-label">Продукт:</label>
            <select name="add_product_id" id="productSelect" style="width:100%" required>
              <option value="">-- Избери продукт --</option>
              <?php foreach ($allProducts as $p): ?>
                <option value="<?= $p['ProductID'] ?>">
                  <?= htmlspecialchars($p['ProductName']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="seasonal-modal-buttons">
              <button type="submit" class="seasonal-save-btn">Добави</button>
              <button type="button" class="seasonal-cancel-btn" onclick="closeAddModal()">Отказ</button>
            </div>
          </form>
        </div>
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



<script>
  function openAddModal() {
    document.getElementById('seasonal-addModal').style.display = 'block';
    document.getElementById('productSearch').value = '';
    filterOptions();
  }
  function closeAddModal() {
    document.getElementById('seasonal-addModal').style.display = 'none';
  }
  window.onclick = e => {
    if (e.target == document.getElementById('seasonal-addModal')) {
      closeAddModal();
    }
  }
  function filterOptions() {
    const filter = document.getElementById('productSearch').value.toLowerCase();
    const sel = document.getElementById('productSelect');
    Array.from(sel.options).forEach(opt => {
      opt.style.display = opt.text.toLowerCase().includes(filter) ? '' : 'none';
    });
  }
  $(document).ready(function () {
    $('#productSelect').select2({
      placeholder: "-- Избери продукт --",
      dropdownParent: $('#seasonal-addModal'),
      width: '100%'
    });
  });

  function openAddModal() {
    $('#seasonal-addModal').show();
    $('#productSelect').val(null).trigger('change');
  }
  function closeAddModal() {
    $('#seasonal-addModal').hide();
  }
  window.onclick = function (e) {
    if (e.target.id === 'seasonal-addModal') {
      closeAddModal();
    }
  };
</script>