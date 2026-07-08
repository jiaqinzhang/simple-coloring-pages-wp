<?php
/**
 * Generic Page template — About, Contact, Privacy Policy, Terms of Use, etc.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	?>
	<main class="wrap" style="max-width:820px;padding-top:24px;padding-bottom:56px">
		<nav aria-label="Breadcrumb" style="display:flex;gap:8px;align-items:center;font-size:13.5px;font-weight:700;color:var(--text-mute);margin-bottom:16px">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration:none;color:var(--blue-dark)">Home</a>
			<span>&rsaquo;</span>
			<span style="color:var(--text-soft)"><?php the_title(); ?></span>
		</nav>

		<h1 style="font-size:clamp(28px,4vw,38px);line-height:1.2;margin:0 0 24px"><?php the_title(); ?></h1>

		<section class="section-card scp-prose">
			<?php the_content(); ?>
		</section>
	</main>
	<?php
endwhile;

get_footer();
