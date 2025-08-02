<?php
/**
 * Dashboard Pet List Panel
 * Displays list of pets with Edit Profile buttons.
 */
?>

<section id="pet-list-panel" class="dashboard-panel pet-list-panel hidden">
  <h2 class="text-2xl font-bold mb-4">All Pets</h2>
  <ul class="space-y-2 pl-0 list-none">
    <?php
    // Query all 'pet' posts
    $pets = get_posts( array(
      'post_type'      => 'pets',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
    ) );

    if ( $pets ) {
      foreach ( $pets as $pet ) {
        // Output each pet with Edit Profile button
        printf(
          '<li class="flex justify-between items-center py-2 px-3 bg-white rounded w-full" style="display: flex; justify-content: space-between; align-items: center; width: 100%%;"><span class="font-medium text-gray-800">%s</span><button type="button" class="edit-profile-btn px-3 py-1 bg-blue-500 text-white rounded" data-pet-id="%d" data-panel="edit_profile">Edit Profile</button></li>',
          esc_html( get_the_title( $pet ) ),
          esc_attr( $pet->ID )
        );
      }
    } else {
      echo '<li class="text-gray-600 w-full">No pets found.</li>';
    }
    ?>
  </ul>
</section>
