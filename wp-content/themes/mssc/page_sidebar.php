<?php /* Template Name: Page w/ Sidebar */ ?>
<?php get_header(); ?>

<?php if(is_page('membership') || is_page('services')) { ?>
<div class="container_12 content page wsidebar"  <?php if(is_page('services')) { echo 'style="min-height: 720px;" '; } ?>>
<?php } else { ?>
<div class="container_12 content page wsidebar" style="padding-bottom: 120px;" >
<?php }  ?>
	<div class="grid_8">
		<div class="loginContainer">
			<div class="loginBg"></div>
			<a class="login" href="<?php echo home_url('/');?>membership/">Member Login</a>
		</div>
		<div style="padding-right: 25px;"><?php if (have_posts()) : while (have_posts()) : the_post(); the_title('<h1>', '</h1>'); the_content(); endwhile; endif; ?></div>
	</div>

	<?php if(is_page('membership') || is_page('services')) { ?>
	<div id="sidebar" class="grid_4"><?php get_sidebar(); ?></div>
	<?php } else { ?>
	<div id="sidebar" class="grid_4" style="padding-top: 150px;"><?php get_sidebar(); ?></div>
	<?php }  ?>
	<div class="push clear"></div>
</div>


<div class="footerBgContainer">
	<div class="footerBg"></div>
</div>


<?php get_footer(); ?>