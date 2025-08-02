
<?php
/**
 * The template for displaying the footer
 *
 * @package poshtik-custom
 */
?>

<!--
  Custom Footer: 4-column layout (Company, Support, Legal, Stay in Touch)
  Uses Tailwind utility classes for dark background, text colors, spacing.
-->
<footer id="colophon" class="bg-gray-800 text-gray-100 py-12">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 md:gap-8 gap-6">
      <!-- Company -->
      <div>
        <h3 class="font-semibold mb-2 text-white">Company</h3>
        <ul class="space-y-1 text-gray-300">
          <li><a href="<?php echo esc_url(home_url('/about/')); ?>" class="hover:text-white">About Us</a></li>
          <li><a href="<?php echo esc_url(home_url('/careers/')); ?>" class="hover:text-white">Careers</a></li>
          <li><a href="<?php echo esc_url(home_url('/blog/')); ?>" class="hover:text-white">Blog</a></li>
        </ul>
      </div>
      <!-- Support -->
      <div>
        <h3 class="font-semibold mb-2 text-white">Support</h3>
        <ul class="space-y-1 text-gray-300">
          <li><a href="<?php echo esc_url(home_url('/faq/')); ?>" class="hover:text-white">FAQ</a></li>
          <li><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="hover:text-white">Contact</a></li>
          <li><a href="<?php echo esc_url(home_url('/help-center/')); ?>" class="hover:text-white">Help Center</a></li>
        </ul>
      </div>
      <!-- Legal -->
      <div>
        <h3 class="font-semibold mb-2 text-white">Legal</h3>
        <ul class="space-y-1 text-gray-300">
          <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" class="hover:text-white">Privacy Policy</a></li>
          <li><a href="<?php echo esc_url(home_url('/terms-of-use/')); ?>" class="hover:text-white">Terms of Use</a></li>
          <li><a href="<?php echo esc_url(home_url('/cookie-policy/')); ?>" class="hover:text-white">Cookie Policy</a></li>
        </ul>
      </div>
      <!-- Stay in Touch -->
      <div>
        <h3 class="font-semibold mb-2 text-white">Stay in Touch</h3>
        <form action="#" class="flex items-center space-x-2">
          <input type="email" placeholder="Your email" class="flex-1 px-4 py-2 rounded-l bg-gray-700 text-gray-200 focus:outline-none" />
          <button type="submit"
                  class="px-4 py-2 bg-[#EB5225] text-white rounded-r font-semibold hover:bg-[#BE3D1B]">
            Subscribe
          </button>
        </form>
        <div class="flex space-x-4 mt-4 justify-center md:justify-start">
          <a href="https://instagram.com/YourHandle" target="_blank" class="hover:text-white">Instagram</a>
          <a href="https://facebook.com/YourPage" target="_blank" class="hover:text-white">Facebook</a>
        </div>
      </div>
    </div>
    <!-- Bottom copyright -->
    <div class="border-t border-gray-700 mt-8 pt-4 text-center text-sm text-gray-400">
      &copy; <?php echo date('Y'); ?> Poshtik Pets. All rights reserved.
    </div>
  </div>
</footer>


<?php 
/*
get_template_part( 'template-parts/modal-announcement' ); 
*/
?>
<?php wp_footer(); ?>
</div>
</body>
</html>
