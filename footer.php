</div>
<footer class="site-footer">
  <div class="footer-logo">
    <img src="Sources/Logo1.png" alt="Logo" width="150vw">
  </div>
  <div class="footer-container">
    <div class="footer-column">
      <h3>За поръчки</h3>
      <p>+359 895 563 245</p>
      <p>ул. "Иван Вазов" 12, Пловдив</p>
      <ul>
        <li class="social-links">
          <a href="#"><i class="fa-brands fa-facebook"></i></a>
          <a href="#"><i class="fa-brands fa-instagram"></i></a>
          <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
        </li>
      </ul>
    </div>
    <div class="footer-column">
      <h3>Работно време</h3>
      <ul>
        <li>Понеделник до Петък</li>
        <li>10:00-23:00</li>
        <li>Събота и Неделя</li>
        <li>10:00-22:00</li>
      </ul>
    </div>

  </div>
  <div class="footer-bottom">
    <p>&copy; <?php echo date("Y"); ?> La Tavola. Всички права запазени.</p>
  </div>
</footer>
</body>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    new Swiper(".mySwiper", {
      loop: true,
      autoplay: {
        delay: 3000,
      },
      effect: "fade",
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      autoplay: {
        delay: 4000, 
      },
    });
  });
</script>

</html>