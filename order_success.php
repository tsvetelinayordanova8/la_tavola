<?php
include("header.php");
?>

<canvas id="confetti-canvas"></canvas>

<div class="card-container-order-success">
    <div class="card-order-success">
        <img src="Sources/colloseum.png" alt="Colloseum">
        <h1>Поръчката ви беше приета успешно!</h1>
        <p>Ще се свържем с вас за потвърждение и доставка. Благодарим, че избрахте вкуса на Италия!</p>
        <a href="index.php" class="button">Обратно към началото</a>
    </div>
</div>
<?php
include("footer.php");
?>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    const duration = 2 * 200;
    const end = Date.now() + duration;

    (function frame() {
        confetti({
            particleCount: 5,
            angle: 60,
            spread: 55,
            origin: { x: 0 },
        });
        confetti({
            particleCount: 5,
            angle: 120,
            spread: 55,
            origin: { x: 1 },
        });

        if (Date.now() < end) {
            requestAnimationFrame(frame);
        }
    })();
</script>