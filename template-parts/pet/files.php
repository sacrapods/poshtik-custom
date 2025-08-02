<?php
/**
 * Template Part: Pet Files
 * Displays uploaded attachments for the current pet.
 */

global $post;
$pet_id = $post->ID;

// Get attachments attached to this pet
$attachments = get_attached_media( '', $pet_id );

if ( $attachments ) : ?>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php foreach ( $attachments as $attachment ) :
      $url = wp_get_attachment_url( $attachment->ID );
      $thumb = wp_get_attachment_image( $attachment->ID, 'medium' );
    ?>
    <div class="border rounded overflow-hidden">
      <a href="#" class="pet-file-thumb" data-url="<?php echo esc_url( $url ); ?>">
        <?php echo $thumb; ?>
      </a>
      <div class="p-2 text-sm text-gray-700">
        <?php echo esc_html( get_the_date( '', $attachment ) ); ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php else : ?>
  <p class="p-4 text-gray-600">No files uploaded for this pet.</p>
<?php endif; ?>

<!-- File Viewer Modal -->
<div id="file-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 items-center justify-center z-50">
  <div class="modal-content bg-white p-4 rounded shadow-lg max-w-screen-lg max-h-screen overflow-auto relative">
    <span id="file-modal-close" class="modal-close absolute top-2 right-3 text-2xl cursor-pointer">&times;</span>
    <div id="file-modal-body"></div>
  </div>
</div>
