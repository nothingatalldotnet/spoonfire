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
        <link rel="stylesheet" href="/assets/css/jquery.fancybox.css" type="text/css"/>
<?php
	function hex2rgb($hex) {
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return $rgb;
	}

	if(get_field('menu_background_colour','option')) {
		$menu_hex = get_field('menu_background_colour','option');
	} else {
		$menu_hex = "#0085a1";
	}
	$menu_rgba = hex2rgb($menu_hex);
?>
<style>
.overlay {
	background: rgba(<?php echo $menu_rgba[0].",".$menu_rgba[1].",".$menu_rgba[2]; ?>, .9);
}
</style>
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
		echo "<a class=\"social-icon social-facebook\" target=\"_blank\" href=\"".get_field('social_facebook','option')."\"><img src=\"/assets/images/social_facebook.png\" alt=\"Facebook\"></a>\n";
	}
	if(get_field('social_twitter','option')) {
		echo "<a class=\"social-icon social-twitter\" target=\"_blank\" href=\"".get_field('social_twitter','option')."\"><img src=\"/assets/images/social_twitter.png\" alt=\"Twitter\"></a>\n";
	}
	if(get_field('social_instagram','option')) {
		echo "<a class=\"social-icon social-instagram\" target=\"_blank\" href=\"".get_field('social_instagram','option')."\"><img src=\"/assets/images/social_instagram.png\" alt=\"Instagram\"></a>\n";
	}
?>
						</div>
						<div class="menu-button right">
							<a id="trigger-overlay" href="javascript:void(0)"><img src="/assets/images/icon-toggle-menu.png" alt="menu"></a>
						</div>
					</div>
				</nav>