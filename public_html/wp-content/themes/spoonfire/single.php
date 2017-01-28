<?php
	get_header()
?>

<div id="main">
	<div id="primary">

<?php 
if ( has_post_thumbnail() ) {
	$feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); 
?>
		<div id="page-cover" style="background: url( <?php echo $feat_image; ?>) no-repeat center center; background-size: cover;">
			<header id="header">
				<div class="wrap">
					<h1><?php the_title(); ?></h1>	
					<?php while ( have_posts() ) : the_post(); ?>
					<h2>By: <?php echo get_the_author_meta('nickname'); ?></h2>
					<?php endwhile; // end of the loop. ?>
				</div>
			</header>
		</div>
		<?php } ?>




		<div id="content" class="wrap">
		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'single' ); ?>

			<?php the_tags( '<div class="tag-link"> ', '', '</div>' ); ?> 


			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) :
					comments_template();
				endif;
			?>

		<?php endwhile; // end of the loop. ?>

		</div>
	</div>
</div>
<?php
	get_footer();
?>