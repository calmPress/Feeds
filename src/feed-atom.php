<?php
/**
 * Atom Feed Template for displaying Atom Posts feed.
 *
 * @package WordPress
 */

declare(strict_types=1);

namespace calmpress\atomfeeds;

header( 'Content-Type: application/atom+xml; charset=' . get_option( 'blog_charset' ), true );
$more = 1;
global $wp;
$current_url = home_url( $wp->request );

echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';

/** This action is documented in WordPress wp-includes/feed-rss2.php */
do_action( 'rss_tag_pre', 'atom' );
?>
<feed
	xmlns="http://www.w3.org/2005/Atom"
	xmlns:thr="http://purl.org/syndication/thread/1.0"
	xml:lang="<?php bloginfo_rss( 'language' ); ?>"
	<?php
	/**
	 * Fires at end of the Atom feed root to add namespaces.
	 *
	 * @since WordPress 2.0.0
	 */
	do_action( 'atom_ns' );
	?>
>
	<title type="text"><?php wp_title_rss(); ?></title>
	<subtitle type="text"><?php bloginfo_rss( 'description' ); ?></subtitle>

	<updated>
	<?php
		$date = get_lastpostmodified( 'GMT' );
		echo $date ? mysql2date( 'Y-m-d\TH:i:s\Z', $date, false ) : date( 'Y-m-d\TH:i:s\Z' );
	?>
	</updated>

	<link rel="alternate" type="<?php bloginfo_rss( 'html_type' ); ?>" href="<?php echo esc_url( str_replace( [ 'atom', 'atom/' ], '', $current_url ) ); ?>" />
	<id><?php echo esc_url( $current_url ); ?></id>
	<link rel="self" type="application/atom+xml" href="<?php self_link(); ?>" />

	<?php
	/**
	 * Fires just before the first Atom feed entry.
	 *
	 * @since WordPress 2.0.0
	 */
	do_action( 'atom_head' );

	while ( have_posts() ) :
		the_post();
		?>
	<entry>
		<author>
			<name><?php the_author(); ?></name>
			<?php
			/**
			 * Fires at the end of each Atom feed author entry.
			 *
			 * @since WordPress 3.2.0
			 */
			do_action( 'atom_author' );
			?>
		</author>
		<title type="<?php html_type_rss(); ?>"><![CDATA[<?php the_title_rss(); ?>]]></title>
		<link rel="alternate" type="<?php bloginfo_rss( 'html_type' ); ?>" href="<?php the_permalink_rss(); ?>" />
		<id><?php the_guid(); ?></id>
		<updated><?php echo get_post_modified_time( 'Y-m-d\TH:i:s\Z', true ); ?></updated>
		<published><?php echo get_post_time( 'Y-m-d\TH:i:s\Z', true ); ?></published>
		<?php the_category_rss( 'atom' ); ?>
		<summary type="<?php html_type_rss(); ?>"><![CDATA[<?php the_excerpt_rss(); ?>]]></summary>
		<?php if ( ! get_option( 'rss_use_excerpt' ) ) : ?>
		<content type="<?php html_type_rss(); ?>" xml:base="<?php the_permalink_rss(); ?>"><![CDATA[<?php the_content_feed( 'atom' ); ?>]]></content>
		<?php endif; ?>
		<?php
		atom_enclosure();
		/**
		 * Fires at the end of each Atom feed item.
		 *
		 * @since WordPress 2.0.0
		 */
		do_action( 'atom_entry' );

		?>
	</entry>
	<?php endwhile; ?>
</feed>
