<?php
// INSTALL SCRIPT
function wp_compare_tables_install () {
	
	global $wpdb;
	
	if (function_exists('is_multisite') && is_multisite()) {
	
		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
				
			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				_wp_compare_tables_install();
			}
			
			restore_current_blog();
			return;
		}	
		
	}	

	_wp_compare_tables_install();

}


// When creating a new blog in WP Multisite
add_action('wpmu_new_blog', 'wp_compare_tables_install_mu_new_blog');

function wp_compare_tables_install_mu_new_blog($blog_id){
			
	switch_to_blog($blog_id);
	_wp_compare_tables_install();
	restore_current_blog();

}

function _wp_compare_tables_install() {

	global $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$Current_Db_Version = get_option("wp_compare_tables");
	
	$table_name = $wpdb->prefix . WP_COMPARE_TABLES_TABLE_MAIN;

	$NewTable = "";
	
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name OR WP_COMPARE_TABLES_DB_VERSION != $Current_Db_Version) {
	
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$NewTable = ", PRIMARY KEY (tab_id)";
		}
			
		$sql = "CREATE TABLE " . $table_name . " (
		  tab_id int(11) NOT NULL AUTO_INCREMENT,
		  tab_name VARCHAR(255) NOT NULL,
		  tab_button_text VARCHAR(255) NOT NULL,
		  tab_button_html VARCHAR(255) NOT NULL
		 ".$NewTable."
		);";
		
		dbDelta($sql);
		
	}
	
	$table_name = $wpdb->prefix . WP_COMPARE_TABLES_TABLE_COLUMNS;
	
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name OR WP_COMPARE_TABLES_DB_VERSION != $Current_Db_Version) {
	
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$NewTable = ", PRIMARY KEY (col_id)";
		}
	
		$sql = "CREATE TABLE " . $table_name . " (
		  col_id int(11) NOT NULL AUTO_INCREMENT,
		  col_table int(11) NOT NULL,
		  col_type VARCHAR(255) NOT NULL,
		  col_name VARCHAR(255) NOT NULL,
		  col_order int(11) NOT NULL,
		  col_html text NOT NULL
		 ".$NewTable."
		);";
		
		dbDelta($sql);
		
	}
	
	$table_name = $wpdb->prefix . WP_COMPARE_TABLES_TABLE_ROWS;
	
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name OR WP_COMPARE_TABLES_DB_VERSION != $Current_Db_Version) {
	
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$NewTable = ", PRIMARY KEY (row_id)";
		}
	
		$sql = "CREATE TABLE " . $table_name . " (
		  row_id int(11) NOT NULL AUTO_INCREMENT,
		  row_table int(11) NOT NULL,
		  row_order int(11) NOT NULL
		 ".$NewTable."
		);";
		
		dbDelta($sql);
		
	}
	
	$table_name = $wpdb->prefix . WP_COMPARE_TABLES_TABLE_VALUES;
	
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name OR WP_COMPARE_TABLES_DB_VERSION != $Current_Db_Version) {
	
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$NewTable = ", PRIMARY KEY (val_id)";
		}
	
		$sql = "CREATE TABLE " . $table_name . " (
		  val_id int(11) NOT NULL AUTO_INCREMENT,
		  val_table int(11) NOT NULL,
		  val_row int(11) NOT NULL,
		  val_col int(11) NOT NULL,
		  val_value longtext NOT NULL
		 ".$NewTable."
		);";
		
		dbDelta($sql);
		
	}
	
	update_option("wp_compare_tables", WP_COMPARE_TABLES_DB_VERSION);
	update_option("wp-compare-tables-designtype", "none");
	
}