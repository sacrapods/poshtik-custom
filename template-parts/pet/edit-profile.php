

<?php
/**
 * Template Part: Pet Edit Profile
 * Displays a form to edit a pet’s details, pre‑filled with existing data.
 */

global $post;
$pet_id = get_the_ID();

// Handle form submission
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['edit_pet_nonce'] ) ) {
    if ( ! wp_verify_nonce( $_POST['edit_pet_nonce'], 'edit_pet' ) ) {
        wp_die( 'Security check failed.' );
    }
    // Sanitize and update title
    $new_title = sanitize_text_field( $_POST['pet_name'] );
    wp_update_post( array(
        'ID'         => $pet_id,
        'post_title' => $new_title,
    ) );
    // Update meta fields
    update_post_meta( $pet_id, 'species', sanitize_text_field( $_POST['species'] ) );
    update_post_meta( $pet_id, 'breed',   sanitize_text_field( $_POST['breed'] ) );
    update_post_meta( $pet_id, 'dob',     sanitize_text_field( $_POST['dob'] ) );
    echo '<div class="notice-success">Profile updated successfully.</div>';
}

// Fetch existing values
$current_species = get_post_meta( $pet_id, 'species', true );
$current_breed   = get_post_meta( $pet_id, 'breed',   true );
$current_dob     = get_post_meta( $pet_id, 'dob',     true );
?>

<section id="edit-profile-panel" class="dashboard-panel edit-profile-panel hidden">
  <h2 class="text-2xl font-semibold mb-4">
    Edit Profile for <?php echo esc_html( get_the_title( $pet_id ) ); ?>
  </h2>

  <form method="post" class="space-y-4">
    <?php wp_nonce_field( 'edit_pet', 'edit_pet_nonce' ); ?>
    <input type="hidden" name="pet_id" value="<?php echo esc_attr( $pet_id ); ?>" />

    <label class="block">
      <span class="text-gray-700">Name</span>
      <input type="text" name="pet_name" required
             value="<?php echo esc_attr( get_the_title( $pet_id ) ); ?>"
             class="mt-1 block w-full border-gray-300 rounded" />
    </label>

    <label class="block">
      <span class="text-gray-700">Species</span>
      <input type="text" name="species"
             value="<?php echo esc_attr( $current_species ); ?>"
             class="mt-1 block w-full border-gray-300 rounded" />
    </label>

    <label class="block">
      <span class="text-gray-700">Breed</span>
      <input type="text" name="breed"
             value="<?php echo esc_attr( $current_breed ); ?>"
             class="mt-1 block w-full border-gray-300 rounded" />
    </label>

    <label class="block">
      <span class="text-gray-700">Date of Birth</span>
      <input type="date" name="dob"
             value="<?php echo esc_attr( $current_dob ); ?>"
             class="mt-1 block w-full border-gray-300 rounded" />
    </label>

    <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
      Save Changes
    </button>
  </form>
</section>