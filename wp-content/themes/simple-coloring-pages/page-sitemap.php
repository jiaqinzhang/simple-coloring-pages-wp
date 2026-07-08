<?php
/**
 * HTML Sitemap — human-readable navigation page linked from the footer
 * (the footer "Sitemap" link points here, not at the XML sitemap).
 * Lists every category and every topic hub so both visitors and crawlers
 * have a single page that reaches the whole site in a couple of clicks.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$categories = get_terms( array( 'taxonomy' => 'topic_category', 'hide_empty' => true ) );

$popular = get_posts( array(
	'post_type'      => 'coloring_topic',
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'orderby'        => 'rand',
) );
?>
<main class="wrap" style="max-width:1000px;padding-top:24px;padding-bottom:56px">
	<nav aria-label="Breadcrumb" style="display:flex;gap:8px;align-items:center;font-size:13.5px;font-weight:700;color:var(--text-mute);margin-bottom:16px">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration:none;color:var(--blue-dark)">Home</a>
		<span>&rsaquo;</span>
		<span style="color:var(--text-soft)">Sitemap</span>
	</nav>

	<h1 style="font-size:clamp(28px,4vw,38px);line-height:1.2;margin:0 0 12px">Sitemap</h1>
	<p style="color:var(--text-mute);font-size:15px;margin:0 0 32px">A complete map of every page on <?php bloginfo( 'name' ); ?> — coloring page categories, topics, and site pages.</p>

	<section class="section-card scp-prose" style="margin-bottom:24px">
		<h2>Main Pages</h2>
		<ul style="columns:2;gap:24px">
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><a href="<?php echo esc_url( home_url( '/coloring-pages/' ) ); ?>">All Coloring Pages</a></li>
			<li><a href="<?php echo esc_url( home_url( '/about' ) ); ?>">About</a></li>
			<li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">Contact</a></li>
			<li><a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>">Privacy Policy</a></li>
			<li><a href="<?php echo esc_url( home_url( '/terms' ) ); ?>">Terms of Use</a></li>
		</ul>
	</section>

	<?php if ( $popular ) : ?>
	<section class="section-card scp-prose" style="margin-bottom:24px">
		<h2>Popular Topics</h2>
		<ul style="columns:3;gap:24px">
			<?php foreach ( $popular as $t ) : ?>
				<li><a href="<?php echo esc_url( get_permalink( $t ) ); ?>"><?php echo esc_html( get_the_title( $t ) ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>

	<?php if ( $categories && ! is_wp_error( $categories ) ) : foreach ( $categories as $cat ) :
		$topics = get_posts( array(
			'post_type'      => 'coloring_topic',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'tax_query'      => array( array(
				'taxonomy' => 'topic_category',
				'field'    => 'term_id',
				'terms'    => $cat->term_id,
			) ),
		) );
		if ( ! $topics ) continue;
	?>
	<section class="section-card scp-prose" style="margin-bottom:24px">
		<h2><a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?> Coloring Pages</a></h2>
		<ul style="columns:3;gap:24px">
			<?php foreach ( $topics as $t ) : ?>
				<li><a href="<?php echo esc_url( get_permalink( $t ) ); ?>"><?php echo esc_html( get_the_title( $t ) ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endforeach; endif; ?>
</main>
<?php get_footer(); ?>
