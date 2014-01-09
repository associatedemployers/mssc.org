<?php
	global $wpdb; 
	global $wp_query; 
	session_start();

	//mail('nick@webgrain.net', 'test', $_POST['mssctoken'] . ' ' . $_POST['clientip']);

	if(isset($_POST['mssctoken']) && isset($_POST['clientip'])) {

		$mssctoken = $_POST['mssctoken'];
		$clientip = $_POST['clientip'];

		$success = '';
		$sql = "SELECT * FROM mssc_sso WHERE token = '{$mssctoken}' AND ip = '{$clientip}' ";
		$result = $wpdb->get_results($wpdb->prepare($sql, 0));
		if(!empty($result)) { $success = $result[0]; }

		if($success) {
			$company_id = $success->company_id;
			$sql = "SELECT * FROM mssc_company WHERE id = {$company_id}";
			$result = $wpdb->get_results($wpdb->prepare($sql, 0));

			if($result) { 
				/*
				if(empty($result[0]->address)) { $result[0]->address = '0'; }
				if(empty($result[0]->address2)) { $result[0]->address2 = '0'; }
				if(empty($result[0]->city)) { $result[0]->city = '0'; }
				if(empty($result[0]->city)) { $result[0]->city = 'Montana'; }
				if(empty($result[0]->zip)) { $result[0]->zip = '0'; }
				*/
				$xml = '<Users>
					<User id="' . $company_id . '">
						<Email>' . $result[0]->email . '</Email>
						<Company>' . htmlentities($result[0]->company) . '</Company>
						<FirstName>' . $result[0]->first_name . '</FirstName>
						<LastName>' . $result[0]->last_name . '</LastName>
						<Address1>' . $result[0]->address . '</Address1>
						<Address2>' . $result[0]->address2 . '</Address2>
						<City>' . $result[0]->city . '</City>
						<State>' . get_state_by_fullname($result[0]->state) . '</State>
						<ZIP>' . $result[0]->zip . '</ZIP>
					</User>
				</Users>';
				header("Content-type: text/xml");
				echo $xml;
				exit();
			}
		}
	}

?>