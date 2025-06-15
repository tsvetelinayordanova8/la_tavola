<?php
include("header.php");
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

            header("Location: index.php");
            exit;
        } else {
            $errors .= "Грешка при запис в базата: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }

}

$conn->close();
?>