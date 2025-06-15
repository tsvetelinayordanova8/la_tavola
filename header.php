<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="Sources/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://kit.fontawesome.com/9419938b26.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Neucha&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <title>La Tavola Italian Cousin</title>

</head>
<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$isLoggedIn = isset($_SESSION["email"]);
$personName = $isLoggedIn ? $_SESSION["personname"] : "";
include("database.php");
?>
<script>
    let logo;

    function toggleAccountMenu() {
        const menu = document.getElementById("account-menu");

        if (menu.classList.contains("hidden")) {
            menu.classList.remove("hidden");
            menu.classList.add("show");
        } else {
            menu.classList.remove("show");
            menu.classList.add("hidden");
        }
    }

    document.addEventListener("click", function (event) {
        const menu = document.getElementById("account-menu");
        const icon = document.querySelector(".fa-circle-user");

        if (!menu.contains(event.target) && !icon.contains(event.target)) {
            menu.classList.remove("show");
            menu.classList.add("hidden");
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        logo = document.getElementById('index_logo');
        const bodyClass = document.body.className;
        if (bodyClass === 'page-index') {
            logo.src = 'Sources/Logo1.png';
        } else {
            logo.src = 'Sources/Logo2.png';
        }
    });


    window.addEventListener('scroll', function () {
        const target = document.getElementById('page-header');
        const triggerPosition = 600;

        if (window.scrollY > triggerPosition) {
            target.classList.add('scrolled');
            if (logo) logo.src = 'Sources/Logo2.png';
        } else {
            target.classList.remove('scrolled');
            const bodyClass = document.body.className;
            if (logo) {
                if (bodyClass === 'page-index') {
                    logo.src = 'Sources/Logo1.png';
                } else {
                    logo.src = 'Sources/Logo2.png';
                }
            }
        }
    });

    function addToCart(productID, quantity = 1) {
        $.ajax({
            url: 'add_to_cart.php',
            method: 'POST',
            data: {
                productID: productID,
                quantity: quantity
            },
            success: function (response) {
                updateCartCount(); 
            },
            error: function (xhr, status, error) {
                console.error("Грешка при добавяне:", error);
            }
        });
    }
    function updateCartCount() {
        $.ajax({
            url: 'get_cart_count.php', 
            method: 'GET',
            success: function (response) {
                $('#cart-count').text(response);
            },
            error: function (xhr, status, error) {
                console.error("Грешка при зареждане на брояча:", error);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', updateCartCount);
</script>


<body class="page-<?php echo $current_page; ?>">
    <div class="page-wrapper">
        <header id="page-header">
            <div class="top-navigation">
                <a href="index.php">
                    <img src="Sources/Logo1.png" alt="Logo" width="180vw" id="index_logo">
                </a>
            </div>

            <nav class="main-nav">
                <ul>
                    <li>
                        <a href="index.php">Начало</a>
                    </li>
                    <li>
                        <a href="menu_page">Меню</a>
                    </li>
                    <li>
                        <a href="lunch_menu.php">Обедно меню</a>
                    </li>
                </ul>
            </nav>

            <div class="form_user">
                <div class="account-wrapper">
                    <i class="fa-solid fa-circle-user fa-2xl" onclick="toggleAccountMenu()"></i>
                    <?php
                    if (!isset($_SESSION['UserID'])) { ?>
                        <div id="account-menu" class="account-menu hidden">
                            <li>
                                <a href="login_page.php" class='user-nav-choices'>
                                    Вход
                                </a>
                            </li>
                            <li>
                                <a href="register_page.php" class='user-nav-choices'>
                                    Регистрация
                                </a>
                            </li>
                        </div>
                    <?php } else {
                        echo "
                        <div id='account-menu' class='account-menu hidden'>
                        <li>
                            <a href='my_profile.php' class='user-nav-choices'>
                                Моят профил
                            </a>
                        </li>
                        <li>
                            <a href='favorites.php' class='user-nav-choices'>
                                Любими продукти
                            </a>
                        </li>
                        <li>
                            <a href='my_orders.php' class='user-nav-choices'>
                                Моите поръчки
                            </a>
                        </li>
                        <li>
                            <a href='settings.php' class='user-nav-choices'>
                                Настройки
                            </a>
                        </li>"
                        ;

                        if (isset($_SESSION['email']) && $_SESSION['usertype'] == '2') {
                            echo "<li><a href='admin_panel.php' class='user-nav-choices'>
                            Админ панел
                        </a>
                        </li>";
                        }


                        echo "<li>
                                <a href='logout.php' class='user-nav-choices'> 
                                    Изход 
                                </a>
                            </li>
                            </div>";
                    }
                    ?>
                </div>
                <div class="header-icon cart-wrapper" style="position: relative;">
                    <a href="cart.php">
                        <i class="fa-solid fa-cart-shopping fa-xl"></i>
                        <span class="cart-count" id="cart-count">0</span>
                    </a>
                </div>


            </div>
        </header>