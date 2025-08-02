<?php
/**
 * Template for the front page
 *
 * @package Poshtik_Custom
 */

get_header(); ?>

<?php
/**
 * Swiper Carousel Markup
 *
 * File: wp-content/themes/poshtik-custom/front-page.php
 * Location: Immediately after the closing </header> tag (so it sits right under your navbar).
 */
?>
<section class="py-12 bg-white container mx-auto px-6">
  <div class="swiper-container h-96 mb-12">
  <div class="swiper-wrapper">
    <!-- Each .swiper-slide is one slide; replace image paths as needed -->
    <div class="swiper-slide">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/slide1.png" alt="Slide 1">
    </div>
    <div class="swiper-slide">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/slide1.png" alt="Slide 2">
    </div>
  </div>
  <!-- Navigation arrows -->
  <div class="swiper-button-prev"></div>
  <div class="swiper-button-next"></div>
  <!-- Pagination dots -->
  <div class="swiper-pagination"></div>
  </div>
</section>

<main>
  <!-- Hero -->
  <section class="bg-blue-50 py-16">
    <div class="container mx-auto px-6 lg:flex lg:items-center lg:justify-between">
      <div class="lg:w-1/2">
        <h1 class="text-5xl font-serif text-primary mb-4">Advanced Nutrition for Your Pet</h1>
        <p class="text-gray-700 mb-6">Premium, scientifically formulated diets to support your pet’s health and well-being.</p>
        <a href="#shop" class="inline-block bg-accent hover:bg-[#d2431f] text-white font-semibold py-3 px-6 rounded-lg">Shop Now</a>
      </div>
      <div class="lg:w-1/2 mt-8 lg:mt-0">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pets.png" alt="Pets" class="w-full object-cover rounded-lg shadow-lg">
      </div>
    </div>
  </section>

  <!-- Why Choose -->
  <section class="py-16">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl font-semibold text-gray-900 mb-8">Why Choose Poshtik Pets?</h2>
      <div class="grid gap-8 md:grid-cols-3">
        <div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Scientifically-Backed</h3>
          <p class="text-gray-600">Prolongata milie: svote-freies pet regulatory problem.</p>
        </div>
        <div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">High-Quality Ingredients</h3>
          <p class="text-gray-600">Sustainable nutrients that support.</p>
        </div>
        <div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Veterinarian Recommended</h3>
          <p class="text-gray-600">Health care to-perkeserving and recommended.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Newsletter -->
  <section class="bg-green-50 py-12">
    <div class="container mx-auto px-6 lg:flex lg:items-center lg:justify-between">
      <div class="lg:w-2/3 mb-6 lg:mb-0">
        <h3 class="text-2xl font-semibold text-gray-900 mb-2">Get 20% Off Your First Order</h3>
        <p class="text-gray-700">Sign up to recenareded address when you spend.</p>
      </div>
      <form class="lg:w-1/3 flex">
        <input type="email" placeholder="Email Address" class="w-full rounded-l-lg border border-gray-300 px-4 py-2 focus:outline-none">
        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 rounded-r-lg">Sign Up</button>
      </form>
    </div>
  </section>
</main>
<?php
/**
 * Initialize Swiper Carousel
 *
 * File: wp-content/themes/poshtik-custom/front-page.php
 * Location: Just before the closing get_footer() call.
 */
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  new Swiper('.swiper-container', {
    loop: true,
    autoplay: {
      delay: 5000,
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });
});
</script>
<?php get_footer(); ?>