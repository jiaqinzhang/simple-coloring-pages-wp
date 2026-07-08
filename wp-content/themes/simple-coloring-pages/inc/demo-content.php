<?php
/**
 * Seeds demo coloring_topic posts from the real generated test images so the
 * theme can be previewed with realistic content before the production
 * image-generation pipeline hands off real data (see PROJECT_HANDOFF.md).
 *
 * Safe to run multiple times — it no-ops if demo content already exists.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function scp_seed_demo_content() {
	if ( get_option( 'scp_demo_seeded' ) ) return;

	$categories = array(
		'Animals'             => 'Cats, dogs, dinosaurs, farm and ocean friends.',
		'Holidays'            => 'Christmas, Halloween, Easter, and more.',
		'Fantasy & Cute'      => 'Unicorns, mermaids, dragons, and kawaii fun.',
		'Vehicles'            => 'Cars, trucks, trains, planes, and rockets.',
		'Nature'              => 'Flowers, trees, rainbows, and weather.',
		'Educational'         => 'Alphabet, numbers, shapes, and learning.',
	);
	$term_ids = array();
	foreach ( $categories as $name => $desc ) {
		$existing = term_exists( $name, 'topic_category' );
		if ( $existing ) {
			$term_ids[ $name ] = (int) $existing['term_id'];
		} else {
			$term = wp_insert_term( $name, 'topic_category', array( 'description' => $desc ) );
			if ( ! is_wp_error( $term ) ) $term_ids[ $name ] = $term['term_id'];
		}
	}

	$demo_dir = get_template_directory() . '/assets/demo-images/';
	$demo_uri = get_template_directory_uri() . '/assets/demo-images/';

	// [ topic title, image file basename (without scale suffix), category ]
	$topics = array(
		array( 'Dinosaur Coloring Pages', 'dinosaur', 'Animals' ),
		array( 'Cat Coloring Pages', 'cat', 'Animals' ),
		array( 'Dog Coloring Pages', 'dog', 'Animals' ),
		array( 'Bunny Coloring Pages', 'rabbit', 'Animals' ),
		array( 'Unicorn Coloring Pages', 'unicorn', 'Fantasy & Cute' ),
		array( 'Princess Coloring Pages', 'princess', 'Fantasy & Cute' ),
		array( 'Truck Coloring Pages', 'truck', 'Vehicles' ),
		array( 'Flower Coloring Pages', 'flower', 'Nature' ),
		array( 'Christmas Tree Coloring Pages', 'christmas-tree', 'Holidays' ),
		array( 'Halloween Pumpkin Coloring Pages', 'halloween-pumpkin', 'Holidays' ),
		array( 'Alphabet Coloring Pages', 'alphabet-a', 'Educational' ),
		array( 'Flower Garden Coloring Pages', 'detailed-flower-garden', 'Nature' ),
	);

	$tints = array( '#DCEEFB', '#FFF3D6', '#DFF3E8', '#FDE4EE', '#EDE6FA', '#FFE9DC' );

	foreach ( $topics as $i => $t ) {
		list( $title, $basename, $cat_name ) = $t;

		$post_id = wp_insert_post( array(
			'post_type'   => 'coloring_topic',
			'post_title'  => $title,
			'post_status' => 'publish',
			'post_content'=> '',
		) );
		if ( is_wp_error( $post_id ) ) continue;

		if ( isset( $term_ids[ $cat_name ] ) ) {
			wp_set_post_terms( $post_id, array( $term_ids[ $cat_name ] ), 'topic_category' );
		}

		$img_1_0 = $demo_uri . $basename . '_scale1.0.png';
		$img_0_5 = $demo_uri . $basename . '_scale0.5.png';

		update_post_meta( $post_id, 'scp_thumb_url', $img_1_0 );
		update_post_meta( $post_id, 'scp_tint', $tints[ $i % count( $tints ) ] );
		update_post_meta( $post_id, 'scp_age_range', '2-8' );
		update_post_meta( $post_id, 'scp_pdf_all_url', '' ); // no real PDF yet — button hides itself.
		update_post_meta( $post_id, 'scp_pdf_all_size', '' );

		// Two demo "pages" per topic (the two LoRA-scale test renders) so the
		// grid + modal have something real to show.
		$pages = array(
			array(
				'title'     => $title . ' - Version 1',
				'alt'       => $title . ' free printable coloring page for kids',
				'thumb_url' => $img_1_0,
				'png_url'   => $img_1_0,
				'pdf_url'   => '',
			),
			array(
				'title'     => $title . ' - Version 2',
				'alt'       => $title . ' free printable coloring page for kids',
				'thumb_url' => $img_0_5,
				'png_url'   => $img_0_5,
				'pdf_url'   => '',
			),
		);
		update_post_meta( $post_id, 'scp_pages', $pages );

		// Seed real single-image coloring_page children for Cat + Unicorn so the
		// new single-image landing page template can be previewed with real data.
		if ( in_array( $basename, array( 'cat', 'unicorn' ), true ) ) {
			scp_seed_demo_single_pages( $post_id, $title, $basename, $img_1_0, $img_0_5 );
		}
	}

	update_option( 'scp_demo_seeded', 1 );
}
add_action( 'init', 'scp_seed_demo_content', 20 );

/** Seed 2 demo coloring_page children under a topic (mirrors the SEO content-regeneration rules). */
function scp_seed_demo_single_pages( $topic_id, $topic_title, $basename, $img_1_0, $img_0_5 ) {
	$noun = $basename === 'cat' ? 'Cat' : 'Unicorn';

	$vocab_banks = array(
		'Cat'     => array( 'whiskers', 'purr', 'kitten' ),
		'Unicorn' => array( 'horn', 'mane', 'sparkle' ),
	);
	$fun_facts = array(
		'Cat'     => 'Cats spend around 70% of their lives asleep, which adds up to 13-16 hours a day.',
		'Unicorn' => 'Unicorns have appeared in myths and stories for over 4,000 years.',
	);

	$variants = array(
		array(
			'tag'   => 'Waving',
			'pose'  => "a cute {$basename} sitting up tall with a big cheerful grin, head tilted slightly, one paw raised in a wave",
			'image' => $img_1_0,
		),
		array(
			'tag'   => 'Sleepy',
			'pose'  => "a cute {$basename} curled up fast asleep, peaceful smile, one eye scrunched shut, paws tucked under its chin",
			'image' => $img_0_5,
		),
	);

	foreach ( $variants as $i => $v ) {
		$page_title = "{$v['tag']} {$noun} Coloring Page";
		$slug       = sanitize_title( $page_title );

		$page_id = wp_insert_post( array(
			'post_type'   => 'coloring_page',
			'post_title'  => $page_title,
			'post_name'   => $slug,
			'post_status' => 'publish',
			'menu_order'  => $i,
		) );
		if ( is_wp_error( $page_id ) ) continue;

		$intro = "This {$noun} coloring page shows {$v['pose']}. It's part of our free printable coloring pages collection, made for parents, teachers, and grandparents who want an easy screen-free activity kids can start in seconds -- just download the PDF and print on Letter or A4 paper. This page fits Ages 3-8. While coloring, children practice fine motor skills, hand-eye coordination, and pencil control, and can pick up three real words: " . implode( ', ', $vocab_banks[ $noun ] ) . ". " . $fun_facts[ $noun ];

		update_post_meta( $page_id, 'scp_topic_id', $topic_id );
		update_post_meta( $page_id, 'scp_meta_description', "Free printable {$basename} coloring page: {$v['pose']}. Download the PDF and print at home for ages 3-8." );
		update_post_meta( $page_id, 'scp_intro', $intro );
		update_post_meta( $page_id, 'scp_vocabulary', $vocab_banks[ $noun ] );
		update_post_meta( $page_id, 'scp_fun_fact', $fun_facts[ $noun ] );
		update_post_meta( $page_id, 'scp_alt_text', "Free printable {$basename} coloring page featuring {$v['pose']}." );
		update_post_meta( $page_id, 'scp_png_url', $v['image'] );
		update_post_meta( $page_id, 'scp_thumb_url', $v['image'] );
		update_post_meta( $page_id, 'scp_pdf_url', '' );
	}
}
