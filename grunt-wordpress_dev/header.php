<?php
/**
 *
 * @package @@PROJECT_NAME
 */
?><!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />

	<title><?php wp_title( '|', true, 'right' ); ?></title>

	<?php wp_head(); ?>

	<script>
		var template_directory_uri = '<?php echo get_template_directory_uri(); ?>';
	</script>

</head>

<body <?php body_class( grunt_wordpress_page_name() ); ?>>

	<!--(if target !build)><!-->
	<script src="http://localhost:35729/livereload.js"></script>
	<!--<!(endif)-->

	<!-- Analytics -->
	<!--(if target build)>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-XXXXXXX-X', 'auto');
		ga('send', 'pageview');
	</script>
    <!(endif)-->

    <!--[if lt IE 9]>
    <p class="browse-happy">You are using an <strong>outdated</strong> browser.<br/>Please <a href="http://browsehappy.com/" target="_blank">upgrade your browser</a>.</p>

    <div class="lt-ie9">
    <![endif]-->


