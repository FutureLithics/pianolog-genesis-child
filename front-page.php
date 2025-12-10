<?php
/**
 * Front Page Template
 *
 * Custom-coded homepage skeleton (no block editor content).
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Remove the default loop and output custom markup.
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'pianolog_front_page_content' );

/**
 * Output custom homepage sections.
 */
function pianolog_front_page_content() {
	$front_id = get_queried_object_id();
	
	$hero_small  = get_the_post_thumbnail_url( $front_id, 'medium' );
	$hero_medium = get_the_post_thumbnail_url( $front_id, 'large' );
	$hero_large  = get_the_post_thumbnail_url( $front_id, 'full' );
	
	$uploads  = wp_get_upload_dir();
	$uploads_base = isset( $uploads['baseurl'] ) ? $uploads['baseurl'] : '';
	$glyph_01 = $uploads_base ? $uploads_base . '/2025/11/Glyphs-01.png' : '';
	$glyph_02 = $uploads_base ? $uploads_base . '/2025/11/Glyphs-02.png' : '';
	$glyph_03 = $uploads_base ? $uploads_base . '/2025/11/Glyphs-03.png' : '';
	$glyph_04 = $uploads_base ? $uploads_base . '/2025/11/Glyphs-04.png' : '';
	?>
	<main class="front-page">
		<section class="hero py-5">
			<?php if ( $hero_medium ) : ?>
				<div 
					class="hero__bg" 
					style="
						--bg-small: url('<?php echo esc_url( $hero_small ); ?>');
						--bg-medium: url('<?php echo esc_url( $hero_medium ); ?>');
						--bg-large: url('<?php echo esc_url( $hero_large ); ?>');
						background-image: var(--bg-medium);
					">
				</div>
			<?php endif; ?>
			<div class="container text-center flex flex-col items-center justify-center h-100 gap-4">
				<h1 class="hero__title text-white mb-0">
					<?php echo esc_html__( 'Grow Your Craft and', 'pianolog-genesis-child' ); ?><br />
					<?php echo esc_html__( 'Create Your Own Sound', 'pianolog-genesis-child' ); ?>
				</h1>
				<p class="hero__subtitle text-white text-center">
					<?php echo esc_html__( 'A curated library of studies, tools, and reviews for anyone learning the piano or seeking to move beyond playing into composing and producing.', 'pianolog-genesis-child' ); ?>
				</p>
        <div class="hero__actions pt-3">
          <a href="#posts-grid-reviews" class="btn-hero-cta u-soft-border px-3 py-2">
						<?php echo esc_html__( 'Explore Reviews', 'pianolog-genesis-child' ); ?>
					</a>
				</div>
			</div>
		</section>

		<section class="intro py-4">
			<div class="container text-center">
				<div class="intro__icon mx-auto" style="width:96px;height:96px;margin-bottom:16px;background:var(--icon-gradient);-webkit-mask-image:url('<?php echo esc_url( $glyph_01 ); ?>');-webkit-mask-repeat:no-repeat;-webkit-mask-position:center;-webkit-mask-size:contain;mask-image:url('<?php echo esc_url( $glyph_01 ); ?>');mask-repeat:no-repeat;mask-position:center;mask-size:contain;"></div>
				<h2 class="intro__title pb-4">
					<?php echo esc_html__( 'A Place for Every Stage of Your', 'pianolog-genesis-child' ); ?>
					<br class="intro__br" />
					<?php echo esc_html__( 'Musical Journey', 'pianolog-genesis-child' ); ?>
				</h2>
				<p class="intro__body px-3">
					<?php echo esc_html__( 'Piano Log welcomes both new learners and experienced players who feel called to create. Here you can study the foundations of theory, learn how to compose and shape ideas, explore sound design and production, evaluate instruments and courses, and develop the habits that support long-term growth. The aim is to help you build a personal musical identity grounded in skill, clarity, and creative discipline.', 'pianolog-genesis-child' ); ?>
				</p>
			</div>
			<div class="intro__cards">
				<div class="intro__card intro__card--lg">
					<div class="intro__card-icon-wrap">
						<div class="intro__card-icon" style="-webkit-mask-image:url('<?php echo esc_url( $glyph_02 ); ?>');mask-image:url('<?php echo esc_url( $glyph_02 ); ?>');"></div>
					</div>
					<h3 class="intro__card-title"><?php echo esc_html__( 'Technique & Gear', 'pianolog-genesis-child' ); ?></h3>
					<p class="intro__card-text">
						<?php echo esc_html__( 'Master your craft with precision. Explore instruments, tools, and methods that elevate your playing, from the first touch to advanced technique.', 'pianolog-genesis-child' ); ?>
					</p>
				</div>
				<div class="intro__card">
					<div class="intro__card-icon-wrap">
						<div class="intro__card-icon" style="-webkit-mask-image:url('<?php echo esc_url( $glyph_03 ); ?>');mask-image:url('<?php echo esc_url( $glyph_03 ); ?>');"></div>
					</div>
					<h3 class="intro__card-title"><?php echo esc_html__( 'Theory & Composition', 'pianolog-genesis-child' ); ?></h3>
					<p class="intro__card-text">
						<?php echo esc_html__( 'Uncover the architecture of music. Learn concepts and techniques that empower your original compositions and creative expression.', 'pianolog-genesis-child' ); ?>
					</p>
				</div>
				<div class="intro__card intro__card--lg">
					<div class="intro__card-icon-wrap">
						<div class="intro__card-icon" style="-webkit-mask-image:url('<?php echo esc_url( $glyph_04 ); ?>');mask-image:url('<?php echo esc_url( $glyph_04 ); ?>');"></div>
					</div>
					<h3 class="intro__card-title"><?php echo esc_html__( 'Audio Production', 'pianolog-genesis-child' ); ?></h3>
					<p class="intro__card-text">
						<?php echo esc_html__( 'Shape your own sonic world. Discover the processes behind recording, arranging, and producing music that reflects your unique voice.', 'pianolog-genesis-child' ); ?>
					</p>
				</div>
			</div>
		</section>
		
		
		<?php echo do_shortcode('[pianolog_posts_section posts="6" category="reviews" title="Recent Reviews" cta_text="View More Reviews" background="manuscript"]'); ?>

		<section class="cta py-5" style="background: var(--color-coal);">
			<div class="container text-center">
				<h2 class="text-white" style="font-family: var(--font-serif);">
					<?php echo esc_html__( 'Ready to begin your next piece?', 'pianolog-genesis-child' ); ?>
				</h2>
				<p class="text-white">
					<?php echo esc_html__( 'Discover curated lessons and insights tailored to your practice.', 'pianolog-genesis-child' ); ?>
				</p>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn-hero-cta u-soft-border px-3 py-2 mt-3">
					<?php echo esc_html__( 'Get in Touch', 'pianolog-genesis-child' ); ?>
				</a>
			</div>
		</section>
	</main>
	<?php
}

// Render.
genesis();


