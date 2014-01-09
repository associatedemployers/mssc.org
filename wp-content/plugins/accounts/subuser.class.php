<?php
class Subuser {
/**********************************************************/
/**********************************************************/
/*********************GET SUBUSERS*******************/
function get_subusers() {
	global $wpdb;
	global $webgrain;
	$subuserArray = array();
	$sql = "SELECT * FROM " . $webgrain->subusertable . " ORDER BY last_name, first_name ASC";

	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	if(!empty($result)) { foreach($result as $r) { $subuserArray[$r->id] = $r; } }
	return $subuserArray;
}


/***************************************************************/
/*********************GET SUBUSER BY ID******************/
/***************************************************************/
function get_subuser_by_id($id) {
	global $wpdb;
	global $webgrain;
	$sql = "SELECT * FROM " . $webgrain->subusertable . " WHERE id = $id";
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	if(!empty($result)) { foreach($result as $r) { return $r; } }
}



/******************************************************************/
/*********************GET COMPANY SELECT******************/
/******************************************************************/
function get_company_select($comp_id) {
	global $wpdb;
	global $webgrain;
	$sql = "SELECT id, company FROM " . $webgrain->usertable . " ORDER BY company ASC";
	$companies = $wpdb->get_results($wpdb->prepare($sql, 0));
	$option = '<option value="0"></option>'; 

	foreach($companies as $company) { 
		if($company->id == $comp_id) { 
			$option .= '<option value="'. $company->id . '" selected="selected">' . $company->company . '</option>'; } else { $option .= '<option value="'. $company->id . '">' . $company->company . '</option>';
		}
	}

	return $option;
}




/*********************************************************/
/*********************SAVE SUBUSER******************/
/*********************************************************/
function save_subuser($values) {
	global $wpdb;
	global $webgrain;
	$id =										$values['id'];
	$company_id =						$values['company'];
	$phone =								$values['phone'];
	$fax =									$values['fax'];
	$first_name =						$values['first_name'];
	$last_name =							$values['last_name'];
	$email =								$values['email'];
	$password =							$values['password_wgsubusr'];
	$active =								$values['active'];


	$pswd = "";
	$pswd_table = "";
	$pswd_data = "";
	if($active == 'on') { $active = 0; } else { $active = 1; } // 0 - active account | 1 - inactive account


	//Setup data for database insert
	if($id) {
		if(isset($password)) { $pswd = " password = MD5('$password'), "; }

		$sql = "UPDATE " . $webgrain->subusertable . " SET company_id = '$company_id', phone = '$phone', fax = '$fax', first_name = '$first_name', last_name = '$last_name', email = '$email', $pswd active = $active WHERE id = $id";

		$wpdb->query($wpdb->prepare($sql, 0));
	} else {
		if(isset($password)) { 
			$pswd_table = ", password"; 
			$pswd_data = ", MD5('$password')"; 
		}

		$sql = "INSERT INTO " . $webgrain->subusertable . " (company_id, phone, fax, first_name, last_name, email, active $pswd_table) VALUES ('$company_id', '$phone', '$fax', '$first_name', '$last_name', '$email', $active $pswd_data)";

		$wpdb->query($wpdb->prepare($sql, 0));
		$id = $wpdb->insert_id;
	}
}


/**********************************************************/
/*********************DELETE SUBUSER****************/
/**********************************************************/
function delete_subuser($id, $brd) {
	global $wpdb;
	global $webgrain;

	$sql = "DELETE FROM " . $webgrain->subusertable . " WHERE id = '$id'";
	if($id) { $wpdb->query($wpdb->prepare($sql)); } //Removes the subuser
}


}
?>