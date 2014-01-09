<ul id="side_widgets">
<?php dynamic_sidebar('Sidebar Widget Area'); ?>
</ul>





<?php if(is_page('home') || is_page('membership')|| is_page('services')) {?>
<div class="sidebarBg"></div>
<div class="loginContainer">
	<div class="loginBg"></div>
	<a class="login" href="#">Member Login</a>
</div>

<div class="upcomingTraining">
	<h3 style="margin-bottom: 30px">Upcoming Training</h3>

	<div class="circleContainer sidebtn">
		<div class="circle firstAid"><a href="<?php echo home_url('/');?>services/upcoming-training-schedule/first-aid-cpr/"><h3>First Aid/CPR</h3></a></div>
		<div class="circle cstop"><a href="<?php echo home_url('/');?>services/upcoming-training-schedule/c-stop-training/"><h3>CSTOP</h3></a></div>
		<div class="circle pit"><a href="<?php echo home_url('/');?>services/upcoming-training-schedule/forklift-train-the-trainer/"><h3>PIT Instructor Certification</h3></a></div>
		<div class="circle osha"><a href="<?php echo home_url('/');?>services/upcoming-training-schedule/osha-10-c-stop-8-combo-class/"><h3>OSHA 10 / C-Stop Initial Combo</h3></a></div>
		<div class="circle cbt"><a href="<?php echo home_url('/');?>services/upcoming-training-schedule/computer-based-training/"><h3>Computer Based Training</h3></a></div>
		<div class="circle osha"><a href="<?php echo home_url('/');?>services/upcoming-training-schedule/ghs-training//"><h3>Globally Harmonized System Training</h3></a></div>
	</div>
</div>


<a href="<?php echo home_url('/');?>safety-conference/"><img src="<?php echo get_bloginfo('template_directory') ?>/img/safety-conference.jpg" /></a>

<?php if(!is_page('services')) { ?>
<div class="isnet">
	<div class="bg"></div>
	<div class="isnLogo"></div>
	<h4><a href="<?php echo home_url('/');?>comply-with-isnetworld/">Comply with ISTNETworld</a></h4>
	<p><a class="more" href="<?php echo home_url('/');?>comply-with-isnetworld/">read more</a></p>
</div>
<?php } ?>
<?php } ?>