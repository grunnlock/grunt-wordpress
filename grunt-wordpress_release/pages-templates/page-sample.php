<?php
/**
 * Sample page template
 *
 * Template Name: Sample
 *
 * @package grunt-wordpress
 */

get_header(); ?>

    <!-- Code -->
    <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
    <?php endwhile;?>

<?php get_footer(); ?>
