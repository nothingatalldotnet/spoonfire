<?php

	update_option('siteurl','http://www.spoonfire.co.uk/');
	update_option('home','http://www.spoonfire.co.uk/');

//	update_option('siteurl','http://192.168.1.201/');
//	update_option('home','http://192.168.1.201/');

	if (!isset($content_width)) {
		$content_width = 640;
	}

	load_theme_textdomain( 'spoonfire', get_template_directory() . '/languages' );

	remove_action('wp_head', 'feed_links_extra');
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'parent_post_rel_link');
	remove_action('wp_head', 'start_post_rel_link');
	remove_action('wp_head', 'adjacent_posts_rel_link');
	remove_action('wp_head', 'locale_stylesheet');
	remove_action('wp_head', 'noindex');
	remove_action('wp_head', 'wp_print_styles');
	remove_action('wp_head', 'wp_print_head_scripts');
	remove_action('wp_head', 'feed_links_extra', 3 );
	remove_action('wp_head', '_ak_framework_meta_tags');
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );
	remove_action('wp_head', 'rest_output_link_wp_head', 10 );
	remove_action('wp_head', 'print_emoji_detection_script', 7 );
	remove_action('admin_print_scripts', 'print_emoji_detection_script' );
	remove_action('wp_print_styles', 'print_emoji_styles' );
	remove_action('admin_print_styles', 'print_emoji_styles' );
	remove_action('do_feed_rdf', 'do_feed_rdf', 10, 1);
	remove_action('do_feed_rss', 'do_feed_rss', 10, 1);
	remove_action('do_feed_rss2', 'do_feed_rss2', 10, 1);
	remove_action('do_feed_atom', 'do_feed_atom', 10, 1);

	show_admin_bar(false);

	add_filter( 'taxonomy-images-disable-public-css', '__return_true' );

	add_filter( 'wp_default_scripts', 'remove_jquery_migrate' );
	function remove_jquery_migrate( &$scripts) {
		if(!is_admin()) {
			$scripts->remove( 'jquery');
			$scripts->add( 'jquery', false, array( 'jquery-core' ), '1.11.1' );
		}
	}

	add_action('init', 'jquery_be_gone');
	function jquery_be_gone() {
		if (!is_admin()) {
			wp_deregister_script('jquery');
			wp_register_script('jquery', '', false, '1.8.3');
			wp_enqueue_script('jquery');
		}
	}

	add_filter('the_generator', 'remove_version');
	function remove_version() { 
		return '';
	}

	add_filter('admin_footer_text', 'change_footer_admin');
	function change_footer_admin () {  
		echo 'Bolted together with little time by <a href="https://nothingatall.net">Rich Jones</a>.';  
	}  

	add_action('pre_comment_on_post', 'block_wp_comments'); 
	function block_wp_comments() { 
		wp_die( __('Sorry, comments are closed for this item.') ); 
	}

	add_action('admin_init', 'df_disable_comments_post_types_support');
	function df_disable_comments_post_types_support() {
		$post_types = get_post_types();
		foreach ($post_types as $post_type) {
			if(post_type_supports($post_type, 'comments')) {
				remove_post_type_support($post_type, 'comments');
				remove_post_type_support($post_type, 'trackbacks');
			}
		}
	}

	add_filter('comments_open', 'df_disable_comments_status', 20, 2);
	add_filter('pings_open', 'df_disable_comments_status', 20, 2);
	function df_disable_comments_status() {
		return false;
	}

	add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);
	function df_disable_comments_hide_existing_comments($comments) {
		$comments = array();
		return $comments;
	}

	add_action('admin_menu', 'df_disable_comments_admin_menu');
	function df_disable_comments_admin_menu() {
		remove_menu_page('edit-comments.php');
	}

	add_action('admin_init', 'df_disable_comments_admin_menu_redirect');
	function df_disable_comments_admin_menu_redirect() {
		global $pagenow;
		if ($pagenow === 'edit-comments.php') {
			wp_redirect(admin_url()); exit;
		}
	}

	add_action('admin_init', 'df_disable_comments_dashboard');
	function df_disable_comments_dashboard() {
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
	}

	add_action('init', 'df_disable_comments_admin_bar');
	function df_disable_comments_admin_bar() {
		if (is_admin_bar_showing()) {
			remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
		}
	}

	add_action( 'admin_footer-post-new.php', 'disable_media_gallery' );
	add_action( 'admin_footer-post.php', 'disable_media_gallery' );
	function disable_media_gallery(){
?>
	<script type="text/javascript">
		jQuery(document).ready( function($) {
			$(document.body).one( 'click', '.insert-media', function( event ) {
				$(".media-menu").find("a:contains('Gallery')").remove();
				$(".media-menu").find("a:contains('Featured')").remove();
			});
		});
	</script>
<?php
	}

	add_theme_support( 'title-tag' );

	function spoonfire_scripts() {
		wp_enqueue_style('spoonfire-style', get_stylesheet_uri() );
		wp_enqueue_script('spoonfire-main', '/assets/js/main.js', array(), '20150505', true );
		wp_enqueue_script('spoonfire-modernizr', '/assets/js/modernizr.js', array(), '20150505', true );
		wp_enqueue_script('spoonfire-classie', '/assets/js/classie.js', array(), '20150505', true );
		wp_enqueue_script('spoonfire-menu', '/assets/js/menu.js', array( 'jquery' ), '20150505', true );
		wp_enqueue_script('spoonfire-skip-link-focus-fix', '/assets/js/skip-link-focus-fix.js', array(), '20130115', true );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'spoonfire_scripts' );

	// Editor styles
	add_editor_style( array(
		'custom-editor-style.css'
	));

	// menus
	register_nav_menus( array(
        'primary' => 'Top Menu'
    ));

	// sizing
	add_theme_support('post-thumbnails');
	add_image_size('gallery-thumbnail', 400, 400, true);
	add_image_size('banner-top', 2000, 80, true);

	// options
    if(function_exists('acf_add_options_page')) {
	    acf_add_options_page(array(
	            'page_title' 	=> 'Home Page',
	            'menu_title'	=> 'Home Page',
	            'menu_slug' 	=> 'home',
	            'capability'	=> 'edit_posts',
	            'icon_url' 		=> 'dashicons-admin-home',
	            'position' 		=> '1.2',
	            'redirect'		=> false
	    ));
	    acf_add_options_page(array(
	            'page_title' 	=> 'Social Links',
	            'menu_title'	=> 'Social Links',
	            'menu_slug' 	=> 'social',
	            'capability'	=> 'edit_posts',
	            'icon_url' 		=> 'dashicons-share',
	            'position' 		=> '1.3',
	            'redirect'		=> false
	    ));
    	acf_add_options_page(array(
            'page_title' 	=> 'Photographs Main Page',
            'menu_title'	=> 'Photographs Main Page',
            'menu_slug' 	=> 'photos',
            'capability'	=> 'edit_posts',
            'parent_slug'	=> 'edit.php?post_type=photos',
            'icon_url' 		=> 'dashicons-welcome-write-blog',
            'position'		=> '0.1',
            'redirect'		=> true
    	));
    	acf_add_options_page(array(
            'page_title' 	=> 'News Main Page',
            'menu_title'	=> 'News Main Page',
            'menu_slug' 	=> 'news',
            'capability'	=> 'edit_posts',
            'parent_slug'	=> 'edit.php?post_type=news',
            'icon_url' 		=> 'dashicons-book',
            'position'		=> '0.4',
            'redirect'		=> true
    	));
	}

	// post types
    add_action('init','create_post_type');
	function create_post_type() {
		register_post_type('Photos',
			array(
				'labels' => array(
					'name' => __('Photographs'),
					'singular_name' => _('Photographs')
				),
				'show_in_nav_menus' => true,
				'menu_position'=> 18,
				'menu_icon'=>'dashicons-camera',
				'public' => true,
				'has_archive' => true,
				'query_var' => true,
				'supports' => array('title','revisions'),
				'rewrite' => array(
					'slug'	=> 'photos',
					'pages' => true,
					'with_front' => true
				),
			)
		);
		register_post_type('News',
			array(
				'labels' => array(
					'name' => __('News'),
					'singular_name' => _('News')
				),
				'show_in_nav_menus' => true,
				'menu_position'=> 18,
				'menu_icon'=>'dashicons-book',
				'public' => true,
				'has_archive' => true,
				'query_var' => true,
				'supports' => array('title','revisions'),
				'rewrite' => array(
					'slug'	=> 'news',
					'pages' => true,
					'with_front' => true
				),
			)
		);
	};
