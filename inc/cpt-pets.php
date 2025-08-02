<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Register 'Pets' Custom Post Type and related taxonomies,
 * and add a rewrite rule so /pets/{ID} maps to each pet.
 */
function poshtik_register_cpt_pets() {
  // CPT labels
  $labels = array(
    'name'               => __( 'Pets', 'poshtik-custom' ),
    'singular_name'      => __( 'Pet', 'poshtik-custom' ),
    'menu_name'          => __( 'Pets', 'poshtik-custom' ),
    'name_admin_bar'     => __( 'Pet', 'poshtik-custom' ),
    'add_new'            => __( 'Add New Pet', 'poshtik-custom' ),
    'add_new_item'       => __( 'Add New Pet', 'poshtik-custom' ),
    'new_item'           => __( 'New Pet', 'poshtik-custom' ),
    'edit_item'          => __( 'Edit Pet', 'poshtik-custom' ),
    'view_item'          => __( 'View Pet', 'poshtik-custom' ),
    'all_items'          => __( 'All Pets', 'poshtik-custom' ),
    'search_items'       => __( 'Search Pets', 'poshtik-custom' ),
    'not_found'          => __( 'No pets found.', 'poshtik-custom' ),
    'not_found_in_trash' => __( 'No pets found in Trash.', 'poshtik-custom' ),
  );

  // CPT arguments
  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'show_in_rest'       => true,
    'has_archive'        => false,
    'rewrite'            => false, // We'll add custom numeric rewrite
    'supports'           => array( 'title', 'editor', 'thumbnail' ),
    'menu_icon'          => 'dashicons-pets',
  );
  register_post_type( 'pets', $args );

  // Register taxonomies for species, breed, allergies
  $taxonomies = array(
    'species'  => array( 'Singular' => 'Species',  'Plural' => 'Species' ),
    'breed'    => array( 'Singular' => 'Breed',    'Plural' => 'Breeds' ),
    'allergies'=> array( 'Singular' => 'Allergy',  'Plural' => 'Allergies' ),
  );
  foreach ( $taxonomies as $tax => $names ) {
    $tax_labels = array(
      'name'              => __( $names['Plural'], 'poshtik-custom' ),
      'singular_name'     => __( $names['Singular'], 'poshtik-custom' ),
      'search_items'      => sprintf( __( 'Search %s', 'poshtik-custom' ), $names['Plural'] ),
      'all_items'         => sprintf( __( 'All %s', 'poshtik-custom' ), $names['Plural'] ),
      'edit_item'         => sprintf( __( 'Edit %s', 'poshtik-custom' ), $names['Singular'] ),
      'update_item'       => sprintf( __( 'Update %s', 'poshtik-custom' ), $names['Singular'] ),
      'add_new_item'      => sprintf( __( 'Add New %s', 'poshtik-custom' ), $names['Singular'] ),
      'new_item_name'     => sprintf( __( 'New %s Name', 'poshtik-custom' ), $names['Singular'] ),
      'menu_name'         => __( $names['Plural'], 'poshtik-custom' ),
    );
    register_taxonomy( $tax, 'pets', array(
      'labels'            => $tax_labels,
      'public'            => true,
      'hierarchical'      => true,
      'show_in_rest'      => true,
      'rewrite'           => false,
    ) );
  }

  // Add rewrite rule: numeric ID URLs
  add_rewrite_rule(
    '^pets/([0-9]+)/?$',
    'index.php?post_type=pets&p=$matches[1]',
    'top'
  );
}
add_action( 'init', 'poshtik_register_cpt_pets' );
