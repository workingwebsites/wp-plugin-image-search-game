<?php
/**
 * @package Image Search Game
 * @version 1.0
 */
/*
Plugin Name: Image Search Game
Plugin URI: http://workingwebsites.ca/
Description: Customized plugin for website
Author: Working Websites (Lisa Armstrong)
Version: 1.0
Author URI: http://workingwebsites.ca
*/

//===== LOAD ADMIN STYLE SHEET =====//
function isg_admin_style() {
  wp_enqueue_style('admin-styles', plugins_url( 'stylesheet/Admin.css', __FILE__  ));
}
add_action('admin_enqueue_scripts', 'isg_admin_style');

//===== CREATE POST TYPE =====//
function isg_custom_post_type() {
// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Search Image', Find', 'Post Type General Name'),
		'singular_name'       => _x( 'Search Image', 'Post Type Singular Name'),
		'menu_name'           => __( 'Search Image'),
		'parent_item_colon'   => __( 'Parent Search Image'),
		'all_items'           => __( 'All Search Image' ),
		'view_item'           => __( 'View Search Image'),
		'add_new_item'        => __( 'Add New Search Image'),
		'add_new'             => __( 'Add New'),
		'edit_item'           => __( 'Edit Search Image'),
		'update_item'         => __( 'Update Search Image'),
		'search_items'        => __( 'Search Search Image'),
		'not_found'           => __( 'Not Found'),
		'not_found_in_trash'  => __( 'Not found in Trash'),
	);

// Set other options for Custom Post Type
	$args = array(
		'label'               => __( 'sea_find'),
		'description'         => __( 'Search for items in image.'),
		'labels'              => $labels,
		'supports'            => array( 'title',
                                    'editor',
                                    'thumbnail',
                                    ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 2,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'taxonomies'          => array( 'category' ),
	);

	// Registering your Custom Post Type
	register_post_type( 'seafind', $args );
}


//Enable seafind to be archived and categorized
function isg_add_custom_types_to_tax( $query ) {
	if( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
		// Get all your post types
		$post_types = array( 'post', 'seafind' );

		$query->set( 'post_type', $post_types );
		return $query;
	}
}
add_filter( 'pre_get_posts', 'isg_add_custom_types_to_tax' );


/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'isg_custom_post_type', 0 );


//===== ADD META BOX =====//

function isg_meta_box_fields(){
    // $post is already set, and contains an object: the WordPress post
    global $post;

	//Set boxes
	$Hint1 = get_post_meta ($post->ID, 'hint1_text', true);
	$Hint2 = get_post_meta ($post->ID, 'hint2_text', true);
	$Hint3 = get_post_meta ($post->ID, 'hint3_text', true);
	$Hint4 = get_post_meta ($post->ID, 'hint4_text', true);

    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );

	//Display boxes
	?>
    <p>
        <label for="hint1_text" style="">Hint 1</label>
        <textarea name="hint1_text" id="hint1_text"><?php echo esc_textarea($Hint1) ?></textarea>
    </p>

    <p>
        <label for="hint2_text">Hint 2</label>
        <textarea name="hint2_text" id="hint2_text"><?php echo esc_textarea($Hint2) ?></textarea>
    </p>

    <p>
        <label for="hint3_text">Hint 3</label>
        <textarea name="hint3_text" id="hint3_text"><?php echo esc_textarea($Hint3) ?></textarea>
    </p>

    <p>
        <label for="hint4_text">Hint 4</label>
        <textarea name="hint4_text" id="hint4_text"><?php echo esc_textarea($Hint4) ?></textarea>
    </p>

    <?php
}

function isg_meta_box_add(){
	add_meta_box( 'isg_meta_box', 'Search Image Hints', 'isg_meta_box_fields', 'seafind', 'normal', 'high' );
}

add_action( 'add_meta_boxes', 'isg_meta_box_add' );


//--- SAVE DATA ---//
function isg_meta_box_save( $post_id ){

	// OK TO SAVE DATA?
	// Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if(!isset( $_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'my_meta_box_nonce')){
		 return;
	}

    // if our current user can't edit this post, bail
    if(!current_user_can('edit_post')){
		return;
	}


	// SAVE THE DATA
    // Make sure your data is set before trying to save it
	if(isset($_POST['hint1_text'])){
        update_post_meta( $post_id, 'hint1_text', wp_kses( $_POST['hint1_text'], $allowed ) );
	}

	if(isset($_POST['hint2_text'])){
        update_post_meta( $post_id, 'hint2_text', wp_kses( $_POST['hint2_text'], $allowed ) );
	}

	if(isset($_POST['hint3_text'])){
        update_post_meta( $post_id, 'hint3_text', wp_kses( $_POST['hint3_text'], $allowed ) );
	}

	if(isset( $_POST['hint4_text'])){
        update_post_meta( $post_id, 'hint4_text', wp_kses( $_POST['hint4_text'], $allowed ) );
	}

}

add_action( 'save_post', 'isg_meta_box_save' );


//===== DISPLAY =====//
//Functions that will display the results
function isg_ObjPost(){
//Returns Page objecta based on URL
	$post_id = get_page_by_path($_SERVER['REQUEST_URI']);
	$post_object = get_post( $post_id );

	return 	$post_object;
}


/**
 * Enqueue a script with jQuery as a dependency.
 */
function isg_scripts_method() {
//Loads script
	if(get_post_type() == 'seafind'){
    	//For page
		wp_enqueue_style('seafind.css', plugins_url('stylesheet/SeaFind.css', __FILE__));
		wp_enqueue_script('isg_SeaFind.js', plugins_url('javascript/isg_SeaFind.js', __FILE__), array('jquery'));

		//For ImageViewer
		wp_enqueue_style('imageviewer.css', plugins_url('javascript/ImageViewer-master/imageviewer.css', __FILE__));
		wp_enqueue_script('imageviewer.js', plugins_url('javascript/ImageViewer-master/imageviewer.js', __FILE__), array('jquery'));
	}
}
add_action( 'wp_enqueue_scripts', 'isg_scripts_method' );
?>
