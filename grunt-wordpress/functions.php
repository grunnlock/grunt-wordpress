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
		'main' => __('Primary Menu', 'grunt_wordpress_setup' )
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
	wp_enqueue_style( 'normalize', get_template_directory_uri() . '/res/css/normalize.css', array(), '3.0.2' );

	wp_enqueue_style( 'keyframes', get_template_directory_uri() . '/assets/css/keyframes.css', array(), '1.0.1' );
	wp_enqueue_style( 'typography', get_template_directory_uri() . '/assets/css/typography.css', array(), '1.0.1' );
	wp_enqueue_style( 'base', get_template_directory_uri() . '/assets/css/base.css', array(), '1.0.1' );
	wp_enqueue_style( 'elements', get_template_directory_uri() . '/assets/css/elements.css', array(), '1.0.1' );

	// JS

	// Deregister jQuery...
	wp_deregister_script('jquery');
	// ... To load it properly through CDN
	wp_enqueue_script('jquery', '//code.jquery.com/jquery-2.1.4.min.js', array(), '2.1.4', true);
	wp_enqueue_script( 'modernizr', '//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js', array(), '2.8.3', false );
	wp_enqueue_script( 'fastclick', get_template_directory_uri() . '/res/js/fastclick.min.js', array(), '1.0.6', true );

	wp_enqueue_script( 'functions', get_template_directory_uri() . '/assets/js/functions.js', array(), '1.0.1', true );
	wp_enqueue_script( 'main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.1', true );

	// Scripts for specific pages
	switch ( grunt_wordpress_page_name() ) {

		// Post types
		case 'page-sample':
			// CSS
			wp_enqueue_style( 'page-sample', get_template_directory_uri() . '/assets/css/page-sample.css', array(), '1.0.1' );

			// JS
			wp_enqueue_script( 'page-sample', get_template_directory_uri() . '/assets/js/page-sample.js', array(), '1.0.1', true );
		break;

		// Post types
		case 'post-blog':
			wp_enqueue_script( 'post-blog', get_template_directory_uri() . '/assets/js/post-blog.js', array(), '1.0.1', true );
		break;

	}

} add_action('wp_enqueue_scripts', 'grunt_wordpress_scripts');

?>
