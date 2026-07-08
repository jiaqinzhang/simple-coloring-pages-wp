<?php
/**
 * "All Coloring Pages" archive — same visual language as the category page,
 * just without a taxonomy filter.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$paged    = max( 1, get_query_var( 'paged' ) );
$topics   = new WP_Query( array( 'post_type' => 'coloring_topic', 'posts_per_page' => 36, 'paged' => $paged ) );
$total    = wp_count_posts( 'coloring_topic' )->publish;
?>

<section style="background:linear-gradient(180deg,var(--blue-bg2) 0%,var(--bg) 100%)">
	<div class="wrap" style="padding-top:24px;padding-bottom:30px">
		<nav aria-label="Breadcrumb" style="display:flex;gap:8px;align-items:center;font-size:13.5px;font-weight:700;color:var(--text-mute);margin-bottom:16px">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration:none;color:var(--blue-dark)">Home</a>
			<span>&rsaquo;</span>
			<span style="color:var(--text-soft)">All Coloring Pages</span>
		</nav>
		<h1 style="font-size:clamp(30px,4vw,42px);margin:0 0 10px">All Coloring Pages</h1>
		<p style="font-size:17px;line-height:1.65;color:var(--text-soft);max-width:720px;margin:0 0 22px"><?php echo esc_html( $total ); ?> free printable coloring page topics &mdash; all free to download and print.</p>
		<form class="search-box" style="min-width:min(320px,100%);max-width:520px" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="text" name="s" placeholder="Search coloring pages...">
			<button type="submit" class="btn btn-primary">Search</button>
		</form>
	</div>
</section>

<div class="wrap" style="padding-top:8px">
	<div class="grid-cards-dense">
		<?php
		$i = 0;
		while ( $topics->have_posts() ) : $topics->the_post();
			scp_render_topic_card( get_the_ID(), $i++, true );
		endwhile;
		wp_reset_postdata();
		?>
	</div>

	<?php if ( $topics->max_num_pages > 1 ) : ?>
		<div style="text-align:center;margin:28px 0 8px">
			<?php echo paginate_links( array( 'total' => $topics->max_num_pages, 'current' => $paged, 'type' => 'plain' ) ); ?>
		</div>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
