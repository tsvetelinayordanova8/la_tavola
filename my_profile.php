<?php
include("header.php");
?>

<h1 class="page-title">Моят профил</h1>
<h2 class="page-title-h2">Здравей, <?php echo $_SESSION["personname"];?></h2>
<div class="profile-sections">
    <a href="my_orders.php" class="profile-box">
        <i class="fas fa-box"></i>
        <span>Моите поръчки</span>
    </a>
    <a href="favorites.php" class="profile-box">
        <i class="fas fa-heart"></i>
        <span>Любими продукти</span>
    </a>
    <a href="settings.php" class="profile-box">
        <i class="fas fa-cog"></i>
        <span>Настройки</span>
    </a>

    <?php if ($_SESSION["usertype"] == '2'): ?>
        <a href="admin_panel.php" class="profile-box">
            <i class="fas fa-tools"></i>
            <span>Административен панел</span>
        </a>

        <a href="logout.php" class="profile-box">
            <i class="fas fa-sign-out-alt"></i>
            <span>Изход</span>
        </a>
    <?php endif; ?> 
</div>
<?php
include("footer.php");
?>