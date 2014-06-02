<?php

function mssc_customize_register( $wp_customize ) {
	//controls for theme (like the slider) C:
	$wp_customize->add_section(
		'feed_section',
		array(
			'title' => __( 'RSS Feed Settings', 'associatedemployers' ),
			'capability' => 'edit_theme_options',
			'description' => __( 'Edit the rss ticker settings', 'associatedemployers' )
			)
	);
	$wp_customize->add_setting(
		'ae_options[rss_feed_link1]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[rss_feed_link1]',
		array(
			'label' => 'RSS Feed Link 1',
			'section' => 'feed_section',
			'type' => 'text',
			'priority' => '1'
			)
	);
	$wp_customize->add_setting(
		'ae_options[rss_feed_link2]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[rss_feed_link2]',
		array(
			'label' => 'RSS Feed Link 2',
			'section' => 'feed_section',
			'type' => 'text',
			'priority' => '2'
			)
	);
	$wp_customize->add_setting(
		'ae_options[rss_feed_link3]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[rss_feed_link3]',
		array(
			'label' => 'RSS Feed Link 3',
			'section' => 'feed_section',
			'type' => 'text',
			'priority' => '3'
			)
	);
	
	$wp_customize->add_section(
		'slider_section',
		array(
			'title' => __( 'Slider Content', 'associatedemployers' ),
			'capability' => 'edit_theme_options',
			'description' => __( 'Edit the slider content', 'associatedemployers' )
			)
	);
	//SLIDE 1
	$wp_customize->add_setting(
		'ae_options[slider_slide1_link]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide1_link]',
		array(
			'label' => 'Slide 1 Link (e.g. http://google.com)',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '3'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide1_heading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide1_heading]',
		array(
			'label' => 'Slide 1 Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '4'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide1_subheading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide1_subheading]',
		array(
			'label' => 'Slide 1 Sub-Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '5'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide1_image]',
		array(
			'default' => 'image.jpg',
			'capability' => 'edit_theme_options',
			'type' => 'option'
			)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control($wp_customize, 'slider_slide1_image',
		array(
			'label' => __('Slide 1 Image', 'associatedemployers'),
			'section' => 'slider_section',
			'settings' => 'ae_options[slider_slide1_image]',
			'priority' => '6'
			)
		)
	);
	//SLIDE 2
	$wp_customize->add_setting(
		'ae_options[slider_slide2_link]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide2_link]',
		array(
			'label' => 'Slide 2 Link (e.g. http://google.com)',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '7'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide2_heading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide2_heading]',
		array(
			'label' => 'Slide 2 Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '8'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide2_subheading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide2_subheading]',
		array(
			'label' => 'Slide 2 Sub-Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '9'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide2_image]',
		array(
			'default' => 'image.jpg',
			'capability' => 'edit_theme_options',
			'type' => 'option'
			)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control($wp_customize, 'slider_slide2_image',
		array(
			'label' => __('Slide 2 Image', 'associatedemployers'),
			'section' => 'slider_section',
			'settings' => 'ae_options[slider_slide2_image]',
			'priority' => '10'
			)
		)
	);
	//SLIDE 3
	$wp_customize->add_setting(
		'ae_options[slider_slide3_link]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide3_link]',
		array(
			'label' => 'Slide 3 Link (e.g. http://google.com)',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '11'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide3_heading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide3_heading]',
		array(
			'label' => 'Slide 3 Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '12'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide3_subheading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide3_subheading]',
		array(
			'label' => 'Slide 3 Sub-Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '13'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide3_image]',
		array(
			'default' => 'image.jpg',
			'capability' => 'edit_theme_options',
			'type' => 'option'
			)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control($wp_customize, 'slider_slide3_image',
		array(
			'label' => __('Slide 3 Image', 'associatedemployers'),
			'section' => 'slider_section',
			'settings' => 'ae_options[slider_slide3_image]',
			'priority' => '14'
			)
		)
	);
	//SLIDE 4
	$wp_customize->add_setting(
		'ae_options[slider_slide4_link]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide4_link]',
		array(
			'label' => 'Slide 4 Link (e.g. http://google.com)',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '15'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide4_heading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide4_heading]',
		array(
			'label' => 'Slide 4 Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '16'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide4_subheading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide4_subheading]',
		array(
			'label' => 'Slide 4 Sub-Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '17'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide4_image]',
		array(
			'default' => 'image.jpg',
			'capability' => 'edit_theme_options',
			'type' => 'option'
			)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control($wp_customize, 'slider_slide4_image',
		array(
			'label' => __('Slide 4 Image', 'associatedemployers'),
			'section' => 'slider_section',
			'settings' => 'ae_options[slider_slide4_image]',
			'priority' => '18'
			)
		)
	);
	//SLIDE 5
	$wp_customize->add_setting(
		'ae_options[slider_slide5_link]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide5_link]',
		array(
			'label' => 'Slide 5 Link (e.g. http://google.com)',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '19'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide5_heading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide5_heading]',
		array(
			'label' => 'Slide 5 Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '20'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide5_subheading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide5_subheading]',
		array(
			'label' => 'Slide 5 Sub-Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '21'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide5_image]',
		array(
			'default' => 'image.jpg',
			'capability' => 'edit_theme_options',
			'type' => 'option'
			)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control($wp_customize, 'slider_slide5_image',
		array(
			'label' => __('Slide 5 Image', 'associatedemployers'),
			'section' => 'slider_section',
			'settings' => 'ae_options[slider_slide5_image]',
			'priority' => '22'
			)
		)
	);
	//SLIDE 6
	$wp_customize->add_setting(
		'ae_options[slider_slide6_link]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide6_link]',
		array(
			'label' => 'Slide 6 Link (e.g. http://google.com)',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '23'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide6_heading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide6_heading]',
		array(
			'label' => 'Slide 6 Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '24'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide6_subheading]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => ''
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide6_subheading]',
		array(
			'label' => 'Slide 6 Sub-Heading',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '25'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide6_image]',
		array(
			'default' => 'image.jpg',
			'capability' => 'edit_theme_options',
			'type' => 'option'
			)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control($wp_customize, 'slider_slide6_image',
		array(
			'label' => __('Slide 6 Image', 'associatedemployers'),
			'section' => 'slider_section',
			'settings' => 'ae_options[slider_slide6_image]',
			'priority' => '26'
			)
		)
	);
	//SLIDE CONTROL
	$wp_customize->add_setting(
		'ae_options[slider_slide_speed]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => '7000'
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide_speed]',
		array(
			'label' => 'Slide Speed in Milliseconds',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '27'
			)
	);
	$wp_customize->add_setting(
		'ae_options[slider_slide_speed_after_click]',
		array(
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'default' => '10000'
		)
	);
	$wp_customize->add_control(
		'ae_options[slider_slide_speed_after_click]',
		array(
			'label' => 'Slide Speed after click in Milliseconds',
			'section' => 'slider_section',
			'type' => 'text',
			'priority' => '28'
			)
	);
}
add_action( 'customize_register', 'mssc_customize_register' );

function ae_options($name, $default = false) {
	$options = ( get_option( 'ae_options' ) ) ? get_option( 'ae_options' ) : null; // return the option if it exists
	if(isset( $options[ $name ] )) {
		return apply_filters( 'ae_options_$name', $options[ $name ] );
	}
	return apply_filters( 'ae_options_$name', $default );
}

add_filter( 'rewrite_rules_array','my_insert_rewrite_rules' );
add_filter( 'query_vars','my_insert_query_vars' );
add_action( 'wp_loaded','my_flush_rules' );

function my_flush_rules(){ $rules = get_option('rewrite_rules'); if(!isset( $rules['(membership/members-only/add-sub-accounts)/(\d*)$'])) { global $wp_rewrite; $wp_rewrite->flush_rules(); } }
function my_insert_rewrite_rules($rules) { $newrules = array();  $newrules['(membership/members-only/add-sub-accounts)/(\d*)$'] = 'index.php?pagename=$matches[1]&user_edit=$matches[2]';  return $newrules + $rules; }
function my_insert_query_vars( $vars ) { array_push($vars, 'user_edit'); return $vars; }

function unique_id($l = 6) { return substr(md5(uniqid(mt_rand(), true)), 0, $l); }

//get attribute from html tag
function getAttribute($attrib, $tag){ $re = '/' . preg_quote($attrib) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is'; if (preg_match($re, $tag, $match)) { return $match[2]; } return false; }



function content($limit) { 
$content = explode(' ', get_the_content()); 
$content = implode(" ", $content); 
$content = preg_replace('/<div (.*?)>(.*?)<\/div>/si','',$content); 
$content = preg_replace('/(\[.*\])/','',$content); 
$content = preg_replace('/<img[^>]+\>/i', '', $content); 
$content = preg_replace('/<iframe (.*?)>(.*?)<\/iframe>/si','',$content); 
//$content = iconv('utf-8', 'ascii//TRANSLIT', $content); 
$content = trim($content); 
$content = explode(' ', $content, $limit); 
array_pop($content); 
$content = implode(" ", $content); 
$content = apply_filters('the_content', $content); 
$content = trim(strip_tags($content)) . '&hellip; '; 
return $content;
}



function guid(){
	mt_srand((double)microtime()*10000);
	$charid = strtoupper(md5(uniqid(rand(), true)));
	$hyphen = chr(45);// "-"
	$uuid = chr(123)// "{"
			.substr($charid, 0, 8).$hyphen
			.substr($charid, 8, 4).$hyphen
			.substr($charid,12, 4).$hyphen
			.substr($charid,16, 4).$hyphen
			.substr($charid,20,12)
			.chr(125);// "}"
	return $uuid;
}












///////////////////////////////
//Create the main navigation
///////////////////////////////
add_action('init', 'main_menu');
function main_menu() {
	register_nav_menu('main-menu', __('Main Menu'));
	register_nav_menu('footer-menu', __('Footer Menu'));
}


if(function_exists('register_sidebar')) { register_sidebar(array('name' => __('Sidebar Widget Area'), 'id' => 'sidebarwidgetarea', 'description' => __('Widget container for the Sidebar', 'twentyeleven'))); }


add_action( 'init', 'register_shortcodes');
function register_shortcodes(){
	add_shortcode('clearfix', 'clearfix');
	add_shortcode('hr', 'hr');
	add_shortcode('ghost', 'ghost');
}
function clearfix() {
	return '<div class="clearfix"></div>';
}
function ghost() {
	return '<div class="ghost"></div>';
}
function hr() {
	return '<hr />';
}



function company_info_by_id($id) {
	global $wpdb;
	global $webgrain;
	$sql = "SELECT company, address, address2, city, state, zip FROM " . $webgrain->usertable . " WHERE id = $id";
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	if(!empty($result)) { foreach($result as $r) { return $r; } }
}



function get_state_by_fullname($stateName) {
	$states = array(array('name'=>'Alabama', 'abbrev'=>'AL'), array('name'=>'Alaska', 'abbrev'=>'AK'), array('name'=>'Arizona', 'abbrev'=>'AZ'), array('name'=>'Arkansas', 'abbrev'=>'AR'), array('name'=>'California', 'abbrev'=>'CA'), array('name'=>'Colorado', 'abbrev'=>'CO'), array('name'=>'Connecticut', 'abbrev'=>'CT'), array('name'=>'Delaware', 'abbrev'=>'DE'), array('name'=>'Florida', 'abbrev'=>'FL'), array('name'=>'Georgia', 'abbrev'=>'GA'), array('name'=>'Hawaii', 'abbrev'=>'HI'), array('name'=>'Idaho', 'abbrev'=>'ID'), array('name'=>'Illinois', 'abbrev'=>'IL'), array('name'=>'Indiana', 'abbrev'=>'IN'), array('name'=>'Iowa', 'abbrev'=>'IA'), array('name'=>'Kansas', 'abbrev'=>'KS'), array('name'=>'Kentucky', 'abbrev'=>'KY'), array('name'=>'Louisiana', 'abbrev'=>'LA'), array('name'=>'Maine', 'abbrev'=>'ME'), array('name'=>'Maryland', 'abbrev'=>'MD'), array('name'=>'Massachusetts', 'abbrev'=>'MA'), array('name'=>'Michigan', 'abbrev'=>'MI'), array('name'=>'Minnesota', 'abbrev'=>'MN'), array('name'=>'Mississippi', 'abbrev'=>'MS'), array('name'=>'Missouri', 'abbrev'=>'MO'), array('name'=>'Montana', 'abbrev'=>'MT'), array('name'=>'Nebraska', 'abbrev'=>'NE'), array('name'=>'Nevada', 'abbrev'=>'NV'), array('name'=>'New Hampshire', 'abbrev'=>'NH'), array('name'=>'New Jersey', 'abbrev'=>'NJ'), array('name'=>'New Mexico', 'abbrev'=>'NM'), array('name'=>'New York', 'abbrev'=>'NY'), array('name'=>'North Carolina', 'abbrev'=>'NC'), array('name'=>'North Dakota', 'abbrev'=>'ND'), array('name'=>'Ohio', 'abbrev'=>'OH'), array('name'=>'Oklahoma', 'abbrev'=>'OK'), array('name'=>'Oregon', 'abbrev'=>'OR'), array('name'=>'Pennsylvania', 'abbrev'=>'PA'), array('name'=>'Rhode Island', 'abbrev'=>'RI'), array('name'=>'South Carolina', 'abbrev'=>'SC'), array('name'=>'South Dakota', 'abbrev'=>'SD'), array('name'=>'Tennessee', 'abbrev'=>'TN'), array('name'=>'Texas', 'abbrev'=>'TX'), array('name'=>'Utah', 'abbrev'=>'UT'), array('name'=>'Vermont', 'abbrev'=>'VT'), array('name'=>'Virginia', 'abbrev'=>'VA'), array('name'=>'Washington', 'abbrev'=>'WA'), array('name'=>'West Virginia', 'abbrev'=>'WV'), array('name'=>'Wisconsin', 'abbrev'=>'WI'), array('name'=>'Wyoming', 'abbrev'=>'WY'));
	foreach($states as $state) { if($state['name'] == $stateName) { return $state['abbrev']; } }
}







///////////////////////////////////////////////
///////////////////////////////////////////////
// EDIT PROFILE
///////////////////////////////////////////////
///////////////////////////////////////////////

//Edit Profile - Company
add_filter("gform_field_value_f3_company", "ep_company", 10, 3);
function ep_company($value){  global $wpdb; global $wp_query; session_start();  $sql = "SELECT company FROM mssc_company WHERE id = " . $_SESSION['comp_id'];  $result = $wpdb->get_results($wpdb->prepare($sql, 0));  if($result) { return $result[0]->company; } }

//Edit Profile - Address
add_filter("gform_field_value_f3_address", "ep_address", 10, 3);
function ep_address($value){ global $wpdb; session_start(); $sql = "SELECT address FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->address; } }

//Edit Profile - Address2
add_filter("gform_field_value_f3_address2", "ep_address2", 10, 3);
function ep_address2($value){ global $wpdb; session_start(); $sql = "SELECT address2 FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->address2; } }

//Edit Profile - City
add_filter("gform_field_value_f3_city", "ep_city", 10, 3);
function ep_city($value){ global $wpdb; session_start(); $sql = "SELECT city FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->city; } }

//Edit Profile - State
add_filter("gform_field_value_f3_state", "f3_state", 10, 3);
function f3_state($value){ global $wpdb; global $member_database; session_start(); $sql = "SELECT state FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->state; } }

//Edit Profile - Zip
add_filter("gform_field_value_f3_zip", "ep_zip", 10, 3);
function ep_zip($value){ global $wpdb; session_start(); $sql = "SELECT zip FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->zip; } }

//Edit Profile - First Name
add_filter("gform_field_value_f3_first_name", "ep_first_name", 10, 3);
function ep_first_name($value){ global $wpdb; session_start(); $sql = "SELECT first_name FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->first_name; } }

//Edit Profile - Last Name
add_filter("gform_field_value_f3_last_name", "ep_last_name", 10, 3);
function ep_last_name($value){ global $wpdb; session_start(); $sql = "SELECT last_name FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->last_name; } }

//Edit Profile - Email
add_filter("gform_field_value_f3_email", "ep_email", 10, 3);
function ep_email($value){ global $wpdb; session_start(); $sql = "SELECT email FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->email; } }

//Edit Profile - Phone
add_filter("gform_field_value_f3_phone", "ep_phone", 10, 3);
function ep_phone($value){ global $wpdb; session_start(); $sql = "SELECT phone FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->phone; } }

//Edit Profile - Alt. Phone
add_filter("gform_field_value_f3_alt_phone", "ep_alt_phone", 10, 3);
function ep_alt_phone($value){ global $wpdb; session_start(); $sql = "SELECT phone_alt FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->phone_alt; } }

//Edit Profile - Fax
add_filter("gform_field_value_f3_fax", "ep_fax", 10, 3);
function ep_fax($value){ global $wpdb; session_start(); $sql = "SELECT fax FROM mssc_company WHERE id = " . $_SESSION['comp_id']; $result = $wpdb->get_results($wpdb->prepare($sql, 0)); if($result) { return $result[0]->fax; } }


add_filter("gform_after_submission_5", "profile_after_submission", 10, 3);
function profile_after_submission($entry, $form) {
	global $wpdb; session_start();
	$company = $entry["9"];
	$address = $entry["7.1"];
	$address2 = $entry["7.2"];
	$city = $entry["7.3"];
	$state = $entry["7.4"];
	$zip = $entry["7.5"];
	$first_name = $entry["1.3"];
	$last_name = $entry["1.6"];
	$email = $entry["2"];
	$phone = $entry["4"];
	$phone_alt = $entry["5"];
	$fax = $entry["6"];

	$password = $entry["3"];
	$sql_extra = '';
	if($password) { $sql_extra = ", password = MD5('" . $password . "') "; }

	$sql = "UPDATE mssc_company SET company = '$company', address = '$address', address2 = '$address2', city = '$city', state = '$state', zip = '$zip',
		first_name = '$first_name', last_name = '$last_name', email = '$email', phone = '$phone', phone_alt = '$phone_alt', fax = '$fax' " . $sql_extra . "
		WHERE id = " . $_SESSION['comp_id'];
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
}










///////////////////////////////////////////////
///////////////////////////////////////////////
// EDIT SUB ACCOUNT
///////////////////////////////////////////////
///////////////////////////////////////////////
//Edit Sub Account - First Name
add_filter("gform_field_value_f2_first_name", "es_first_name", 10, 2);
function es_first_name($value){
	global $wpdb; global $wp_query; session_start();
	$id = $wp_query->query_vars['user_edit'];
	if(!$id && $_SESSION['user_type'] == 'sub_account') { $id = $_SESSION['user_id']; }

	if($id) {
		$sql = "SELECT first_name FROM mssc_user WHERE id = " . $id . " AND company_id = " . $_SESSION['comp_id'];
		$result = $wpdb->get_results($wpdb->prepare($sql, 0));
		if($result) { return $result[0]->first_name; }
	}
}

//Edit Sub Account - Last Name
add_filter("gform_field_value_f2_last_name", "es_last_name", 10, 2);
function es_last_name($value){
	global $wpdb; global $wp_query; session_start();
	$id = $wp_query->query_vars['user_edit'];
	if(!$id && $_SESSION['user_type'] == 'sub_account') { $id = $_SESSION['user_id']; }

	if($id) {
		$sql = "SELECT last_name FROM mssc_user WHERE id = " . $id . " AND company_id = " . $_SESSION['comp_id'];
		$result = $wpdb->get_results($wpdb->prepare($sql, 0));
		if($result) { return $result[0]->last_name; }
	}
}

//Edit Sub Account - Email
add_filter("gform_field_value_f2_email", "es_email", 10, 2);
function es_email($value){
	global $wpdb; global $wp_query; session_start();
	$id = $wp_query->query_vars['user_edit'];
	if(!$id && $_SESSION['user_type'] == 'sub_account') { $id = $_SESSION['user_id']; }

	if($id) {
		$sql = "SELECT email FROM mssc_user WHERE id = " . $id . " AND company_id = " . $_SESSION['comp_id'];
		$result = $wpdb->get_results($wpdb->prepare($sql, 0));
		if($result) { return $result[0]->email; }
	}
}

//Edit Sub Account - Phone
add_filter("gform_field_value_f2_phone", "es_phone", 10, 2);
function es_phone($value){
	global $wpdb; global $wp_query; session_start();
	$id = $wp_query->query_vars['user_edit'];
	if(!$id && $_SESSION['user_type'] == 'sub_account') { $id = $_SESSION['user_id']; }

	if($id) {
		$sql = "SELECT phone FROM mssc_user WHERE id = " . $id . " AND company_id = " . $_SESSION['comp_id'];
		$result = $wpdb->get_results($wpdb->prepare($sql, 0));
		if($result) { return $result[0]->phone; }
	}
}

//Edit Sub Account - Alt. Phone
add_filter("gform_field_value_f2_alt_phone", "es_alt_phone", 10, 2);
function es_alt_phone($value){
	global $wpdb; global $wp_query; session_start();
	$id = $wp_query->query_vars['user_edit'];
	if(!$id && $_SESSION['user_type'] == 'sub_account') { $id = $_SESSION['user_id']; }

	if($id) {
		$sql = "SELECT phone_alt FROM mssc_user WHERE id = " . $id . " AND company_id = " . $_SESSION['comp_id'];
		$result = $wpdb->get_results($wpdb->prepare($sql, 0));
		if($result) { return $result[0]->phone_alt; }
	}
}

//Edit Sub Account - Fax
add_filter("gform_field_value_f2_fax", "es_fax", 10, 2);
function es_fax($value){
	global $wpdb; global $wp_query; session_start();
	$id = $wp_query->query_vars['user_edit'];
	if(!$id && $_SESSION['user_type'] == 'sub_account') { $id = $_SESSION['user_id']; }

	if($id) {
		$sql = "SELECT fax FROM mssc_user WHERE id = " . $id . " AND company_id = " . $_SESSION['comp_id'];
		$result = $wpdb->get_results($wpdb->prepare($sql, 0));
		if($result) { return $result[0]->fax; }
	}
}


add_filter('gform_after_submission_6', 'user_after_submission', 10, 2);
function user_after_submission($entry, $form) {
	global $wpdb;
	global $wp_query;
	session_start();
	$id = $wp_query->query_vars['user_edit'];
	if(!$id && $_SESSION['user_type'] == 'sub_account') { $id = $_SESSION['user_id']; }

	$first_name = $entry["1.3"];
	$last_name = $entry["1.6"];
	$email = $entry["2"];
	$phone = $entry["4"];
	$phone_alt = $entry["5"];
	$fax = $entry["6"];
	$password = $entry["3"];
	$sql_extra = '';
	if($password) { $sql_extra = ", password = MD5('" . $password . "') "; }

	if($id) { //UPDATE
		$sql = "UPDATE mssc_user SET first_name = '$first_name', last_name = '$last_name', email = '$email', phone = '$phone', phone_alt = '$phone_alt', fax = '$fax' " . $sql_extra . " WHERE id = " . $id . " AND company_id = " . $_SESSION['comp_id'];
	} else { //NEW
		$sql = "INSERT INTO mssc_user (first_name, last_name, email, phone, phone_alt, fax, password, company_id) VALUES ('$first_name', '$last_name', '$email', '$phone', '$phone_alt', '$fax', MD5('" . $password . "'), " . $_SESSION['comp_id'] . ")";
	}

	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
}



///////////////////////////////////////////////
///////////////////////////////////////////////
// New Member
///////////////////////////////////////////////
///////////////////////////////////////////////

add_filter('gform_after_submission_1', 'newmember_after_submission', 10, 2);
function newmember_after_submission($entry, $form) {
	global $wpdb; session_start();
	$company = $entry["1"];
	$address = $entry["4.1"];
	$address2 = $entry["4.2"];
	$city = $entry["4.3"];
	$state = $entry["4.4"];
	$zip = $entry["4.5"];
	$first_name = $entry["12.3"];
	$last_name = $entry["12.6"];
	$email = $entry["11"];
	$phone = $entry["5"];
	$fax = $entry["6"];

	$password = $entry["3"];

	$sql = "INSERT INTO mssc_company (company, address, address2, city, state, zip, first_name, last_name, email, phone, fax, active) VALUES
	('$company', '$address', '$address2', '$city', '$state', '$zip', '$first_name', '$last_name', '$email', '$phone', '$fax', 1)";
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
}






///////////////////////////////////////////////
///////////////////////////////////////////////
// RENEWALS
///////////////////////////////////////////////
///////////////////////////////////////////////
add_filter("gform_field_value_f4_company_name", "renw_company", 10, 4);
function renw_company($value){
	global $wpdb; global $wp_query; session_start();
	$sql = "SELECT company FROM mssc_company WHERE id = " . $_SESSION['comp_id'];
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	if($result) { return $result[0]->company; }
}




add_filter("gform_submit_button", "form_submit_button", 10, 2);
function form_submit_button($button, $form){
	global $wpdb; global $wp_query;
	$value = getAttribute('value', $button);

	$id = $wp_query->query_vars['user_edit'];
	if($id && is_page('add-sub-account')) { $value = 'Update Account'; }

	return "<button class='button btn btn-success' id='gform_submit_button_{$form["id"]}'><span>{$value}</span></button>";
}





//Hides SSL warning for testing purposes only
add_filter("gform_is_ssl", "set_ssl");
function set_ssl() { return true; }
















//Removes Entries from the Database
add_action('gform_post_submission_5', 'remove_form_entry', 10, 3);
add_action('gform_post_submission_6', 'remove_form_entry', 10, 3);
function remove_form_entry($entry, $form){
	global $wpdb;
	$lead_id = $entry['id'];
	$lead_table = RGFormsModel::get_lead_table_name();
	$lead_notes_table = RGFormsModel::get_lead_notes_table_name();
	$lead_detail_table = RGFormsModel::get_lead_details_table_name();
	$lead_detail_long_table = RGFormsModel::get_lead_details_long_table_name();

	$sql = $wpdb->prepare("DELETE FROM $lead_detail_long_tableWHERE lead_detail_id IN(SELECT id FROM $lead_detail_table WHERE lead_id=%d)", $lead_id);
	//Delete from detail long
	$wpdb->query($sql);
	//Delete from lead details
	$sql = $wpdb->prepare("DELETE FROM $lead_detail_table WHERE lead_id=%d", $lead_id);
	$wpdb->query($sql);
	//Delete from lead notes
	$sql = $wpdb->prepare("DELETE FROM $lead_notes_table WHERE lead_id=%d", $lead_id);
	$wpdb->query($sql);
	//Delete from lead
	$sql = $wpdb->prepare("DELETE FROM $lead_table WHERE id=%d", $lead_id);
	$wpdb->query($sql);
}









?>