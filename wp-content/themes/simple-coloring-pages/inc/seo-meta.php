<?php
/**
 * Open Graph / Twitter Card meta tags + Schema.org JSON-LD structured data.
 * Hooked into wp_head so it applies automatically on every template.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/** Build a BreadcrumbList schema.org node from a flat [ [name, url], ... ] list. */
function scp_breadcrumb_schema( $items ) {
	$list = array();
	foreach ( $items as $i => $item ) {
		$node = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item[0],
		);
		if ( ! empty( $item[1] ) ) {
			$node['item'] = $item[1];
		}
		$list[] = $node;
	}
	return array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	);
}

function scp_print_ld_json( $data ) {
	echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>' . "\n";
}

/**
 * Only the first N sibling pages of each topic (by menu_order, the same
 * field used for the "More {Topic}" grid ordering) are the "most complete /
 * representative" pages kept in the search index; the rest stay reachable,
 * downloadable, and internally linked but are excluded from indexing to
 * avoid flooding search with near-duplicate templated pages.
 */
define( 'SCP_INDEXED_SIBLINGS_PER_TOPIC', 2 );

function scp_page_should_index( $post_id ) {
	$post = get_post( $post_id );
	return $post && (int) $post->menu_order < SCP_INDEXED_SIBLINGS_PER_TOPIC;
}

function scp_output_robots_meta() {
	if ( is_singular( 'coloring_page' ) ) {
		$post_id = get_the_ID();
		if ( ! scp_page_should_index( $post_id ) ) {
			echo '<meta name="robots" content="noindex, follow">' . "\n";
			return;
		}
	}
	echo '<meta name="robots" content="index, follow">' . "\n";
}
add_action( 'wp_head', 'scp_output_robots_meta', 1 );

/**
 * Keep the noindex'd majority of coloring_page siblings out of the native
 * WordPress XML sitemap (wp-sitemap.xml) -- only the indexed 1-3 per topic
 * (see scp_page_should_index()) should ever appear there.
 */
function scp_sitemap_index_only_where( $where ) {
	global $wpdb;
	remove_filter( 'posts_where', 'scp_sitemap_index_only_where' );
	return $where . $wpdb->prepare( " AND {$wpdb->posts}.menu_order < %d", SCP_INDEXED_SIBLINGS_PER_TOPIC );
}

function scp_sitemap_query_args( $args, $post_type ) {
	if ( 'coloring_page' === $post_type ) {
		add_filter( 'posts_where', 'scp_sitemap_index_only_where' );
	}
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'scp_sitemap_query_args', 10, 2 );

function scp_output_seo_meta() {
	$site_name = get_bloginfo( 'name' ) ?: 'Simple Coloring Pages';

	if ( is_singular( 'coloring_page' ) ) {
		$post_id  = get_the_ID();
		$title    = get_the_title( $post_id );
		$desc     = get_post_meta( $post_id, 'scp_meta_description', true );
		$alt      = get_post_meta( $post_id, 'scp_alt_text', true ) ?: $title;
		$image    = get_post_meta( $post_id, 'scp_png_url', true ) ?: get_post_meta( $post_id, 'scp_thumb_url', true );
		$url      = get_permalink( $post_id );
		$topic_id = (int) get_post_meta( $post_id, 'scp_topic_id', true );

		echo "\n<!-- Open Graph -->\n";
		printf( '<meta property="og:type" content="article">' . "\n" );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site_name ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		if ( $desc ) printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		if ( $image ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
			printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $alt ) );
		}

		echo "<!-- Twitter Card -->\n";
		printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
		printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
		if ( $desc ) printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
		if ( $image ) printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );

		echo "<!-- Schema.org -->\n";
		scp_print_ld_json( array(
			'@context'         => 'https://schema.org',
			'@type'            => 'ImageObject',
			'contentUrl'       => $image,
			'name'             => $title,
			'description'      => $alt,
			'url'              => $url,
			'license'          => home_url( '/terms/' ),
			'acquireLicensePage' => home_url( '/terms/' ),
			'creditText'       => $site_name,
			'creator'          => array( '@type' => 'Organization', 'name' => $site_name ),
			'copyrightNotice'  => $site_name,
		) );

		$crumbs = array( array( 'Home', home_url( '/' ) ) );
		if ( $topic_id ) {
			$terms = get_the_terms( $topic_id, 'topic_category' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$crumbs[] = array( $terms[0]->name, get_term_link( $terms[0] ) );
			}
			$crumbs[] = array( get_the_title( $topic_id ), get_permalink( $topic_id ) );
		}
		$crumbs[] = array( $title, $url );
		scp_print_ld_json( scp_breadcrumb_schema( $crumbs ) );

	} elseif ( is_singular( 'coloring_topic' ) ) {
		$post_id = get_the_ID();
		$title   = get_the_title( $post_id );
		$desc    = get_post_meta( $post_id, 'scp_intro', true );
		$image   = get_post_meta( $post_id, 'scp_thumb_url', true );
		$url     = get_permalink( $post_id );

		echo "\n<!-- Open Graph -->\n";
		printf( '<meta property="og:type" content="website">' . "\n" );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site_name ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		if ( $desc ) printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( wp_trim_words( $desc, 40 ) ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		if ( $image ) printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );

		echo "<!-- Twitter Card -->\n";
		printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
		printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
		if ( $image ) printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );

		echo "<!-- Schema.org -->\n";
		$terms = get_the_terms( $post_id, 'topic_category' );
		$crumbs = array( array( 'Home', home_url( '/' ) ) );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$crumbs[] = array( $terms[0]->name, get_term_link( $terms[0] ) );
		}
		$crumbs[] = array( $title, $url );
		scp_print_ld_json( scp_breadcrumb_schema( $crumbs ) );

	} elseif ( is_tax( 'topic_category' ) ) {
		$term = get_queried_object();
		echo "\n<!-- Schema.org -->\n";
		scp_print_ld_json( scp_breadcrumb_schema( array(
			array( 'Home', home_url( '/' ) ),
			array( $term->name . ' Coloring Pages', get_term_link( $term ) ),
		) ) );

	} elseif ( is_front_page() ) {
		echo "\n<!-- Schema.org -->\n";
		scp_print_ld_json( array(
			'@context' => 'https://schema.org',
			'@type'    => 'WebSite',
			'name'     => $site_name,
			'url'      => home_url( '/' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		) );
		scp_print_ld_json( array(
			'@context' => 'https://schema.org',
			'@type'    => 'Organization',
			'name'     => $site_name,
			'url'      => home_url( '/' ),
		) );
	}
}
add_action( 'wp_head', 'scp_output_seo_meta' );
