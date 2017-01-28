<!DOCTYPE html>
<html class="no-js" lang="en">
	<head>    
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="IE=edge" >
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, user-scalable=no">
		<meta name="google-site-verification" content="m-gjo2EHS4nDaOwmLlj1OE4NWb1Au5ZGRNMLrGKo7vw" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<?php wp_head(); ?>
		<link href="https://fonts.googleapis.com/css?family=Oswald:300,400" rel="stylesheet">
        <link rel="shortcut icon" type="image/png" href="/favicon.png" /> 
	</head>
	<body <?php body_class(); ?>>
		<div class="container">
			<div class="wrapper">
				<nav id="navigation" class="navbar-custom navbar-fixed-top">
					<div class="wrap-top-nav clearfix">
						<div class="logo left">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
						</div>
						<div class="social centre">
<?php
	if(get_field('social_facebook','option')) {
		echo "<a class=\"social-icon social-facebook\" target=\"_blank\" href=\"".get_field('social_facebook','option')."\"><img src=\"".get_template_directory_uri()."/images/social_facebook.png\" alt=\"Facebook\"></a>\n";
	}
	if(get_field('social_twitter','option')) {
		echo "<a class=\"social-icon social-twitter\" target=\"_blank\" href=\"".get_field('social_twitter','option')."\"><img src=\"".get_template_directory_uri()."/images/social_twitter.png\" alt=\"Twitter\"></a>\n";
	}
	if(get_field('social_instagram','option')) {
		echo "<a class=\"social-icon social-instagram\" target=\"_blank\" href=\"".get_field('social_instagram','option')."\"><img src=\"".get_template_directory_uri()."/images/social_instagram.png\" alt=\"Instagram\"></a>\n";
	}
?>
						</div>
						<div class="menu-button right">
							<a id="trigger-overlay" href="javascript:void(0)"><img src="<?php echo get_template_directory_uri(); ?>/images/icon-toggle-menu.png" alt="menu"></a>
						</div>
					</div>
				</nav>