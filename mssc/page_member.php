<?php /* Template Name: Member */ ?>
<?php
	session_start(); 
	if(isset($_POST['logout'])) { session_destroy(); echo "<meta http-equiv='refresh' content='0;url=" . home_url("/") . "membership/members-only/' />"; exit; }
	if(isset($_POST['home'])) { echo "<meta http-equiv='refresh' content='0;url=" . home_url("/") . "membership/members-only/' />"; exit; }

	$logged_in = false;
	$error = false;
	if(!empty($_POST['email_recover'])) {
		$found_email = false;
		$notfound = false; 
		$email = $_POST['email_recover'];
		$sql = "SELECT id, email FROM mssc_company WHERE (email = '$email' AND active = 0) OR (old_login = '$email' AND active = 0)";
		$result = $wpdb->get_results($wpdb->prepare($sql, 0));

		if(count($result) == 1) { 
			$email = $result[0]->email;
			$found_email = true;
			$password = unique_id();
			$sql = "UPDATE mssc_company SET password = MD5('$password') WHERE id = " . $result[0]->id;
			$wpdb->get_results($wpdb->prepare($sql, 0));
		} else { 
			$sql = "SELECT id FROM mssc_user WHERE email = '$email' AND active = 0";
			$result = $wpdb->get_results($wpdb->prepare($sql, 0));
			if(count($result) == 1) {
				$found_email = true;
				$password = unique_id();
				$sql = "UPDATE mssc_company SET password = MD5('$password') WHERE id = " . $result[0]->id;
				$wpdb->get_results($wpdb->prepare($sql, 0));
			}
		}


		if($found_email) {
			$message = '<html><head><title></title><meta name="viewport" content="width=320, target-densitydpi=device-dpi"><style type="text/css">/* Mobile-specific Styles */@media only screen and (max-width: 660px){table[class=w0], td[class=w0]{width: 0 !important;}table[class=w10], td[class=w10], img[class=w10]{width:10px !important;}table[class=w15], td[class=w15], img[class=w15]{width:5px !important;}table[class=w30], td[class=w30], img[class=w30]{width:10px !important;}table[class=w60], td[class=w60], img[class=w60]{width:10px !important;}table[class=w125], td[class=w125], img[class=w125]{width:80px !important;}table[class=w130], td[class=w130], img[class=w130]{width:55px !important;}table[class=w140], td[class=w140], img[class=w140]{width:90px !important;}table[class=w160], td[class=w160], img[class=w160]{width:180px !important;}table[class=w170], td[class=w170], img[class=w170]{width:100px !important;}table[class=w180], td[class=w180], img[class=w180]{width:80px !important;}table[class=w195], td[class=w195], img[class=w195]{width:80px !important;}table[class=w220], td[class=w220], img[class=w220]{width:80px !important;}table[class=w240], td[class=w240], img[class=w240]{width:180px !important;}table[class=w255], td[class=w255], img[class=w255]{width:185px !important;}table[class=w275], td[class=w275], img[class=w275]{width:135px !important;}table[class=w280], td[class=w280], img[class=w280]{width:135px !important;}table[class=w300], td[class=w300], img[class=w300]{width:140px !important;}table[class=w325], td[class=w325], img[class=w325]{width:95px !important;}table[class=w360], td[class=w360], img[class=w360]{width:140px !important;}table[class=w410], td[class=w410], img[class=w410]{width:180px !important;}table[class=w470], td[class=w470], img[class=w470]{width:200px !important;}table[class=w580], td[class=w580], img[class=w580]{width:280px !important;}table[class=w640], td[class=w640], img[class=w640]{width:300px !important;}table[class*=hide], td[class*=hide], img[class*=hide], p[class*=hide], span[class*=hide]{display:none !important;}table[class=h0], td[class=h0]{height: 0 !important;}p[class=footer-content-left]{text-align: center !important;}#headline p{font-size: 30px !important;}.article-content, #left-sidebar{-webkit-text-size-adjust: 90% !important;-ms-text-size-adjust: 90% !important;}.header-content, .footer-content-left{-webkit-text-size-adjust: 80% !important;-ms-text-size-adjust: 80% !important;}img{height: auto;line-height: 100%;}}/* Client-specific Styles */#outlook a{padding: 0;}/* Force Outlook to provide a "view in browser" button. */body{width: 100% !important;}.ReadMsgBody{width: 100%;}.ExternalClass{width: 100%;display:block !important;}/* Force Hotmail to display emails at full width *//* Reset Styles *//* Add 100px so mobile switch bar doesnt cover street address. */body{background-color: #faffe5;margin: 0;padding: 0;}img{outline: none;text-decoration: none;display: block;}br, strong br, b br, em br, i br{line-height:100%;}h1, h2, h3, h4, h5, h6{line-height: 100% !important;-webkit-font-smoothing: antialiased;}h1 a, h2 a, h3 a, h4 a, h5 a, h6 a{color: blue !important;}h1 a:active, h2 a:active, h3 a:active, h4 a:active, h5 a:active, h6 a:active{color: red !important;}/* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */h1 a:visited, h2 a:visited, h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited{color: purple !important;}/* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */ table td, table tr{border-collapse: collapse;}.yshortcuts, .yshortcuts a, .yshortcuts a:link,.yshortcuts a:visited, .yshortcuts a:hover, .yshortcuts a span{color: black;text-decoration: none !important;border-bottom: none !important;background: none !important;}/* Body text color for the New Yahoo. This example sets the font of Yahoos Shortcuts to black. *//* This most probably wont work in all email clients. Dont include code blocks in email. */code{white-space: normal;word-break: break-all;}#background-table{background-color: #faffe5;}/* Webkit Elements */#top-bar{border-radius: 6px 6px 0px 0px;-moz-border-radius: 6px 6px 0px 0px;-webkit-border-radius: 6px 6px 0px 0px;-webkit-font-smoothing: antialiased;background-color: #007928;color: #FFFFFF;}#top-bar a{font-weight: bold;color: #ffffff;text-decoration: none;}#footer{border-radius:0px 0px 6px 6px;-moz-border-radius: 0px 0px 6px 6px;-webkit-border-radius:0px 0px 6px 6px;-webkit-font-smoothing: antialiased;}/* Fonts and Content */body, td{font-family: HelveticaNeue, sans-serif;text-align: center;}.header-content, .footer-content-left, .footer-content-right{-webkit-text-size-adjust: none;-ms-text-size-adjust: none;}.header-content{font-size: 12px;color: #d9ff00;}.header-content a{font-weight: bold;color: #ffffff;text-decoration: none;}#headline p{color: #d9fffd;font-family: "Helvetica Neue", Arial, Helvetica, Geneva, sans-serif;font-size: 36px;text-align: center;margin-top:0px;margin-bottom:30px;}#headline p a{color: #d9fffd;text-decoration: none;}.article-title{font-size: 18px;line-height:24px;color: #c25130;font-weight:bold;margin-top:0px;margin-bottom:18px;font-family: HelveticaNeue, sans-serif;}.article-title a{color: #c25130;text-decoration: none;}.article-title.with-meta{margin-bottom: 0;}.article-meta{font-size: 13px;line-height: 20px;color: #ccc;font-weight: bold;margin-top: 0;}.article-content{font-size: 13px;line-height: 18px;color: #444444;margin-top: 0px;margin-bottom: 18px;font-family: HelveticaNeue, sans-serif;}.article-content a{color: #3f6569;font-weight:bold;text-decoration:none;}.article-content img{max-width: 100%}.article-content ol, .article-content ul{margin-top:0px;margin-bottom:18px;margin-left:19px;padding:0;}.article-content li{font-size: 13px;line-height: 18px;color: #444444;}.article-content li a{color: #3f6569;text-decoration:underline;}.article-content p{margin-bottom: 15px;}.footer-content-left{font-size: 12px;line-height: 15px;color: #d9fffd;margin-top: 0px;margin-bottom: 15px;}.footer-content-left a{color: #d9fffd;font-weight: bold;text-decoration: none;}.footer-content-right{font-size: 11px;line-height: 16px;color: #d9fffd;margin-top: 0px;margin-bottom: 15px;}.footer-content-right a{color: #d9fffd;font-weight: bold;text-decoration: none;}#footer{background-color: #87BB84;color: #d9fffd;}#footer a{color: #d9fffd;text-decoration: none;font-weight: bold;}#permission-reminder{white-space: normal;}#street-address{color: #d9fffd;white-space: normal;}.passkey-title{}.passkey{font-size:35px;font-weight:bolder;}.left-align{text-align:left;}</style></head><body><table width="100%" cellpadding="0" cellspacing="0" border="0" id="background-table"><tbody><tr><td align="center" bgcolor="#faffe5"><table class="w640" style="margin:0 10px;" width="640" cellpadding="0" cellspacing="0" border="0"><tbody><tr><td class="w640" width="640" height="20"></td></tr><tr><td class="w640" width="640"><table id="top-bar" class="w640" width="640" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff"><tbody><tr><td class="w15" width="15"></td><td class="w325" width="350" valign="middle" align="left"><strong>MSSC</strong></td><td class="w30" width="30"></td><td class="w255" width="255" valign="middle" align="right"><table class="w255" width="255" cellpadding="0" cellspacing="0" border="0"><tbody><tr><td class="w255" width="255" height="8"></td></tr></tbody></table></td><td class="w15" width="15"></td></tr></tbody></table></td></tr><tr><td id="header" class="w640" width="640" align="center" bgcolor="#ffffff"><div align="left" style="text-align: left"><a href="http://www.mssc.org"><img id="customHeaderImage" label="Header Image" editable="true" src="http://www.mssc.org/wp-content/themes/mssc/img/logo.png" width="350" height="100" class="w640" border="0" align="top" style="display: inline"></a></div></td></tr><tr><td class="w640" width="640" height="30" bgcolor="#ffffff"></td></tr><tr id="simple-content-row"><td class="w640" width="640" bgcolor="#ffffff"><table class="w640" width="640" cellpadding="0" cellspacing="0" border="0"><tbody><tr><td class="w30" width="30"></td><td class="w580" width="580"><layout label="Text only"><table class="w580" width="580" cellpadding="0" cellspacing="0" border="0"><tbody><tr><td class="w580" width="580"><p align="left" class="article-title"><singleline label="Title">Your account password has been reset.</singleline></p><p><span class="passkey-title">Your new password is:</span><br/><span class="passkey">'. $password .'</span></p><p>Thank you!<br><strong>If you did not request this password reset contact us at 406.248.4893</strong></p><p>&nbsp;</p></td></tr></tbody></table></layout></td></tr></tbody></table></td></tr><tr><td class="w640" width="640"><table id="footer" class="w640" width="640" cellpadding="0" cellspacing="0" border="0" bgcolor="#425470"><tbody><tr><td class="w30" width="30"></td><td class="w580 h0" width="360" height="30"></td><td class="w0" width="27"></td><td class="w0" width="193"></td><td class="w30" width="30"></td></tr><tr><td class="w30" width="30"></td><td class="w580" width="360" valign="top"><span class="hide"><p id="permission-reminder" align="left" class="footer-content-left">&nbsp;</p><p align="left" class="footer-content-left"><span>You are receiving this notification because you requested a password reset for this account</span></p></span></td><td class="hide w0" width="27"></td><td class="hide w0" width="193" valign="top"><p id="street-address" align="right" class="footer-content-right"><strong>Montana Safety Services Council</strong><br><span>2727 Central Avenue</span><br><span>Suite 2</span><br><span>Billings, MT 59102</span><br><span>(406) 248-4893</span></p></td><td class="w30" width="30"></td></tr><tr><td class="w30" width="30"></td><td class="w580 h0" width="360" height="15"></td><td class="w0" width="27"></td><td class="w0" width="193"></td><td class="w30" width="30"></td></tr></tbody></table></td></tr><tr><td class="w640" width="640" height="60"></td></tr></tbody></table></td></tr></tbody></table></body></html>';
			$headers = "MIME-Version: 1.0" . " \n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1" . " \n";
			$headers .= "From: no-reply@mssc.org" . " \n";
			$headers .= "Bcc: cyndi@aehr.org" . " \n";
			mail($email, 'Password Reset', $message, $headers);
		} else { $notfound = true; }
	}


	if(!empty($_POST['email'])) {
		$email = $_POST['email'];
		$password = $_POST['pass'];
		$sql = "SELECT * FROM mssc_company WHERE (email = '$email' AND password = MD5('$password') AND active = 0) OR (old_login = '$email' AND password = MD5('$password') AND active = 0)";
		$result = $wpdb->get_results($wpdb->prepare($sql, 0));
		$_SESSION['user_type'] = 'company_account';

		if(count($result) == 0) { 
			$sql = "SELECT * FROM mssc_user WHERE email = '$email' AND password = MD5('$password') AND active = 0";
			$result = $wpdb->get_results($wpdb->prepare($sql, 0));
			$_SESSION['user_type'] = 'sub_account';
		}

		if(count($result) == 1) { 
			$logged_in = true;
			$result = current($result);
			$_SESSION['email'] = $result->email;
			$_SESSION['user_id'] = $result->id;

			if($_SESSION['user_type'] == 'company_account') {
				$_SESSION['comp_id'] = $result->id;
				$sql = "UPDATE mssc_company SET last_login = NOW() WHERE id = " . $result->id;
				$result = $wpdb->get_results($wpdb->prepare($sql, 0));
			}
			if($_SESSION['user_type'] == 'sub_account') {
				$_SESSION['comp_id'] = $result->company_id;
				$sql = "UPDATE mssc_user SET last_login = NOW() WHERE id = " . $result->company_id;
				$result = $wpdb->get_results($wpdb->prepare($sql, 0));
			}
		} else { 
			$error = 'Invalid email or password.';
		}
	}

	if(!empty($_SESSION['comp_id'])) { 
		if(empty($_SESSION['uuid'])) { $_SESSION['uuid'] = guid(); }
		$logged_in = true; 
	}

?>
<?php get_header(); ?>
<?php if($logged_in && isset($_GET['remove_sub'])) { 
	$remove_id = $_GET['remove_sub'];
	$sql = "DELETE FROM mssc_user WHERE id = " . $remove_id . " AND company_id = " . $_SESSION['comp_id'];
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	echo "<meta http-equiv='refresh' content='0;url=" . home_url("/") . "membership/members-only/' />";
	exit;
 } ?>
<div class="container_12 content page wsidebar" style="margin-bottom: 314px;">

	<div class="grid_8">
		<div class="loginContainer">
			<div class="loginBg"></div>
			<a class="login" href="<?php echo home_url('/');?>membership/">Member Login</a>
		</div>
		<div style="padding-right: 25px;">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<?php if($error) { ?><p style="color: red;"><?php echo $error; ?></p><?php } ?>



			<?php if(!$logged_in) { ?>
				<h3>Member Login</h3>
				<form method="post">
					<label>Email or Username</label><input type="text" name="email" /><br/>
					<label>Password</label><input type="password" name="pass" /><br/>
					<input type="submit" class="btn btn-success" value="Login" />
				</form>

				<button class="btn btn-warning" onclick="$('#recover').show(); $(this).hide();">Lost your Password?</button>
				<form id="recover" method="post" style="display: none;" >
					<label>Email</label><input type="text" name="email_recover" /><br/>
					<input type="submit" name="recover" class="btn btn-success" value="Recover Password" />
				</form>
				<?php if($found_email) { echo '<br/><br/><p>Your new password has been sent to your email</p>'; } ?>
				<?php if($notfound) { echo '<br/><br/><p>Your email/username not found.</p>'; } ?>

				<br/><br/><p>Still can't login? <a href="<?php echo home_url('/');?>contact-us">Click here</a> to contact us.</p>
			<?php } else { ?>
				<?php $id = $wp_query->query_vars['user_edit'];
					if(is_page('add-sub-accounts')) { 
						if(!empty($id)) { echo '<h1>Edit Sub Account</h1>'; }
						else if($_SESSION['user_type'] == 'sub_account') { echo '<h1>Edit Profile</h1>'; }
						else { the_title('<h1>', '</h1>'); }
					} ?>
				<?php the_content(); ?>

				<?php if(is_page('members-only') && $_SESSION['user_type'] != 'sub_account') { ?>
					<br/><br/><h3>Sub Accounts</h3>
					<?php $sql = "SELECT * FROM mssc_user WHERE company_id = " . $_SESSION['comp_id'] . " ORDER BY first_name ASC "; $result = $wpdb->get_results($wpdb->prepare($sql, 0));
						if($result) { 
							echo '<table width="100%" class="table">';
							foreach($result as $r) { echo '<tr><td width="100%"><a href="' . home_url('/') . 'membership/members-only/add-sub-account/' . $r->id . '">' . $r->first_name . ' ' . $r->last_name . '</a></td><td><a href="?remove_sub=' . $r->id . '" style="color: red;">remove</a></tr>'; } 
							echo '</table>';
						} else { 
							echo '<p><form style="display: inline;" method="post"><button class="btn btn-success" formaction="' . home_url('/') . 'membership/members-only/add-sub-account/">Create a sub account</button></form></p>';
						}
					} ?>
			<?php } ?>

			<?php endwhile; endif; ?>




		</div>
	</div>

	<div id="sidebar" class="grid_4">



		<?php if($logged_in) { ?>
			<br/><br/>
			<div id="logaction_btns">
				<form style="display: inline;" method="post"><input type="hidden" value="logout" name="logout" /><input type="submit" class="btn btn-success" value="Logout" /></form>
				<form style="display: inline;" method="post"><input type="hidden" value="home" name="home" /><button formaction="<?php echo home_url('/');?>membership/members-only/" class="btn btn-warning">Member Home</button></form>
			</div>
			<br/>

			<?php 
			$exclude = '';
			$profile_edit = '';
			if($_SESSION['user_type'] == 'sub_account') { 
				$profile_edit = '<li><a href="' . home_url('/') . 'membership/members-only/add-sub-account/' . $_SESSION['user_id'] . '">Edit Profile</a></li>';
				$exclude = '&exclude=508,505,510';
			}

			$children = wp_list_pages('title_li=&child_of=483&echo=0' . $exclude);

			if($children || $profile_edit) { echo '<br/><h4>Member Pages</h4><ul class="subpages">' . $profile_edit . $children . '</ul>'; }



			$mssctoken = $_SESSION['uuid'];
			$company_id = $_SESSION['comp_id'];
			$clientip = $_SERVER['REMOTE_ADDR'];

			$sql = "DELETE FROM mssc_sso WHERE company_id = $company_id";
			$result = $wpdb->get_results($wpdb->prepare($sql, 0));
			$sql = "INSERT INTO mssc_sso (company_id, token, ip) VALUES ($company_id, '$mssctoken', '$clientip')";
			$result = $wpdb->get_results($wpdb->prepare($sql, 0)); ?>


			<ul style="margin-left: 10px;">
				<li id="sso_wrap"><a href="https://www.lossfreerx.com/sso/mssc.ashx?mssctoken=<?php echo $mssctoken; ?>" id="sso" target="_blank"><strong>Risk Management ></strong></a></li>
			</ul>

		<?php } ?>
		<em>Risk Management is currently not compatible with Google Chrome</em>



		<?php get_sidebar(); ?>
	</div>
	<div class="push clear"></div>
</div>


<div class="footerBgContainer">
	<div class="footerBg"></div>
</div>

<?php get_footer(); ?>