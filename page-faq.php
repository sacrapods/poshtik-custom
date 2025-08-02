<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package poshtik-custom
 */

get_header();
?>
<?php
/**
 * Template Name: FAQs
 * File: page-FAQ.php
 * Description: Displays Frequently Asked Questions for Poshtik Pets veterinary clinic,
 * including consultation fee, booking info, hours, and more, with an attractive styled layout.
 */
?>

<main id="primary" class="site-main container mx-auto px-6 py-12">
  <!-- Hero -->
  <section class="mb-8 text-center">
    <h1 class="text-4xl font-bold text-primary mb-4">Frequently Asked Questions</h1>
    <p class="text-lg text-gray-700">
      Questions about our clinic? Find the answers below. If you need further assistance, please contact us.
    </p>
  </section>

  <!-- FAQ Accordion -->
  <section>
    <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
      <!-- Consultation Fee -->
      <details class="p-6">
        <summary class="font-semibold text-primary cursor-pointer">What is the consultation fee?</summary>
        <p class="mt-4 text-gray-700">
          Our standard consultation fee is <strong>₹500</strong>. This includes a full examination and treatment plan discussion.
        </p>
      </details>
      <!-- How to Book -->
      <details class="p-6">
        <summary class="font-semibold text-primary cursor-pointer">How do I book an appointment?</summary>
        <p class="mt-4 text-gray-700">
          You can book an appointment by calling <a href="tel:+911234567890" class="text-accent underline">+91 12345 67890</a> 
          or by filling out the form on our <a href="<?php echo home_url('/contacts/'); ?>" class="text-accent underline">Contact</a> page.
        </p>
      </details>
      <!-- Operating Hours -->
      <details class="p-6">
        <summary class="font-semibold text-primary cursor-pointer">What are your operating hours?</summary>
        <p class="mt-4 text-gray-700">
          We are open Monday–Saturday from 9 AM to 6 PM, and Sunday by appointment only.
        </p>
      </details>
      <!-- Home Visits -->
      <details class="p-6">
        <summary class="font-semibold text-primary cursor-pointer">Do you offer home visits?</summary>
        <p class="mt-4 text-gray-700">
          Yes, home visits are available within a 10 km radius for an additional charge. Please call to schedule.
        </p>
      </details>
      <!-- Payment Methods -->
      <details class="p-6">
        <summary class="font-semibold text-primary cursor-pointer">What payment methods do you accept?</summary>
        <p class="mt-4 text-gray-700">
          We accept cash, credit/debit cards, and UPI payments.
        </p>
      </details>
      <!-- What to Bring -->
      <details class="p-6">
        <summary class="font-semibold text-primary cursor-pointer">What should I bring to my pet's appointment?</summary>
        <p class="mt-4 text-gray-700">
          Please bring any previous medical records, a list of current medications, and your pet's vaccination history.
        </p>
      </details>
    </div>
  </section>
</main>
<?php

get_footer();
