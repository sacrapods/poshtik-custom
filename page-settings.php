

<?php
/**
 * Template Name: Settings
 */

// Redirect non-logged-in users to login
if ( ! is_user_logged_in() ) {
  wp_redirect( home_url( 'login' ) );
  exit;
}

// $current will be set after header (and after possible update)
$current = wp_get_current_user();
$errors  = array();
$success = false;

// Handle form submission
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['poshtik_update_profile'] ) ) {
  // Check nonce
  if ( ! isset( $_POST['poshtik_profile_nonce'] ) || ! wp_verify_nonce( $_POST['poshtik_profile_nonce'], 'poshtik_update_profile' ) ) {
    $errors[] = 'Security check failed.';
  } else {
    // Sanitize inputs
    $display_name = sanitize_text_field( $_POST['display_name'] );
    $email        = sanitize_email( $_POST['email'] );
    $current_pass = $_POST['current_pass'];
    $new_pass     = $_POST['new_pass'];
    $new_pass2    = $_POST['new_pass2'];

    // Verify current password
    if ( ! wp_check_password( $current_pass, $current->user_pass, $current->ID ) ) {
      $errors[] = 'Current password is incorrect.';
    }

    // Validate email
    if ( empty( $email ) || ! is_email( $email ) ) {
      $errors[] = 'Please enter a valid email address.';
    } elseif ( email_exists( $email ) && $email !== $current->user_email ) {
      $errors[] = 'That email is already in use.';
    }

    // Validate new password if provided
    if ( ! empty( $new_pass ) ) {
      if ( strlen( $new_pass ) < 8 ) {
        $errors[] = 'New password must be at least 8 characters.';
      }
      if ( $new_pass !== $new_pass2 ) {
        $errors[] = 'New passwords do not match.';
      }
    }

    // If no errors, update user
    if ( empty( $errors ) ) {
      // Update name and email
      wp_update_user( array(
        'ID'           => $current->ID,
        'display_name' => $display_name,
        'user_email'   => $email,
      ) );

      // Update password if set
      if ( ! empty( $new_pass ) ) {
        wp_set_password( $new_pass, $current->ID );
        // Re-authenticate so user stays logged in
        wp_signon( array(
          'user_login'    => $current->user_login,
          'user_password' => $new_pass,
          'remember'      => true,
        ), false );
      }

      $success = true;
      // Refresh current user data
      $current = wp_get_current_user();
    }
  }
}

// Now that updates are processed, load the header so it reflects new user data
get_header();

// Refresh the $current user object for header display
$current = wp_get_current_user();

// Display success or errors
if ( $success ) {
  echo '<div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800">Profile updated successfully.</div>';
} elseif ( ! empty( $errors ) ) {
  echo '<div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800">';
  foreach ( $errors as $error ) {
    echo '<p>' . esc_html( $error ) . '</p>';
  }
  echo '</div>';
}
?>

<form method="post" class="max-w-md mx-auto space-y-4">
  <?php wp_nonce_field( 'poshtik_update_profile', 'poshtik_profile_nonce' ); ?>
  <input type="hidden" name="poshtik_update_profile" value="1" />

  <label class="block">
    <span class="text-gray-700">Display Name</span>
    <input type="text" name="display_name" required
      class="mt-1 block w-full border-gray-300 rounded"
      value="<?php echo esc_attr( $_POST['display_name'] ?? $current->display_name ); ?>" />
  </label>

  <label class="block">
    <span class="text-gray-700">Email Address</span>
    <input type="email" name="email" required
      class="mt-1 block w-full border-gray-300 rounded"
      value="<?php echo esc_attr( $_POST['email'] ?? $current->user_email ); ?>" />
  </label>

  <label class="block">
    <span class="text-gray-700">Current Password</span>
    <input type="password" name="current_pass" required
      class="mt-1 block w-full border-gray-300 rounded" />
  </label>

  <label class="block">
    <span class="text-gray-700">New Password (leave blank to keep unchanged)</span>
    <input type="password" name="new_pass"
      class="mt-1 block w-full border-gray-300 rounded" />
  </label>

  <label class="block">
    <span class="text-gray-700">Confirm New Password</span>
    <input type="password" name="new_pass2"
      class="mt-1 block w-full border-gray-300 rounded" />
  </label>

  <button type="submit"
    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
    Save Changes
  </button>
</form>

<?php
get_footer();