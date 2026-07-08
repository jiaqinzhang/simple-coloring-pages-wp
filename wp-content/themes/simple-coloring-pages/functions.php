<?php
/**
 * Simple Coloring Pages theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SCP_THEME_VERSION', '1.0.1' );

/* ------------------------------------------------------------------ */
/* Theme setup                                                         */
/* ------------------------------------------------------------------ */
function scp_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'simple-coloring-pages' ),
		'footer'  => __( 'Footer Menu', 'simple-coloring-pages' ),
	) );

	// Card art thumbnail (matches the 17:22 / Letter-page aspect ratio used across the design).
	add_image_size( 'scp-card', 400, 517, true );
}
add_action( 'after_setup_theme', 'scp_theme_setup' );

function scp_enqueue_assets() {
	wp_enqueue_style( 'scp-google-fonts', 'https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Nunito:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap', array(), null );
	wp_enqueue_style( 'scp-style', get_stylesheet_uri(), array(), SCP_THEME_VERSION );
	wp_enqueue_script( 'scp-main', get_template_directory_uri() . '/assets/js/main.js', array(), SCP_THEME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'scp_enqueue_assets' );

/* ------------------------------------------------------------------ */
/* Custom Post Type: coloring_topic (e.g. "Dinosaur Coloring Pages")   */
/* ------------------------------------------------------------------ */
function scp_register_post_types() {
	register_post_type( 'coloring_topic', array(
		'labels' => array(
			'name'          => __( 'Coloring Topics', 'simple-coloring-pages' ),
			'singular_name' => __( 'Coloring Topic', 'simple-coloring-pages' ),
			'add_new_item'  => __( 'Add New Coloring Topic', 'simple-coloring-pages' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-art',
		'rewrite'       => array( 'slug' => 'coloring-pages' ),
		'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
	) );

	register_taxonomy( 'topic_category', 'coloring_topic', array(
		'labels' => array(
			'name'          => __( 'Categories', 'simple-coloring-pages' ),
			'singular_name' => __( 'Category', 'simple-coloring-pages' ),
		),
		'hierarchical'  => true,
		'public'        => true,
		'show_in_rest'  => true,
		'rewrite'       => array( 'slug' => 'coloring-category' ),
	) );

	// Single-image landing page (one per printable page inside a coloring_topic).
	// URL is handled manually (see scp_coloring_page_rewrite_rules) so it can
	// nest under its parent topic's slug: /coloring-pages/{topic}/{page}/
	register_post_type( 'coloring_page', array(
		'labels' => array(
			'name'          => __( 'Coloring Pages (Single Images)', 'simple-coloring-pages' ),
			'singular_name' => __( 'Coloring Page', 'simple-coloring-pages' ),
			'add_new_item'  => __( 'Add New Coloring Page', 'simple-coloring-pages' ),
		),
		'public'        => true,
		'has_archive'   => false,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-format-image',
		'supports'      => array( 'title', 'thumbnail', 'custom-fields' ),
		'rewrite'       => false,
	) );
}
add_action( 'init', 'scp_register_post_types' );

/* ------------------------------------------------------------------ */
/* Routing for coloring_page: /coloring-pages/{topic-slug}/{page-slug}/ */
/* ------------------------------------------------------------------ */
function scp_coloring_page_rewrite_rules() {
	add_rewrite_rule(
		'^coloring-pages/([^/]+)/([^/]+)/?$',
		'index.php?coloring_page=$matches[2]&scp_topic_slug=$matches[1]',
		'top'
	);
}
add_action( 'init', 'scp_coloring_page_rewrite_rules' );

function scp_add_query_vars( $vars ) {
	$vars[] = 'scp_topic_slug';
	return $vars;
}
add_filter( 'query_vars', 'scp_add_query_vars' );

function scp_coloring_page_permalink( $url, $post ) {
	if ( $post->post_type === 'coloring_page' ) {
		$topic_id   = (int) get_post_meta( $post->ID, 'scp_topic_id', true );
		$topic_slug = $topic_id ? get_post_field( 'post_name', $topic_id ) : '';
		if ( $topic_slug ) {
			$url = home_url( '/coloring-pages/' . $topic_slug . '/' . $post->post_name . '/' );
		}
	}
	return $url;
}
add_filter( 'post_type_link', 'scp_coloring_page_permalink', 10, 2 );

/**
 * Meta fields on a coloring_topic post (set by the content-import pipeline, not hand-edited):
 *
 * scp_pages       (array)  Each item: [ title, alt, thumb_url, png_url, pdf_url ]
 * scp_tint        (string) Hex color used for the card thumbnail background wash.
 * scp_intro       (string) One paragraph intro under the H1.
 * scp_pdf_all_url (string) URL to the "download all pages" bundled PDF.
 * scp_pdf_all_size (string) Human readable file size, e.g. "6.2 MB".
 * scp_age_range   (string) e.g. "2-10"
 */
function scp_register_meta_fields() {
	$fields = array( 'scp_tint', 'scp_intro', 'scp_pdf_all_url', 'scp_pdf_all_size', 'scp_age_range' );
	foreach ( $fields as $field ) {
		register_post_meta( 'coloring_topic', $field, array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
		) );
	}

	register_post_meta( 'coloring_topic', 'scp_pages', array(
		'show_in_rest' => array(
			'schema' => array(
				'type'  => 'array',
				'items' => array( 'type' => 'object' ),
			),
		),
		'single'        => true,
		'type'          => 'array',
		'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
	) );
}
add_action( 'init', 'scp_register_meta_fields' );

/**
 * Meta fields on a coloring_page post (set by the content-import pipeline):
 *
 * scp_topic_id        (int)    Parent coloring_topic post ID.
 * scp_meta_description(string) <meta name="description">, unique per image.
 * scp_intro           (string) 200-500 word body text, unique per image.
 * scp_vocabulary      (array)  3 real topic-relevant vocabulary words.
 * scp_fun_fact        (string) One real fact about the topic.
 * scp_alt_text        (string) Full, non-truncated image alt text.
 * scp_png_url         (string) High-res PNG (used as the large on-page image).
 * scp_pdf_url         (string) Printable PDF for this single page.
 * scp_thumb_url       (string) Small thumbnail for grids/strips.
 *
 * Ordering between sibling pages within a topic uses the native menu_order field.
 */
function scp_register_coloring_page_meta_fields() {
	$string_fields = array( 'scp_meta_description', 'scp_intro', 'scp_fun_fact', 'scp_alt_text', 'scp_png_url', 'scp_pdf_url', 'scp_thumb_url' );
	foreach ( $string_fields as $field ) {
		register_post_meta( 'coloring_page', $field, array(
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'string',
			'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
		) );
	}
	register_post_meta( 'coloring_page', 'scp_topic_id', array(
		'show_in_rest'  => true,
		'single'        => true,
		'type'          => 'integer',
		'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
	) );
	register_post_meta( 'coloring_page', 'scp_vocabulary', array(
		'show_in_rest'  => array( 'schema' => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ) ),
		'single'        => true,
		'type'          => 'array',
		'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
	) );
}
add_action( 'init', 'scp_register_coloring_page_meta_fields' );

/* ------------------------------------------------------------------ */
/* Helpers used across templates                                       */
/* ------------------------------------------------------------------ */

/** Rotating tint palette for cards that don't have an explicit tint set. */
function scp_tint_for( $index ) {
	$tints = array( '#DCEEFB', '#FFF3D6', '#DFF3E8', '#FDE4EE', '#EDE6FA', '#FFE9DC' );
	return $tints[ $index % count( $tints ) ];
}

/** Get the repeatable "pages" meta as a plain array, always. */
function scp_get_pages( $post_id ) {
	$pages = get_post_meta( $post_id, 'scp_pages', true );
	return is_array( $pages ) ? $pages : array();
}

/** Render a topic card (used on homepage, category page, search results). */
function scp_render_topic_card( $post_id, $index = 0, $dense = false ) {
	$title = get_the_title( $post_id );
	$count = count( scp_get_pages( $post_id ) );
	$tint  = get_post_meta( $post_id, 'scp_tint', true ) ?: scp_tint_for( $index );
	$thumb = get_post_meta( $post_id, 'scp_thumb_url', true ) ?: get_the_post_thumbnail_url( $post_id, 'scp-card' );
	$class = 'topic-card' . ( $dense ? ' topic-card-dense' : '' );
	?>
	<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="<?php echo esc_attr( $class ); ?>">
		<div class="topic-card-thumb" style="background:<?php echo esc_attr( $tint ); ?>">
			<div class="topic-card-art">
				<?php if ( $thumb ) : ?>
					<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
				<?php endif; ?>
			</div>
		</div>
		<div class="topic-card-body">
			<div class="topic-card-title"><?php echo esc_html( $title ); ?></div>
			<div class="topic-card-meta"><?php echo esc_html( $count ); ?> pages<?php echo $dense ? '' : ' &middot; Free PDF'; ?></div>
			<?php if ( ! $dense ) : ?>
				<span class="topic-card-cta">View Pages</span>
			<?php endif; ?>
		</div>
	</a>
	<?php
}

/** All coloring_page children of a topic, ordered for prev/next + thumbnail strip. */
function scp_get_page_siblings( $topic_id ) {
	return get_posts( array(
		'post_type'      => 'coloring_page',
		'posts_per_page' => -1,
		'meta_key'       => 'scp_topic_id',
		'meta_value'     => $topic_id,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
}

/** Render one thumbnail in the "All Pages" strip — links to the image's own URL. */
function scp_render_page_thumb( $post_id, $is_active = false ) {
	$thumb = get_post_meta( $post_id, 'scp_thumb_url', true ) ?: get_post_meta( $post_id, 'scp_png_url', true );
	$title = get_the_title( $post_id );
	$border = $is_active ? '3px solid var(--blue)' : '2px solid var(--border)';
	?>
	<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"
	   class="scp-thumb-link"
	   style="display:block;background:#fff;border:<?php echo esc_attr( $border ); ?>;border-radius:12px;overflow:hidden;aspect-ratio:11/14">
		<?php if ( $thumb ) : ?>
			<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" style="width:100%;height:100%;object-fit:contain;background:#F4F8FC;padding:4px">
		<?php endif; ?>
	</a>
	<?php
}

/** Site logo mark (the 3-bar icon) reused in header + footer. */
function scp_render_logo_bars( $size = 'header' ) {
	if ( $size === 'header' ) {
		echo '<div class="brand-bars"><div style="height:26px;background:#4E9FD9"></div><div style="height:32px;background:#FFB84D"></div><div style="height:22px;background:#F48FB1"></div></div>';
	} else {
		echo '<div class="brand-bars"><div style="width:7px;height:20px;border-radius:4px;background:#7FC0EA"></div><div style="width:7px;height:25px;border-radius:4px;background:#FFD08A"></div><div style="width:7px;height:17px;border-radius:4px;background:#F8B9D0"></div></div>';
	}
}

require get_template_directory() . '/inc/demo-content.php';
require get_template_directory() . '/inc/seo-meta.php';
