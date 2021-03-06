<?php
	get_header();

	if(have_posts()) {
		while(have_posts()) {
			the_post();

			$group_title = get_the_title();
			$group_text = get_field('photo_group_details');

			$group_banner_id = get_field('photo_group_image');
			$group_banner = wp_get_attachment_image_src($group_banner_id, "banner-top");
			$group_colour = get_field('photo_group_color');

			$hex_group_colour = hex2rgb($group_colour);
		}
	}
?>

<style>
	#content a {
		color: <?php echo $group_colour; ?>;
		font-weight: bold;
	}

	.photo form {
		background: rgba(<?php echo $hex_group_colour[0].",".$hex_group_colour[1].",".$hex_group_colour[2]; ?>,0.5);
	}

	#banner-image {
		background-image: url(<?php echo $group_banner[0]; ?>);
		background-repeat: no-repeat;
		background-size: cover;
	}
</style>
<div id="main">
	<div id="primary">
		<div id="banner-image"></div>
		<div id="content" class="wrap">
			<div class="entry-header">
				<h1 class="entry-title"><?php echo $group_title; ?></h1>
			</div>
			<div>
				<p><?php echo $group_text; ?></p>
			</div>
		</div>
		<div id="gallery">
<?php
	if(have_rows('photo_group_repeater')) {
	    while (have_rows('photo_group_repeater')) {
    		the_row();
			$this_title = get_sub_field('photo_group_photo_title');
			$this_image_id = get_sub_field('photo_group_photo_thumbnail');
			$this_image = wp_get_attachment_image_src($this_image_id, 'gallery-thumbnail');
			$this_big_image = wp_get_attachment_image_src($this_image_id, 'fancybox');
			$this_purchase = get_sub_field('photo_group_photo_allow_purchase');
			$can_i_buy = get_sub_field('photo_group_photo_allow_purchase');
			if($can_i_buy) {
				$buy = get_sub_field('photo_group_photo_download_shortcode');
			}
?>
				<article class="photo">
					<a class="fancybox" rel="group" href="<?php echo $this_big_image[0]; ?>">
						<img class="lazy" src="/assets/images/watermark-grey.png" data-original="<?php echo $this_image[0]; ?>">
					</a>
<?php
	if($can_i_buy) {
		echo do_shortcode($buy);
	}
?>
				</article>
<?php
    	}
	}
?>
		</div>
	</div>
</div>
<?php
	get_footer();
?>