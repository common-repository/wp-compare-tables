<?php

/**
 * Function to run when user decides to deactivate the plugin.
 *
 * @author Martin van de Belt
 */
function wp_compare_tables_uninstall () {

	/**
	 * Check whether the user decided to delete any content stored by WP Compare Tables or not. Setting is provided in options page.
	 *
	 * @author Martin van de Belt
	 */
	if(get_option('wp_compare_tables_uninstall') == 'true')	
	{
	
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	
	
		/**
		 * Delete options created by WP Compare Tables
		 *
		 * @author Martin van de Belt
		 */
		delete_option('wp_compare_tables_uninstall');
		delete_option('wp_compare_tables');


		/**
		 * Delete any table created by WP Compare Tables
		 *
		 * @author Martin van de Belt
		 */		
        $table_name = $wpdb->prefix . WP_COMPARE_TABLES_TABLE_MAIN;
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		
        $table_name = $wpdb->prefix . WP_COMPARE_TABLES_TABLE_COLUMNS;
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		
        $table_name = $wpdb->prefix . WP_COMPARE_TABLES_TABLE_ROWS;
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		
        $table_name = $wpdb->prefix . WP_COMPARE_TABLES_TABLE_VALUES;
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
	
	}

}