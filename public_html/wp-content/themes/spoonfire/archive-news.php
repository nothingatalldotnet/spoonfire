<?php
	get_header();

	$page_title = get_field('news_main_title', 'option');

	$banner_image_id = get_field('news_main_banner_image', 'option');
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
<?php
	while(have_posts()) {
		the_post();
//		$news_link = get_permalink();
		$news_title = get_the_title();
		$news_color = get_field('news_group_color');
		$news_text = get_the_content();
?>
			<article class="news-item">
				<h3><?php echo $group_title; ?></h3>
				<?php echo $news_text; ?>
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