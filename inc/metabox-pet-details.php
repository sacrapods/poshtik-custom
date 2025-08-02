<?php
/**
 * Register and render meta-boxes for the 'pets' Custom Post Type.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Add Pet Details meta-box to the 'pets' post type.
 */
function poshtik_add_pet_meta_boxes() {
  add_meta_box(
    'poshtik_pet_details',          // Unique ID
    'Pet Details',                  // Box title
    'poshtik_render_pet_meta_box',  // Callback function
    'pets',                         // Post type
    'normal',                       // Context (normal, side, advanced)
    'high'                          // Priority
  );
}
add_action( 'add_meta_boxes', 'poshtik_add_pet_meta_boxes' );

/**
 * Render the Pet Details meta-box form fields.
 *
 * @param WP_Post $post The current post object.
 */
function poshtik_render_pet_meta_box( $post ) {
  // Use nonce for verification
  wp_nonce_field( 'poshtik_save_pet_details', 'poshtik_pet_nonce' );

  // Retrieve existing values or set defaults
  $owner_id         = get_post_meta( $post->ID, 'owner_id', true );
  $date_of_birth    = get_post_meta( $post->ID, 'date_of_birth', true );
  $sex              = get_post_meta( $post->ID, 'sex', true );
  $weight           = get_post_meta( $post->ID, 'weight', true );
  $microchip_number = get_post_meta( $post->ID, 'microchip_number', true );
  $spayed_neutered  = get_post_meta( $post->ID, 'spayed_neutered', true );

  // Owner selection
  echo '<p><label for="poshtik_owner_id"><strong>Owner (User):</strong></label><br />';
  wp_dropdown_users( array(
    'show_option_none' => 'Select owner',
    'name'             => 'poshtik_owner_id',
    'selected'         => $owner_id,
  ) );
  echo '</p>';

  // Date of Birth
  echo '<p><label for="poshtik_date_of_birth"><strong>Date of Birth:</strong></label><br />';
  echo '<input type="date" id="poshtik_date_of_birth" name="poshtik_date_of_birth" value="' . esc_attr( $date_of_birth ) . '" style="width:100%; max-width:250px;" />';
  echo '</p>';

  // Sex
  echo '<p><label for="poshtik_sex"><strong>Sex:</strong></label><br />';
  echo '<select id="poshtik_sex" name="poshtik_sex">';
  echo '<option value="Male"'   . selected( $sex, 'Male', false )   . '>Male</option>';
  echo '<option value="Female"' . selected( $sex, 'Female', false ) . '>Female</option>';
  echo '</select></p>';

  // Weight
  echo '<p><label for="poshtik_weight"><strong>Weight:</strong></label><br />';
  echo '<input type="text" id="poshtik_weight" name="poshtik_weight" value="' . esc_attr( $weight ) . '" placeholder="e.g. 12.5 kg" style="width:100%; max-width:250px;" />';
  echo '</p>';

  // Microchip Number
  echo '<p><label for="poshtik_microchip_number"><strong>Microchip Number:</strong></label><br />';
  echo '<input type="text" id="poshtik_microchip_number" name="poshtik_microchip_number" value="' . esc_attr( $microchip_number ) . '" style="width:100%; max-width:250px;" />';
  echo '</p>';

  // Spayed/Neutered
  echo '<p><label for="poshtik_spayed_neutered"><strong>Spayed/Neutered:</strong></label><br />';
  echo '<input type="checkbox" id="poshtik_spayed_neutered" name="poshtik_spayed_neutered" value="1"' . checked( $spayed_neutered, '1', false ) . ' /> Yes';
  echo '</p>';
}

/**
 * Save the Pet Details meta-box data when the post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 */
function poshtik_save_pet_details( $post_id ) {
  // Verify nonce
  if ( ! isset( $_POST['poshtik_pet_nonce'] ) || ! wp_verify_nonce( $_POST['poshtik_pet_nonce'], 'poshtik_save_pet_details' ) ) {
    return;
  }
  // Bail on autosave
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }
  // Check user permissions
  if ( ! current_user_can( 'edit_post', $post_id ) ) {
    return;
  }

  // Sanitize and save each field
  if ( isset( $_POST['poshtik_owner_id'] ) ) {
    update_post_meta( $post_id, 'owner_id', absint( $_POST['poshtik_owner_id'] ) );
  }
  if ( isset( $_POST['poshtik_date_of_birth'] ) ) {
    update_post_meta( $post_id, 'date_of_birth', sanitize_text_field( $_POST['poshtik_date_of_birth'] ) );
  }
  if ( isset( $_POST['poshtik_sex'] ) ) {
    update_post_meta( $post_id, 'sex', sanitize_text_field( $_POST['poshtik_sex'] ) );
  }
  if ( isset( $_POST['poshtik_weight'] ) ) {
    update_post_meta( $post_id, 'weight', sanitize_text_field( $_POST['poshtik_weight'] ) );
  }
  if ( isset( $_POST['poshtik_microchip_number'] ) ) {
    update_post_meta( $post_id, 'microchip_number', sanitize_text_field( $_POST['poshtik_microchip_number'] ) );
  }
  $spayed = isset( $_POST['poshtik_spayed_neutered'] ) && '1' === $_POST['poshtik_spayed_neutered'] ? 1 : 0;
  update_post_meta( $post_id, 'spayed_neutered', $spayed );
}
add_action( 'save_post_pets', 'poshtik_save_pet_details' );