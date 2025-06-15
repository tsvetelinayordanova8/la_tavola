<?php

require 'database.php';

$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['delete'])) {
    $productID = (int) $_GET['delete'];
    $conn->query("DELETE FROM products WHERE ProductID = $productID");
    header("Location: update_products.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = (int) $_POST['edit_id'];
    $name = trim($_POST['name']);
    $weight = trim($_POST['weight']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $categoryID = (int) $_POST['category'];

    $stmt = $conn->prepare("UPDATE products SET ProductName=?, Weight=?, Price=?, Description=?, CategoryID=? WHERE ProductID=?");
    $stmt->bind_param("ssdssi", $name, $weight, $price, $description, $categoryID, $id);
    $stmt->execute();
}

$products = $conn->query("SELECT p.*, c.CategoryName FROM products p JOIN categories c ON p.CategoryID = c.CategoryID")->fetch_all(MYSQLI_ASSOC);

include("header.php");

if (isset($_SESSION['email']) && $_SESSION['usertype'] == '2') {
    ?>
    <div class="update-product-container">
        <?php include("admin_menu.php") ?>
        <main class="update-products">
            <h2>Редактиране на продукти</h2>
            <div class="search-bar">
                <label for="orderSearch">🔍 Търси продукт:</label>
                <input type="text" id="orderSearch" class="search-input" placeholder="Въведи име или описание...">
            </div>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Име</th>
                        <th>Грамаж</th>
                        <th>Цена</th>
                        <th>Описание</th>
                        <th>Категория</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr data-id="<?= $product['ProductID'] ?>" data-category-id="<?= $product['CategoryID'] ?>">
                            <td><?= $product['ProductID'] ?></td>
                            <td class="col-name"><?= htmlspecialchars($product['ProductName']) ?></td>
                            <td class="col-weight"><?= $product['Weight'] ?></td>
                            <td class="col-price"><?= $product['Price'] ?> лв</td>
                            <td class="col-description"><?= htmlspecialchars($product['Description']) ?></td>
                            <td><?= htmlspecialchars($product['CategoryName']) ?></td>
                            <td class="p-3">
                                <button onclick="editProduct(<?= $product['ProductID'] ?>)">Редактирай</button>
                                <button onclick="confirmDelete(<?= $product['ProductID'] ?>)">Изтрий</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div id="editModal" class="edit-modal">
                <div class="edit-modal-content">
                    <span class="edit-close" onclick="closeModal()">×</span>
                    <h3>Редактиране на продукт</h3>
                    <form method="POST" id="editForm">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <label>Име: <input type="text" name="name" id="edit_name" required></label>
                        <label>Грамаж: <input type="text" name="weight" id="edit_weight" required></label>
                        <label>Цена: <input type="number" step="0.01" name="price" id="edit_price" required></label>
                        <label>Описание: <input type="text" name="description" id="edit_description" required></label>
                        <label>Категория:
                            <select name="category" id="edit_category" class="form_select" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['CategoryID'] ?>"><?= htmlspecialchars($cat['CategoryName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <div class="form-actions">
                            <button type="button" class="edit-cancel" onclick="closeModal()">Отказ</button>
                            <button type="submit" class="edit-save">Запази</button>
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
    document.getElementById('orderSearch').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const name = row.querySelector('.col-name').innerText.toLowerCase();
            const description = row.querySelector('.col-description').innerText.toLowerCase();
            if (name.includes(searchTerm) || description.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    function confirmDelete(id) {
        if (confirm("Сигурни ли сте, че искате да изтриете този продукт?")) {
            window.location.href = 'update_products.php?delete=' + id;
        }
    }
    function editProduct(id) {
        const row = document.querySelector(`tr[data-id='${id}']`);
        const name = row.querySelector('.col-name').innerText;
        const weight = row.querySelector('.col-weight').innerText;
        const price = row.querySelector('.col-price').innerText.replace(' лв', '');
        const description = row.querySelector('.col-description').innerText;
        const categoryID = row.getAttribute('data-category-id');

        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_weight').value = weight;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_category').value = categoryID;

        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    window.onclick = function (event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>