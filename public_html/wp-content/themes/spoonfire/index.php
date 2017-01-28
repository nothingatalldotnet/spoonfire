<?php
	get_header();
	$intro_image_id = get_field('home_image', 'option');
	$intro_image = wp_get_attachment_url($intro_image_id);
	$intro_h1 = get_field('home_intro_header_1', 'option');
	$intro_h2 = get_field('home_intro_header_2', 'option');
	$intro_p = get_field('home_intro_text', 'option');
?>

<div id="main" class="home">
	<div id="primary">
		<div id="page-cover" style="background: url( <?php echo $intro_image; ?> ) no-repeat center center; background-size: cover;">
			<header id="header">
				<div class="wrap">
					<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo $intro_h1; ?></a></h1>	
					<h2><?php echo $intro_h2; ?></h2>
				</div>
			</header>
		</div>
	</div>
</div>

<?php
	get_footer();
?>