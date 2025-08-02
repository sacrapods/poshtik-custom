<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package poshtik-custom
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<!-- Tailwind Play CDN Config -->
	<script>
		tailwind.config = {
			content: [
				"<?php echo get_template_directory_uri(); ?>/**/*.php",
				"<?php echo get_stylesheet_directory_uri(); ?>/assets/js/**/*.js"
			],
			theme: {
				extend: {
					colors: {
						primary: '#0F1D32',
						accent: '#EB5225',
					},
				},
			},
			plugins: [],
		}
	</script>
	<script src="https://cdn.tailwindcss.com"></script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="flex flex-col min-h-screen">
<?php wp_body_open(); ?>
<header class="fixed top-0 inset-x-0 z-50 bg-white shadow">
  <div class="container mx-auto px-6 py-4 flex items-center justify-between">
    <!-- Logo -->
    <a href="<?php echo home_url(); ?>" class="text-2xl font-bold text-primary">Poshtik Pets</a>
    <!-- Dynamic Primary Menu -->
    <nav class="hidden md:flex flex-1 justify-center items-center space-x-8">
      <?php
        wp_nav_menu( array(
          'theme_location' => 'primary',
          'container'      => false,
          'menu_class'     => 'flex items-center space-x-6',
          'fallback_cb'    => false,
          'depth'          => 2,
        ) );
      ?>
      <?php if ( is_user_logged_in() ) : 
        $current = wp_get_current_user(); ?>
        <div class="relative">
          <!-- Avatar button -->
          <button id="profile-toggle" class="focus:outline-none">
            <?php echo get_avatar( $current->ID, 32, '', '', ['class'=>'rounded-full'] ); ?>
          </button>
          <!-- Dropdown menu -->
          <div id="profile-menu" class="hidden absolute right-0 mt-2 w-48 bg-white shadow-lg rounded border">
            <div class="px-4 py-2 border-b text-sm text-gray-700">
              Hi, <?php echo esc_html( $current->display_name ); ?>!
            </div>
            <a href="<?php echo esc_url( home_url('my-pets') ); ?>" class="block px-4 py-2 hover:bg-gray-100 text-gray-800">
              My Pets
            </a>
            <a href="<?php echo esc_url( home_url('settings') ); ?>" class="block px-4 py-2 hover:bg-gray-100 text-gray-800">
              Settings
            </a>
            <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="block px-4 py-2 hover:bg-gray-100 text-red-600">
              Log Out
            </a>
          </div>
        </div>
      <?php else : ?>
        <a href="<?php echo esc_url( home_url('login') ); ?>" class="text-sm text-primary">
          Log In
        </a>
        <a href="<?php echo esc_url( home_url('register') ); ?>" class="bg-accent hover:bg-[#d2431f] text-white font-semibold text-sm py-1 px-3 rounded">
          Register
        </a>
      <?php endif; ?>
    </nav>
    <!-- Mobile Toggle -->
    <button type="button" id="mobile-menu-button"
            class="md:hidden focus:outline-none">
      <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </div>
  <!-- Mobile Menu -->
  <nav id="mobile-menu" class="md:hidden hidden bg-white shadow p-4">
    <?php
      wp_nav_menu( array(
        'theme_location' => 'primary',
        'container'      => false,
        'items_wrap'     => '<ul class="flex flex-col space-y-2">%3$s</ul>',
      ) );
    ?>
    <?php if ( is_user_logged_in() ) : 
      $current = wp_get_current_user(); ?>
      <ul class="flex flex-col space-y-2 mt-4">
        <li class="text-sm">Hello, <?php echo esc_html( $current->display_name ?: $current->user_email ); ?></li>
        <li><a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="text-sm text-accent">Sign Out</a></li>
      </ul>
    <?php else : ?>
      <ul class="flex flex-col space-y-2 mt-4">
        <li><a href="<?php echo esc_url( home_url('login') ); ?>" class="text-sm text-primary">Log In</a></li>
        <li><a href="<?php echo esc_url( home_url('register') ); ?>" class="text-sm text-primary">Register</a></li>
      </ul>
    <?php endif; ?>
  </nav>
  <script>
    (function() {
      var btn = document.getElementById('mobile-menu-button');
      var menu = document.getElementById('mobile-menu');
      if (btn && menu) {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          menu.classList.toggle('hidden');
        });
      }
    })();
  </script>
</header>
