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

	//Register the new taxonomy and assign it to the attachment, actualite (post) and publication (publications) post types
	register_taxonomy(
		'secteur',
		array( 'publications', 'post', 'attachment' ),
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

add_action( 'admin_menu', 'fneeq_add_categories_to_topics_subbmenu' );

function fneeq_add_categories_to_topics_subbmenu() {
	add_submenu_page('edit.php?post_type=topic', __( 'Categories', 'fneeq'  ), __( 'Categories', 'fneeq'  ), 'manage_categories', 'edit-tags.php?taxonomy=category&post_type=topic');
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

/**
 * On each forum (where the topics are displayed), show the category that each topic has been assigned.  
 *
 * @see wp-content/plugins/bbpress/templates/default/bbpress/loop-single-topic.php
 */

//Action executes in the loop-single-topic.php template
add_action( 'bbp_theme_after_topic_title', 'fneeq_show_category_terms_beside_topics_title' );

function fneeq_show_category_terms_beside_topics_title() {
	
	//The machine name of the category taxonomy
	$taxonomy_name = 'category';

	//Get the topic ID
	$topic_id = bbp_get_topic_id();
	
	//Retrieve the terms assigned to the topic (WP_Term objects)
	$terms = wp_get_post_terms( $topic_id, $taxonomy_name );
	
	//Loop indexes used for formatting the string of terms to be shown
	$number_of_terms = count( $terms );	
	$index = 0;
	
	//Initialize variables to create a string of all the taxonomy terms
	$terms_string = '';
	$term_delimiter = ', ';

	while( $index < $number_of_terms ) {
		
		//Don't prefix the term with a comma and a space if it is the first term
		if ($index > 0) {
			$terms_string = $terms_string . $term_delimiter;
		}

		//Concatenate the current term in the loop to the string of terms to output
		$terms_string = $terms_string . $terms[$index]->name;
		$index ++;
	}
	
	//Display the terms beside the title
	if( '' !== $terms_string ) {
		$html_output = sprintf( "<span class=topic-%u-%s-terms>(%s)</span>", $topic_id, $taxonomy_name, $terms_string );
		echo $html_output;
	}
}
