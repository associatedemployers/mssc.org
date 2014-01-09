<?php
class User {
/*****************************************************/
/*********************GET USERS*******************/
/*****************************************************/
function get_users() {
	global $wpdb;
	global $webgrain;
	$userArray = array();
	$sql = "SELECT * FROM " . $webgrain->usertable . " ORDER BY last_name, first_name ASC";

	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	if(!empty($result)) { foreach($result as $r) { $userArray[$r->id] = $r; } }
	return $userArray;
}


/**********************************************************/
/*********************GET USER BY ID******************/
/**********************************************************/
function get_user_by_id($id) {
	global $wpdb;
	global $webgrain;
	$sql = "SELECT * FROM " . $webgrain->usertable . " WHERE id = $id";
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	if(!empty($result)) { foreach($result as $r) { return $r; } }
}


/****************************************************/
/*********************SAVE USER******************/
/****************************************************/
function save_user($values) {
	global $wpdb;
	global $webgrain;
	$id =										$values['id'];
	$company =							$values['company'];
	$address =							$values['address'];
	$address2 =							$values['address2'];
	$city =									$values['city'];
	$state =								$values['state'];
	$zip =									$values['zip'];
	$phone =								$values['phone'];
	$fax =									$values['fax'];
	$first_name =						$values['first_name'];
	$last_name =							$values['last_name'];
	$email =								$values['email'];
	$password =							$values['password_wgusr'];
	$active =								$values['active'];


	$pswd = "";
	$pswd_table = "";
	$pswd_data = "";
	if($active == 'on') { $active = 0; } else { $active = 1; } // 0 - active account | 1 - inactive account


	//Setup data for database insert
	if($id) {
		if(isset($password)) { $pswd = " password = MD5('$password'), "; }

		$sql = "UPDATE " . $webgrain->usertable . " SET 
			company = '$company', address = '$address', address2 = '$address2', city = '$city', state = '$state', zip = '$zip', 
			phone = '$phone', fax = '$fax', first_name = '$first_name', last_name = '$last_name', email = '$email', 
			$pswd active = $active
			WHERE id = $id";

		$wpdb->query($wpdb->prepare($sql, 0));
	} else {
		if(isset($password)) { 
			$pswd_table = ", password"; 
			$pswd_data = ", MD5('$password')"; 
		}

		$sql = "INSERT INTO " . $webgrain->usertable . " 
			(company, address, address2, city, state, zip, phone, fax, first_name, last_name, email, active $pswd_table) 
			VALUES 
			('$company', '$address', '$address2', '$city', '$state', '$zip', '$phone', '$fax', '$first_name', '$last_name', '$email', $active $pswd_data)";

		$wpdb->query($wpdb->prepare($sql, 0));
		$id = $wpdb->insert_id;
	}
}


/*****************************************************/
/*********************DELETE USER****************/
/*****************************************************/
function delete_user($id, $brd) {
	global $wpdb;
	global $webgrain;

	$sql = "DELETE FROM " . $webgrain->usertable . " WHERE id = '$id'";
	if($id) { $wpdb->query($wpdb->prepare($sql)); } //Removes the user
}


}
?>