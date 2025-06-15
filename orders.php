<?php
include("header.php");
$statusTranslations = [
    'Pending' => 'Обработва се',
    'Shipped' => 'Изпратена',
    'Delivered' => 'Доставена',
    'Cancelled' => 'Отказана',
];

$sql = "
    SELECT o.OrderID, o.CustomerID, o.OrderDate, o.TotalAmount, o.Status, u.PersonName
    FROM orders o
    JOIN users u ON o.CustomerID = u.UserID
    ORDER BY o.OrderDate DESC
";

$result = $conn->query($sql);
?>
<?php
if (isset($_SESSION['email']) && $_SESSION['usertype'] == '2') {
    ?>
    <div class="orders-container">
        <?php include("admin_menu.php") ?>
        <main class="update-products">
            <h2>Поръчки</h2>
            <div class="search-bar">
                <label for="orderSearch">🔍 Търси продукт:</label>
                <input type="text" id="orderSearch" class="search-input" placeholder="Въведи име или номер поръчка...">
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="p-3 text-left">#</th>
                        <th class="p-3 text-left">Клиент</th>
                        <th class="p-3 text-left">Дата</th>
                        <th class="p-3 text-left">Стойност</th>
                        <th class="p-3 text-left">Статус</th>
                        <th class="p-3 text-left">Детайли</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "
    SELECT o.OrderID, o.CustomerID, o.OrderDate, o.TotalAmount, o.Status, u.PersonName
    FROM orders o
    JOIN users u ON o.CustomerID = u.UserID
    ORDER BY o.OrderDate DESC
";

                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0):
                        while ($order = $result->fetch_assoc()):
                            ?>
                            <tr class="border-b" data-id="<?= $order['OrderID'] ?>">
                                <td class="p-3 col-id"><?= htmlspecialchars($order['OrderID']) ?></td>
                                <td class="p-3 col-name"><?= htmlspecialchars($order['PersonName']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($order['OrderDate']) ?></td>
                                <td class="p-3"><?= number_format($order['TotalAmount'], 2) ?> лв.</td>
                                <td class="col-status">
                                    <select class="status-select" data-order-id="<?= $order['OrderID'] ?>">
                                        <?php foreach ($statusTranslations as $eng => $bg): ?>
                                            <option value="<?= $eng ?>" <?= $order['Status'] == $eng ? 'selected' : '' ?>>
                                                <?= $bg ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                                <td class="p-3">
                                    <button onclick="showOrderDetails(<?= $order['OrderID'] ?>)">Преглед</button>
                                    <button onclick="deleteOrder(<?= $order['OrderID'] ?>)"
                                        class="ml-2 text-red-600">Изтрий</button>
                                </td>
                            </tr>
                            <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">Няма поръчки.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div id="order-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-xl w-11/12 max-w-2xl overflow-y-auto max-h-[80vh] relative">
                    <h2 class="text-xl font-bold mb-4">Продукти в поръчката</h2>
                    <div id="order-items-container"></div>
                    <div class="form-actions">
                        <button type="button" class="edit-save" onclick="closeModal()">Ок</button>
                    </div>
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
    async function showOrderDetails(orderId) {
        const response = await fetch(`order-details.php?order_id=${orderId}`);
        const items = await response.json();

        const container = document.getElementById("order-items-container");
        container.innerHTML = "";

        let total = 0;

        items.forEach(item => {
            const subtotal = item.Quantity * item.UnitPrice;
            total += subtotal;

            const card = document.createElement("div");
            card.className = "bg-white shadow p-4 rounded-xl mb-2 flex items-center";

            card.innerHTML = `
            <img src="${item.PhotoSource}" alt="${item.ProductName}" class="w-20 h-20 object-cover rounded-lg mr-4">
            <div>
                <h3 class="text-lg font-semibold">${item.ProductName}</h3>
                <p>Количество: ${item.Quantity}</p>
                <p>Единична цена: ${item.UnitPrice.toFixed(2)} лв.</p>
                <p>Обща стойност: ${(subtotal).toFixed(2)} лв.</p>
            </div>
        `;

            container.appendChild(card);
        });

        // Добави тотала след продуктите
        const totalElement = document.createElement("div");
        totalElement.className = "text-right font-bold text-lg mt-4";
        totalElement.innerHTML = `Обща сума на поръчката: ${total.toFixed(2)} лв.`;

        container.appendChild(totalElement);

        document.getElementById("order-modal").classList.remove("hidden");
    }


    function closeModal() {
        document.getElementById("order-modal").classList.add("hidden");
    }
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', async function () {
            const orderId = this.dataset.orderId;
            const newStatus = this.value;

            try {
                const response = await fetch('update_order_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ order_id: orderId, status: newStatus })
                });
                const result = await response.json();

                if (result.success) {
                    alert('Статусът е обновен успешно!');
                } else {
                    alert('Грешка при обновяването: ' + result.error);
                }
            } catch (error) {
                alert('Грешка при свързване със сървъра.');
            }
        });
    });
    async function deleteOrder(orderId) {
        if (!confirm("Сигурни ли сте, че искате да изтриете тази поръчка?")) return;

        try {
            const response = await fetch('delete_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ order_id: orderId })
            });

            const result = await response.json();

            if (result.success) {
                location.reload();
            } else {
                alert("Грешка при изтриване: " + result.error);
            }
        } catch (error) {
            alert("Грешка при връзка със сървъра.");
        }
    }
    document.getElementById("order-modal").addEventListener("click", function (e) {
        if (e.target === this) {
            closeModal();
        }
    });
    document.getElementById('orderSearch').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const id = row.querySelector('.col-id')?.innerText.toLowerCase() || '';
            const name = row.querySelector('.col-name')?.innerText.toLowerCase() || '';
            const status = row.querySelector('.col-status select')?.selectedOptions[0].text.toLowerCase() || '';

            if (id.includes(searchTerm) || name.includes(searchTerm) || status.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });


</script>