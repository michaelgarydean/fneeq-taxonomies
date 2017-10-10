<?php
/**
 * Plugin Name:     Taxonomies de la FNEEQ
 * Plugin URI:      https://github.com/mykedean/fneeq-taxonomies
 * Description:     Taxonomies personnalisées utilisées pour l'espace membre de la FNEEQ.
 * Author:          Michael Dean
 * Author URI:      https://github.com/mykedean
 * Text Domain:     fneeq-taxonomies
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Fneeq_Taxonomies
 */

/**
 * Create taxonomy for organizing content by secteurs.
 *
 */

function fneeq_register_secteurs_taxonomy() {
	
  //Set paramaters to define the taxonomy
	$labels = array(
		'name'            => __( 'Secteurs', 'fneeq' ),
    'singular_name'   => __( 'Secteur', 'fneeq' ),
		'all_items'       => __( 'Tous les secteurs', 'fneeq' ),
	);

	$args = array(
		'labels'            => $labels,
		'public'            => false,
		'show_ui'           => true,
		'show_ui_in_menu'   => true,
		'show_in_nav_menus' => true,
	  'hierarchical'      => true,
  );	

	//Register the new taxonomy and assign it to the actualite (post) and publication (publications) post types
	register_taxonomy(
		'secteur',
		array( 'publications', 'post' ),
		$args
	);

}

//Register the secteur taxonomy when wordpress initializes
//Prioritize the action so it executes after the post post type is registered
add_action( 'init', 'fneeq_register_secteurs_taxonomy' );
