

<?php
/**
 * Template Name: Products Page
 * File: page-products.php
 * Description: Displays a “Coming Soon” message with a placeholder for product listings.
 */
get_header(); 
?>

<main id="primary" class="site-main container mx-auto px-6 py-16">
  <!-- Hero / Coming Soon Banner -->
  <section class="text-center mb-12">
    <h1 class="text-5xl font-bold text-primary mb-4">Our Products</h1>
    <p class="text-lg text-gray-700 mb-8">
      We’re working hard to bring you the best pet nutrition products. Check back soon!
    </p>
    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/coming-soon1.png" 
         alt="Products Coming Soon" 
         class="mx-auto w-full max-w-md rounded-lg shadow-lg">
  </section>

  <!-- Placeholder Grid -->
  <section>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php for ( $i = 0; $i < 6; $i++ ) : ?>
        <div class="bg-gray-100 rounded-lg p-6 text-center animate-pulse">
          <div class="h-40 bg-gray-300 rounded mb-4"></div>
          <h3 class="h-6 bg-gray-300 rounded w-3/4 mx-auto mb-2"></h3>
          <p class="h-4 bg-gray-300 rounded w-1/2 mx-auto"></p>
        </div>
      <?php endfor; ?>
    </div>
  </section>
</main>

<?php
get_footer();
?>