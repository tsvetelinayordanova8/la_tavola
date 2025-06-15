<?php
include("database.php");

$action = $_GET['action'] ?? '';

switch ($action) {
  case 'get_categories':
    $result = mysqli_query($conn, "SELECT CategoryID, CategoryName FROM categories");
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($categories);
    exit;

  case 'get_products':
    $query = "SELECT * FROM lunch_menu_products WHERE 1=1";
    if (!empty($_GET['category'])) {
      $category = intval($_GET['category']);
      $query .= " AND CategoryID = $category";
    }
    if (!empty($_GET['max_price'])) {
      $price = floatval($_GET['max_price']);
      $query .= " AND Price <= $price";
    }
    if (!empty($_GET['search'])) {
      $search = mysqli_real_escape_string($conn, $_GET['search']);
      $query .= " AND ProductName LIKE '%$search%'";
    }
    $result = mysqli_query($conn, $query);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($products);
    exit;

  case 'save_product':
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['LunchProductID'] ?? null;
    $name = mysqli_real_escape_string($conn, $data['ProductName']);
    $desc = mysqli_real_escape_string($conn, $data['Description']);
    $weight = intval($data['Weight']);
    $price = floatval($data['Price']);
    $category = intval($data['CategoryID']);
    $isActive = !empty($data['IsActive']) ? 1 : "NULL";

    if ($id) {
      $query = "UPDATE lunch_menu_products SET ProductName='$name', Description='$desc', Weight=$weight, Price=$price, CategoryID=$category, IsActive=$isActive WHERE LunchProductID=$id";
    } else {
      $query = "INSERT INTO lunch_menu_products (ProductName, Description, Weight, Price, CategoryID, IsActive, PhotoSource) 
          VALUES ('$name','$desc',$weight,$price,$category,$isActive,'Sources/no_photo_sign.png')";
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => mysqli_query($conn, $query)]);
    exit;

  case 'delete_product':
    $id = intval($_GET['id']);
    $query = "DELETE FROM lunch_menu_products WHERE LunchProductID = $id";
    header('Content-Type: application/json');
    echo json_encode(['success' => mysqli_query($conn, $query)]);
    exit;

  case 'toggle_active':
    $id = intval($_GET['id']);
    $active = $_GET['active'] === '1' ? 1 : "NULL";
    $query = "UPDATE lunch_menu_products SET IsActive = $active WHERE LunchProductID = $id";
    header('Content-Type: application/json');
    echo json_encode(['success' => mysqli_query($conn, $query)]);
    exit;
}

include("header.php");

if (isset($_SESSION['email']) && $_SESSION['usertype'] == '2') {
  ?>
  <div class="container-lunch">
    <?php include("admin_menu.php") ?>
    <main class="update-products">
      <h2>Управление на обедното меню</h2>

      <div class="top-controls-lunch">
        <button class="edit-save" onclick="openCreateModal()">+ Добави продукт</button>
        <input type="number" id="priceFilter" placeholder="Макс. цена">
        <input type="text" id="searchInput" placeholder="Търси по име...">
        <select id="categoryFilter">
          <option value="">Всички категории</option>
        </select>


        <table id="productsTable-lunch">
          <thead>
            <tr>
              <th>Име</th>
              <th>Описание</th>
              <th>Грамаж</th>
              <th>Цена</th>
              <th>Категория</th>
              <th>В меню</th>
              <th>Действия</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>

      <div id="productModal-lunch" class="modal-lunch">
        <div class="modal-content-lunch">
          <span class="edit-close" onclick="closeModal()">×</span>
          <h3 id="modalTitle-lunch">Нов продукт</h3>
          <label>Име</label>
          <input type="text" id="productName">

          <label>Описание</label>
          <textarea id="productDescription-lunch"></textarea>

          <label>Грамаж (гр)</label>
          <input type="number" id="productWeight-lunch">

          <label>Цена (лв)</label>
          <input type="number" id="productPrice-lunch" step="0.01">

          <label>Категория</label>
          <select id="productCategory-lunch" required>
          </select>

          <label>Включи в обедното меню</label>
          <label class="switch">
            <input type="checkbox" id="productIsActive-lunch">
            <span class="slider round"></span>
          </label>
          <div class="form-actions">
            <button class="edit-cancel" onclick="closeModal()">Отказ</button>
            <button class="edit-save" onclick="saveProduct()">Запази</button>
          </div>
        </div>
        <?php
} else {
  echo "<p class='no-permission-text'>Нямате права за тази страница!</p";
}
?>
  </main>
</div>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    loadCategories();
    loadProducts();

    document.getElementById("searchInput").addEventListener("input", loadProducts);
    document.getElementById("categoryFilter").addEventListener("change", loadProducts);
    document.getElementById("priceFilter").addEventListener("input", loadProducts);
  });

  function loadCategories() {
    fetch("lunch-menu-admin.php?action=get_categories")
      .then(res => res.json())
      .then(data => {
        const catSelect = document.getElementById("categoryFilter");
        const formSelect = document.getElementById("productCategory-lunch");
        data.forEach(cat => {
          const opt = new Option(cat.CategoryName, cat.CategoryID);
          const opt2 = new Option(cat.CategoryName, cat.CategoryID);
          catSelect.add(opt);
          formSelect.add(opt2);
        });
      });
  }

  function loadProducts() {
    const category = document.getElementById("categoryFilter").value;
    const price = document.getElementById("priceFilter").value;
    const search = document.getElementById("searchInput").value;
    const url = `lunch-menu-admin.php?action=get_products&category=${category}&max_price=${price}&search=${search}`;

    fetch(url)
      .then(res => res.json())
      .then(products => {
        const tbody = document.querySelector("#productsTable-lunch tbody");
        tbody.innerHTML = "";
        products.forEach(p => {
          const row = document.createElement("tr");
          row.innerHTML = `
          <td>${p.ProductName}</td>
          <td>${p.Description}</td>
          <td>${p.Weight}</td>
          <td>${parseFloat(p.Price).toFixed(2)}</td>
          <td>${p.CategoryID}</td>
          <td>
  <label class="switch">
    <input type="checkbox" ${p.IsActive ? "checked" : ""} onchange="toggleActive(${p.LunchProductID}, this.checked)">
    <span class="slider round"></span>
  </label>
</td>
          <td class="p-3">
            <button class="edit-btn-lunch" onclick="editProduct('${encodeURIComponent(JSON.stringify(p))}')">Редактирай</button>

            <button class="delete-btn-lunch" onclick="deleteProduct(${p.LunchProductID})">Изтрий</button>
          </td>`;
          tbody.appendChild(row);
        });
      });
  }

  function openCreateModal() {
    document.getElementById("modalTitle-lunch").innerText = "Нов продукт";
    document.getElementById("productModal-lunch").style.display = "flex";
    document.getElementById("productName").value = "";
    document.getElementById("productDescription-lunch").value = "";
    document.getElementById("productWeight-lunch").value = "";
    document.getElementById("productPrice-lunch").value = "";
    document.getElementById("productCategory-lunch").value = "";
    document.getElementById("productIsActive-lunch").checked = true;
    document.getElementById("productModal-lunch").dataset.editing = "";
  }

  function editProduct(pStr) {
    const p = JSON.parse(decodeURIComponent(pStr));
    document.getElementById("modalTitle-lunch").innerText = "Редакция на продукт";
    document.getElementById("productModal-lunch").style.display = "flex";
    document.getElementById("productName").value = p.ProductName;
    document.getElementById("productDescription-lunch").value = p.Description;
    document.getElementById("productWeight-lunch").value = p.Weight;
    document.getElementById("productPrice-lunch").value = p.Price;
    document.getElementById("productCategory-lunch").value = p.CategoryID;
    document.getElementById("productIsActive-lunch").checked = !!p.IsActive;
    document.getElementById("productModal-lunch").dataset.editing = p.LunchProductID;
  }

  function saveProduct() {
    const id = document.getElementById("productModal-lunch").dataset.editing;
    const category = document.getElementById("productCategory-lunch").value;

    if (!category) {
      alert("Моля, изберете категория.");
      return;
    }

    const payload = {
      LunchProductID: id || null,
      ProductName: document.getElementById("productName").value,
      Description: document.getElementById("productDescription-lunch").value,
      Weight: document.getElementById("productWeight-lunch").value,
      Price: document.getElementById("productPrice-lunch").value,
      CategoryID: category,
      IsActive: document.getElementById("productIsActive-lunch").checked
    };

    fetch("lunch-menu-admin.php?action=save_product", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    }).then(() => {
      closeModal();
      loadProducts();
    });
  }


  function closeModal() {
    document.getElementById("productModal-lunch").style.display = "none";
  }

  function deleteProduct(id) {
    if (!confirm("Сигурни ли сте, че искате да изтриете този продукт?")) return;
    fetch(`lunch-menu-admin.php?action=delete_product&id=${id}`)
      .then(() => loadProducts());
  }

  function toggleActive(id, state) {
    fetch(`lunch-menu-admin.php?action=toggle_active&id=${id}&active=${state ? 1 : 0}`)
      .then(() => loadProducts());
  }
  document.getElementById("productModal-lunch").addEventListener("click", function (event) {
    if (event.target === this) {
      closeModal();
    }
  });
</script>
<?php
include("footer.php");
?>