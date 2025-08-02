<?php
/**
 * Template Part: Pet Visits
 * Lists all Visit CPT items for the current pet.
 */

global $post;
$pet_id = $post->ID;
// Add Visit button
echo '<p class="mb-4">';
echo '<a href="' . esc_url( home_url( 'add-visit?pet_id=' . $pet_id ) ) . '" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add Visit</a>';
echo '</p>';

// Query visits linked to this pet
$visits = new WP_Query( array(
  'post_type'      => 'visit',
  'posts_per_page' => -1,
  'meta_key'       => 'pet_id',
  'meta_value'     => $pet_id,
  'orderby'        => 'date',
  'order'          => 'DESC',
) );

if ( $visits->have_posts() ) : ?>
  <table class="min-w-full bg-white">
    <thead>
      <tr>
        <th class="px-4 py-2">Date</th>
        <th class="px-4 py-2">Vet</th>
        <th class="px-4 py-2">Reason</th>
        <th class="px-4 py-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ( $visits->have_posts() ) : $visits->the_post(); 
        $vet_id = get_post_meta( get_the_ID(), 'vet_id', true );
        $vet = $vet_id ? get_userdata( $vet_id ) : null;
      ?>
      <tr class="border-t">
        <td class="px-4 py-2"><?php echo esc_html( get_the_date() ); ?></td>
        <td class="px-4 py-2"><?php echo $vet ? esc_html( $vet->display_name ) : '—'; ?></td>
        <td class="px-4 py-2"><?php echo esc_html( get_the_title() ); ?></td>
        <td class="px-4 py-2">
          <a href="<?php echo esc_url( get_permalink() ); ?>" class="text-blue-600">View</a>
        </td>
      </tr>
      <?php endwhile; wp_reset_postdata(); ?>
    </tbody>
  </table>
<?php else : ?>
  <p class="p-4 text-gray-600">No visits recorded for this pet.</p>
<?php endif; ?>
