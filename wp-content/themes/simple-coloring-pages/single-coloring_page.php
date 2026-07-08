<?php
/**
 * Single image landing page — e.g. "Sleepy Cat Coloring Page".
 * One URL per printable page, so each can be indexed and ranked on its own.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	$post_id      = get_the_ID();
	$topic_id     = (int) get_post_meta( $post_id, 'scp_topic_id', true );
	$png_url      = get_post_meta( $post_id, 'scp_png_url', true );
	$pdf_url      = get_post_meta( $post_id, 'scp_pdf_url', true );
	$alt_text     = get_post_meta( $post_id, 'scp_alt_text', true ) ?: get_the_title();
	$intro        = get_post_meta( $post_id, 'scp_intro', true );
	$vocabulary   = get_post_meta( $post_id, 'scp_vocabulary', true );
	$vocabulary   = is_array( $vocabulary ) ? $vocabulary : array();
	$fun_fact     = get_post_meta( $post_id, 'scp_fun_fact', true );
	$meta_desc    = get_post_meta( $post_id, 'scp_meta_description', true );
	$show_ads     = true;

	$topic_title  = $topic_id ? get_the_title( $topic_id ) : '';
	$topic_link   = $topic_id ? get_permalink( $topic_id ) : home_url( '/' );
	$age_range    = $topic_id ? ( get_post_meta( $topic_id, 'scp_age_range', true ) ?: '2-10' ) : '2-10';
	$pdf_all_url  = $topic_id ? get_post_meta( $topic_id, 'scp_pdf_all_url', true ) : '';

	$terms = $topic_id ? get_the_terms( $topic_id, 'topic_category' ) : null;
	$primary_cat = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;

	$siblings = $topic_id ? scp_get_page_siblings( $topic_id ) : array();
	$sibling_ids = wp_list_pluck( $siblings, 'ID' );
	$current_index = array_search( $post_id, $sibling_ids, true );
	$prev_id = ( $current_index !== false && $current_index > 0 ) ? $sibling_ids[ $current_index - 1 ] : null;
	$next_id = ( $current_index !== false && $current_index < count( $sibling_ids ) - 1 ) ? $sibling_ids[ $current_index + 1 ] : null;
	?>

	<?php if ( $meta_desc ) : ?>
		<meta name="description" content="<?php echo esc_attr( $meta_desc ); ?>">
	<?php endif; ?>

	<main class="wrap" style="max-width:1000px;padding-top:24px">
		<nav aria-label="Breadcrumb" style="display:flex;gap:8px;align-items:center;font-size:13.5px;font-weight:700;color:var(--text-mute);margin-bottom:16px;flex-wrap:wrap">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration:none;color:var(--blue-dark)">Home</a>
			<?php if ( $primary_cat ) : ?>
				<span>&rsaquo;</span>
				<a href="<?php echo esc_url( get_term_link( $primary_cat ) ); ?>" style="text-decoration:none;color:var(--blue-dark)"><?php echo esc_html( $primary_cat->name ); ?></a>
			<?php endif; ?>
			<?php if ( $topic_id ) : ?>
				<span>&rsaquo;</span>
				<a href="<?php echo esc_url( $topic_link ); ?>" style="text-decoration:none;color:var(--blue-dark)"><?php echo esc_html( $topic_title ); ?></a>
			<?php endif; ?>
			<span>&rsaquo;</span>
			<span style="color:var(--text-soft)"><?php the_title(); ?></span>
		</nav>

		<h1 style="font-size:clamp(26px,4vw,36px);line-height:1.15;margin:0 0 20px"><?php the_title(); ?></h1>

		<div style="display:flex;gap:24px;margin-bottom:28px;flex-wrap:wrap;align-items:flex-start">
			<!-- 大图区域 -->
			<div style="flex:1;min-width:280px">
				<div style="background:#F4F8FC;border-radius:20px;padding:28px;display:flex;justify-content:center;min-height:460px">
					<div id="scp-print-area" style="width:100%;max-width:400px;display:flex;align-items:center">
						<img src="<?php echo esc_url( $png_url ); ?>" alt="<?php echo esc_attr( $alt_text ); ?>" style="width:100%;height:auto;border-radius:8px;box-shadow:0 4px 20px rgba(61,66,102,.16)">
					</div>
				</div>

				<!-- Prev / Next -->
				<?php if ( $prev_id || $next_id ) : ?>
					<div style="display:flex;justify-content:space-between;gap:12px;margin-top:14px">
						<?php if ( $prev_id ) : ?>
							<a href="<?php echo esc_url( get_permalink( $prev_id ) ); ?>" class="btn btn-outline" style="font-size:14px">&larr; <?php echo esc_html( get_the_title( $prev_id ) ); ?></a>
						<?php else : ?><span></span><?php endif; ?>
						<?php if ( $next_id ) : ?>
							<a href="<?php echo esc_url( get_permalink( $next_id ) ); ?>" class="btn btn-outline" style="font-size:14px"><?php echo esc_html( get_the_title( $next_id ) ); ?> &rarr;</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- 下载信息区 -->
			<div style="flex:0 1 340px;display:flex;flex-direction:column;gap:16px">
				<div>
					<?php if ( $topic_title ) : ?>
						<div style="font-size:12px;font-weight:800;letter-spacing:.8px;color:var(--text-mute);text-transform:uppercase;margin-bottom:8px">Part of <?php echo esc_html( $topic_title ); ?></div>
					<?php endif; ?>
					<p style="font-size:14.5px;line-height:1.65;color:var(--text-soft);margin:0">High-resolution printable, sized for US Letter &amp; A4. Free for personal and classroom use.</p>
				</div>

				<div style="display:flex;flex-direction:column;gap:10px">
					<a href="<?php echo esc_url( $pdf_url ?: '#' ); ?>" class="btn btn-primary" style="text-align:center">Download PDF</a>
					<a href="<?php echo esc_url( $png_url ?: '#' ); ?>" class="btn btn-outline" style="text-align:center;cursor:pointer" download>Download PNG</a>
					<button onclick="window.print()" class="btn btn-outline">Print This Page</button>
				</div>

				<?php if ( $show_ads ) : ?>
					<div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--border)">
						<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-xxxxxxxxxxxxxxxx" data-ad-slot="1234567890" data-ad-format="auto" data-full-width-responsive="true"></ins>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- 正文介绍 -->
		<?php if ( $intro ) : ?>
			<section class="section-card" style="margin-bottom:24px">
				<p style="font-size:15.5px;line-height:1.75;color:var(--text-soft);margin:0"><?php echo esc_html( $intro ); ?></p>
			</section>
		<?php endif; ?>

		<!-- 词汇 + 趣味知识 -->
		<?php if ( $vocabulary || $fun_fact ) : ?>
			<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;margin-bottom:28px">
				<?php if ( $vocabulary ) : ?>
					<div style="background:var(--blue-bg2);border:1px solid var(--blue-border);border-radius:18px;padding:20px 22px">
						<div style="font-family:var(--font-display);font-weight:700;font-size:15px;color:var(--blue-dark);margin-bottom:10px">Learn 3 New Words</div>
						<div style="display:flex;gap:8px;flex-wrap:wrap">
							<?php foreach ( $vocabulary as $word ) : ?>
								<span style="background:#fff;border:1px solid var(--blue-border2);color:var(--blue-dark);font-weight:700;font-size:13.5px;padding:6px 14px;border-radius:999px"><?php echo esc_html( $word ); ?></span>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( $fun_fact ) : ?>
					<div style="background:var(--amber-bg);border-radius:18px;padding:20px 22px">
						<div style="font-family:var(--font-display);font-weight:700;font-size:15px;color:var(--amber-icon-text);margin-bottom:8px">Fun Fact</div>
						<p style="font-size:14.5px;line-height:1.65;color:var(--text-soft);margin:0"><?php echo esc_html( $fun_fact ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $show_ads ) : ?>
			<div style="margin-bottom:32px;text-align:center">
				<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-xxxxxxxxxxxxxxxx" data-ad-slot="0987654321" data-ad-format="horizontal" data-full-width-responsive="true"></ins>
			</div>
		<?php endif; ?>

		<!-- 同 Topic 下所有单图 -->
		<?php if ( count( $siblings ) > 1 ) : ?>
			<section style="margin-bottom:32px">
				<h3 style="font-size:16px;font-weight:700;margin:0 0 12px;color:var(--text-soft)">More <?php echo esc_html( $topic_title ); ?></h3>
				<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:12px">
					<?php foreach ( $siblings as $sib ) : ?>
						<?php scp_render_page_thumb( $sib->ID, $sib->ID === $post_id ); ?>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $topic_id ) : ?>
			<div style="background:var(--green-bg);border-radius:20px;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:32px">
				<div>
					<div style="font-family:var(--font-display);font-weight:700;font-size:18px;color:var(--green-text)">Want the whole set?</div>
					<div style="font-size:14px;font-weight:700;color:var(--green-text2)">See all <?php echo esc_html( count( $siblings ) ); ?> <?php echo esc_html( $topic_title ); ?> pages</div>
				</div>
				<a href="<?php echo esc_url( $topic_link ); ?>" class="btn btn-green">View <?php echo esc_html( $topic_title ); ?></a>
			</div>
		<?php endif; ?>

		<!-- Related topics -->
		<?php if ( $primary_cat ) : ?>
			<section style="margin-bottom:36px">
				<h2 style="font-size:24px;margin-bottom:16px">Related Coloring Pages</h2>
				<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px">
					<?php
					$related = get_posts( array(
						'post_type'      => 'coloring_topic',
						'posts_per_page' => 4,
						'post__not_in'   => array( $topic_id ),
						'tax_query'      => array( array( 'taxonomy' => 'topic_category', 'field' => 'term_id', 'terms' => $primary_cat->term_id ) ),
					) );
					$ri = 0;
					foreach ( $related as $r ) :
						$tint = scp_tint_for( $ri++ );
						?>
						<a href="<?php echo esc_url( get_permalink( $r ) ); ?>" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:12px;background:#fff;border:1px solid var(--border);border-radius:16px;padding:14px 16px">
							<div style="width:40px;height:40px;border-radius:12px;background:<?php echo esc_attr( $tint ); ?>;flex:none"></div>
							<div>
								<div style="font-family:var(--font-display);font-weight:700;font-size:15px"><?php echo esc_html( get_the_title( $r ) ); ?></div>
								<div style="font-size:12px;font-weight:700;color:var(--text-mute)"><?php echo esc_html( count( scp_get_pages( $r->ID ) ) ); ?> pages</div>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</main>

<?php endwhile; ?>
<?php get_footer(); ?>
