<?php /* Template Name: Page */ ?>
<?php get_header(); ?>

<div class="container_12 content page <?php if(is_page('about')) { echo 'ghost'; } ?>">

	<div class="grid_12">
		<div class="loginContainer">
			<div class="loginBg"></div>
			<a class="login" href="<?php echo home_url('/');?>membership/">Member Login</a>
		</div>
		<?php if (have_posts()) : while (have_posts()) : the_post(); the_title('<h1>', '</h1>'); echo '<strong>'; the_date('F, j, Y'); echo '</strong>';  the_content(); endwhile; endif; ?>
	</div>

	<div class="push clear"></div>
</div>

<div class="footerBgContainer">
	<div class="footerBg"></div>
</div>

<?php get_footer(); ?>