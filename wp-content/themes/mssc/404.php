<?php /* Template Name: Page */ ?>
<?php get_header(); ?>

<div class="container_12 content page <?php if(is_page('about')) { echo 'ghost'; } ?>">

	<div class="grid_12">
		<div class="loginContainer">
			<div class="loginBg"></div>
			<a class="login" href="<?php echo home_url('/');?>membership/">Member Login</a>
		</div>
		<h1>404 Error. Page Not Found.</h1>
		<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for.', 'twentyeleven' ); ?></p>
	</div>

	<div class="push clear"></div>
</div>

<div class="footerBgContainer">
	<div class="footerBg"></div>
</div>

<?php get_footer(); ?>