<?php
/* 
Plugin Name: MSSC Member Management
Version: 1.0
Author: Webgrain
*/

date_default_timezone_set('America/Denver');
wp_enqueue_script(array("common", "post"));
wp_enqueue_style('style', WP_PLUGIN_URL . '/webgrain/css/style.css');

wp_enqueue_script('jquery_validate', WP_PLUGIN_URL . '/webgrain/js/jquery.validate.min.js');
wp_enqueue_script('additional-methods', WP_PLUGIN_URL . '/webgrain/js/additional-methods.min.js');



/*
<---- DISABLE THIS FOR FORM POST TESTING ---->
If a form has been submitted with the PROCESS $_GET var.
If YES then redirect the user to the list page.
This prevents form resubmission.

NOTE: this check for the page "user-handle", which is the quote list page
<---- DISABLE THIS FOR FORM POST TESTING ---->
*/
$pg_page = $_GET['page'];
$pg_process = $_GET['process'];
if(isset($pg_page) && isset($pg_process)) { if($pg_page == 'user-handle' && $pg_process == true) { header('Location: ' . get_settings('siteurl') . '/wp-admin/admin.php?page=user-handle'); } }



/***********************************************/
/************WEBGRAIN OBJECT************/
/***********************************************/
class Webgrain {
	public $tabname = 'MSSC';
	public $tabicon = 'mssc.png';
	public $usertable = 'mssc_company';
	public $subusertable = 'mssc_user';
}

$webgrain = new Webgrain();

add_action('admin_menu', 'user_menu');

function user_menu() {
	global $webgrain;
	add_menu_page($webgrain->tabname, $webgrain->tabname, 'mssc_member_access', 'cm-handle', 'init_order_list', get_settings('siteurl') . '/wp-content/plugins/accounts/' . $webgrain->tabicon, 4);
	add_submenu_page('cm-handle', 'Accounts', 'Accounts', 'mssc_member_access',('cm-handle'), 'init_user_list');
	add_submenu_page('cm-handle', 'Sub Accounts', 'Sub Accounts', 'mssc_member_access','cm-subuser', 'init_subuser_list');
}


/******************************/
/************USER************/
/******************************/
require_once('user.class.php');
require_once('user.list.class.php');
require_once('user.list.table.class.php');
require_once('user.modify.class.php');
function init_user_list() { $userAdmin = new UserList(); }
function get_user_admin_url($submit) { if($submit) { return 'admin.php?page=cm-handle&process=true'; } else { return 'admin.php?page=cm-handle'; } }
function get_user_admin_page() { return 'cm-handle'; }




/***********************************/
/************SUBUSER************/
/***********************************/
require_once('subuser.class.php');
require_once('subuser.list.class.php');
require_once('subuser.list.table.class.php');
require_once('subuser.modify.class.php');
function init_subuser_list() { $subuserAdmin = new SubuserList(); 
}
function get_subuser_admin_url($submit) { if($submit) { return 'admin.php?page=cm-subuser&process=true'; } else { return 'admin.php?page=cm-subuser'; } }
function get_subuser_admin_page() { return 'cm-subuser'; }




/*************************************************/
/************IMAGE MODIFICATION************/
/*************************************************/
function get_file_extension($file_name) { return substr(strrchr($file_name, '.'), 1); }

//Used to resize images
function smart_resize_image($file, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false) {
	if($height <= 0 && $width <= 0) return false;

	# Setting defaults and meta
	$info = getimagesize($file);
	$image = '';
	$final_width = 0;
	$final_height = 0;
	list($width_old, $height_old) = $info;

	# Calculating proportionality
	if($proportional) {
		if($width  == 0) { 
			$factor = $height / $height_old;
		} else if($height == 0) {
			$factor = $width / $width_old; 
		} else { 
			$factor = min($width / $width_old, $height / $height_old);
		}

		$final_width  = round($width_old * $factor);
		$final_height = round($height_old * $factor);
	} else {
			$final_width = ($width <= 0) ? $width_old : $width;
			$final_height = ($height <= 0) ? $height_old : $height;
	}

	# Loading image to memory according to type
	switch($info[2]){
		case IMAGETYPE_GIF: $image = imagecreatefromgif($file); break;
		case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($file); break;
		case IMAGETYPE_PNG: $image = imagecreatefrompng($file); break;
		default: return false;
	}


	# This is the resizing/resampling/transparency-preserving magic
	$image_resized = imagecreatetruecolor($final_width, $final_height);
	if(($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
		$transparency = imagecolortransparent($image);

		if($transparency >= 0) {
			$transparent_color = imagecolorsforindex($image, $trnprt_indx);
			$transparency = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
			imagefill($image_resized, 0, 0, $transparency);
			imagecolortransparent($image_resized, $transparency);
		} elseif ($info[2] == IMAGETYPE_PNG) {
			imagealphablending($image_resized, false);
			$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
			imagefill($image_resized, 0, 0, $color);
			imagesavealpha($image_resized, true);
		}
	}
	imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);

	# Taking care of original, if needed
	if($delete_original) {
		if($use_linux_commands) { exec('rm '. $file); } else { @unlink($file); }
	}

	# Preparing a method of providing result
	switch(strtolower($output)){
		case 'browser': $mime = image_type_to_mime_type($info[2]); header("Content-type: $mime"); $output = NULL; break;
		case 'file': $output = $file; break;
		case 'return': return $image_resized; break;
		default: break;
	}

	# Writing image according to type to the output destination
	switch($info[2]){
		case IMAGETYPE_GIF: imagegif($image_resized, $output); break;
		case IMAGETYPE_JPEG: imagejpeg($image_resized, $output); break;
		case IMAGETYPE_PNG: imagepng($image_resized, $output); break;
		default: return false;
	}

	return true;
}


/*************************************************/
/**************STATE SELECTION**************/
/*************************************************/
function get_state_select($curr_state) {
	$states = array('', 'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming');
	$option = null;
	foreach($states as $state) { if(empty($state)) { $option .= '<option value="0"></option>'; } else { if($state == $curr_state) { $option .= '<option value="'. $state . '" selected="selected">' . $state . '</option>'; } else { $option .= '<option value="'. $state . '">' . $state . '</option>'; } } }
	return $option;
}



/**********************************/
/************TinyMCE************/
/**********************************/
add_action('admin_print_scripts', 'do_jslibs' );
add_action('admin_print_styles', 'do_css' );
function do_css() { wp_enqueue_style('thickbox'); }
function do_jslibs() { wp_enqueue_script('editor'); wp_enqueue_script('thickbox'); wp_enqueue_script('media-upload'); add_action( 'admin_head', 'wp_tiny_mce' ); }

?>