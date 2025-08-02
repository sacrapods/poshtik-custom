<?php
/**
 * Template Name: About Us
 * Description: Custom About page for Dr. Eunice Thomas veterinary clinic.
 */

get_header(); ?>

<main id="primary" class="site-main container mx-auto px-6 py-12">

  <!-- Hero section -->
  <section class="flex flex-col md:flex-row items-center gap-8 mb-12">
    <div class="md:w-1/2">
      <h1 class="text-4xl font-bold text-primary mb-4">About Dr. Eunice Thomas</h1>
      <p class="text-lg text-gray-700 leading-relaxed">
        Welcome to Poshtik Pets, where your pet's health and happiness are our top priority. Led by Dr. Eunice Thomas, our board-certified veterinary professional with over 15 years of experience in animal care. Dr. Thomas is passionate about preventive medicine, nutritional therapy, and compassionate care for every pet.
      </p>
    </div>
    <div class="md:w-1/2">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/eunice.jpeg" alt="Dr. Eunice Thomas" class="rounded-lg shadow-lg" />
    </div>
  </section>

  <!-- Mission -->
  <section class="bg-green-50 p-8 rounded-lg mb-12">
    <h2 class="text-2xl font-semibold text-primary mb-4">Our Mission</h2>
    <p class="text-gray-700 leading-relaxed">
      At Poshtik Pets, our mission is to provide advanced, science-backed veterinary services that ensure the health and well-being of your beloved pets. We strive to create a welcoming environment where pets and owners feel informed, supported, and confident in their care.
    </p>
  </section>

  <!-- Core Values -->
  <section class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="text-center">
      <h3 class="text-xl font-semibold text-primary mb-2">Compassion</h3>
      <p class="text-gray-700">We treat every animal with kindness, respect, and empathy.</p>
    </div>
    <div class="text-center">
      <h3 class="text-xl font-semibold text-primary mb-2">Expertise</h3>
      <p class="text-gray-700">We stay at the forefront of veterinary medicine through continuous learning and research.</p>
    </div>
    <div class="text-center">
      <h3 class="text-xl font-semibold text-primary mb-2">Collaboration</h3>
      <p class="text-gray-700">We work closely with pet owners to create personalized care plans.</p>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="text-center">
    <a href="<?php echo home_url('/contacts/'); ?>" class="inline-block bg-accent hover:bg-accent-dark text-white font-semibold py-3 px-6 rounded-lg transition">
      Schedule an Appointment
    </a>
  </section>

</main>

<?php
get_footer();