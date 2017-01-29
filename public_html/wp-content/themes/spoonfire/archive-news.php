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
			<div class="entry-meta"></div>
			<div class="entry-content">
<?php

	$the_query = new WP_Query(array('post_type' => 'News', 'posts_per_page' => -1));
	if ($the_query->have_posts()) {
		while ($the_query->have_posts()) {
			$the_query->the_post();

			$news_link = get_permalink();
			$news_title = get_the_title();
			$news_color = get_field('news_accent_colour');
			$news_text = get_field('news_item');
			$news_date = get_the_date('d-m-Y');
			$news_date_long = get_the_date('Y-m-d');
?>
			<article class="news-item">
				<style>
					.news-item a {
						color: <?php echo $news_color; ?>;
						font-weight: bold;
					}
				</style>
				<h3><?php echo $news_title; ?></h3>
				<h4><?php echo $news_date; ?></h4>
				<p><?php echo $news_text; ?></p>
				<script type="application/ld+json">
				{
					"@context": "http://schema.org",
					"@type": "NewsArticle",
					"mainEntityOfPage": {
						"@type": "WebPage",
						"@id": "https://google.com/article"
					},
					"headline": "<?php echo $news_title; ?>",
					"datePublished": "<?php echo $news_date_long; ?>",
					"dateModified": "<?php echo $news_date_long; ?>",
					"author": {
						"@type": "Person",
						"name": "Dave Malcolm"
					},
					"publisher": {
						"@type": "Organization",
						"name": "Google"
					}
				}
				</script>
			</article>
<?php
		}
	}
	wp_reset_query();
?>
			</div>
		</div>
	</div>
</div>
<?php
	get_footer();
?>