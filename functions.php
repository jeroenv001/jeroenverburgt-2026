<?php
/**
 * Jeroen Verburgt 2026 – theme functions.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'script', 'style' ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	$v = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'jv-fonts', 'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Poppins:wght@400;500;600;700&display=swap', array(), null );
	wp_enqueue_style( 'jv-style', get_stylesheet_uri(), array( 'jv-fonts' ), $v );
	wp_enqueue_script( 'jv-site', get_template_directory_uri() . '/assets/js/site.js', array(), $v, true );
} );

add_action( 'wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	echo "<script>(function(){var m=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;if(!m&&'IntersectionObserver' in window){document.documentElement.classList.add('js-rv');}})();</script>\n";
	$desc = jv_is_en()
		? 'Jeroen Verburgt, Content Marketer & Video. I help SMEs and agencies create content that works, and measure whether it does.'
		: 'Jeroen Verburgt, Content Marketeer & Video. Ik help kmo’s en bureaus aan content die werkt, en meet of dat ook zo is.';
	echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<link rel="alternate" hreflang="nl" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
	echo '<link rel="alternate" hreflang="en" href="' . esc_url( home_url( '/en/' ) ) . '">' . "\n";
	echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
}, 1 );

add_filter( 'document_title_parts', function ( $parts ) {
	if ( jv_is_en() ) {
		$parts = array( 'title' => 'Jeroen Verburgt', 'tagline' => 'Content Marketer & Video' );
	} elseif ( is_front_page() ) {
		$parts['title'] = 'Jeroen Verburgt';
		$parts['tagline'] = 'Content Marketeer & Video';
	}
	return $parts;
} );

/**
 * True when the request is for the English version (/en/).
 */
function jv_is_en() {
	static $is_en = null;
	if ( null !== $is_en ) { return $is_en; }
	$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
	$base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$rel  = '/' . ltrim( substr( $path, strlen( rtrim( $base, '/' ) ) ), '/' );
	$is_en = (bool) preg_match( '#^/en(/|$)#', $rel );
	return $is_en;
}

/* Serve /en/ with status 200, even when no WordPress page "en" exists. */
add_action( 'template_redirect', function () {
	global $wp_query;
	if ( jv_is_en() && is_404() ) {
		$wp_query->is_404 = false;
		status_header( 200 );
	}
} );

add_filter( 'language_attributes', function ( $output ) {
	return jv_is_en() ? 'lang="en-GB"' : 'lang="nl-BE"';
} );

/**
 * Image URL: uses the copy in the media library (uploads/2026/11) when it
 * exists, otherwise falls back to the copy shipped with the theme.
 */
function jv_img( $file ) {
	$file = sanitize_file_name( $file );
	$up   = wp_upload_dir( '2026/11', false );
	if ( empty( $up['error'] ) && file_exists( trailingslashit( $up['path'] ) . $file ) ) {
		return trailingslashit( $up['url'] ) . $file;
	}
	return get_template_directory_uri() . '/assets/img/' . $file;
}
