<?php
	get_header();

	$banner_image_id = get_field('home_image', 'option');
	$banner_image = wp_get_attachment_image_src($banner_image_id, 'banner-top');

?>
<div id="main">
	<div id="primary">
		<div id="banner-image">
			<img src="<?php echo $banner_image[0]; ?>">
		</div>
			<div id="content" class="wrap">
			<div class="entry-header">
				<h1 class="entry-title">Balls! You got a 404 page!</h1>
				<p>(that means whatever it is you were looking for doesnt exist)</p>
			</div>
	</div>
</div>
<?php
	get_footer();
?>
