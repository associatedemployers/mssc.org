<?php
class SubuserList {

function subuserList() {
	$subuser = new Subuser();
	$subuser_functions = new SubuserModify();

	$new_subuser =			$_GET['new'];
	$edit_subuser =				$_GET['edit'];
	$delete_subuser =			$_GET['delete'];

	if($_POST['Save']) { $subuser->save_subuser($_POST); }
	if($_POST['Delete']) {
		$subusr_id = $_POST['id'];
		$subusr = $subuser->get_subuser_by_id($subusr_id);
		$subuser->delete_subuser($subusr_id, $subusr);
	}
	?>

	<div class="wrap wg">
		<h2>SUB ACCOUNTS</h2>
		<?php
			if(isset($new_subuser)) {
				$subuser_functions->subuserPopulate(null, $subuser);
			} else if(isset($edit_subuser)) {
				$subusr = $subuser->get_subuser_by_id($edit_subuser);
				$subuser_functions->subuserPopulate($subusr, $subuser);
			} else if($delete_subuser) {
				$subusr = $subuser->get_subuser_by_id($delete_subuser);
				$this->delete_subuser_layout($delete_subuser, $subusr);
			} else { ?>
				<form method="post">
					<input type="hidden" name="page" value="<?php echo get_subuser_admin_page(); ?>" />
					<?php 
					$subuser_list_table = subuser_list_table__render();
					$subuser_list_table->search_box('search', 'search_id');
					?>
				</form>

				<div id="button_bar">
					<div><a class='button-primary' href="<?php echo get_subuser_admin_url(null); ?>&new=1" title='New Sub Account'>New Sub Account</a></div>
				</div><div style="clear: both;"></div>
				<?php $subuser_list_table->display(); ?>
		<?php } ?>
	</div><?php
}


/************************************************************/
/*********************DELETE SUBUSER******************/
/************************************************************/
function delete_subuser_layout($id, $subusr) {
	$first_name = $subusr->first_name;
	$last_name = $subusr->last_name; 
	?>

	<form method="POST" action="<?php echo get_subuser_admin_url(true); ?>">
		<input id="action" name="action" type="hidden" value="Delete" />
		<input id="id" name="id" type="hidden" value="<?php echo $id; ?>" />

		<h3>Deleting Subuser: <em><?php echo $first_name; ?> <?php echo $last_name; ?></em></h3>
		<p>Are you sure you want to <b>DELETE</b> <?php echo $first_name; ?> <?php echo $last_name; ?>?</p>

		<input class="button-primary" type="submit" name="Delete" value="Delete" id="submitbutton"/>
		<a class="button-secondary" href="<?php echo get_subuser_admin_url(null); ?>" title="Cancel">Cancel</a>
	</form>

	<?php
}

}
?>