<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<footer class="site-footer">
	<div class="wrap">
		<div class="footer-cols">
			<div>
				<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
					<?php scp_render_logo_bars( 'footer' ); ?>
					<span style="font-family:var(--font-display);font-weight:800;font-size:19px;color:#fff"><?php bloginfo( 'name' ); ?></span>
				</div>
				<p style="font-size:13.5px;line-height:1.7;color:var(--footer-mute);margin:0">Free printable coloring pages for kids &mdash; made for families, teachers, and classrooms.</p>
			</div>
			<div>
				<div class="footer-heading">Categories</div>
				<div style="display:flex;flex-direction:column;gap:8px">
					<?php
					$categories = get_terms( array( 'taxonomy' => 'topic_category', 'hide_empty' => false, 'number' => 6 ) );
					if ( ! is_wp_error( $categories ) ) {
						foreach ( $categories as $cat ) {
							printf( '<a href="%s" class="footer-link">%s</a>', esc_url( get_term_link( $cat ) ), esc_html( $cat->name ) );
						}
					}
					?>
				</div>
			</div>
			<div>
				<div class="footer-heading">Popular Pages</div>
				<div style="display:flex;flex-direction:column;gap:8px">
					<?php
					$popular = get_posts( array( 'post_type' => 'coloring_topic', 'posts_per_page' => 6, 'orderby' => 'rand' ) );
					foreach ( $popular as $p ) {
						printf( '<a href="%s" class="footer-link">%s</a>', esc_url( get_permalink( $p ) ), esc_html( get_the_title( $p ) ) );
					}
					?>
				</div>
			</div>
			<div>
				<div class="footer-heading"><?php bloginfo( 'name' ); ?></div>
				<div style="display:flex;flex-direction:column;gap:8px">
					<a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="footer-link">About</a>
					<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="footer-link">Contact</a>
					<a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>" class="footer-link">Privacy Policy</a>
					<a href="<?php echo esc_url( home_url( '/terms' ) ); ?>" class="footer-link">Terms of Use</a>
					<a href="<?php echo esc_url( home_url( '/sitemap/' ) ); ?>" class="footer-link">Sitemap</a>
				</div>
			</div>
		</div>
		<div class="footer-bottom">
			<span>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php echo esc_html( parse_url( home_url(), PHP_URL_HOST ) ?: 'simplecoloringpagesforkids.com' ); ?> &mdash; All coloring pages are original artwork, free for personal and classroom use.</span>
			<span>Made with love for little artists</span>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
