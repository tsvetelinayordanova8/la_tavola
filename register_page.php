<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("database.php");

$errors = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors .= "Невалиден имейл адрес.<br>";
    }

    if (
        strlen($password) < 12 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        $errors .= "Паролата трябва да е поне 12 символа, да съдържа поне една главна буква и цифра.<br>";
    }

    if (empty($errors)) {
        $checkStmt = $conn->prepare("SELECT UserID FROM users WHERE Email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $errors .= "Имейлът вече е регистриран.<br>";
        }
        $checkStmt->close();
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (PersonName, Email, Password, Usertype) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            $userID = $stmt->insert_id;

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION["UserID"] = $userID;
            $_SESSION["email"] = $email;
            $_SESSION["personname"] = $name;
            $_SESSION["usertype"] = 1;

            $stmt->close();
            $conn->close();

            header("Location: index.php");
            exit;
        }
    }

    $conn->close();
}
?>

<?php include "header.php"; ?>

<div class="login-container">
    <div class="left-side-login"></div>

    <div class="image-divider">
        <img src="Sources/pisa_tower_cartoon.png" alt="Разделителна снимка" width="420px">
    </div>

    <div class="right-side-login">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="login-form">
            <h2>Регистрация</h2>
            <input type="text" name="name" placeholder="Име и фамилия" required><br>
            <input type="email" name="email" placeholder="Имейл" required><br>
            <input type="password" name="password" placeholder="Парола" required><br>
            <button type="submit" class="login-button">Регистрирай се</button>

            <div class="error-message <?php echo !empty($errors) ? 'visible' : ''; ?>"><?php echo $errors; ?></div>
        </form>
    </div>
</div>
<?php
include("footer.php");
?>