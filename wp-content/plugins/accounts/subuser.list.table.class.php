<?php

if(!class_exists('WP_List_Table')) { require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php'); } //Includes WP_List_table class
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////USER TABLE
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
class subuser_list_table extends WP_List_Table { 
	function get_columns(){
		return array('id' => 'ID', 'company' => 'Company', 'first_name' => 'First Name', 'last_name' => 'Last Name');
	}


	function column_company($item) {
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&edit=%s">Edit</a>', $_REQUEST['page'], $item['id']),
			'delete' => sprintf('<a href="?page=%s&delete=%s">Delete</a>', $_REQUEST['page'], $item['id'])
		);
		return sprintf('%1$s %2$s', $item['company'], $this->row_actions($actions));
	}


	function get_data(){
		global $wpdb;
		global $webgrain;
		$column_array = array();
		$search = '';

		$sort_column = '';
		if(isset($_GET['orderby'])) { $sort_column = " ORDER BY " . $_GET['orderby'] . " " . $_GET['order']; }

		if(isset($_POST['s'])) {
			$search_term = $_POST['s'];
			$search = " WHERE first_name LIKE '%$search_term%' 
								OR last_name LIKE '%$search_term%' ";
		}

		$sql = "SELECT id, first_name, last_name, company_id FROM  " . $webgrain->subusertable . " " . $search . " " . $sort_column;
		$result = $wpdb->get_results($sql);

		foreach($result as $r) {
			$id = $r->id;
			$first_name = $r->first_name; 
			$last_name = $r->last_name;
			$company_id = $r->company_id; 

			$sql2 = "SELECT company FROM " . $webgrain->usertable . " WHERE id = $company_id";
			$result2 = $wpdb->get_results($wpdb->prepare($sql2, 0));
			if(!empty($result2)) { foreach($result2 as $r2) { $company = $r2->company; } } else { $company = ''; }

			$data_array = array('id' => $id, 'company'=>$company, 'first_name'=>$first_name, 'last_name'=>$last_name);
			array_push($column_array, $data_array);
		}

		return $column_array;
	}


	function get_sortable_columns() {
		$sortable_columns = array(
			'first_name' => array('first_name', false),
			'last_name' => array('last_name', false)
		);
		return $sortable_columns;
	}


	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array('id');
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$data = $this->get_data();

		$per_page = 20;
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$this->found_data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
		$this->set_pagination_args(array('total_items' => $total_items, 'per_page' => $per_page));
		$this->items = $this->found_data;
	}


	function column_default($item, $column_name) {
		switch($column_name) {
			case 'id':
			case 'company':
			case 'first_name':
			case 'last_name':
				return $item[$column_name];
			default:
				return print_r($item, true) ; //Show the whole array for troubleshooting purposes
		}
	}
}


function subuser_list_table__render(){
	$subuser_list_table = new subuser_list_table();
	$subuser_list_table->prepare_items();
	return $subuser_list_table;
}

?>