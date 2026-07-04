<?php
/**
 * Single Topic template — e.g. "Dinosaur Coloring Pages".
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	$post_id      = get_the_ID();
	$pages        = scp_get_pages( $post_id );
	$page_count   = count( $pages );
	$intro        = get_post_meta( $post_id, 'scp_intro', true );
	$age_range    = get_post_meta( $post_id, 'scp_age_range', true ) ?: '2-10';
	$pdf_all_url  = get_post_meta( $post_id, 'scp_pdf_all_url', true );
	$pdf_all_size = get_post_meta( $post_id, 'scp_pdf_all_size', true );
	$show_ads     = true;

	$terms = get_the_terms( $post_id, 'topic_category' );
	$primary_cat = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
	?>

	<main class="wrap" style="max-width:1000px;padding-top:24px">
		<nav aria-label="Breadcrumb" style="display:flex;gap:8px;align-items:center;font-size:13.5px;font-weight:700;color:var(--text-mute);margin-bottom:16px;flex-wrap:wrap">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration:none;color:var(--blue-dark)">Home</a>
			<?php if ( $primary_cat ) : ?>
				<span>&rsaquo;</span>
				<a href="<?php echo esc_url( get_term_link( $primary_cat ) ); ?>" style="text-decoration:none;color:var(--blue-dark)"><?php echo esc_html( $primary_cat->name ); ?></a>
			<?php endif; ?>
			<span>&rsaquo;</span>
			<span style="color:var(--text-soft)"><?php the_title(); ?></span>
		</nav>

		<h1 style="font-size:clamp(28px,4vw,40px);line-height:1.15;margin:0 0 12px">Free Printable <?php the_title(); ?> for Kids</h1>
		<p style="font-size:16.5px;line-height:1.7;color:var(--text-soft);max-width:760px;margin:0 0 20px">
			<?php echo $intro ? esc_html( $intro ) : esc_html( $page_count . ' free printable coloring pages. Perfect for kids ages ' . $age_range . ', at home or in the classroom. Download any page as a PDF, or grab the whole set at once.' ); ?>
		</p>

		<?php if ( $pdf_all_url ) : ?>
			<div style="background:var(--green-bg);border-radius:20px;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px">
				<div>
					<div style="font-family:var(--font-display);font-weight:700;font-size:18px;color:var(--green-text)">Get the complete set</div>
					<div style="font-size:14px;font-weight:700;color:var(--green-text2)">All <?php echo esc_html( $page_count ); ?> pages in one printable PDF<?php echo $pdf_all_size ? ' &middot; ' . esc_html( $pdf_all_size ) : ''; ?></div>
				</div>
				<a href="<?php echo esc_url( $pdf_all_url ); ?>" class="btn btn-green">Download All Pages PDF</a>
			</div>
		<?php endif; ?>

		<?php if ( $show_ads ) : ?>
			<div class="ad-slot" style="height:96px;margin-bottom:28px">AD PLACEHOLDER &middot; 728 &times; 90 BANNER</div>
		<?php endif; ?>

		<?php if ( $page_count > 0 ) : ?>
			<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:18px">
				<?php foreach ( $pages as $i => $p ) :
					if ( $i === 12 && $show_ads ) : ?>
						<div class="ad-slot" style="height:96px;grid-column:1/-1">AD PLACEHOLDER &middot; IN-GRID RESPONSIVE</div>
					<?php endif; ?>
					<div style="background:#fff;border:1px solid var(--border);border-radius:18px;overflow:hidden;box-shadow:0 3px 10px rgba(61,66,102,.06)">
						<button
							data-open-preview
							data-title="<?php echo esc_attr( $p['title'] ?? '' ); ?>"
							data-png="<?php echo esc_url( $p['png_url'] ?? '' ); ?>"
							data-pdf="<?php echo esc_url( $p['pdf_url'] ?? '' ); ?>"
							aria-label="<?php echo esc_attr( $p['alt'] ?? ( $p['title'] ?? '' ) ); ?>"
							style="display:block;width:100%;border:none;cursor:zoom-in;background:#F4F8FC;padding:16px 0"
						>
							<div style="width:64%;aspect-ratio:17/22;background:#fff;border-radius:6px;box-shadow:0 2px 8px rgba(61,66,102,.14);margin:0 auto;overflow:hidden">
								<?php if ( ! empty( $p['thumb_url'] ) ) : ?>
									<img src="<?php echo esc_url( $p['thumb_url'] ); ?>" alt="<?php echo esc_attr( $p['alt'] ?? '' ); ?>" loading="lazy" style="width:100%;height:100%;object-fit:contain">
								<?php endif; ?>
							</div>
						</button>
						<div style="padding:12px 14px 15px">
							<div style="font-family:var(--font-display);font-weight:700;font-size:15px;line-height:1.3;margin-bottom:10px"><?php echo esc_html( $p['title'] ?? '' ); ?></div>
							<div style="display:flex;gap:8px;flex-wrap:wrap">
								<?php if ( ! empty( $p['pdf_url'] ) ) : ?>
									<a href="<?php echo esc_url( $p['pdf_url'] ); ?>" class="btn btn-pill-sm">Download PDF</a>
								<?php endif; ?>
								<a href="<?php echo esc_url( $p['png_url'] ?? '#' ); ?>" class="btn btn-pill-outline-sm">Print</a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="ad-slot" style="height:160px;border-style:solid">Pages for this topic haven't been imported yet.</div>
		<?php endif; ?>

		<section style="margin-top:48px">
			<h2 style="font-size:26px;margin-bottom:18px">How to Use These Coloring Pages</h2>
			<div class="grid-steps">
				<div class="step-card" style="background:var(--blue-bg2)"><div class="step-num" style="background:var(--blue)">1</div><div class="step-title">Download the PDF</div><div class="step-desc">Click Download on any page &mdash; it's instant and free, no sign-up.</div></div>
				<div class="step-card" style="background:var(--amber-bg)"><div class="step-num" style="background:var(--amber);color:var(--amber-text)">2</div><div class="step-title">Print at home or school</div><div class="step-desc">Sized for US Letter and A4. Print as many copies as you need.</div></div>
				<div class="step-card" style="background:var(--pink-bg)"><div class="step-num" style="background:var(--pink);color:var(--pink-text)">3</div><div class="step-title">Color away!</div><div class="step-desc">Crayons, markers, or colored pencils &mdash; every page looks great.</div></div>
			</div>
		</section>

		<section style="margin-top:44px">
			<h2 style="font-size:24px;margin-bottom:16px">Related Coloring Pages</h2>
			<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px">
				<?php
				$related = array();
				if ( $primary_cat ) {
					$related = get_posts( array(
						'post_type'      => 'coloring_topic',
						'posts_per_page' => 4,
						'post__not_in'   => array( $post_id ),
						'tax_query'      => array( array( 'taxonomy' => 'topic_category', 'field' => 'term_id', 'terms' => $primary_cat->term_id ) ),
					) );
				}
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

		<?php if ( $show_ads ) : ?>
			<div class="ad-slot" style="height:96px;margin:36px 0 0">AD PLACEHOLDER &middot; 728 &times; 90 BANNER</div>
		<?php endif; ?>

		<section class="section-card" style="margin-top:36px">
			<h2 style="font-size:24px;margin-bottom:16px">Frequently Asked Questions</h2>
			<div style="display:flex;flex-direction:column;gap:10px">
				<?php
				$faqs = array(
					array( 'q' => 'Are these coloring pages free?', 'a' => 'Yes &mdash; all pages are 100% free to download, print, and color. No account or email required.' ),
					array( 'q' => 'Can I print these coloring pages?', 'a' => 'Absolutely. Every page is a high-resolution PDF sized for standard US Letter and A4 paper.' ),
					array( 'q' => 'Are these pages suitable for preschoolers?', 'a' => 'Yes! Pages marked "easy" or "cute" have big, bold outlines perfect for ages 2-4, while detailed scenes suit older kids.' ),
					array( 'q' => 'Can teachers use them in the classroom?', 'a' => 'Of course &mdash; teachers are welcome to print unlimited copies for classroom activities and early finishers.' ),
				);
				foreach ( $faqs as $f ) : ?>
					<details class="faq-item">
						<summary><?php echo esc_html( $f['q'] ); ?></summary>
						<p><?php echo wp_kses_post( $f['a'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</section>
	</main>

	<!-- ============ PREVIEW MODAL ============ -->
	<div id="scp-preview-modal" data-close-preview style="display:none;position:fixed;inset:0;background:rgba(61,66,102,.55);z-index:100;align-items:center;justify-content:center;padding:20px">
		<div data-modal-card style="background:#fff;border-radius:24px;max-width:760px;width:100%;max-height:90vh;overflow:auto;padding:28px;position:relative;box-shadow:0 24px 60px rgba(61,66,102,.35)">
			<button data-close-preview aria-label="Close" style="position:absolute;top:16px;right:16px;width:38px;height:38px;border-radius:999px;border:none;background:#F2EFE7;color:var(--text-soft);font-size:18px;font-weight:800;cursor:pointer">&times;</button>
			<div style="display:flex;gap:28px;flex-wrap:wrap">
				<div style="flex:1;min-width:260px;background:#F4F8FC;border-radius:18px;padding:24px;display:flex;justify-content:center">
					<div style="width:82%;aspect-ratio:17/22;background:#fff;border-radius:8px;box-shadow:0 4px 16px rgba(61,66,102,.16);overflow:hidden">
						<img data-modal-img src="" alt="" style="width:100%;height:100%;object-fit:contain">
					</div>
				</div>
				<div style="flex:1;min-width:240px;display:flex;flex-direction:column;justify-content:center">
					<div style="font-size:12px;font-weight:800;letter-spacing:.8px;color:var(--text-mute);text-transform:uppercase;margin-bottom:6px"><?php the_title(); ?></div>
					<h2 data-modal-title style="font-size:26px;margin-bottom:8px"></h2>
					<p style="font-size:14.5px;line-height:1.65;color:var(--text-soft);margin:0 0 20px">High-resolution printable, sized for US Letter &amp; A4. Free for personal and classroom use.</p>
					<div style="display:flex;flex-direction:column;gap:10px">
						<a data-modal-download href="#" class="btn btn-primary">Download PDF</a>
						<div style="display:flex;gap:10px">
							<button onclick="window.print()" class="btn btn-outline" style="flex:1">Print</button>
							<a data-modal-download-png href="#" class="btn btn-outline" style="flex:1" download>Download PNG</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php endwhile; ?>
<?php get_footer(); ?>
