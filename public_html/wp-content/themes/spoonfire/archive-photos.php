<?php
	get_header();

	$page_title = get_field('photos_main_title', 'option');
	$page_text = get_field('photos_main_overview', 'option');

	$banner_image_id = get_field('photos_main_banner_image', 'option');
	$banner_image = wp_get_attachment_image_src($banner_image_id, 'banner-top');
?>

<div id="main">
	<div id="primary">
		<div id="banner-image">
			<img src="<?php echo $banner_image[0]; ?>">
		</div>
		<div id="content" class="wrap">
			<div class="entry-header">
				<h1 class="entry-title"><?php echo $page_title; ?></h1>
			</div>
			<div>
				<p><?php echo $page_text; ?></p>
			</div>
			<div>
<?php
	while(have_posts()) {
		the_post();
		$group_link = get_permalink();
		$group_title = get_the_title();
		$group_colour = get_field('photo_group_color');
		$group_overview = get_field('photo_group_overview');
		$group_image_id = get_field('photo_group_image');
		$group_image = wp_get_attachment_image_src($group_image_id, 'gallery-thumbnail');

?>
			<article class="photo-group" style="background:<?php echo $group_colour; ?>;">
				<a href="<?php echo $group_link; ?>">
					<div class="photo-group-image">
						<img src="<?php echo $group_image[0]; ?>">
					</div>
					<div class="photo-group-text">
						<h3><?php echo $group_title; ?></h3>
						<p><?php echo $group_overview; ?></p>
					</div>
				</a>
			</article>
<?php
	}
?>
			</div>
		</div>
	</div>
</div>
<?php
	get_footer();
?>