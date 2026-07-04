<?php
/**
 * Fallback template — used for any request that doesn't match a more
 * specific template (front-page.php, single-coloring_topic.php,
 * taxonomy-topic_category.php, archive-coloring_topic.php, search.php).
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<main class="wrap" style="padding-top:24px;padding-bottom:56px">
	<?php if ( have_posts() ) : ?>
		<div class="grid-cards">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php if ( get_post_type() === 'coloring_topic' ) : ?>
					<?php scp_render_topic_card( get_the_ID() ); ?>
				<?php else : ?>
					<a href="<?php the_permalink(); ?>" class="topic-card">
						<div class="topic-card-body">
							<div class="topic-card-title"><?php the_title(); ?></div>
						</div>
					</a>
				<?php endif; ?>
			<?php endwhile; ?>
		</div>
	<?php else : ?>
		<p>Nothing found.</p>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
