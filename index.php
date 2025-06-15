<?php
include('header.php');

$query = "
    SELECT p.ProductName, p.PhotoSource
    FROM products p
    INNER JOIN seasonal_products sp ON p.ProductID = sp.productid
";
$result = mysqli_query($conn, $query);
?>

<div class="video-wrapper">
  <video autoplay loop muted playsinline class="fullscreen-video">
    <source src="Sources/pasta_video.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  <div class="video-overlay"></div>

  <div class="content">
    <h1>La Tavola –  Където всяко <br> ястие е изживяване</h1>
    <p>Съвременна италианска кухня с внимание към детайла и вкуса.</p>
    <a href="menu_page.php">
      <button class="menu-btn">Към менюто</button>
    </a>
  </div>
</div>
<main>
  <section class="dish-section">
    <div class="dish-content">
      <div class="dish-text">
        <h2>Сезонни предложения</h2>
        <p>Открий вкуса на сезона с нашите специално подбрани предложения, вдъхновени от традициите на италианската
          кухня и свежестта на местните продукти.
          Всяка седмица нашите майстори-готвачи създават нови ястия, които да събудят сетивата ти – от прясна паста с
          ароматни билки до неустоими десерти, които разтапят.</p>
      </div>
      <div class="dish-slider">
        <div class="swiper mySwiper">
          <div class="swiper-wrapper">
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
              $photoPath = htmlspecialchars($row['PhotoSource']);
              $productName = htmlspecialchars($row['ProductName']);
              $productUrl = 'menu_page.php';
              echo "
                <div class='swiper-slide'>
                  <a href='$productUrl'>
                    <img src='$photoPath' alt='$productName' width='200px'>
                  </a>
                </div>
              ";
            }
            ?>
          </div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-pagination"></div>
        </div>
      </div>
    </div>
  </section>
  <div class="wave-divider-top">
    <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
      <path d="M0,100 C480,0 960,100 1440,0 L1440,100 L0,100 Z" fill="#328a2f"></path>
    </svg>
  </div>
  <section class="groceries-section">
    <img src="Sources/groceries.jpg" alt="Италианско ястие">
    <div class="groceries-text">
      <h2>Истинският вкус на Италия</h2>
      <p>В La Tavola вярваме, че истинската италианска кухня започва не в кухнята, а в земята, от която произлизат
        съставките. Затова сме горди да предложим ястия, приготвени с автентични продукти, внесени директно от различни
        региони на Италия – без компромиси в качеството, вкуса и традицията.
        Всеки продукт е внимателно подбран не просто заради произхода си, а заради душата, която носи в себе си – защото
        за нас Италия не е просто кухня, а култура, страст и начин на живот.
      </p>
    </div>
  </section>
  <div class="wave-divider">
    <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
      <path d="M0,0 C480,100 960,0 1440,100 L1440,0 L0,0 Z" fill="#328a2f"></path>
    </svg>
  </div>
  <section class="restaurant-section">
    <div class="restaurant-text">
      <h2>La Tavola – частица от Италия в сърцето на града</h2>
      <p>Всеки продукт е внимателно подбран не просто заради произхода си, а заради душата, която носи в себе си –
        защото за нас Италия не е просто кухня, а култура, страст и начин на живот.
        При нас няма имитации, няма „по италиански“ – има само истински италиански вкус, поднесен с внимание, уважение и
        любов.</p>
    </div>
    <img src="Sources/restaurant.jpg" alt="Италианско ястие">
  </section>

</main>


<script>



</script>
<?php include('footer.php'); ?>