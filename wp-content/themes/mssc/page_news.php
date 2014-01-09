<?php /* Template Name: News w/ Sidebar */ ?>
<?php get_header(); ?>

<div class="container_12 content page wsidebar" style="padding-bottom: 120px;" >
	<div class="grid_8">
		<div class="loginContainer">
			<div class="loginBg"></div>
			<a class="login" href="<?php echo home_url('/');?>membership/">Member Login</a>
		</div>
		<div style="padding-right: 25px;">
		<?php if (have_posts()) : while (have_posts()) : the_post(); the_title('<h1>', '</h1>'); the_content(); endwhile; endif; ?>

			<?php query_posts('orderby=date'); ?>
			<?php while (have_posts()) : the_post(); ?>
			<div class="blog_post">
				<div class="blog_copy">
					<div class="blog_title"><strong><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></strong></div>
					<div class="blog_date"><strong><?php the_date('F, j, Y'); ?></strong></div>
					<div class="blog_text"><?php echo content(50, get_the_content()); ?></div>
					<a class="blog_link button" href="<?php the_permalink() ?>">read more ></a>
				</div>
			</div><div class="clear"></div>
			<?php endwhile;?>
		</div>
	</div>

	<div id="sidebar" class="grid_4" style="padding-top: 150px;"><?php get_sidebar(); ?></div>
	<div class="push clear"></div>
</div>


<div class="footerBgContainer">
	<div class="footerBg"></div>
</div>


<?php get_footer(); ?>