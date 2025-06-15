<?php
include("header.php");
include('database.php');

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['UserID'];

$stmt = $conn->prepare("
    SELECT p.ProductID, p.ProductName, p.Price, p.PhotoSource
    FROM Favorites f
    JOIN products p ON f.ProductID = p.ProductID
    WHERE f.UserID = ?
");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$favorites = [];
while ($row = $result->fetch_assoc()) {
    $favorites[] = $row;
}
?>
<div class="favorites-container">
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
    <main class="favorites">
        <h2 class="favorites-title">Любими продукти</h2>
        <div class="favorites-grid">


            <?php if (count($favorites) > 0): ?>
                <div class="favorites-grid">
                    <?php foreach ($favorites as $product): ?>
                        <div class="favorites-product-card">
                            <a class="remove-fav" href="favorite_remove.php?product_id=<?= $product['ProductID'] ?>"
                                title="Премахни от любими"
                                onclick="return confirm('Сигурни ли сте, че искате да изтриете този продукт?');"><i class="fa-solid fa-circle-xmark"></i></a>
                            <img src="<?= htmlspecialchars($product['PhotoSource']) ?>"
                                alt="<?= htmlspecialchars($product['ProductName']) ?>">
                            <div class="product-info">
                                <div class="product-title"><?= htmlspecialchars($product['ProductName']) ?></div>
                                <div class="product-price"><?= number_format($product['Price'], 2) ?> лв.</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="favorites-empty-message">Нямате добавени любими продукти.</p>
            <?php endif; ?>
        </div>
    </main>
    
</div>
<?php
    include("footer.php");
    ?>