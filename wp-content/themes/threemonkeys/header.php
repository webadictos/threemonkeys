<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<?php wp_head(); ?>
</head>



<body <?php body_class(); ?>>

	<?php wp_body_open(); ?>

	<div id="wrapper">
		<?php
		do_action('wa_before_header');
		?>
		<?php get_template_part('template-parts/header/header'); ?>
		<?php
		do_action('wa_after_header');
		?>