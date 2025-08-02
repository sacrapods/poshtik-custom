<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Handle custom registration form submission.
 */
function poshtik_handle_custom_registration() {
  if ( ! is_page( 'register' ) || $_SERVER['REQUEST_METHOD'] !== 'POST' || empty( $_POST['poshtik_register'] ) ) {
    return;
  }

  $name  = sanitize_text_field( $_POST['user_name'] );
  $email = sanitize_email( $_POST['user_email'] );
  $pass  = $_POST['user_pass'];

  if ( username_exists( $name ) || email_exists( $email ) ) {
    wp_die( 'Username or email already exists.' );
  }
  if ( empty( $pass ) || strlen( $pass ) < 8 ) {
    wp_die( 'Password must be at least 8 characters.' );
  }

  $user_id = wp_insert_user( array(
    'user_login'   => $name,
    'user_email'   => $email,
    'user_pass'    => $pass,
    'role'         => 'pet_parent',
    'display_name' => $name,
  ) );

  if ( is_wp_error( $user_id ) ) {
    wp_die( $user_id->get_error_message() );
  }

  update_user_meta( $user_id, 'is_verified', 0 );
  $token = wp_generate_password( 20, false );
  update_user_meta( $user_id, 'email_verify_token', $token );

  $verify_link = add_query_arg( array( 'token' => $token ), home_url( 'verify-email' ) );
  $subject     = 'Verify your email address';
  $message     = file_get_contents( get_template_directory() . '/inc/email-templates/verify-email.php' );
  $message     = str_replace( '%%VERIFY_LINK%%', esc_url( $verify_link ), $message );
  wp_mail( $email, $subject, $message );

  wp_redirect( home_url( 'register?checkemail=1' ) );
  exit;
}
add_action( 'template_redirect', 'poshtik_handle_custom_registration' );

/**
 * Handle email verification.
 */
function poshtik_handle_email_verification() {
  if ( ! is_page( 'verify-email' ) || empty( $_GET['token'] ) ) {
    return;
  }

  $token = sanitize_text_field( $_GET['token'] );
  $users = get_users( array(
    'meta_key'   => 'email_verify_token',
    'meta_value' => $token,
    'number'     => 1,
    'count_total'=> false,
  ) );

  if ( empty( $users ) ) {
    echo '<p>Invalid or expired token.</p>';
    exit;
  }

  $user = $users[0];
  update_user_meta( $user->ID, 'is_verified', 1 );
  delete_user_meta( $user->ID, 'email_verify_token' );

  echo '<p>Email verified! You can now <a href="' . esc_url( home_url( 'login' ) ) . '">log in</a>.</p>';
  exit;
}
add_action( 'template_redirect', 'poshtik_handle_email_verification' );

/**
 * Handle custom login form.
 */
function poshtik_handle_custom_login() {
  if ( ! is_page( 'login' ) || $_SERVER['REQUEST_METHOD'] !== 'POST' || empty( $_POST['poshtik_login'] ) ) {
    return;
  }

  $creds = array();
  $creds['user_login']    = sanitize_text_field( $_POST['log'] );
  $creds['user_password'] = sanitize_text_field( $_POST['pwd'] );
  $creds['remember']      = isset( $_POST['rememberme'] );

  $user = wp_signon( $creds, false );
  if ( is_wp_error( $user ) ) {
    wp_die( $user->get_error_message() );
  }

  if ( ! get_user_meta( $user->ID, 'is_verified', true ) ) {
    wp_logout();
    wp_die( 'Please verify your email before logging in.' );
  }

  wp_redirect( home_url() );
  exit;
}
add_action( 'template_redirect', 'poshtik_handle_custom_login' );
