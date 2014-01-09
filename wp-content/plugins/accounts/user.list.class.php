<?php
class UserList {

function userList() {
	$user = new User();
	$user_functions = new UserModify();

	$new_user =				$_GET['new'];
	$edit_user =				$_GET['edit'];
	$delete_user =			$_GET['delete'];

	if($_POST['Save']) { $user->save_user($_POST); }
	if($_POST['Delete']) {
		$usr_id = $_POST['id'];
		$usr = $user->get_user_by_id($usr_id);
		$user->delete_user($usr_id, $usr);
	}
	?>

	<div class="wrap wg">
		<h2>USERS</h2>
		<?php
			if(isset($new_user)) {
				$user_functions->userPopulate(null, $user);
			} else if(isset($edit_user)) {
				$usr = $user->get_user_by_id($edit_user);
				$user_functions->userPopulate($usr, $user);
			} else if($delete_user) {
				$usr = $user->get_user_by_id($delete_user);
				$this->delete_user_layout($delete_user, $usr);
			} else { ?>
				<form method="post">
					<input type="hidden" name="page" value="<?php echo get_user_admin_page(); ?>" />
					<?php 
					$user_list_table = user_list_table__render();
					$user_list_table->search_box('search', 'search_id');
					?>
				</form>

				<div id="button_bar">
					<div><a class='button-primary' href="<?php echo get_user_admin_url(null); ?>&new=1" title='New User'>New User</a></div>
				</div><div style="clear: both;"></div>
				<?php $user_list_table->display(); ?>
		<?php } ?>
	</div><?php
}


/*******************************************************/
/*********************DELETE USER******************/
/*******************************************************/
function delete_user_layout($id, $usr) {
	$first_name = $usr->first_name;
	$last_name = $usr->last_name; 
	?>

	<form method="POST" action="<?php echo get_user_admin_url(true); ?>">
		<input id="action" name="action" type="hidden" value="Delete" />
		<input id="id" name="id" type="hidden" value="<?php echo $id; ?>" />

		<h3>Deleting User: <em><?php echo $first_name; ?> <?php echo $last_name; ?></em></h3>
		<p>Are you sure you want to <b>DELETE</b> <?php echo $first_name; ?> <?php echo $last_name; ?>?</p>

		<input class="button-primary" type="submit" name="Delete" value="Delete" id="submitbutton"/>
		<a class="button-secondary" href="<?php echo get_user_admin_url(null); ?>" title="Cancel">Cancel</a>
	</form>

	<?php
}

}
?>