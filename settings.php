<?php
ob_start();
include("header.php");

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}
require 'database.php';

$userID = $_SESSION['UserID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['field']) && isset($_POST['value'])) {
        $field = $_POST['field'];
        $value = trim($_POST['value']);

        if ($field === 'password') {
            $value = password_hash($value, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET Password = ? WHERE UserID = ?";
        } elseif (in_array($field, ['Email', 'PersonName'])) {
            $sql = "UPDATE users SET $field = ? WHERE UserID = ?";
        } else {
            exit("Невалидно поле.");
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $value, $userID);
        $stmt->execute();
        header("Location: settings.php");
        exit;
    }
}
$stmt = $conn->prepare("SELECT PersonName, Email FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<div class="settings-container">
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
    <main class="settings">
        <h2>Настройки на профила</h2>
        <div class="settings-row">
            <span class="settings-label">Име: <?= htmlspecialchars($user['PersonName']) ?></span>
            <button class="settings-edit-btn"
                onclick="openModal('PersonName', '<?= htmlspecialchars($user['PersonName']) ?>')">Редактирай</button>
        </div>
        <div class="settings-row">
            <span class="settings-label">Имейл: <?= htmlspecialchars($user['Email']) ?></span>
            <button class="settings-edit-btn"
                onclick="openModal('Email', '<?= htmlspecialchars($user['Email']) ?>')">Редактирай</button>
        </div>
        <div class="settings-row">
            <span class="settings-label">Парола: ••••••••</span>
            <button class="settings-edit-btn" onclick="openModal('password', '')">Редактирай</button>
        </div>
</div>

<div id="settings-editModal" class="settings-modal">
    <div class="settings-modal-content">
        <span class="settings-close" onclick="closeModal()">&times;</span>
        <h3 id="settings-modal-title">Редактиране</h3>
        <form method="POST">
            <input type="hidden" name="field" id="fieldName">
            <label for="newValue">Нова стойност:</label>
            <input type="text" name="value" id="newValue" required>
            <div>
                <button class="settings-cancel-btn" type="button" onclick="closeModal()">Отказ</button>
                <button class="settings-save-btn" type="submit">Запази</button>
            </div>
        </form>
    </div>
</div>
</main>
<?php
include("footer.php");
?>

<script>
    function openModal(field, value) {
        document.getElementById("settings-editModal").style.display = "block";
        document.getElementById("fieldName").value = field;
        document.getElementById("newValue").value = value;
        document.getElementById("settings-modal-title").textContent = "Редактиране на " +
            (field === "PersonName" ? "име" : field === "Email" ? "имейл" : "парола");

        document.getElementById("newValue").type = field === "password" ? "password" : "text";
    }

    function closeModal() {
        document.getElementById("settings-editModal").style.display = "none";
    }

    window.onclick = function (event) {
        let modal = document.getElementById("settings-editModal");
        if (event.target === modal) {
            closeModal();
        }
    }

    document.querySelector(".settings-modal-content form").addEventListener("submit", function (e) {
        const field = document.getElementById("fieldName").value;
        const value = document.getElementById("newValue").value;

        if (field === "password") {
            const hasUpper = /[A-Z]/.test(value);
            const hasLower = /[a-z]/.test(value);
            const isLongEnough = value.length >= 12;

            if (!hasUpper || !hasLower || !isLongEnough) {
                alert("Паролата трябва да е поне 12 символа, с поне една главна и една малка буква.");
                e.preventDefault();
            }
        }
    });
</script>
<?php ob_end_flush(); ?>