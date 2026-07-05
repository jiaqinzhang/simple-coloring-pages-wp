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

		<!-- ========== LARGE IMAGE VIEWER (方案A) ========== -->
		<?php if ( $page_count > 0 ) : ?>
			<div style="display:flex;gap:24px;margin-bottom:32px;flex-wrap:wrap;align-items:flex-start">
				<!-- 大图区域 -->
				<div style="flex:1;min-width:280px">
					<div style="background:#F4F8FC;border-radius:20px;padding:28px;display:flex;justify-content:center;aspect-ratio:auto;min-height:500px">
						<div style="width:100%;max-width:400px;display:flex;align-items:center">
							<img id="scp-main-image" src="<?php echo esc_url( $pages[0]['png_url'] ?? '' ); ?>" alt="<?php echo esc_attr( $pages[0]['alt'] ?? $pages[0]['title'] ?? '' ); ?>" style="width:100%;height:auto;border-radius:8px;box-shadow:0 4px 20px rgba(61,66,102,.16)">
						</div>
					</div>
				</div>

				<!-- 下载信息区 -->
				<div style="flex:0 1 340px;display:flex;flex-direction:column;gap:16px">
					<div>
						<div style="font-size:12px;font-weight:800;letter-spacing:.8px;color:var(--text-mute);text-transform:uppercase;margin-bottom:8px"><?php the_title(); ?></div>
						<h2 id="scp-page-title" style="font-size:24px;line-height:1.3;margin:0 0 12px"><?php echo esc_html( $pages[0]['title'] ?? '' ); ?></h2>
						<p style="font-size:14.5px;line-height:1.65;color:var(--text-soft);margin:0">High-resolution printable, sized for US Letter &amp; A4. Free for personal and classroom use.</p>
					</div>

					<!-- 主要下载按钮 -->
					<div style="display:flex;flex-direction:column;gap:10px">
						<a id="scp-download-pdf" href="<?php echo esc_url( $pages[0]['pdf_url'] ?? '#' ); ?>" class="btn btn-primary" style="text-align:center">Download PDF</a>
						<a id="scp-download-png" href="<?php echo esc_url( $pages[0]['png_url'] ?? '#' ); ?>" class="btn btn-outline" style="text-align:center;cursor:pointer" download>Download PNG</a>
						<button onclick="window.print()" class="btn btn-outline">Print This Page</button>
					</div>

					<!-- Google AdSense 广告位 (300x250 或 336x280) -->
					<?php if ( $show_ads ) : ?>
						<div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--border)">
							<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-xxxxxxxxxxxxxxxx" data-ad-slot="1234567890" data-ad-format="auto" data-full-width-responsive="true"></ins>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- 缩略图滚动列表 -->
			<div style="margin-bottom:32px">
				<h3 style="font-size:16px;font-weight:700;margin:0 0 12px;color:var(--text-soft)">All Pages</h3>
				<div id="scp-thumbnails" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:12px">
					<?php foreach ( $pages as $i => $p ) : ?>
						<button
							class="scp-thumb-btn"
							data-index="<?php echo esc_attr( $i ); ?>"
							data-title="<?php echo esc_attr( $p['title'] ?? '' ); ?>"
							data-png="<?php echo esc_url( $p['png_url'] ?? '' ); ?>"
							data-pdf="<?php echo esc_url( $p['pdf_url'] ?? '' ); ?>"
							data-alt="<?php echo esc_attr( $p['alt'] ?? $p['title'] ?? '' ); ?>"
							style="background:#fff;border:2px solid var(--border);border-radius:12px;overflow:hidden;padding:0;cursor:pointer;transition:all 200ms;aspect-ratio:11/14"
							<?php echo $i === 0 ? 'style="background:#fff;border:3px solid var(--blue);border-radius:12px;overflow:hidden;padding:0;cursor:pointer"' : ''; ?>
						>
							<?php if ( ! empty( $p['thumb_url'] ) ) : ?>
								<img src="<?php echo esc_url( $p['thumb_url'] ); ?>" alt="<?php echo esc_attr( $p['alt'] ?? '' ); ?>" loading="lazy" style="width:100%;height:100%;object-fit:contain;background:#F4F8FC;padding:4px">
							<?php endif; ?>
						</button>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- 中间广告位 (728x90 或响应式) -->
			<?php if ( $show_ads ) : ?>
				<div style="margin:32px 0;text-align:center">
					<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-xxxxxxxxxxxxxxxx" data-ad-slot="0987654321" data-ad-format="horizontal" data-full-width-responsive="true"></ins>
				</div>
			<?php endif; ?>

		<?php else : ?>
			<div style="padding:40px;background:#F9F9F9;border-radius:12px;text-align:center;color:var(--text-soft)">Pages for this topic haven't been imported yet.</div>
		<?php endif; ?>

		<!-- Google AdSense 底部广告位 (728x90) -->
		<?php if ( $show_ads ) : ?>
			<div style="margin:40px 0;text-align:center">
				<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-xxxxxxxxxxxxxxxx" data-ad-slot="5555555555" data-ad-format="horizontal" data-full-width-responsive="true"></ins>
			</div>
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

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const thumbBtns = document.querySelectorAll('.scp-thumb-btn');
		const mainImg = document.getElementById('scp-main-image');
		const pageTitle = document.getElementById('scp-page-title');
		const downloadPdf = document.getElementById('scp-download-pdf');
		const downloadPng = document.getElementById('scp-download-png');

		thumbBtns.forEach(btn => {
			btn.addEventListener('click', function() {
				const title = this.getAttribute('data-title');
				const png = this.getAttribute('data-png');
				const pdf = this.getAttribute('data-pdf');
				const alt = this.getAttribute('data-alt');

				mainImg.src = png;
				mainImg.alt = alt;
				pageTitle.textContent = title;
				downloadPdf.href = pdf;
				downloadPng.href = png;

				thumbBtns.forEach(b => {
					b.style.borderColor = 'var(--border)';
					b.style.borderWidth = '2px';
				});
				this.style.borderColor = 'var(--blue)';
				this.style.borderWidth = '3px';
			});
		});
	});
	</script>

<?php endwhile; ?>
<?php get_footer(); ?>
