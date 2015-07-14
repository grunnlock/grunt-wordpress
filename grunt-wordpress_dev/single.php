<?php
/**
 * Page template to display posts
 *
 * @package @@PROJECT_NAME
 */

get_header(); ?>

<?php

	switch( get_post_type( $post ) ) {

		// Default posts
		default:
			get_template_part('posts-templates/post', 'blog');
		break;

	}

?>

<?php get_footer(); ?>
