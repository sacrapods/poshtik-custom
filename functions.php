<?php
/**
 * poshtik-custom functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package poshtik-custom
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function poshtik_custom_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on poshtik-custom, use a find and replace
		* to change 'poshtik-custom' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'poshtik-custom', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	// right after add_theme_support( 'post-thumbnails' ); for example
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'poshtik-custom' ),
		'footer'  => __('Footer Menu','poshtik-custom'),
	) );

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'poshtik_custom_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'poshtik_custom_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function poshtik_custom_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'poshtik_custom_content_width', 640 );
}
add_action( 'after_setup_theme', 'poshtik_custom_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function poshtik_custom_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'poshtik-custom' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'poshtik-custom' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'poshtik_custom_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function poshtik_custom_scripts() {
	wp_enqueue_style( 'poshtik-custom-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'poshtik-custom-style', 'rtl', 'replace' );

	wp_enqueue_script( 'poshtik-custom-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	wp_enqueue_script(
	  'poshtik-profile-menu',
	  get_template_directory_uri() . '/js/profile-menu.js',
	  array(),
	  _S_VERSION,
	  true
	);

	// NEW CSS ORGANIZATION - Enqueue organized dashboard styles
	if ( is_page_template( 'page-vet-dashboard.php' ) ) {
		// Core dashboard layout
		wp_enqueue_style(
		  'dashboard-layout',
		  get_template_directory_uri() . '/assets/css/layouts/dashboard.css',
		  array(),
		  _S_VERSION
		);
		
		// Sidebar component
		wp_enqueue_style(
		  'sidebar-component',
		  get_template_directory_uri() . '/assets/css/components/sidebar.css',
		  array('dashboard-layout'),
		  _S_VERSION
		);
		
		// Pet overview component
		wp_enqueue_style(
		  'pet-overview-component',
		  get_template_directory_uri() . '/assets/css/components/pet-overview.css',
		  array('dashboard-layout'),
		  _S_VERSION
		);
		
		// Mobile responsive styles
		wp_enqueue_style(
		  'dashboard-mobile',
		  get_template_directory_uri() . '/assets/css/layouts/mobile.css',
		  array('dashboard-layout'),
		  _S_VERSION,
		  'screen and (max-width: 768px)'
		);
		
		// Dashboard JavaScript
		wp_enqueue_script(
		  'poshtik-dashboard-script',
		  get_template_directory_uri() . '/js/dashboard.js',
		  array(),
		  _S_VERSION,
		  true
		);
		
		// Provide AJAX URL to dashboard script
		wp_add_inline_script(
		  'poshtik-dashboard-script',
		  'var ajaxurl = "' . esc_url( admin_url( 'admin-ajax.php' ) ) . '";'
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'poshtik_custom_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load custom authentication handlers.
 */
require get_template_directory() . '/auth-handlers.php';

/**
 * Register Pets custom post type and related taxonomies.
 */
require get_template_directory() . '/inc/cpt-pets.php';

/**
 * Enqueue Swiper carousel assets (CSS & JS)
 * Uses Swiper v8 via CDN for any carousels on the front end.
 * Place the hook anywhere after your existing enqueue calls.
 */
function poshtik_enqueue_swiper() {
    // Swiper core styles
    wp_enqueue_style(
        'swiper-css',
        'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css',
        array(),
        null
    );
    // Swiper core JS
    wp_enqueue_script(
        'swiper-js',
        'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
        array(),
        null,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'poshtik_enqueue_swiper' );

/**
 * Output modal initialization script in footer (only on Vet Dashboard template).
 */
function poshtik_custom_modal_script() {
    if ( ! is_page_template( 'page-vet-dashboard.php' ) ) {
        return;
    }
    ?>
    <script>
    (function(){
        document.body.classList.add('modal-open');
        var closeBtn = document.getElementById('custom-close');
        if ( closeBtn ) {
            closeBtn.addEventListener('click', function(){
                document.body.classList.remove('modal-open');
            });
        }
    })();
    </script>
    <?php
}

add_action( 'wp_footer', 'poshtik_custom_modal_script' );

// Register custom roles on init
function poshtik_add_custom_roles() {
    add_role('pet_parent', 'Pet Parent', [
        'read' => true,
    ]);
    add_role('vet', 'Vet', [
        'read' => true,
        'edit_pets' => true,
        'manage_appointments' => true,
    ]);
}
add_action('init', 'poshtik_add_custom_roles');

// Assign 'pet_parent' role on user registration
function poshtik_set_default_user_role($user_id) {
    $user = new WP_User($user_id);
    $user->set_role('pet_parent');
}
add_action('user_register', 'poshtik_set_default_user_role');

function poshtik_social_login_buttons() {
  if ( function_exists('nsl_display_login') ) {
    echo nsl_display_login();
  }
}

// Map /login and /register slugs to their pages
function poshtik_add_custom_rewrites() {
    add_rewrite_rule( '^login/?$', 'index.php?pagename=login', 'top' );
    add_rewrite_rule( '^register/?$', 'index.php?pagename=register', 'top' );
    add_rewrite_rule( '^verify-email/?$', 'index.php?pagename=verify-email', 'top' );
}
add_action( 'init', 'poshtik_add_custom_rewrites' );

// Flush rewrite rules on theme activation to register new rewrites
function poshtik_flush_rewrites() {
    flush_rewrite_rules();
}

add_action( 'after_switch_theme', 'poshtik_flush_rewrites' );

// Ensure WordPress allows user registration (overrides General Settings if needed)
add_filter( 'pre_option_users_can_register', '__return_true' );

// Redirect any default WP registration attempts to our frontend /register page
add_action( 'login_init', function() {
    if ( isset( $_GET['action'] ) && 'register' === $_GET['action'] ) {
        wp_redirect( home_url( 'register' ) );
        exit;
    }
} );


// In your theme's functions.php, add the following to allow admins to mark email verification:

/**
 * Add a 'Verified' column to the Users list.
 */
add_filter('manage_users_columns', function($columns) {
    $columns['email_verified'] = 'Verified';
    return $columns;
});
add_filter('manage_users_custom_column', function($value, $column_name, $user_id) {
    if ($column_name === 'email_verified') {
        return get_user_meta($user_id, 'is_verified', true) ? 'Yes' : 'No';
    }
    return $value;
}, 10, 3);

/**
 * Add a checkbox to the user profile for email verification.
 */
add_action('show_user_profile', 'poshtik_profile_verification_field');
add_action('edit_user_profile', 'poshtik_profile_verification_field');
function poshtik_profile_verification_field($user) {
    if (!current_user_can('edit_users', $user->ID)) return;
    $verified = get_user_meta($user->ID, 'is_verified', true);
    ?>
    <h2>Email Verification</h2>
    <table class="form-table">
        <tr>
            <th><label for="is_verified">Verified?</label></th>
            <td>
                <input type="checkbox" name="is_verified" id="is_verified" value="1" <?php checked(1, $verified); ?> />
                <p class="description">Check to mark this user as email-verified.</p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save the email verification field from the profile.
 */
add_action('personal_options_update', 'poshtik_save_profile_verification');
add_action('edit_user_profile_update', 'poshtik_save_profile_verification');
function poshtik_save_profile_verification($user_id) {
    if (!current_user_can('edit_users', $user_id)) return;
    update_user_meta($user_id, 'is_verified', isset($_POST['is_verified']) ? 1 : 0);
}
// Hide the WordPress admin bar on the front end for users with the 'pet_parent' role or on the Vet Dashboard template.
add_filter( 'show_admin_bar', function( $show ) {
    // Hide for users with the 'pet_parent' role
    if ( in_array( 'pet_parent', wp_get_current_user()->roles ) ) {
        return false;
    }
    // Hide on Vet Dashboard page template
    if ( is_page_template( 'page-vet-dashboard.php' ) ) {
        return false;
    }
    return $show;
} );

// In functions.php, alongside your other custom rewrites:
add_rewrite_rule( '^settings/?$', 'index.php?pagename=settings', 'top' );

/**
 * AJAX handler for Vet Dashboard panels.
 */
add_action( 'wp_ajax_poshtik_load_pet_panel', 'poshtik_load_pet_panel' );
add_action( 'wp_ajax_nopriv_poshtik_load_pet_panel', 'poshtik_load_pet_panel' );
function poshtik_load_pet_panel() {
    // Security: Nonce check
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'vet_appointments_nonce') ) {
        wp_send_json_error('Security check failed');
    }
    // Validate inputs
    if ( empty( $_POST['pet_id'] ) || empty( $_POST['panel'] ) ) {
        wp_send_json_error( 'Missing parameters.' );
    }
    $pet_id = absint( $_POST['pet_id'] );
    $panel  = sanitize_key( $_POST['panel'] );

    // Setup global post for template functions
    global $post;
    $post = get_post( $pet_id );
    setup_postdata( $post );

    ob_start();
    // Load the corresponding pet template part
    switch ( $panel ) {
        case 'overview':
            get_template_part( 'template-parts/pet/overview' );
            break;
        case 'visits':
            get_template_part( 'template-parts/pet/visits' );
            break;
        case 'files':
            get_template_part( 'template-parts/pet/files' );
            break;
        case 'comments':
            get_template_part( 'template-parts/pet/comments' );
            break;
        case 'pet_list':
            get_template_part( 'template-parts/dashboard/pet-list' );
            break;
        case 'edit_profile':
            get_template_part( 'template-parts/pet/edit-profile' );
            break;
        case 'list':
            get_template_part( 'template-parts/dashboard/pet-list' );
            break;
        default:
            echo '<p>Invalid panel specified.</p>';
    }
    wp_reset_postdata();

    $html = ob_get_clean();
    echo $html;
    wp_die();
}

// Add Google Fonts for modern dashboard
function poshtik_enqueue_google_fonts() {
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' );
}
add_action( 'wp_enqueue_scripts', 'poshtik_enqueue_google_fonts' );


// AJAX handler for loading all pets
add_action('wp_ajax_load_all_pets', 'handle_load_all_pets');
function handle_load_all_pets() {
    // Security: Nonce check
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'vet_appointments_nonce') ) {
        wp_send_json_error('Security check failed');
    }
    $pets = get_posts(array(
        'post_type' => 'pets',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    if ($pets) {
        echo '<h2>All Pets (' . count($pets) . ')</h2>';
        echo '<div class="pets-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem;">';
        
        foreach ($pets as $pet) {
            $pet_name = get_the_title($pet);
            $pet_initials = strtoupper(substr($pet_name, 0, 1));
            echo '<div class="pet-card" style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem;">';
            echo '<div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">';
            echo '<div style="width: 40px; height: 40px; background: #f093fb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">' . $pet_initials . '</div>';
            echo '<div>';
            echo '<div style="font-weight: 600; color: #374151;">' . esc_html($pet_name) . '</div>';
            echo '<div style="font-size: 0.875rem; color: #6b7280;">Pet ID: ' . $pet->ID . '</div>';
            echo '</div>';
            echo '</div>';
            echo '<button style="width: 100%; padding: 0.5rem; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer;" onclick="viewPet(' . $pet->ID . ')">View Details</button>';
            echo '</div>';
        }
        
        echo '</div>';
    } else {
        echo '<h2>All Pets</h2><p>No pets found.</p>';
    }
    
    wp_die();
}

// Enhanced AJAX handler for loading all pets with search
add_action('wp_ajax_load_all_pets_enhanced', 'handle_load_all_pets_enhanced');
function handle_load_all_pets_enhanced() {
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    $args = array(
        'post_type' => 'pets',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    
    if ($search) {
        $args['s'] = $search;
    }
    
    $pets = get_posts($args);
    
    if ($pets) {
        echo '<div class="pets-list-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">';
        echo '<h2 style="margin: 0;">All Pets (' . count($pets) . ')</h2>';
        echo '<div class="search-container" style="position: relative;">';
        echo '<input type="text" id="pet-search" placeholder="Search pets..." style="';
        echo 'padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; ';
        echo 'width: 250px; font-size: 0.875rem; transition: all 0.1s ease;">';
        echo '</div></div>';
        
        echo '<div class="pets-list" id="pets-list">';
        
        foreach ($pets as $pet) {
            $pet_name = get_the_title($pet);
            $pet_initials = strtoupper(substr($pet_name, 0, 1));
            $species = wp_get_post_terms($pet->ID, 'species', array('fields' => 'names'));
            $breed = wp_get_post_terms($pet->ID, 'breed', array('fields' => 'names'));
            
            echo '<div class="pet-row" data-pet-name="' . esc_attr(strtolower($pet_name)) . '" style="';
            echo 'display: flex; align-items: center; justify-content: space-between; ';
            echo 'padding: 1rem; background: #f8fafc; border: 1px solid #e5e7eb; ';
            echo 'border-radius: 8px; margin-bottom: 0.75rem; ';
            echo 'transition: all 0.1s ease; cursor: pointer;">';
            
            echo '<div style="display: flex; align-items: center; gap: 1rem;">';
            echo '<div style="width: 40px; height: 40px; background: #667eea; border-radius: 8px; ';
            echo 'display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">';
            echo $pet_initials . '</div>';
            
            echo '<div>';
            echo '<div style="font-weight: 600; color: #374151; font-size: 1.1rem;">' . esc_html($pet_name) . '</div>';
            echo '<div style="font-size: 0.875rem; color: #6b7280;">';
            if (!empty($species)) {
                echo esc_html($species[0]);
                if (!empty($breed)) {
                    echo ' • ' . esc_html($breed[0]);
                }
            }
            echo ' • ID: ' . $pet->ID . '</div>';
            echo '</div></div>';
            
            echo '<button class="edit-profile-btn" data-pet-id="' . $pet->ID . '" style="';
            echo 'background: #667eea; color: white; border: none; padding: 0.75rem 1.5rem; ';
            echo 'border-radius: 6px; cursor: pointer; font-weight: 500; ';
            echo 'transition: all 0.1s ease;">Edit Profile</button>';
            echo '</div>';
        }
        
        echo '</div>';
    } else {
        echo '<h2>All Pets</h2><p>No pets found.</p>';
    }
    
    wp_die();
}

// AJAX handler for loading pet edit profile with comments
add_action('wp_ajax_load_pet_edit_profile', 'handle_load_pet_edit_profile');
function handle_load_pet_edit_profile() {
    $pet_id = isset($_POST['pet_id']) ? absint($_POST['pet_id']) : 0;
    if (!$pet_id) {
        wp_die('Invalid pet ID');
    }
    
    global $post;
    $post = get_post($pet_id);
    setup_postdata($post);
    
    // Get pet data
    $pet_name = get_the_title($pet_id);
    $species = get_post_meta($pet_id, 'species', true);
    $breed = get_post_meta($pet_id, 'breed', true);
    $dob = get_post_meta($pet_id, 'dob', true);
    $weight = get_post_meta($pet_id, 'weight', true);
    $microchip = get_post_meta($pet_id, 'microchip_number', true);
    $owner_id = get_post_meta($pet_id, 'owner_id', true);
    $owner = $owner_id ? get_userdata($owner_id) : null;
    
    // Get comments
    $comments = get_comments(array(
        'post_id' => $pet_id,
        'type' => 'pet_note',
        'order' => 'DESC'
    ));
    
    echo '<div class="pet-edit-profile">';
    echo '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">';
    echo '<h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Edit Profile: ' . esc_html($pet_name) . '</h2>';
    echo '<button onclick="loadAllPetsEnhanced()" style="background: #6b7280; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">← Back to List</button>';
    echo '</div>';
    
    echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">';
    
    // Left column - Pet details form
    echo '<div class="pet-details-form" style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e5e7eb;">';
    echo '<h3 style="margin-bottom: 1.5rem; font-weight: 600;">Pet Information</h3>';
    
    echo '<form id="pet-edit-form" style="display: grid; gap: 1rem;">';
    echo wp_nonce_field('edit_pet', 'edit_pet_nonce', true, false);
    echo '<input type="hidden" name="pet_id" value="' . $pet_id . '">';
    
    echo '<div><label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Name</label>';
    echo '<input type="text" name="pet_name" value="' . esc_attr($pet_name) . '" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 6px;"></div>';
    
    echo '<div><label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Species</label>';
    echo '<input type="text" name="species" value="' . esc_attr($species) . '" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 6px;"></div>';
    
    echo '<div><label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Breed</label>';
    echo '<input type="text" name="breed" value="' . esc_attr($breed) . '" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 6px;"></div>';
    
    echo '<div><label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Date of Birth</label>';
    echo '<input type="date" name="dob" value="' . esc_attr($dob) . '" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 6px;"></div>';
    
    if ($owner) {
        echo '<div style="background: #f8fafc; padding: 1rem; border-radius: 8px;">';
        echo '<strong>Owner:</strong> ' . esc_html($owner->display_name) . '<br>';
        echo '<span style="color: #6b7280; font-size: 0.875rem;">' . esc_html($owner->user_email) . '</span>';
        echo '</div>';
    }
    
    echo '<button type="submit" style="background: #667eea; color: white; border: none; padding: 0.75rem; border-radius: 6px; font-weight: 500; cursor: pointer;">Save Changes</button>';
    echo '</form>';
    echo '</div>';
    
    // Right column - Comments
    echo '<div class="pet-comments" style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e5e7eb;">';
    echo '<h3 style="margin-bottom: 1.5rem; font-weight: 600;">Medical Notes</h3>';
    
    // Add comment form
    echo '<form id="add-comment-form" style="margin-bottom: 1.5rem;">';
    echo wp_nonce_field('add_pet_comment', 'pet_comment_nonce', true, false);
    echo '<textarea name="pet_comment" placeholder="Add a medical note..." style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 0.75rem;" rows="3"></textarea>';
    echo '<button type="submit" style="background: #43e97b; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">Add Note</button>';
    echo '</form>';
    
    // Comments list
    echo '<div class="comments-list" style="max-height: 400px; overflow-y: auto;">';
    if ($comments) {
        foreach ($comments as $comment) {
            $author = get_userdata($comment->user_id);
            echo '<div style="padding: 1rem; background: #f8fafc; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #667eea;">';
            echo '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">';
            echo '<strong style="color: #374151;">' . ($author ? esc_html($author->display_name) : 'Unknown') . '</strong>';
            echo '<span style="color: #6b7280; font-size: 0.875rem;">' . esc_html(get_comment_date('M j, Y g:i A', $comment)) . '</span>';
            echo '</div>';
            echo '<p style="color: #374151; margin: 0;">' . esc_html($comment->comment_content) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p style="color: #6b7280; text-align: center; padding: 2rem;">No medical notes yet.</p>';
    }
    echo '</div>';
    echo '</div>';
    
    echo '</div>'; // Close grid
    echo '</div>'; // Close container
    
    wp_reset_postdata();
    wp_die();
}

//Appointments

// Create appointment tables on theme activation
function vet_create_appointment_tables() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Appointments table using WordPress prefix
    $appointments_table = $wpdb->prefix . 'vet_appointments';
    $appointments_sql = "CREATE TABLE $appointments_table (
        id int(11) NOT NULL AUTO_INCREMENT,
        pet_id int(11) NOT NULL,
        appointment_date date NOT NULL,
        appointment_time time NOT NULL,
        appointment_type varchar(50) NOT NULL,
        assigned_vet int(11) DEFAULT NULL,
        duration int(11) DEFAULT 30,
        status enum('confirmed','completed','cancelled','rescheduled') DEFAULT 'confirmed',
        notes text DEFAULT NULL,
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_date_time (appointment_date, appointment_time),
        KEY idx_pet_id (pet_id),
        KEY idx_status (status)
    ) $charset_collate;";
    
    // Pets table (if you don't have custom post type)
    $pets_table = $wpdb->prefix . 'vet_pets';
    $pets_sql = "CREATE TABLE $pets_table (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        species varchar(50) DEFAULT 'Dog',
        breed varchar(100) DEFAULT NULL,
        age int(11) DEFAULT NULL,
        owner_name varchar(100) NOT NULL,
        owner_email varchar(100) DEFAULT NULL,
        owner_phone varchar(20) DEFAULT NULL,
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_name_owner (name, owner_name),
        UNIQUE KEY unique_pet_owner (name, owner_name)
    ) $charset_collate;";
    
    // Vets table
    $vets_table = $wpdb->prefix . 'vet_staff';
    $vets_sql = "CREATE TABLE $vets_table (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) DEFAULT NULL,
        phone varchar(20) DEFAULT NULL,
        specialization varchar(100) DEFAULT NULL,
        status enum('active','inactive') DEFAULT 'active',
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_status (status)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($appointments_sql);
    dbDelta($pets_sql);
    dbDelta($vets_sql);
    
    // Insert sample data
    $wpdb->insert($vets_table, array(
        'name' => 'Dr. Sarah Johnson',
        'email' => 'sarah@vetclinic.com',
        'specialization' => 'General Practice'
    ));
    
    $wpdb->insert($vets_table, array(
        'name' => 'Dr. Michael Chen', 
        'email' => 'michael@vetclinic.com',
        'specialization' => 'Surgery'
    ));
    
    $wpdb->insert($pets_table, array(
        'name' => 'juca',
        'species' => 'Dog',
        'owner_name' => 'John Smith',
        'owner_email' => 'john@email.com'
    ));
    
    $wpdb->insert($pets_table, array(
        'name' => 'thomas',
        'species' => 'Cat', 
        'owner_name' => 'Jane Doe',
        'owner_email' => 'jane@email.com'
    ));
    
    // Insert sample appointments for today
    $today = date('Y-m-d');
    $wpdb->insert($appointments_table, array(
        'pet_id' => 1,
        'appointment_date' => $today,
        'appointment_time' => '09:00:00',
        'appointment_type' => 'checkup',
        'assigned_vet' => 1,
        'status' => 'confirmed',
        'notes' => 'Annual wellness exam'
    ));
    
    $wpdb->insert($appointments_table, array(
        'pet_id' => 2,
        'appointment_date' => $today,
        'appointment_time' => '14:30:00',
        'appointment_type' => 'vaccination',
        'assigned_vet' => 1,
        'status' => 'confirmed',
        'notes' => 'Rabies vaccination'
    ));
}

// Run table creation on theme activation
add_action('after_switch_theme', 'vet_create_appointment_tables');

// AJAX Handler: Get all data in one call for performance
add_action('wp_ajax_vet_get_all_data', 'vet_handle_get_all_data');
function vet_handle_get_all_data() {
    // Security check
    if (!wp_verify_nonce($_POST['nonce'], 'vet_appointments_nonce')) {
        wp_die('Security check failed');
    }
    
    global $wpdb;
    
    $date = sanitize_text_field($_POST['date'] ?? date('Y-m-d'));
    $view = sanitize_text_field($_POST['view'] ?? 'day');
    
    try {
        $appointments_table = $wpdb->prefix . 'vet_appointments';
        $pets_table = $wpdb->prefix . 'vet_pets';
        $vets_table = $wpdb->prefix . 'vet_staff';
        
        // Get appointments based on view
        if ($view === 'day') {
            $appointments = $wpdb->get_results($wpdb->prepare("
                SELECT a.*, p.name as pet_name, p.owner_name, v.name as vet_name, v.id as vet_id
                FROM $appointments_table a
                LEFT JOIN $pets_table p ON a.pet_id = p.id
                LEFT JOIN $vets_table v ON a.assigned_vet = v.id
                WHERE a.appointment_date = %s
                ORDER BY a.appointment_time ASC
                LIMIT 50
            ", $date), ARRAY_A);
        } else {
            $start = date('Y-m-01', strtotime($date));
            $end = date('Y-m-t', strtotime($date));
            
            $appointments = $wpdb->get_results($wpdb->prepare("
                SELECT a.*, p.name as pet_name, p.owner_name, v.name as vet_name, v.id as vet_id
                FROM $appointments_table a
                LEFT JOIN $pets_table p ON a.pet_id = p.id
                LEFT JOIN $vets_table v ON a.assigned_vet = v.id
                WHERE a.appointment_date BETWEEN %s AND %s
                ORDER BY a.appointment_date ASC, a.appointment_time ASC
                LIMIT 200
            ", $start, $end), ARRAY_A);
        }
        
        // Get pets and vets
        $pets = $wpdb->get_results("
            SELECT id, name, owner_name 
            FROM $pets_table 
            ORDER BY name ASC 
            LIMIT 100
        ", ARRAY_A);
        
        $vets = $wpdb->get_results("
            SELECT id, name 
            FROM $vets_table 
            WHERE status = 'active' 
            ORDER BY name ASC 
            LIMIT 20
        ", ARRAY_A);
        
        // Calculate stats in one query for performance
        $today = date('Y-m-d');
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $month_start = date('Y-m-01');
        
        $stats = $wpdb->get_row($wpdb->prepare("
            SELECT 
                SUM(CASE WHEN appointment_date = %s THEN 1 ELSE 0 END) as today,
                SUM(CASE WHEN appointment_date >= %s THEN 1 ELSE 0 END) as week,
                SUM(CASE WHEN appointment_date >= %s THEN 1 ELSE 0 END) as month,
                ROUND(
                    SUM(CASE WHEN appointment_date >= %s AND status = 'completed' THEN 1 ELSE 0 END) * 100.0 / 
                    NULLIF(SUM(CASE WHEN appointment_date >= %s THEN 1 ELSE 0 END), 0), 0
                ) as completion_rate
            FROM $appointments_table
        ", $today, $week_start, $month_start, $month_start, $month_start), ARRAY_A);
        
        wp_send_json_success(array(
            'appointments' => $appointments ?: array(),
            'pets' => $pets ?: array(),
            'vets' => $vets ?: array(),
            'stats' => $stats ?: array('today' => 0, 'week' => 0, 'month' => 0, 'completion_rate' => 0)
        ));
        
    } catch (Exception $e) {
        wp_send_json_error('Database error: ' . $e->getMessage());
    }
}

