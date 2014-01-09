<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/css/reset.css" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/css/960.css" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/style.css" />
	<meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">

	<!--[if lt IE 9]>
	<script>
		document.createElement('header');
		document.createElement('section');
		document.createElement('article');
		document.createElement('aside');
		document.createElement('nav');
		document.createElement('footer');
	</script>
	<![endif]-->

		<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/css/smoothness/jquery-ui-1.8.23.custom.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
		<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-ui-1.8.23.custom.min.js" type="text/javascript"></script>
		<script src="<?php bloginfo('stylesheet_directory'); ?>/js/js.js" type="text/javascript"></script>
		<?php if(is_front_page()) { ?>
		<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.jfeed.js" type="text/javascript"></script>
		<?php } ?>
	<?php wp_head(); ?>
</head>
<?php if(is_page('membership') || is_page('services')) { ?>
<body class="sidebar_bg" style="background-image: none !important;">
<?php } else if(is_page_template('page_sidebar.php') || is_page_template('page_news.php')) { ?>
<body class="sidebar_bg">
<?php } else {?>
<body>
<?php } ?>
<header class="container_12">
	<a class="logo" href='<?php echo home_url('/');?>'></a>

	<p class="info">
		<span class="phoneNumber">406.248.4893</span>
		<span class="address">2727 Central Avenue, Suite 2<br/>Billings, Montana 59102</span>
	</p>

	<nav class="main"><ul><?php wp_nav_menu(array('theme_location' => 'main-menu')); ?></ul>
	<?php if(is_front_page()) { ?>
	<div class="rss-feed-wrapper">
		<div class="rss-feed">
		</div>
	</div>
	<script>
		var feedLink1 = "<?php echo ae_options('rss_feed_link1'); ?>",
		feedLink2 = "<?php echo ae_options('rss_feed_link2'); ?>",
		feedLink3 = "<?php echo ae_options('rss_feed_link3'); ?>",
		fr = true,
		run = true;
		$(document).ready(function () {
			feed.populate();
			var i = setInterval(function() {
				feed.animate();
			}, 4500);
			$(".rss-feed").hover(function() {
				run = false;
			}, function() {
				run = true;
			});
		});
		var feed = {
			populate: function (cb) {
				$.getFeed({
					url: '<?php bloginfo('stylesheet_directory'); ?>/proxy.php?url=' + feedLink1,
					success: function(feed) {
						$.each(feed.items, function(key, feeditem) {
							$(".rss-feed").append('<a href="' + feeditem.link + '" class="rss-feed-article" target="_blank"><span class="rss-title">' + feeditem.title + '</span></a>');
						});
					}
				});
				$.getFeed({
					url: '<?php bloginfo('stylesheet_directory'); ?>/proxy.php?url=' + feedLink2,
					success: function(feed) {
						$.each(feed.items, function(key, feeditem) {
							$(".rss-feed").append('<a href="' + feeditem.link + '" class="rss-feed-article" target="_blank"><span class="rss-title">' + feeditem.title + '</span></a>');
						});
					}
				});
				$.getFeed({
					url: '<?php bloginfo('stylesheet_directory'); ?>/proxy.php?url=' + feedLink3,
					success: function(feed) {
						$.each(feed.items, function(key, feeditem) {
							$(".rss-feed").append('<a href="' + feeditem.link + '" class="rss-feed-article" target="_blank"><span class="rss-title">' + feeditem.title + '</span></a>');
						});
					}
				});
			},
			animate: function () {
				console.log("running animate");
				if(!run) {
					console.log("returning, run false");
					return;
				}
				if(fr) {
					console.log("first run");
					$(".rss-feed-article").first().addClass("rss-feed-active").show();
					fr = false;
				} else {
					console.log("running reg.");
					var x = $(".rss-feed-active").next();
					if(!x.is("a")) {
						x = $(".rss-feed-article").first();	
					}
					$(".rss-feed-active").removeClass("rss-feed-active").slideUp(1000, function() {
						$(x).addClass("rss-feed-active").slideDown(1000);
					});
				}
			}
		}
	</script>
	<?php } ?>
	</nav>
</header>


<?php if(is_front_page()) { ?>
<div class="bannerContainer">
	<div class="bigBanner"><div class="slider">
		<div class="slide ac">
			<div class="slide-image" style="background: url('<?php echo esc_url(ae_options('slider_slide1_image')); ?>') center no-repeat; background-size:contain;"></div>
			<div class="slide-text">
				<h1 class="slide-heading"><?php echo ae_options('slider_slide1_heading'); ?></h1>
				<h2 class="slide-subheading"><?php echo ae_options('slider_slide1_subheading'); ?></h2>
			</div>
		</div>
		<div class="slide">
			<div class="slide-image" style="background: url('<?php echo esc_url(ae_options('slider_slide2_image')); ?>') center no-repeat; background-size:contain;"></div>
			<div class="slide-text">
				<h1 class="slide-heading"><?php echo ae_options('slider_slide2_heading'); ?></h1>
				<h2 class="slide-subheading"><?php echo ae_options('slider_slide2_subheading'); ?></h2>
			</div>
		</div>
		<div class="slide">
			<div class="slide-image" style="background: url('<?php echo esc_url(ae_options('slider_slide3_image')); ?>') center no-repeat; background-size:contain;"></div>
			<div class="slide-text">
				<h1 class="slide-heading"><?php echo ae_options('slider_slide3_heading'); ?></h1>
				<h2 class="slide-subheading"><?php echo ae_options('slider_slide3_subheading'); ?></h2>
			</div>
		</div>
		<div class="slide">
			<div class="slide-image" style="background: url('<?php echo esc_url(ae_options('slider_slide4_image')); ?>') center no-repeat; background-size:contain;"></div>
			<div class="slide-text">
				<h1 class="slide-heading"><?php echo ae_options('slider_slide4_heading'); ?></h1>
				<h2 class="slide-subheading"><?php echo ae_options('slider_slide4_subheading'); ?></h2>
			</div>
		</div>
		<div class="slide">
			<div class="slide-image" style="background: url('<?php echo esc_url(ae_options('slider_slide5_image')); ?>') center no-repeat; background-size:contain;"></div>
			<div class="slide-text">
				<h1 class="slide-heading"><?php echo ae_options('slider_slide5_heading'); ?></h1>
				<h2 class="slide-subheading"><?php echo ae_options('slider_slide5_subheading'); ?></h2>
			</div>
		</div>
		<div class="slide">
			<div class="slide-image" style="background: url('<?php echo esc_url(ae_options('slider_slide6_image')); ?>') center no-repeat; background-size:contain;"></div>
			<div class="slide-text">
				<h1 class="slide-heading"><?php echo ae_options('slider_slide6_heading'); ?></h1>
				<h2 class="slide-subheading"><?php echo ae_options('slider_slide6_subheading'); ?></h2>
			</div>
		</div>
		<div class="c-strip">
			<div class="c-cont c-container-1"><div class="c-circle c-active" data-dtlt="1"></div></div>
			<div class="c-cont c-container-2"><div class="c-circle" data-dtlt="2"></div></div>
			<div class="c-cont c-container-3"><div class="c-circle" data-dtlt="3"></div></div>
			<div class="c-cont c-container-4"><div class="c-circle" data-dtlt="4"></div></div>
			<div class="c-cont c-container-5"><div class="c-circle" data-dtlt="5"></div></div>
			<div class="c-cont c-container-6"><div class="c-circle" data-dtlt="6"></div></div>
		</div>
		<div class="loginContainer">
			<div class="loginBg"></div>
			<a class="login" href="<?php echo home_url('/');?>membership/members-only/">Member Login</a>
		</div>
	</div></div>
<script>
function animateSlide(){$(".ac").toggle("clip",{duration:"350",easing:"swing"},function(){$(this).removeClass("ac");if($(this).next().hasClass("slide")){$(this).next(".slide").toggle("clip",{duration:"350",easing:"swing"}).addClass("ac")}else{$(".slide").first().toggle("clip",{duration:"350",easing:"swing"}).addClass("ac")}});var e=$(".c-circle").filter(".c-active");var t=parseFloat(e.attr("data-dtlt"))+1<=6?parseFloat(e.attr("data-dtlt"))+1:1;var n=$(".c-circle[data-dtlt='"+t+"']");e.removeClass("c-active");n.addClass("c-active")}function overrideSlide(e){var t=$(".slide").eq(e);var n=$(".ac");n.toggle("clip",{duration:"150",easing:"swing"},function(){t.toggle("clip",{duration:"150",easing:"swing"}).addClass("ac")}).removeClass("ac");$(".c-active").removeClass("c-active");$(".c-circle").eq(e).addClass("c-active")}$(document).ready(function(){$(".slide:not(.ac)").hide();var e=setInterval("animateSlide()",parseFloat(<?php echo ae_options('slider_slide_speed'); ?>));$(".c-circle").click(function(){var t=$(this).index(".c-circle");clearInterval(e);e=setInterval("animateSlide()",parseFloat(<?php echo ae_options('slider_slide_speed_after_click'); ?>));overrideSlide(t)})})
</script>
</div>
<?php } else { ?>
<div class="bannerContainerSub">
	<div class="bigBannerSub"></div>
</div>
<?php } ?>



