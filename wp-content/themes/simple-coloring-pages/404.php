<?php
/**
 * 404 template — friendly recovery page instead of a bare "Nothing found."
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<main class="wrap" style="max-width:720px;padding-top:56px;padding-bottom:64px;text-align:center">
	<div style="display:inline-flex;align-items:center;gap:8px;background:#fff;border:1px solid var(--border);border-radius:999px;padding:7px 16px;font-size:13px;font-weight:800;color:var(--blue-dark);letter-spacing:.4px;margin-bottom:20px">
		<span style="width:8px;height:8px;border-radius:999px;background:var(--pink);display:inline-block"></span>
		404 &middot; PAGE NOT FOUND
	</div>
	<h1 style="font-size:clamp(28px,4vw,40px);margin:0 0 14px">Oops! This page wandered off.</h1>
	<p style="font-size:16.5px;line-height:1.7;color:var(--text-soft);max-width:520px;margin:0 auto 28px">We couldn't find the page you were looking for &mdash; it may have been moved, or the link might have a typo. Let's find you a coloring page instead.</p>

	<form class="search-box" style="max-width:480px;margin:0 auto 36px" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="text" name="s" placeholder="Search coloring pages...">
		<button type="submit" class="btn btn-amber">Search</button>
	</form>

	<div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-bottom:48px">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">Go to Homepage</a>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'coloring_topic' ) ); ?>" class="btn btn-outline">Browse All Coloring Pages</a>
	</div>

	<h2 style="font-size:22px;margin-bottom:16px">Popular Coloring Pages</h2>
	<div class="grid-cards" style="text-align:left">
		<?php
		$popular = get_posts( array( 'post_type' => 'coloring_topic', 'posts_per_page' => 4, 'orderby' => 'rand' ) );
		$i = 0;
		foreach ( $popular as $p ) {
			scp_render_topic_card( $p->ID, $i++ );
		}
		?>
	</div>
</main>

<?php get_footer(); ?>
