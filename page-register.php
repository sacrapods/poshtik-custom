<?php
$registration_errors = array();
// Handle front‑end registration
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['user_name'] ) ) {
    // Sanitize inputs
    $name  = sanitize_text_field( $_POST['user_name'] );
    $email = sanitize_email( $_POST['user_email'] );
    $pass  = $_POST['user_pass'];

    // Simple validation
    if ( username_exists( $name ) || email_exists( $email ) ) {
        $registration_errors[] = 'Username or email already exists.';
    }
    if ( strlen( $pass ) < 8 ) {
        $registration_errors[] = 'Password must be at least 8 characters.';
    }

    // Only proceed if no errors
    if ( empty( $registration_errors ) ) {
        // Create user
        $user_id = wp_insert_user( array(
            'user_login'   => $name,
            'user_email'   => $email,
            'user_pass'    => $pass,
            'role'         => 'pet_parent',
            'display_name' => $name,
        ) );

        if ( is_wp_error( $user_id ) ) {
            $registration_errors[] = $user_id->get_error_message();
        } else {
            // Mark as unverified and generate token
            update_user_meta( $user_id, 'is_verified', 0 );
            $token = wp_generate_password( 20, false );
            update_user_meta( $user_id, 'email_verify_token', $token );

            // Send verification email
            $verify_link = add_query_arg( 'token', $token, home_url( 'verify-email' ) );
            $message = "Hi $name,\n\nThanks for registering! Please verify your email by clicking the link below:\n\n$verify_link\n\nIf you did not register, just ignore this email.";
            wp_mail( $email, 'Please verify your email', $message );

            // Redirect back with a flag
            wp_redirect( home_url( 'register?checkemail=1' ) );
            // exit; // Remove exit so form redisplays with errors if any
        }
    }
}

/*
Template Name: Register
*/

if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

get_header();
?>
<?php
if ( ! empty( $registration_errors ) ) {
    echo '<div class="error-messages">';
    foreach ( $registration_errors as $error ) {
        echo '<p class="error">' . esc_html( $error ) . '</p>';
    }
    echo '</div>';
}
?>
<div id="register-overlay" class="login-overlay">
  <div class="login-card">
    <div class="logo">
      <span class="icon">✦</span> Sign Up
    </div>
    <div class="social-buttons">
      <a href="<?php echo esc_url('#'); ?>" class="social-btn google">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google logo" />
        Sign up with Google
      </a>
      <a href="<?php echo esc_url('#'); ?>" class="social-btn facebook">
        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c2/F_icon.svg" alt="Facebook logo" />
        Sign up with Facebook
      </a>
    </div>
    <button class="view-more" style="display:none;">View more</button>
    <div class="divider"><span>Or</span></div>
    <form method="post" action="<?php echo esc_url( home_url('register') ); ?>" class="email-form">
      <input type="hidden" name="poshtik_register" value="1" />
      <label for="name-input" class="email-label">Your Name</label>
      <input id="name-input" type="text" name="user_name" placeholder="Your Name" required class="email-input" value="<?php echo esc_attr( $_POST['user_name'] ?? '' ); ?>" />
      <label for="email-input" class="email-label">Your E-mail</label>
      <input id="email-input" type="email" name="user_email" placeholder="Your E-mail" required class="email-input" value="<?php echo esc_attr( $_POST['user_email'] ?? '' ); ?>" />
      <label for="password-input" class="email-label">Password</label>
      <div class="password-field">
        <input id="password-input" type="password" name="user_pass" placeholder="At least 8 characters" required minlength="8" class="email-input" />
        <button type="button" class="toggle-password" aria-label="Show password">
          <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
        </button>
      </div>
      <div class="checkbox-wrap">
        <input id="terms-checkbox" type="checkbox" required />
        <label for="terms-checkbox">I agree to all the <a href="#">Term</a>, <a href="#">Privacy Policy</a> and <a href="#">Fees</a>.</label>
      </div>
      <button type="submit" class="continue-btn full">Continue</button>
    </form>
    <script>
    // Heroicons outline eye and eye-off SVGs (with slash for eye-off)
    const eyeOpenSVG = `<svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>`;
    const eyeClosedSVG = `<svg xmlns="http://www.w3.org/2000/svg" class="eye-off-icon" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.944-9.543-7a10.056 10.056 0 012.56-3.607M6.11 6.11A9.963 9.963 0 0112 5c4.478 0 8.269 2.944 9.543 7a9.963 9.963 0 01-4.722 5.316M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
    </svg>`;
    document.querySelectorAll('.toggle-password').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = btn.parentNode.querySelector('input');
        const isPassword = input.getAttribute('type') === 'password';
        input.setAttribute('type', isPassword ? 'text' : 'password');
        btn.innerHTML = isPassword ? eyeClosedSVG : eyeOpenSVG;
      });
    });
    </script>
    <p class="secondary">Have an account? <a href="<?php echo esc_url( home_url('login') ); ?>">Log in</a></p>
  </div>
</div>
<script>
(function() {
  var overlay = document.getElementById('register-overlay');
  if (!overlay) return;
  overlay.addEventListener('click', function(e) {
    if (e.target === overlay) {
      window.location.href = '<?php echo esc_url( home_url() ); ?>';
    }
  });
})();
</script>
<script>
(function() {
  var form = document.querySelector('form.email-form');
  if (!form) return;
  form.addEventListener('submit', function(e) {
    var pwd = document.getElementById('password-input');
    if (pwd.value.length < 8) {
      e.preventDefault();
      var errDiv = document.querySelector('.error-messages');
      if (!errDiv) {
        errDiv = document.createElement('div');
        errDiv.className = 'error-messages';
        form.parentNode.insertBefore(errDiv, form);
      }
      errDiv.innerHTML = '<p class="error">Password must be at least 8 characters.</p>';
      pwd.focus();
    }
  });
})();
</script>
<?php get_footer(); ?>