<?php
/**
 * Category archive template — e.g. "Animal Coloring Pages".
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$term      = get_queried_object();
$total     = $term->count;
$per_page  = 36;
$paged     = max( 1, get_query_var( 'paged' ) );

$topics = new WP_Query( array(
	'post_type'      => 'coloring_topic',
	'posts_per_page' => $per_page,
	'paged'          => $paged,
	'tax_query'      => array( array( 'taxonomy' => 'topic_category', 'field' => 'term_id', 'terms' => $term->term_id ) ),
) );

$scp_category_copy = include get_template_directory() . '/inc/category-copy.php';
$scp_about_text     = $scp_category_copy[ $term->slug ] ?? '';
?>

<section style="background:linear-gradient(180deg,var(--blue-bg2) 0%,var(--bg) 100%)">
	<div class="wrap" style="padding-top:24px;padding-bottom:30px">
		<nav aria-label="Breadcrumb" style="display:flex;gap:8px;align-items:center;font-size:13.5px;font-weight:700;color:var(--text-mute);margin-bottom:16px;flex-wrap:wrap">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration:none;color:var(--blue-dark)">Home</a>
			<span>&rsaquo;</span>
			<span style="color:var(--text-soft)"><?php echo esc_html( $term->name ); ?> Coloring Pages</span>
		</nav>
		<h1 style="font-size:clamp(30px,4vw,42px);margin:0 0 10px"><?php echo esc_html( $term->name ); ?> Coloring Pages</h1>
		<p style="font-size:17px;line-height:1.65;color:var(--text-soft);max-width:720px;margin:0 0 22px">
			<?php echo esc_html( $total ); ?> <?php echo esc_html( strtolower( $term->name ) ); ?> topics with free printable coloring pages &mdash; all free to download and print.
			<?php echo esc_html( $term->description ); ?>
		</p>
		<form class="search-box" style="min-width:min(320px,100%);max-width:520px" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="text" name="s" placeholder="Search <?php echo esc_attr( strtolower( $term->name ) ); ?> pages...">
			<button type="submit" class="btn btn-primary">Search</button>
		</form>
	</div>
</section>

<div class="wrap has-sidebar" style="padding-top:8px;display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:28px;align-items:start">
	<div>
		<div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px">
			<h2 style="font-size:22px">All <?php echo esc_html( $term->name ); ?> Topics</h2>
			<span style="font-size:13.5px;font-weight:800;color:var(--text-mute)">Showing <?php echo esc_html( $topics->post_count ); ?> of <?php echo esc_html( $total ); ?> topics</span>
		</div>

		<div class="grid-cards-dense">
			<?php
			$i = 0;
			while ( $topics->have_posts() ) : $topics->the_post();
				scp_render_topic_card( get_the_ID(), $i++, true );
			endwhile;
			?>
		</div>

		<?php if ( $topics->max_num_pages > 1 ) : ?>
			<div style="text-align:center;margin:28px 0 8px">
				<?php
				echo paginate_links( array(
					'total'   => $topics->max_num_pages,
					'current' => $paged,
					'type'    => 'plain',
				) );
				?>
			</div>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>

		<section class="section-card scp-prose" style="margin-top:36px">
			<h2 style="font-size:24px;margin-bottom:12px">About Our <?php echo esc_html( $term->name ); ?> Coloring Pages</h2>
			<?php if ( $scp_about_text ) : ?>
				<?php foreach ( explode( "\n\n", $scp_about_text ) as $para ) : ?>
					<p><?php echo esc_html( $para ); ?></p>
				<?php endforeach; ?>
			<?php else : ?>
				<p>Every page features original artwork with bold outlines that are easy for little hands to color. Pages range from simple toddler-friendly designs to more detailed scenes for older kids &mdash; perfect for home, preschool, and elementary classrooms.</p>
			<?php endif; ?>
		</section>

		<section style="margin-top:36px">
			<h2 style="font-size:22px;margin-bottom:16px">Explore More Categories</h2>
			<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:14px">
				<?php
				$related = get_terms( array( 'taxonomy' => 'topic_category', 'hide_empty' => false, 'exclude' => array( $term->term_id ), 'number' => 4 ) );
				$ri = 0;
				if ( ! is_wp_error( $related ) ) foreach ( $related as $rc ) :
					$tint = scp_tint_for( $ri++ );
					?>
					<a href="<?php echo esc_url( get_term_link( $rc ) ); ?>" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:12px;background:#fff;border:1px solid var(--border);border-radius:16px;padding:14px 16px">
						<div style="width:40px;height:40px;border-radius:12px;background:<?php echo esc_attr( $tint ); ?>;display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-weight:800;font-size:17px;flex:none"><?php echo esc_html( mb_substr( $rc->name, 0, 1 ) ); ?></div>
						<div>
							<div style="font-family:var(--font-display);font-weight:700;font-size:15px"><?php echo esc_html( $rc->name ); ?></div>
							<div style="font-size:12px;font-weight:700;color:var(--text-mute)"><?php echo esc_html( $rc->count ); ?> topics</div>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	</div>

	<aside class="sidebar" style="display:flex;flex-direction:column;gap:20px;position:sticky;top:86px">
		<div style="background:var(--amber-bg2);border-radius:20px;padding:22px">
			<div style="font-family:var(--font-display);font-weight:700;font-size:17px;margin-bottom:8px">Most Printed This Week</div>
			<div style="display:flex;flex-direction:column;gap:9px">
				<?php
				$most_printed = get_posts( array( 'post_type' => 'coloring_topic', 'posts_per_page' => 5, 'tax_query' => array( array( 'taxonomy' => 'topic_category', 'field' => 'term_id', 'terms' => $term->term_id ) ), 'orderby' => 'rand' ) );
				$n = 1;
				foreach ( $most_printed as $mp ) :
					?>
					<a href="<?php echo esc_url( get_permalink( $mp ) ); ?>" style="text-decoration:none;color:var(--text-soft);font-size:14px;font-weight:700"><?php echo esc_html( $n++ ); ?>. <?php echo esc_html( get_the_title( $mp ) ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</aside>
</div>

<?php get_footer(); ?>
