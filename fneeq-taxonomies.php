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

//Register the secteur taxonomy when wordpress initializes
add_action( 'init', 'fneeq_register_secteurs_taxonomy' );

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

/**
 * Add the category taxonomy to attachments.
 */

//Register action when Wordpress initializes
add_action( 'init' , 'fneeq_add_categories_to_attachments' );

function fneeq_add_categories_to_attachments() {

    register_taxonomy_for_object_type( 'category', 'attachment' );

}

/**
 * Add categories to the sub-menu of topics.
 *
 * @TODO only do this if the topic post type exists.
 */

function fneeq_add_categories_to_topics_subbmenu() {
	
}

/**
 * Assign a taxonomy term to a topic after it has been selected in the front end (using a <select> element).
 *
 * @param Int 		$topic_id		The id for the current topic being created.
 * @param Int		$forum_id		The id for the current forum the topic is being created in.
 * @param Int		$anonymous_data		??
 * @param Int		$topic_author		The id of the user who created the topic.
 *
 * @see https://codex.wordpress.org/Function_Reference/wp_dropdown_categories 
 */

//Update the taxonomy terms when a new topic is created or edited.
add_action( 'bbp_new_topic', 'fneeq_assign_category_to_post', 20, 4 );

function fneeq_assign_category_to_post( $topic_id, $forum_id, $anonymous_data, $topic_author ) {
	
	//Get the category submitted by the user
	$taxonomy_name = 'category';			//the slug, or machine name, or the taxonomy
	$term_id = intval( $_POST['cat'] );		//Get value from the <select> element as an interger

	//@TODO error checking if term or taxonomy does not exists (returns false if none exists)
	$term_object = get_term_by( 'id', $term_id, $taxonomy_name );

	//Replace the existing subject with the selected one
	wp_set_post_terms( $topic_id, array( $term_id ), $taxonomy_name, false );
}
