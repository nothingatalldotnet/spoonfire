<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Elo
 */

get_header(); ?>

<!-- main begin -->
<div id="main">
	<div id="primary">

		<div id="content" class="wrap">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php
						if ( is_category() ) :
							single_cat_title();

						elseif ( is_tag() ) :
							single_tag_title();

						elseif ( is_author() ) :
							printf( __( 'Author: %s', 'elo' ), '<span class="vcard">' . get_the_author() . '</span>' );

						elseif ( is_day() ) :
							printf( __( 'Day: %s', 'elo' ), '<span>' . get_the_date() . '</span>' );

						elseif ( is_month() ) :
							printf( __( 'Month: %s', 'elo' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'elo' ) ) . '</span>' );

						elseif ( is_year() ) :
							printf( __( 'Year: %s', 'elo' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'elo' ) ) . '</span>' );

						elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
							_e( 'Asides', 'elo' );

						elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) :
							_e( 'Galleries', 'elo' );

						elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
							_e( 'Images', 'elo' );

						elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
							_e( 'Videos', 'elo' );

						elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
							_e( 'Quotes', 'elo' );

						elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
							_e( 'Links', 'elo' );

						elseif ( is_tax( 'post_format', 'post-format-status' ) ) :
							_e( 'Statuses', 'elo' );

						elseif ( is_tax( 'post_format', 'post-format-audio' ) ) :
							_e( 'Audios', 'elo' );

						elseif ( is_tax( 'post_format', 'post-format-chat' ) ) :
							_e( 'Chats', 'elo' );

						else :
							_e( 'Archives', 'elo' );

						endif;
					?>
				</h1>
				<?php
					// Show an optional term description.
					$term_description = term_description();
					if ( ! empty( $term_description ) ) :
						printf( '<div class="taxonomy-description">%s</div>', $term_description );
					endif;
				?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>

			<div id="content-list">
				<ul>

				<!-- the loop -->
				<?php while ( have_posts() ) : the_post(); ?>
					<li>
						
						<?php echo elo_categories_link(); ?>

						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<?php the_excerpt(); ?>
						<p class="meta"><span><?php the_time(get_option('date_format')); ?></span></p>
					</li>
				<?php endwhile; ?>
				<!-- end of the loop -->

				</ul>
			</div>

			<?php elo_paging_nav(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</div>
	</div>
</div>
<!-- main end -->

<?php get_footer(); ?>
