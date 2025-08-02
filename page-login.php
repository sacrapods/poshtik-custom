<?php
/*
Template Name: Login
*/

// Handle front‑end login
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['log'] ) && ! empty( $_POST['pwd'] ) ) {
    $creds = array(
        'user_login'    => sanitize_text_field( $_POST['log'] ),
        'user_password' => sanitize_text_field( $_POST['pwd'] ),
        'remember'      => true,
    );
    $user = wp_signon( $creds, false );
    if ( is_wp_error( $user ) ) {
        wp_die( $user->get_error_message() );
    }
    // Check email verification
    if ( ! get_user_meta( $user->ID, 'is_verified', true ) ) {
        wp_logout();
        wp_die( 'Please verify your email before logging in.' );
    }
    wp_redirect( home_url() );
    exit;
}

if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

get_header();
?>
<div id="login-overlay" class="login-overlay">
  <div class="login-card">
    <div class="logo">Poshtik Pets</div>
    <h2 class="headline">Sign In</h2>
    <div class="social-buttons">
      <a href="<?php echo esc_url('#'); ?>" class="social-btn google">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google logo" />
        Continue with Google
      </a>
      <a href="<?php echo esc_url('#'); ?>" class="social-btn facebook">
        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c2/F_icon.svg" alt="Facebook logo" />
        Continue with Facebook
      </a>
    </div>
    <div class="divider"><span>Or</span></div>
    <form method="post" action="<?php echo esc_url( home_url('login') ); ?>" class="email-form">
      <input type="hidden" name="poshtik_login" value="1" />
      <label for="email-input" class="email-label">Continue with email</label>
      <input
        id="email-input"
        type="email"
        name="log"
        placeholder="Email address"
        required
        class="email-input"
      />
      <label for="password-input" class="email-label">Password</label>
      <input
        id="password-input"
        type="password"
        name="pwd"
        placeholder="Password"
        required
        class="email-input"
      />
      <button type="submit" class="continue-btn">Continue</button>
    </form>
  </div>
</div>
<script>
(function() {
  var overlay = document.getElementById('login-overlay');
  if (!overlay) return;
  overlay.addEventListener('click', function(e) {
    // if click is directly on the overlay (not inside the card), close it
    if (e.target === overlay) {
      window.location.href = '<?php echo esc_url( home_url() ); ?>';
    }
  });
})();
</script>
<?php get_footer(); ?>