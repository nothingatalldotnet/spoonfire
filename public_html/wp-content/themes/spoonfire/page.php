<?php
	get_header();
	if(get_the_ID() == 39) {
		// checkout
		$banner_image_id = get_field('checkout_main_banner_image');
	} else if(get_the_ID() == 6) {
		// about
		$banner_image_id = get_field('about_main_banner_image');
	} else if(get_the_ID() == 21) {
		// contact
		$banner_image_id = get_field('contact_main_banner_image');
	} else {
		$banner_image_id = get_field('photos_main_banner_image', 'option');
	}
	$banner_image = wp_get_attachment_image_src($banner_image_id, 'banner-top');
?>
<style>
	#banner-image {
		background-image: url(<?php echo $banner_image[0]; ?>);
		background-repeat: no-repeat;
		background-size: cover;
	}
</style>
<div id="main">
	<div id="primary">
		<div id="banner-image"></div>
		<div id="content" class="wrap">
<?php
	while (have_posts()) {
		the_post();
?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</div>
			<div class="entry-meta"></div>
			<div class="entry-content">									
<?php
		the_content();
?>
				</div>
			</article>
<?php
	}
?>
		</div>
	</div>
</div>

<?php
	get_footer();
?>
