<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="page">
 *
 * @package Cyprus
 */

?><!doctype html>
<html<?php cyprus_attr( 'html' ); ?>>
<head<?php cyprus_attr( 'head' ); ?>>
	<?php wp_head(); ?>
</head>

<body<?php cyprus_attr( 'body' ); ?>>

	<?php get_template_part( 'template-parts/header/clickable', 'background' ); ?>

	<div<?php cyprus_attr( 'main' ); ?>>

		<?php cyprus_action( 'before_wrapper' ); ?>
