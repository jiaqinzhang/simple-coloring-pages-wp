<?php
/**
 * Homepage template.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<!-- ============ HERO ============ -->
<section style="background:linear-gradient(180deg,var(--blue-bg2) 0%,var(--bg) 100%);position:relative;overflow:hidden">
	<div style="position:relative;max-width:860px;margin:0 auto;padding:72px 24px 56px;text-align:center">
		<div style="display:inline-flex;align-items:center;gap:8px;background:#fff;border:1px solid var(--border);border-radius:999px;padding:7px 16px;font-size:13px;font-weight:800;color:var(--blue-dark);letter-spacing:.4px;box-shadow:0 2px 8px rgba(61,66,102,.06)">
			<span style="width:8px;height:8px;border-radius:999px;background:var(--green-soft);display:inline-block"></span>
			100% FREE &middot; PRINT AT HOME OR SCHOOL
		</div>
		<h1 style="font-size:clamp(36px,5.5vw,58px);line-height:1.1;margin:20px 0 14px">Free Printable Coloring Pages <span style="color:var(--blue)">for Kids</span></h1>
		<p style="font-size:18px;line-height:1.65;color:var(--text-soft);max-width:640px;margin:0 auto 30px">Download cute and easy coloring pages for kids, including animals, holidays, unicorns, dinosaurs, alphabet pages, and more.</p>
		<form class="search-box" style="max-width:560px;margin:0 auto" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="text" name="s" placeholder="Search coloring pages...">
			<button type="submit" class="btn btn-amber">Search</button>
		</form>
		<div style="display:flex;gap:14px;justify-content:center;margin-top:26px;flex-wrap:wrap">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'coloring_topic' ) ); ?>" class="btn btn-primary">Browse All Coloring Pages</a>
			<a href="#popular" class="btn btn-outline">Popular Coloring Pages</a>
		</div>
	</div>
</section>

<!-- ============ POPULAR ============ -->
<section id="popular" class="wrap" style="padding-top:56px;padding-bottom:8px">
	<div style="display:flex;align-items:baseline;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:24px">
		<h2 style="font-size:32px">Popular Coloring Pages</h2>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'coloring_topic' ) ); ?>" style="text-decoration:none;color:var(--blue-dark);font-weight:800;font-size:15px">View all topics &rarr;</a>
	</div>
	<div class="grid-cards">
		<?php
		$popular = new WP_Query( array( 'post_type' => 'coloring_topic', 'posts_per_page' => 8, 'meta_key' => 'scp_view_count', 'orderby' => 'meta_value_num date', 'order' => 'DESC' ) );
		$i = 0;
		while ( $popular->have_posts() ) : $popular->the_post();
			scp_render_topic_card( get_the_ID(), $i++ );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>


<!-- ============ CATEGORIES ============ -->
<section class="wrap" style="padding-top:56px;padding-bottom:8px">
	<h2 style="font-size:32px;margin-bottom:8px">Browse by Category</h2>
	<p style="margin:0 0 24px;color:var(--text-soft);font-size:16px">Explore our full collection of free printable coloring pages by category.</p>
	<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:18px">
		<?php
		$categories = get_terms( array( 'taxonomy' => 'topic_category', 'hide_empty' => false ) );
		if ( ! is_wp_error( $categories ) ) :
			$ci = 0;
			foreach ( $categories as $cat ) :
				$tint = scp_tint_for( $ci );
				?>
				<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" style="text-decoration:none;color:inherit;display:block;background:#fff;border:1px solid var(--border);border-radius:20px;padding:22px 20px;box-shadow:0 4px 14px rgba(61,66,102,.06)">
					<div style="width:52px;height:52px;border-radius:16px;background:<?php echo esc_attr( $tint ); ?>;display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-weight:800;font-size:22px;margin-bottom:14px"><?php echo esc_html( mb_substr( $cat->name, 0, 1 ) ); ?></div>
					<div style="font-family:var(--font-display);font-weight:700;font-size:18px"><?php echo esc_html( $cat->name ); ?></div>
					<div style="font-size:13.5px;line-height:1.5;color:var(--text-soft);margin:6px 0 14px"><?php echo esc_html( $cat->description ?: ( $cat->count . ' topics' ) ); ?></div>
					<span style="font-family:var(--font-display);font-weight:700;font-size:14px;color:var(--blue-dark)">View Category &rarr;</span>
				</a>
				<?php
				$ci++;
			endforeach;
		endif;
		?>
	</div>
</section>

<!-- ============ CATEGORY SHOWCASES ============ -->
<?php
$showcase_cats = get_terms( array( 'taxonomy' => 'topic_category', 'hide_empty' => true ) );
$showcase_bgs  = array( 'var(--blue-bg2)', 'var(--amber-bg2)', 'var(--purple-bg2)' );
if ( ! is_wp_error( $showcase_cats ) ) :
	$si = 0;
	foreach ( $showcase_cats as $cat ) :
		$topics = new WP_Query( array( 'post_type' => 'coloring_topic', 'posts_per_page' => 8, 'tax_query' => array( array( 'taxonomy' => 'topic_category', 'field' => 'term_id', 'terms' => $cat->term_id ) ) ) );
		if ( ! $topics->have_posts() ) { wp_reset_postdata(); continue; }
		?>
		<section class="wrap" style="padding-top:52px;padding-bottom:8px">
			<div style="background:<?php echo esc_attr( $showcase_bgs[ $si % 3 ] ); ?>;border-radius:28px;padding:32px 28px 28px">
				<div style="display:flex;align-items:baseline;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px">
					<h2 style="font-size:26px"><?php echo esc_html( $cat->name ); ?> Coloring Pages</h2>
					<span style="font-size:13px;font-weight:800;color:var(--footer-mute);letter-spacing:.4px"><?php echo esc_html( $cat->count ); ?> topics</span>
				</div>
				<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px">
					<?php
					$ti = 0;
					while ( $topics->have_posts() ) : $topics->the_post();
						scp_render_topic_card( get_the_ID(), $ti++, true );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
				<div style="text-align:center;margin-top:22px">
					<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="btn btn-outline" style="border-color:rgba(58,135,196,.25)">View All <?php echo esc_html( $cat->name ); ?> Coloring Pages</a>
				</div>
			</div>
		</section>
		<?php
		$si++;
	endforeach;
endif;
?>

<!-- ============ LATEST ============ -->
<section class="wrap" style="padding-top:56px;padding-bottom:8px">
	<div style="display:flex;align-items:baseline;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:24px">
		<h2 style="font-size:32px">Latest Coloring Pages</h2>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'coloring_topic' ) ); ?>" style="text-decoration:none;color:var(--blue-dark);font-weight:800;font-size:15px">See what's new &rarr;</a>
	</div>
	<div class="grid-cards">
		<?php
		$latest = new WP_Query( array( 'post_type' => 'coloring_topic', 'posts_per_page' => 4, 'orderby' => 'date', 'order' => 'DESC' ) );
		$i = 0;
		while ( $latest->have_posts() ) : $latest->the_post();
			$tint = get_post_meta( get_the_ID(), 'scp_tint', true ) ?: scp_tint_for( $i );
			$thumb = get_post_meta( get_the_ID(), 'scp_thumb_url', true ) ?: get_the_post_thumbnail_url( get_the_ID(), 'scp-card' );
			?>
			<a href="<?php the_permalink(); ?>" class="topic-card" style="position:relative">
				<div style="position:absolute;top:12px;left:12px;background:var(--green-soft);color:var(--green-text);font-family:var(--font-display);font-weight:700;font-size:11.5px;letter-spacing:.5px;padding:4px 11px;border-radius:999px;z-index:1">NEW</div>
				<div class="topic-card-thumb" style="background:<?php echo esc_attr( $tint ); ?>">
					<div class="topic-card-art"><?php if ( $thumb ) : ?><img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy"><?php endif; ?></div>
				</div>
				<div class="topic-card-body">
					<div class="topic-card-title"><?php the_title(); ?></div>
					<div class="topic-card-meta">Added <?php echo esc_html( get_the_date( 'M j' ) ); ?> &middot; <?php echo esc_html( count( scp_get_pages( get_the_ID() ) ) ); ?> pages</div>
				</div>
			</a>
			<?php
			$i++;
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>

<!-- ============ SEO CONTENT ============ -->
<section class="wrap" style="padding-top:64px;padding-bottom:24px">
	<div class="section-card" style="border-radius:28px;padding:44px clamp(24px,5vw,56px);box-shadow:0 4px 14px rgba(61,66,102,.05)">
		<h2 style="font-size:28px;margin-bottom:14px">Free Printable Coloring Pages for Kids</h2>
		<p style="font-size:16px;line-height:1.75;color:var(--text-soft);margin:0 0 12px;max-width:820px"><?php bloginfo( 'name' ); ?> offers hundreds of free printable coloring pages for toddlers, preschoolers, and elementary school kids. Every page is an original, kid-friendly illustration with bold, easy-to-color outlines &mdash; perfect for crayons, markers, and colored pencils. Simply download the PDF and print at home or in the classroom.</p>
		<p style="font-size:16px;line-height:1.75;color:var(--text-soft);margin:0;max-width:820px">New coloring sheets are added every week across animals, holidays, fantasy, vehicles, nature, and educational themes like the alphabet and numbers.</p>

		<h3 style="font-size:21px;margin:36px 0 18px">How to Use Our Coloring Pages</h3>
		<div class="grid-steps">
			<div class="step-card" style="background:var(--blue-bg2)"><div class="step-num" style="background:var(--blue)">1</div><div class="step-title">Download the PDF</div><div class="step-desc">Pick any page and click Download &mdash; no sign-up needed.</div></div>
			<div class="step-card" style="background:var(--amber-bg)"><div class="step-num" style="background:var(--amber);color:var(--amber-text)">2</div><div class="step-title">Print at home or school</div><div class="step-desc">Pages are sized for standard US Letter and A4 paper.</div></div>
			<div class="step-card" style="background:var(--pink-bg)"><div class="step-num" style="background:var(--pink);color:var(--pink-text)">3</div><div class="step-title">Color and have fun</div><div class="step-desc">Use crayons, markers, or pencils &mdash; then hang it on the fridge!</div></div>
		</div>

		<h3 style="font-size:21px;margin:36px 0 14px">Frequently Asked Questions</h3>
		<div style="display:flex;flex-direction:column;gap:10px">
			<?php
			$faqs = array(
				array( 'q' => 'Are these coloring pages free?', 'a' => 'Yes! Every coloring page on ' . get_bloginfo( 'name' ) . ' is 100% free to download and print for personal and classroom use.' ),
				array( 'q' => 'Can I print these coloring pages?', 'a' => 'Absolutely. Each page is a high-resolution PDF sized for standard US Letter and A4 paper, so it prints perfectly at home or school.' ),
				array( 'q' => 'Are these pages suitable for preschoolers?', 'a' => 'Yes — most collections include simple pages with big, bold outlines that are perfect for toddlers and preschoolers, plus more detailed pages for older kids.' ),
				array( 'q' => 'Can teachers use them in the classroom?', 'a' => 'Of course! Teachers are welcome to print pages for classroom activities, holiday units, early finishers, and homework fun.' ),
			);
			foreach ( $faqs as $f ) : ?>
				<details class="faq-item">
					<summary><?php echo esc_html( $f['q'] ); ?></summary>
					<p><?php echo esc_html( $f['a'] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
