// Crappy js, but it will do for now

(function() {

	$("body").on("contextmenu", "img", function(e) {
		return false;
	});

	$(".fancybox").fancybox();


	$("img.lazy").lazyload();


	var container, button, menu,
		iOS = navigator.userAgent.match(/(iPod|iPhone|iPad)/);

	if(iOS) {
		iosVhHeightBug();
		$(window).bind('resize', iosVhHeightBug);
	} 


	container = document.getElementById( 'site-navigation' );
	if ( ! container )
		return;

	button = container.getElementsByTagName('button')[0];
	if ( 'undefined' === typeof button )
		return;

	menu = container.getElementsByTagName('ul')[0];

	if ('undefined' === typeof menu) {
		button.style.display = 'none';
		return;
	}

	if ( -1 === menu.className.indexOf('nav-menu'))
		menu.className += ' nav-menu';

	button.onclick = function() {
		if (-1 !== container.className.indexOf('toggled'))
			container.className = container.className.replace(' toggled', '');
		else
			container.className += ' toggled';
	};

	function iosVhHeightBug() {
		var height = $(window).height();
		$("#page-cover").css('min-height', height);
	}
})();
