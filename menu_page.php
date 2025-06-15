<?php
include('header.php');
include('database.php');

$userId = $_SESSION['UserID'] ?? null;

$categories = [];
$catResult = $conn->query("SELECT * FROM categories ORDER BY CategoryID");

if (!$catResult) {
    die("Грешка при заявката към базата: " . $conn->error);
}

while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}

$selectedCategory = $_GET['category'] ?? null;

$products = [];

if ($userId) {
    if ($selectedCategory == 8) {
        $stmt = $conn->prepare("
            SELECT p.*, 
            EXISTS(SELECT 1 FROM favorites f WHERE f.UserID = ? AND f.ProductID = p.ProductID) AS is_favorite 
            FROM products p 
            JOIN seasonal_products sp ON p.ProductID = sp.ProductID
        ");
        $stmt->bind_param("i", $userId);
    } elseif ($selectedCategory) {
        $stmt = $conn->prepare("
            SELECT p.*, 
            EXISTS(SELECT 1 FROM favorites f WHERE f.UserID = ? AND f.ProductID = p.ProductID) AS is_favorite 
            FROM products p WHERE p.CategoryID = ?
        ");
        $stmt->bind_param("ii", $userId, $selectedCategory);
    } else {
        $stmt = $conn->prepare("
            SELECT p.*, 
            EXISTS(SELECT 1 FROM favorites f WHERE f.UserID = ? AND f.ProductID = p.ProductID) AS is_favorite 
            FROM products p 
            JOIN seasonal_products sp ON p.ProductID = sp.ProductID
        ");
        $stmt->bind_param("i", $userId);
    }

    $stmt->execute();
    $result = $stmt->get_result();

} else {
    if ($selectedCategory == 8) {
        $result = $conn->query("
            SELECT p.* FROM products p 
            JOIN seasonal_products sp ON p.ProductID = sp.ProductID
        ");
    } elseif ($selectedCategory) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE CategoryID = ?");
        $stmt->bind_param("i", $selectedCategory);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("
            SELECT p.* FROM products p 
            JOIN seasonal_products sp ON p.ProductID = sp.ProductID
        ");
    }
}

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

?>
<div class="main-container-menu">
    <nav class="category-nav">
        <?php foreach ($categories as $cat): ?>
            <a href="?category=<?= $cat['CategoryID'] ?>"><?= htmlspecialchars($cat['CategoryName']) ?></a>
        <?php endforeach; ?>
    </nav>

    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <a href="#" class="product-card <?= !empty($product['is_favorite']) ? 'favorite' : '' ?>"
                data-name="<?= htmlspecialchars($product['ProductName']) ?>"
                data-photo="<?= htmlspecialchars($product['PhotoSource']) ?>"
                data-weight="<?= htmlspecialchars($product['Weight']) ?>"
                data-price="<?= htmlspecialchars($product['Price']) ?>"
                data-description="<?= htmlspecialchars($product['Description']) ?>" data-id="<?= $product['ProductID'] ?>">
                <img src="<?= htmlspecialchars($product['PhotoSource']) ?>"
                    alt="<?= htmlspecialchars($product['ProductName']) ?>">
                <h2><?= htmlspecialchars($product['ProductName']) ?></h2>
                <p><?= mb_strimwidth(htmlspecialchars($product['Description']), 0, 60, '...') ?></p>
                <div class="product-card">
                    <button class="product-order-button" id="product-order-button">Поръчай</button>
                </div>

            </a>
        <?php endforeach; ?>
    </div>
    <div id="product-modal" class="product-modal hidden">
        <div class="product-modal-content">
            <span class="product-close-button">&times;</span>
            <i id="favorite-icon" class="fa-solid fa-heart favorite-icon"></i>
            <img id="product-modal-photo" src="" alt="">
            <h2 id="product-modal-name"></h2>
            <div class="product-modal-top-row">
                <div class="product-modal-box">
                    <i class="fa-solid fa-weight-hanging"></i>
                    <span id="product-modal-weight"></span>
                </div>
                <div class="product-modal-box" id="product-favorite-box">
                    <i class="fa-solid fa-coins"></i>
                    <span id="product-modal-price"></span>
                </div>
            </div>
            <p id="product-modal-description"></p>
            <button class="product-order-button">Поръчай</button>
        </div>
    </div>
</div>

<script>
    const isLoggedIn = <?= $userId ? 'true' : 'false' ?>;
    document.querySelector('.product-close-button').addEventListener('click', function () {
        document.getElementById('product-modal').classList.add('hidden');
    });

    document.getElementById('product-modal').addEventListener('click', function (e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function (e) {
            e.preventDefault();
            const modal = document.getElementById('product-modal');
            const productId = this.dataset.id;

            document.getElementById('product-modal-photo').src = this.dataset.photo;
            document.getElementById('product-modal-name').textContent = this.dataset.name;
            document.getElementById('product-modal-weight').textContent = this.dataset.weight + ' гр.';
            document.getElementById('product-modal-price').textContent = this.dataset.price + ' лв.';
            document.getElementById('product-modal-description').textContent = this.dataset.description;

            const favIcon = document.getElementById('favorite-icon');
            favIcon.dataset.productId = productId;

            const isFavorite = this.classList.contains('favorite');
            favIcon.classList.toggle('active', isFavorite);

            modal.classList.remove('hidden');
        });
    });

    document.getElementById('favorite-icon').addEventListener('click', function () {
        if (!isLoggedIn) {
            window.location.href = 'login_page.php';
            return;
        }

        const productId = this.dataset.productId;

        if (!productId) {
            console.error("Липсва productId при клика на сърцето.");
            return;
        }

        const isNowFavorite = this.classList.toggle('active');

        fetch('toggle_favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'product_id=' + encodeURIComponent(productId) +
                '&action=' + encodeURIComponent(isNowFavorite ? 'add' : 'remove')
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error("HTTP статус: " + res.status);
                }
                return res.text();
            })
            .then(data => {
                console.log("Отговор от сървъра:", data);
            })
            .catch(error => {
                console.error("Грешка при заявката:", error);
            });
    });

    document.querySelector('.product-modal .product-order-button').addEventListener('click', function () {
        const productId = document.getElementById('favorite-icon').dataset.productId;
        if (!productId) {
            console.error("Липсва productId при поръчка.");
            return;
        }

        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'product_id=' + encodeURIComponent(productId) + '&quantity=1'
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
                    alert('Не сте влезли в профила си!');
                } else {
                    alert('Продуктът беше добавен в кошницата!');
                    console.log("Отговор от сървъра:", response);
                }
            })

            .catch(error => {
                console.error("Грешка при заявката:", error);
                alert('Възникна грешка при добавяне в кошницата.');
            });
    });

    document.addEventListener("DOMContentLoaded", () => {
        const links = document.querySelectorAll(".category-nav a");
        const urlParams = new URLSearchParams(window.location.search);
        const selectedCategory = urlParams.get("category");

        links.forEach(link => {
            const linkParams = new URLSearchParams(link.search);
            const linkCategory = linkParams.get("category");

            if ((selectedCategory && linkCategory === selectedCategory) ||
                (!selectedCategory && linkCategory === "8")) {
                link.classList.add("active");
            }
        });
    });


</script>
<?php
include("footer.php");
?>