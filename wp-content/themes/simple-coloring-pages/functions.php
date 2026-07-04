<?php
/**
 * Simple Coloring Pages theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SCP_THEME_VERSION', '1.0.0' );

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
		'has_archive'   => false,
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
		'rewrite'       => array( 'slug' => 'category' ),
	) );
}
add_action( 'init', 'scp_register_post_types' );

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
	$fields = array( 'scp_pages', 'scp_tint', 'scp_intro', 'scp_pdf_all_url', 'scp_pdf_all_size', 'scp_age_range' );
	foreach ( $fields as $field ) {
		register_post_meta( 'coloring_topic', $field, array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => $field === 'scp_pages' ? 'array' : 'string',
			'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
		) );
	}
}
add_action( 'init', 'scp_register_meta_fields' );

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

/** Site logo mark (the 3-bar icon) reused in header + footer. */
function scp_render_logo_bars( $size = 'header' ) {
	if ( $size === 'header' ) {
		echo '<div class="brand-bars"><div style="height:26px;background:#4E9FD9"></div><div style="height:32px;background:#FFB84D"></div><div style="height:22px;background:#F48FB1"></div></div>';
	} else {
		echo '<div class="brand-bars"><div style="width:7px;height:20px;border-radius:4px;background:#7FC0EA"></div><div style="width:7px;height:25px;border-radius:4px;background:#FFD08A"></div><div style="width:7px;height:17px;border-radius:4px;background:#F8B9D0"></div></div>';
	}
}

require get_template_directory() . '/inc/demo-content.php';
