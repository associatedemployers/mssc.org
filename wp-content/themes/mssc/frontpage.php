<?php /* Template Name: Frontpage */ ?>
<?php get_header(); ?>

<div class="container_12 content frontpage">
	<div class="grid_8">
		<div class="loginContainer">
			<div class="loginBg"></div>
			<a class="login" href="<?php echo home_url('/');?>membership/">Member Login</a>
		</div>
		<?php if (have_posts()) : while (have_posts()) : the_post(); the_content(); endwhile; endif; ?>

		<div class="latestContainer">
			<div class="latest">
				<?php $query = query_posts('posts_per_page=1'); while (have_posts()) : the_post(); ?>
				<h3><a href="<?php echo home_url('/');?>about/news/">The Latest</a></h3>
				<p class="date"><?php the_date('F, j, Y'); ?></p>
				<p><?php echo content(18, get_the_content()); ?> <a class="more" href="<?php the_permalink() ?>">Read more</a></p>
				<?php endwhile;?><?php wp_reset_query(); ?>
			</div>
		</div>


		<div class="circleContainer">
			<div class="circle training"><a href="<?php echo home_url('/');?>services/customized-training/"><h3>Training</h3><p>MSSC offers OSHA and best practices classes.  Customized company specific classes are also available.</p></a></div>
			<div class="circle consulting"><a href="<?php echo home_url('/');?>services/"><h3>Consulting</h3><p>We can help with safety related issues and provide you with practical solutions.</p></a></div>
			<div class="circle compliance"><a href="<?php echo home_url('/');?>services/comply-with-isnetworld/"><h3>Compliance</h3><p>MSSC provides business owners & subcontractors assistance with compliance issues with the government or host employer requirements.</p></a></div>
			<div class="clear"></div>
		</div><div class="clear"></div>

		<div class="customizedTraining">
			<div class="safetyImage"></div>
			<h2>Need Customized Training?</h2>
			<p>All companies need training for regulatory compliance, employee development and team building.  Although training can be a complicated process, MSSC can help simplify it.  We provide training that is specifically tailored to your company's needs.  Training can be provided in our training rooms or onsite.</p>
		</div>
	</div>

	<div id="sidebar" class="grid_4"><?php get_sidebar(); ?></div>

	<div class="push clear"></div>
</div>

<div class="footerBgContainer">

	<div class="footerBg"></div>
</div>

<?php get_footer(); ?>