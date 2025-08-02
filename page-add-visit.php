<?php
/**
 * Template Name: Add Visit
 */

// Only logged-in users can add visits
if ( ! is_user_logged_in() ) {
  wp_redirect( home_url( 'login' ) );
  exit;
}

get_header();

// Get the pet ID from URL or form
$pet_id = isset( $_GET['pet_id'] ) ? absint( $_GET['pet_id'] ) : 0;

$errors = array();

// Handle submission
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['add_visit_nonce'] ) ) {
  if ( ! wp_verify_nonce( $_POST['add_visit_nonce'], 'poshtik_add_visit' ) ) {
    $errors[] = 'Security check failed.';
  } else {
    // Sanitize inputs
    $visit_date = sanitize_text_field( $_POST['visit_date'] );
    $vet_id     = absint( $_POST['vet_id'] );
    $reason     = sanitize_text_field( $_POST['reason'] );
    $notes      = sanitize_textarea_field( $_POST['notes'] );

    // Create Visit CPT
    $visit_id = wp_insert_post( array(
      'post_type'   => 'visit',
      'post_title'  => $reason,
      'post_status' => 'publish',
      'post_date'   => $visit_date . ' 00:00:00',
    ) );

    if ( is_wp_error( $visit_id ) ) {
      $errors[] = $visit_id->get_error_message();
    } else {
      // Link to pet and vet
      update_post_meta( $visit_id, 'pet_id', $pet_id );
      update_post_meta( $visit_id, 'vet_id', $vet_id );
      // Save notes as comment
      if ( ! empty( $notes ) ) {
        wp_insert_comment( array(
          'comment_post_ID' => $visit_id,
          'comment_content' => $notes,
          'user_id'         => get_current_user_id(),
          'comment_type'    => 'visit_note',
        ) );
      }
      // Redirect to pet profile
      wp_redirect( home_url( 'pets/' . $pet_id ) );
      exit;
    }
  }
}
?>

<div class="container mx-auto p-6 max-w-md">
  <h1 class="text-2xl font-bold mb-4">Add Visit for Pet #<?php echo esc_html( $pet_id ); ?></h1>

  <?php if ( ! empty( $errors ) ) : ?>
    <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800">
      <?php foreach ( $errors as $error ) : ?>
        <p><?php echo esc_html( $error ); ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" class="space-y-4">
    <?php wp_nonce_field( 'poshtik_add_visit', 'add_visit_nonce' ); ?>
    <input type="hidden" name="pet_id" value="<?php echo esc_attr( $pet_id ); ?>" />

    <label class="block">
      <span class="text-gray-700">Visit Date</span>
      <input type="date" name="visit_date" required class="mt-1 block w-full border-gray-300 rounded" />
    </label>

    <label class="block">
      <span class="text-gray-700">Veterinarian</span>
      <?php
        wp_dropdown_users( array(
          'name'             => 'vet_id',
          'show_option_none' => 'Select Vet',
          'role'             => 'vet',
          'selected'         => get_current_user_id(),
          'class'            => 'mt-1 block w-full border-gray-300 rounded',
        ) );
      ?>
    </label>

    <label class="block">
      <span class="text-gray-700">Reason</span>
      <input type="text" name="reason" required class="mt-1 block w-full border-gray-300 rounded" />
    </label>

    <label class="block">
      <span class="text-gray-700">Notes</span>
      <textarea name="notes" rows="4" class="mt-1 block w-full border-gray-300 rounded"></textarea>
    </label>

    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
      Create Visit
    </button>
  </form>
</div>

<?php get_footer(); ?>
