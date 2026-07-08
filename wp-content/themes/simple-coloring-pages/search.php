<?php
/**
 * Search Results template.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$query_str = get_search_query();
$has_results = have_posts();
?>

<section style="background:linear-gradient(180deg,var(--blue-bg2) 0%,var(--bg) 100%)">
	<div style="max-width:800px;margin:0 auto;padding:40px 24px 34px;text-align:center">
		<h1 style="font-size:clamp(28px,4vw,38px);margin:0 0 18px">Search Coloring Pages</h1>
		<form class="search-box" style="max-width:560px;margin:0 auto" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="text" name="s" value="<?php echo esc_attr( $query_str ); ?>" placeholder="Search coloring pages...">
			<button type="submit" class="btn btn-amber">Search</button>
		</form>
		<div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:16px">
			<span style="font-size:13px;font-weight:800;color:var(--text-mute);align-self:center">Popular:</span>
			<?php foreach ( array( 'dinosaur', 'unicorn', 'christmas', 'alphabet' ) as $term ) : ?>
				<a href="<?php echo esc_url( home_url( '/?s=' . urlencode( $term ) ) ); ?>" style="text-decoration:none;background:#fff;color:var(--blue-dark);font-weight:800;font-size:13px;padding:6px 14px;border-radius:999px;border:1px solid var(--blue-border)"><?php echo esc_html( $term ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<main class="wrap" style="padding-top:8px">
	<?php if ( $has_results ) : ?>
		<div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px">
			<h2 style="font-size:22px"><?php printf( esc_html__( '%d results for "%s"', 'simple-coloring-pages' ), (int) $GLOBALS['wp_query']->found_posts, esc_html( $query_str ) ); ?></h2>
			<span style="font-size:13.5px;font-weight:800;color:var(--text-mute)">Sorted by relevance</span>
		</div>
		<div class="grid-cards">
			<?php
			$i = 0;
			while ( have_posts() ) : the_post();
				if ( get_post_type() === 'coloring_topic' ) {
					scp_render_topic_card( get_the_ID(), $i++ );
				}
			endwhile;
			?>
		</div>
	<?php else : ?>
		<div style="text-align:center;background:#fff;border:1px solid var(--border);border-radius:24px;padding:48px 24px;margin-bottom:8px">
			<div style="width:72px;height:72px;border-radius:999px;background:var(--amber-bg);margin:0 auto 18px;display:flex;align-items:center;justify-content:center">
				<span class="nav-search-icon" style="width:22px;height:22px;border-width:3.5px"></span>
			</div>
			<h2 style="font-size:24px;margin-bottom:8px">No coloring pages found for "<?php echo esc_html( $query_str ); ?>"</h2>
			<p style="font-size:15.5px;color:var(--text-soft);margin:0 0 22px">Try a different word, or start with one of our most popular topics below.</p>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'coloring_topic' ) ); ?>" class="btn btn-primary">Browse All Coloring Pages</a>
		</div>
		<h2 style="font-size:22px;margin:32px 0 18px">Popular Coloring Pages</h2>
		<div class="grid-cards">
			<?php
			$popular = get_posts( array( 'post_type' => 'coloring_topic', 'posts_per_page' => 8, 'orderby' => 'rand' ) );
			$i = 0;
			foreach ( $popular as $p ) {
				scp_render_topic_card( $p->ID, $i++ );
			}
			?>
		</div>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
