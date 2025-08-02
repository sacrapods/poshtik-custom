<?php
// Handle new pet comment submission
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['add_pet_comment'] ) ) {
    // Verify nonce
    if ( ! isset( $_POST['pet_comment_nonce'] ) || ! wp_verify_nonce( $_POST['pet_comment_nonce'], 'add_pet_comment' ) ) {
        wp_die( 'Security check failed.' );
    }
    // Must be logged in
    if ( ! is_user_logged_in() ) {
        wp_die( 'You must be logged in to add a note.' );
    }
    // Sanitize and prepare comment data
    $comment_content = sanitize_text_field( $_POST['pet_comment'] );
    $user            = wp_get_current_user();
    $commentdata = array(
        'comment_post_ID'      => get_the_ID(),
        'comment_content'      => $comment_content,
        'user_id'              => $user->ID,
        'comment_type'         => 'pet_note',
        'comment_approved'     => 1,
    );
    // Insert the comment
    wp_insert_comment( $commentdata );
    // Redirect to avoid reposting
    wp_redirect( get_permalink() . '#comments' );
    exit;
}
?>
<?php
/**
 * Template Part: Pet Comments
 * Shows notes/comments for the current pet.
 */

global $post;
$pet_id = $post->ID;

// Fetch comments of type 'pet_note'
$comments = get_comments( array(
  'post_id' => $pet_id,
  'type'    => 'pet_note',
  'order'   => 'ASC',
) );
?>

<!-- Add new comment form -->
<form method="post" class="mb-4">
  <?php wp_nonce_field( 'add_pet_comment', 'pet_comment_nonce' ); ?>
  <textarea name="pet_comment" rows="3" required
    class="w-full border rounded p-2"
    placeholder="Add a note..."></textarea>
  <button type="submit" name="add_pet_comment"
    class="mt-2 px-4 py-2 bg-blue-600 text-white rounded">
    Add Note
  </button>
</form>

<!-- Timeline -->
<div class="space-y-4">
  <?php if ( $comments ) : 
    foreach ( $comments as $c ) :
      $author = get_userdata( $c->user_id );
  ?>
    <div class="border-l-4 border-blue-500 pl-4">
      <p class="text-sm text-gray-600">
        <?php echo esc_html( get_comment_date( '', $c ) ); ?> by 
        <?php echo $author ? esc_html( $author->display_name ) : 'Unknown'; ?>
      </p>
      <p class="mt-1"><?php echo esc_html( $c->comment_content ); ?></p>
    </div>
  <?php endforeach; 
  else : ?>
    <p class="p-4 text-gray-600">No notes yet. Add one above.</p>
  <?php endif; ?>
</div>
