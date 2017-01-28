<?php
/**
 * The template for displaying search results pages.
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
				<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'elo' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>

			<div id="content-list" class="search-result-page">
				<ul>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
					/**
					 * Run the loop for the search to output the results.
					 * If you want to overload this in a child theme then include a file
					 * called content-search.php and that will be used instead.
					 */
					get_template_part( 'content', 'search' );
					?>

				<?php endwhile; ?>
				</ul>
			</div>

			<!-- page nav begin -->			
			<?php elo_paging_nav(); ?>
			<!-- page nav end -->

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		<?php //get_sidebar(); ?>

		</div>
	</div>
</div>
<!-- main end -->


<?php get_footer(); ?>
