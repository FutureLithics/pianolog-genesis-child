<?php
/**
 * Archive Template (primarily for categories)
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'genesis_loop', 'genesis_do_loop' );
// Remove Genesis' default archive title/description wrappers to avoid duplicate markup.
remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );
remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );
add_action( 'genesis_loop', function () {
	$term         = get_queried_object();
	$is_category   = is_category() && $term && ! is_wp_error( $term );
	$is_tag        = is_tag() && $term && ! is_wp_error( $term );
	$is_date       = is_date();
	if ( $is_category ) {
		$title       = single_cat_title( '', false );
		$description = category_description();
	} elseif ( $is_tag ) {
		$title       = single_tag_title( '', false );
		$description = tag_description();
	} elseif ( $is_date ) {
		// Generate clean date titles without "Month:" / "Year:" prefixes.
		if ( is_day() ) {
			$year  = (int) get_query_var( 'year' );
			$month = (int) get_query_var( 'monthnum' );
			$day   = (int) get_query_var( 'day' );
			$title = date_i18n( 'F j, Y', mktime( 0, 0, 0, $month ?: 1, $day ?: 1, $year ?: (int) date_i18n( 'Y' ) ) );
		} elseif ( is_month() ) {
			// Returns e.g. "November 2024"
			$title = single_month_title( ' ', false );
		} elseif ( is_year() ) {
			$title = (string) get_query_var( 'year' );
		} else {
			$title = get_the_archive_title();
		}
		$description = '';
    } else {
		$title       = get_the_archive_title();
		$description = get_the_archive_description();
	}
	$category_slug = $is_category ? $term->slug : '';
	?>
	<section class="archive-header pt-5" style="background:#ffffff;">
		<div class="container text-center">
			<h1 style="font-family: var(--font-serif); font-size: 48px; font-weight: 700; margin: 0;">
				<?php echo esc_html( $title ); ?>
			</h1>
			<?php if ( $description ) : ?>
				<div style="font-family: var(--font-sans); font-size: 20px; margin: 0 auto; max-width: 75ch;">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php
	if ( $is_category ) {
		// Posts grid with Load More (initial 9) for this category.
		echo do_shortcode(
			sprintf(
				'[pianolog_posts_section category="%s" posts="all" title="%s" background="white" show_title="false"]',
				esc_attr( $category_slug ),
				esc_attr( $title )
			)
		);
	} elseif ( $is_tag ) {
		// Tag archives: render using the same grid component for visual parity.
		$q = new WP_Query(
			array(
				'posts_per_page' => 9,
				'post_status'    => 'publish',
				'no_found_rows'  => true,
				'tag'            => $term->slug,
				'paged'          => 1,
			)
		);
		get_template_part(
			'template-parts/sections/posts-grid',
			null,
			array(
				'title'        => $title,
				'cta_text'     => '', // no CTA for tag pages by default
				'cta_link'     => '',
				'background'   => 'white',
				'section_id'   => 'posts-grid-tag-' . esc_attr( $term->slug ),
				'query'        => $q,
				'load_more'    => false, // category has Load More; tag uses static grid for parity in look
				'category_slug'=> '',
				'per_page'     => 9,
				'show_title'   => false,
			)
		);
	} elseif ( is_date() ) {
		// Date archives: use the same posts grid styling as category/tag archives.
		$year  = get_query_var( 'year' );
		$month = get_query_var( 'monthnum' );
		$day   = get_query_var( 'day' );
		$date_query = array();
		if ( $year ) {
			$date_query['year'] = (int) $year;
		}
		if ( $month ) {
			$date_query['monthnum'] = (int) $month;
		}
		if ( $day ) {
			$date_query['day'] = (int) $day;
		}
		$q = new WP_Query(
			array(
				'posts_per_page' => 9,
				'post_status'    => 'publish',
				'no_found_rows'  => true,
				'date_query'     => array( $date_query ),
				'paged'          => 1,
			)
		);
		$section_id = 'posts-grid-date';
		if ( $year ) {
            $section_id .= '-' . $year;
        }
        if ( $month ) {
            $section_id .= sprintf( '%02d', $month );
        }
        if ( $day ) {
            $section_id .= sprintf( '%02d', $day );
        }
		get_template_part(
			'template-parts/sections/posts-grid',
			null,
			array(
				'title'        => $title,
				'cta_text'     => '',
				'cta_link'     => '',
				'background'   => 'white',
				'section_id'   => esc_attr( $section_id ),
				'query'        => $q,
				'load_more'    => false,
				'category_slug'=> '',
				'per_page'     => 9,
				'show_title'   => false,
			)
		); 
	} else {
		// Fallback to a generic loop for non-category archives.
		if ( have_posts() ) :
			echo '<div class="container py-4"><div class="gear-grid">';
			while ( have_posts() ) :
				the_post();
				$thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
				?>
				<article <?php post_class( 'gear-card' ); ?>>
					<a class="gear-card__link" href="<?php the_permalink(); ?>">
						<div class="gear-card__image">
							<?php if ( $thumb_url ) : ?>
								<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
							<?php else : ?>
								<div class="gear-card__image--placeholder"></div>
							<?php endif; ?>
						</div>
						<div class="gear-card__content">
							<h3 class="gear-card__title"><?php the_title(); ?></h3>
							<p class="gear-card__excerpt">
								<?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 32 ) ); ?>
							</p>
							<div class="gear-card__meta">
								<span class="gear-card__author"><?php echo esc_html( get_the_author() ); ?></span>
							</div>
						</div>
					</a>
				</article>
				<?php
			endwhile;
			echo '</div></div>';
			the_posts_navigation();
		else :
			echo '<div class="container py-4"><p>' . esc_html__( 'No posts found.', 'pianolog-genesis-child' ) . '</p></div>';
		endif;
	}
} );

genesis();


