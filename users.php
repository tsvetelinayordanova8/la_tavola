<?php
require 'database.php';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM users WHERE UserID = $id");
    header("Location: users.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = (int) $_POST['edit_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $usertype = (int) $_POST['usertype'];
    $stmt = $conn->prepare("
      UPDATE users 
      SET PersonName = ?, Email = ?, Usertype = ? 
      WHERE UserID = ?");
    $stmt->bind_param("ssii", $name, $email, $usertype, $id);
    $stmt->execute();
    header("Location: users.php");
    exit;
}

$users = $conn->query("SELECT UserID, PersonName, Email, Usertype FROM users ORDER BY UserID ASC")->fetch_all(MYSQLI_ASSOC);

include "header.php";

if (isset($_SESSION['email']) && $_SESSION['usertype'] == '2') {
    ?>
    <div class="users-container">
        <?php include("admin_menu.php") ?>
        <main class="update-products">
            <h2>Управление на потребители</h2>  
            <div class="search-bar">
                <label for="productSearch">🔍 Търси потребител:</label>
                <input type="text" id="productSearch" placeholder="Въведи име или имейл...">
            </div>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Име</th>
                        <th>Имейл</th>
                        <th>Тип</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr data-id="<?= $u['UserID'] ?>">
                            <td class="col-id"><?= $u['UserID'] ?></td>
                            <td class="col-name"><?= htmlspecialchars($u['PersonName']) ?></td>
                            <td class="col-email"><?= htmlspecialchars($u['Email']) ?></td>
                            <td class="col-type"><?= $u['Usertype'] == 2 ? 'Администратор' : 'Потребител' ?></td>
                            <td class="p-3">
                                <button class="edit-btn" onclick="openEditModal(<?= $u['UserID'] ?>)">Редактирай</button>
                                <button class="del-btn"
                                    onclick="if(confirm('Сигурни ли сте?')){location='users.php?delete=<?= $u['UserID'] ?>'}">
                                    Изтрий
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
} else {
    echo "<p class='no-permission-text'>Нямате права за тази страница!</p";
}
?>
    </main>

    <div id="userEditModal" class="userEditModal">
        <div class="users-modal-content">
            <span class="users-close" onclick="closeEditModal()">×</span>
            <h3>Редактиране на потребител</h3>
            <form method="POST" id="editUserForm">
                <input type="hidden" name="edit_id" id="edit_id">
                <label>Име:<input type="text" name="name" id="edit_name" required></label>
                <label>Имейл:<input type="email" name="email" id="edit_email" required></label>
                <label>Тип:
                    <select name="usertype" id="edit_type">
                        <option value="1">Потребител</option>
                        <option value="2">Администратор</option>
                    </select>
                </label>
                <div class="form-actions">
                    <button type="button" class="edit-cancel" onclick="closeEditModal()">Отказ</button>
                    <button type="submit" class="edit-save">Запази</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include("footer.php");
?>
<script>
    function openEditModal(id) {
        const row = document.querySelector(`tr[data-id='${id}']`);
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = row.querySelector('.col-name').innerText;
        document.getElementById('edit_email').value = row.querySelector('.col-email').innerText;
        const typeText = row.querySelector('.col-type').innerText;
        document.getElementById('edit_type').value = typeText === 'Администратор' ? 2 : 1;
        document.getElementById('userEditModal').style.display = 'block';
    }
    function closeEditModal() {
        document.getElementById('userEditModal').style.display = 'none';
    }
    window.onclick = e => {
        if (e.target.id === 'userEditModal') closeEditModal();
    };

    document.getElementById('productSearch').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const name = row.querySelector('.col-name').innerText.toLowerCase();
            const email = row.querySelector('.col-email').innerText.toLowerCase();
            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

</script>