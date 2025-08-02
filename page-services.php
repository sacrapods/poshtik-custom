<?php
/**
 * Template Name: Services Page
 *
 * Displays veterinary services with custom layout and call-to-action.
 * @package poshtik-custom
 */
get_header();
?>
<main id="primary" class="site-main container mx-auto px-6 py-12">
  <!-- Hero Section -->
  <section class="text-center mb-12">
    <h1 class="text-4xl font-bold text-primary mb-4">Our Services</h1>
    <p class="text-lg text-gray-700">Comprehensive veterinary care & tailored nutrition plans for your beloved pet.</p>
  </section>
  <!-- Services Grid -->
  <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
    <!-- Consultation Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/full-consultation.png" alt="Full Consultation" class="h-48 w-full object-cover">
      <div class="p-6">
        <h3 class="text-2xl font-semibold text-primary mb-2">Full Consultation</h3>
        <p class="text-gray-700 mb-4">In-depth online and offline consultations to diagnose and recommend the best care for your pet.</p>
        <a href="<?php echo home_url('/contacts/'); ?>" class="text-accent font-semibold hover:underline">Learn More &rarr;</a>
      </div>
    </div>
    <!-- Custom Diet Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/service-diet.png" alt="Custom Diet Plans" class="h-48 w-full object-cover">
      <div class="p-6">
        <h3 class="text-2xl font-semibold text-primary mb-2">Custom Diet Plans</h3>
        <p class="text-gray-700 mb-4">Personalized nutrition plans crafted by Dr. Eunice Thomas to meet your pet’s unique needs.</p>
        <a href="<?php echo home_url('/contacts/'); ?>" class="text-accent font-semibold hover:underline">Get Started &rarr;</a>
      </div>
    </div>
    <!-- Online Nutrition Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/service-online.png" alt="Online Nutrition" class="h-48 w-full object-cover">
      <div class="p-6">
        <h3 class="text-2xl font-semibold text-primary mb-2">Online Nutrition</h3>
        <p class="text-gray-700 mb-4">Virtual nutrition consultations and follow-ups for pets anywhere, anytime.</p>
        <a href="<?php echo home_url('/contacts/'); ?>" class="text-accent font-semibold hover:underline">Book Online &rarr;</a>
      </div>
    </div>
  </section>
  <!-- Call to Action Banner -->
  <section class="bg-primary text-white py-12 rounded-lg text-center">
    <h2 class="text-3xl font-semibold mb-4">Ready to start?</h2>
    <p class="mb-6">Schedule your pet’s comprehensive health consultation today for just <strong>₹500</strong>.</p>
    <a href="<?php echo home_url('/contacts/'); ?>" class="inline-block bg-accent hover:bg-accent-dark text-white font-semibold py-3 px-6 rounded-lg">Book Now</a>
  </section>
</main>
<?php

get_footer();
