<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1617875813767295"
     crossorigin="anonymous"></script>
<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( get_template_directory_uri() . '/assets/favicon.svg' ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="site-header-inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand">
			<?php scp_render_logo_bars( 'header' ); ?>
			<div>
				<div class="brand-name"><?php bloginfo( 'name' ); ?></div>
				<div class="brand-sub">for Kids &middot; 100% Free</div>
			</div>
		</a>
		<nav class="main-nav" id="scp-main-nav">
			<?php
			$categories = get_terms( array( 'taxonomy' => 'topic_category', 'hide_empty' => false, 'number' => 5 ) );
			if ( ! is_wp_error( $categories ) ) {
				foreach ( $categories as $cat ) {
					$active = is_tax( 'topic_category', $cat->term_id ) ? ' is-active' : '';
					printf( '<a href="%s" class="nav-link%s">%s</a>', esc_url( get_term_link( $cat ) ), esc_attr( $active ), esc_html( $cat->name ) );
				}
			}
			?>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'coloring_topic' ) ); ?>" class="nav-cta">All Coloring Pages</a>
			<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="nav-search" aria-label="Search">
				<span class="nav-search-icon"></span>
			</a>
		</nav>
		<button class="mobile-nav-btn" id="scp-mobile-nav-btn" aria-label="Menu" aria-expanded="false" aria-controls="scp-main-nav"><span></span><span></span><span></span></button>
	</div>
</header>
<script>
(function() {
	var btn = document.getElementById('scp-mobile-nav-btn');
	var nav = document.getElementById('scp-main-nav');
	if (!btn || !nav) return;
	btn.addEventListener('click', function() {
		var open = nav.classList.toggle('is-open');
		btn.classList.toggle('is-open', open);
		btn.setAttribute('aria-expanded', open ? 'true' : 'false');
	});
})();
</script>
