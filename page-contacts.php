<?php
/**
 * Template Name: Contacts Template
 * Template Post Type: page
 */
get_header();
?>

<main id="primary" class="site-main">
  <?php
    while ( have_posts() ) :
      the_post();
      the_content();
    endwhile;
  ?>
  <section class="max-w-xl mx-auto mt-12 mb-16 bg-white rounded-xl shadow-lg p-8">
    <h2 class="text-3xl font-bold text-center text-green-900 mb-4">Contact Us</h2>
    <p class="text-center text-gray-600 mb-8">We'd love to hear from you. Fill out the form below and our team will get back to you soon!</p>
    <form class="space-y-6" action="#" method="post" autocomplete="off">
      <div>
        <label for="name" class="block text-sm font-semibold text-green-900 mb-1">Name</label>
        <input type="text" id="name" name="name" required
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-300 focus:border-green-400 transition" />
      </div>
      <div>
        <label for="email" class="block text-sm font-semibold text-green-900 mb-1">Email</label>
        <input type="email" id="email" name="email" required
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-300 focus:border-green-400 transition" />
      </div>
      <div>
        <label for="subject" class="block text-sm font-semibold text-green-900 mb-1">Subject</label>
        <input type="text" id="subject" name="subject"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-300 focus:border-green-400 transition" />
      </div>
      <div>
        <label for="message" class="block text-sm font-semibold text-green-900 mb-1">Message</label>
        <textarea id="message" name="message" rows="5" required
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-300 focus:border-green-400 transition resize-none"></textarea>
      </div>
      <div>
        <button type="submit"
          class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-6 rounded-md shadow transition duration-150 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
          Send Message
        </button>
      </div>
    </form>
    <div class="mt-8 text-center text-gray-500 text-sm">
      Or email us directly at
      <a href="mailto:info@poshtik.com" class="text-green-700 hover:underline">info@poshtik.com</a>
    </div>
  </section>
</main>

<?php
get_footer();