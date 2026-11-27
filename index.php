<?php
/**
 * One-page homepage (NL on /, EN on /en/).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> style="margin:0">
<?php wp_body_open(); ?>
<?php include get_template_directory() . ( jv_is_en() ? '/parts/content-en.php' : '/parts/content-nl.php' ); ?>
<?php wp_footer(); ?>
</body>
</html>
