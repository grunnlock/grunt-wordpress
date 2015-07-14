<?php
/**
 *
 * @package grunt-wordpress
 */

// Get page name (template or post type)
function grunt_wordpress_page_name() {

	global $post;

	$pageTemplate = substr( basename( get_page_template() ), 0, -4 );

	if( !$pageTemplate ) {

		if( get_post_type( $post ) === 'post' ) {
			return 'post-blog';
		} else {
			return 'post-' . get_post_type( $post );
		}

	} else {

		return $pageTemplate;

	}


}

// Enable RSS feed and navigation menu
function grunt_wordpress_setup() {

	// Add navigaiton menus support
	register_nav_menus(array(
		'primary' => __('Primary Menu', 'grunt_wordpress_setup' )
	));

} add_action('after_setup_theme', 'grunt_wordpress_setup');

// Allow email address as username to log in
function grunt_wordpress_authenticate( $username ) {

    $user = get_user_by( 'email', $username );

    if ( !empty( $user->user_login ) ) {
        $username = $user->user_login;
    }

    return $username;

} add_action('wp_authenticate', 'grunt_wordpress_authenticate');

// Enqueue scripts
function grunt_wordpress_scripts() {

	// CSS
	wp_enqueue_style( 'main', get_template_directory_uri() . '/assets/css/main.css', array(), '0.0.2' );

	// JS

	// Deregister jQuery...
	wp_deregister_script('jquery');
	// ... To load it properly through CDN
	wp_enqueue_script('jquery', '//code.jquery.com/jquery-1.11.1.min.js', array(), '1.11.1', true);
	wp_enqueue_script( 'fastclick', get_template_directory_uri() . '/res/js/fastclick.min.js', array(), '1.0.6', true );

	wp_enqueue_script( 'functions', get_template_directory_uri() . '/assets/js/functions.js', array(), '0.0.2', true );
	wp_enqueue_script( 'main', get_template_directory_uri() . '/assets/js/main.js', array(), '0.0.2', true );


	// Scripts for specific pages
	switch ( grunt_wordpress_page_name() ) {

		// Post types
		case 'post-blog':
			wp_enqueue_script( 'post-blog', get_template_directory_uri() . '/assets/js/post-blog.js', array(), '0.0.2', true );
		break;

	}

} add_action('wp_enqueue_scripts', 'grunt_wordpress_scripts');

?>
