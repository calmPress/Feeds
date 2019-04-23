<?php
/**
 * Main file for the plugin
 *
 * @package     atom-feeds
 *
 * Plugin Name: calm Atom Feeds
 * Plugin URI:  https://example.com/plugin-name
 * Description: Implementation of Atom feeds for calmPress.
 * Version:     1.0.0
 * Author:      calmPress
 * Author URI:  https://calmpress.org
 * Text Domain: calm_atom_feeds
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace calmpress\atomfeeds;

add_action( 'atom_head', __NAMESPACE__ . '\atom_site_icon' );
add_action( 'do_feed_atom', __NAMESPACE__ . '\do_feed_atom', 10, 1 );

/**
 * Displays Site Icon in atom feeds.
 *
 * @since 1.0.0
 */
function atom_site_icon() {
	$url = get_site_icon_url( 32 );
	if ( $url ) {
		echo '<icon>' . esc_url( $url ) . "</icon>\n";
	}
}

/**
 * Load the Atom posts feed.
 *
 * @since 1.0.0
 *
 * @param bool $for_comments Legacy from WordPress, being ignored.
 */
function do_feed_atom( $for_comments ) {
	load_template( __DIR__ . '/feed-atom.php' );
}

/*
 * Set the category name sanitization applied for the Atom feeds to "raw".
 */
add_filter( 'calm_feed_category_name_sanitization_type', function ( string $v, string $type ) {
	if ( 'atom' === $type ) {
		$v = 'raw';
	}
	return $v;
}, 10, 2 );

/*
 * Format the category name element in Atom feeds.
 */
add_filter( 'calm_feed_category_name_format', function ( string $v, string $name, string $type ) {
	if ( 'atom' === $type ) {
		$v = sprintf( '<category scheme="%1$s" term="%2$s" />', esc_attr( get_bloginfo_rss( 'url' ) ), esc_attr( $name ) );
	}
	return $v;
}, 10, 3 );

/*
 * Set the correct mime type for Atom Feeds.
 */
add_filter( 'feed_content_type', function ( string $content_type, string $type ) {
	if ( 'atom' === $type ) {
		$content_type = 'application/atom+xml';
	}
	return $content_type;
}, 10, 2 );

/*
 * Add "atom" as a feed type. It will indicate that an atom feed is being requested
 * when used in URLs instead (and in the same place) of "feed" and "rss2".
 */
add_filter( 'calm_feed_types', function ( array $feeds ) {
	$feeds[] = 'atom';
	return $feeds;
}, 10, 1 );

/**
 * Display the atom enclosure for the current post.
 *
 * Uses the global $post to check whether the post requires a password and if
 * the user has the password for the post. If not then it will return before
 * displaying.
 *
 * Also uses the function get_post_custom() to get the post's 'enclosure'
 * metadata field and parses the value to display the enclosure(s). The
 * enclosure(s) consist of link HTML tag(s) with a URI and other attributes.
 *
 * @since 1.0.0
 */
function atom_enclosure() {

	foreach ( (array) get_post_custom() as $key => $val ) {
		if ( 'enclosure' === $key ) {
			foreach ( (array) $val as $enc ) {
				$enclosure = explode( "\n", $enc );
				/**
				 * Filters the atom enclosure HTML link tag for the current post.
				 *
				 * @since 1.0.0
				 *
				 * @param string $html_link_tag The HTML link tag with a URI and other attributes.
				 */
				echo apply_filters( 'atom_enclosure', '<link href="' . esc_url( trim( $enclosure[0] ) ) . '" rel="enclosure" length="' . absint( trim( $enclosure[1] ) ) . '" type="' . esc_attr( trim( $enclosure[2] ) ) . '" />' . "\n" );
			}
		}
	}
}
