<?php
include("header.php");
require 'database.php';

$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $weight = trim($_POST['weight']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $categoryID = (int) $_POST['add-product-category'];

    $stmt = $conn->prepare("SELECT CategoryName FROM categories WHERE CategoryID = ?");
    $stmt->bind_param("i", $categoryID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $categoryName = $result['CategoryName'];


    $categoryName_final = [
        "Салати" => "Salads",
        "Хляб" => "Bread",
        "Десерти" => "Desserts",
        "Основни" => "Main_dishes",
        "Паста" => "Pasta",
        "Пица" => "Pizza",
        "Предястия" => "Starters",
        "Сезонни предложения" => "Season_choices"
    ];

    $categprySource = isset($categoryName_final[$categoryName]) ? $categoryName_final[$categoryName] : "Unknown";

    $targetDir = "Sources/Menu/" . $categprySource;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $fileName = basename($_FILES["add-product-photo"]["name"]);
    $targetFile = $targetDir . "/" . $fileName;
    $photoPath = "Sources/Menu/" . $categprySource . "/" . $fileName;

    if (move_uploaded_file($_FILES["add-product-photo"]["tmp_name"], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO products (ProductName, Weight, Price, Description, CategoryID, PhotoSource)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsss", $name, $weight, $price, $description, $categoryID, $photoPath);
        $stmt->execute();
        $success = true;
    } else {
        $error = "Грешка при качване на снимката.";
    }
}
?>
<?php
if (isset($_SESSION['email']) && $_SESSION['usertype'] == '2') {
    ?>
    <div class="add-product-container">
        <?php include("admin_menu.php") ?>
        <main class="add-product">
            <h2 class="add-product-h2">Създаване на нов продукт</h2>

            <?php if (!empty($success)): ?>
                <div class="add-product-success">✅ Продуктът беше добавен успешно!</div>
            <?php elseif (!empty($error)): ?>
                <div class="add-product-error">❌ <?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="add-product-form">
                <label for="name">Име:</label>
                <input type="text" name="name" id="name" required><br>
                <div class="price-weight" id="weight">
                    <label for="weight">Грамаж:</label>
                    <input type="text" name="weight" id="weight" required>
                </div>
                <div class="price-weight">
                    <label for="price">Цена (лв):</label>
                    <input type="number" step="0.01" name="price" id="price" required>
                </div>
                <label for="description">Описание:</label>
                <textarea name="description" id="description" rows="4" required></textarea>

                <label for="category">Категория:</label>
                <select name="add-product-category" id="add-product-category" required>
                    <option value="">-- Избери категория --</option>
                    <?php foreach ($categories as $cat): ?>
                        <?php if ($cat['CategoryName'] !== 'Сезонни предложения'): ?>
                            <option value="<?= $cat['CategoryID'] ?>"><?= htmlspecialchars($cat['CategoryName']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </select>

                <label for="add-product-photo">Снимка:</label>
                <input type="file" name="add-product-photo" id="add-product-photo" accept="image/*" required>

                <button type="submit" class="add-product-button"><i class="fas fa-upload"></i> Добави продукт</button>
            </form>
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