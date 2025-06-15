<?php
include "header.php";

$emailErr = $passwordErr = "";
$email = $password = "";
$loginSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $db = "la_tavola_db";
    $conn = new mysqli("localhost", "root", "", $db);

    if ($conn->connect_error) {
        die("Грешка при свързване с базата: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT UserID, PersonName, Email, Password, Usertype FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row["Password"])) {
            // Успешен вход
            $_SESSION["email"] = $row["Email"];
            $_SESSION["usertype"] = $row["Usertype"];
            $_SESSION["personname"] = htmlspecialchars($row["PersonName"]);
            $_SESSION["UserID"] = $row["UserID"];
            $loginSuccess = true;
        } else {
            $passwordErr = "Грешна парола!";
        }
    } else {
        $emailErr = "Няма потребител с такъв имейл!";
    }

    $stmt->close();
    $conn->close();
}

if ($loginSuccess) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
?>

<div class="login-container">
    <div class="left-side-login"></div>

    <div class="image-divider">
        <img src="Sources/pisa_tower_cartoon.png" alt="Разделителна снимка" width="420px">
    </div>

    <div class="right-side-login">
        <form action="" method="POST" class="login-form" novalidate>
            <h2>Вход</h2>
            <input type="email" name="email" placeholder="Имейл" required value="<?= htmlspecialchars($email) ?>">
            <div class="error-message-login" style="color: red; font-size: 0.9em; margin-bottom: 1vh;">
                <?= $emailErr ?>
            </div><br>

            <input type="password" name="password" placeholder="Парола" required>
            <div class="error-message-login" style="color: red; font-size: 0.9em; margin-bottom: 1vh">
                <?= $passwordErr ?>
            </div><br>

            <button type="submit" class="login-button">Влез</button>
            <p class="redirect_register">Нямаш профил? Регистрирай се лесно от <a class="register_link" href="register_page.php">Тук</a></p>
        </form>
        
    </div>
</div>

<?php
include("footer.php");
?>