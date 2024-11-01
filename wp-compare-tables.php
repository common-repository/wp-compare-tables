<?php
/*
Plugin Name: WP Compare Tables
Plugin URI: http://vandebelt.dk/wp-compare-tables/
Description: This plugin allows you to easily create and manage tables, which main function is to compare products or services against each other. It is mainly used by affiliate webpages. Tables can be included by using a shortcode.
Version: 1.0.5
Author: Martin van de Belt
Author URI: http://vandebelt.dk/
Author eMail: martin@vandebelt.dk
License: GPL 2
Donate URI: http://vandebelt.dk/donate/
Text Domain: wp-compare-tables
*/

/*  Copyright 2011 Martin van de Belt  (email : martin@vandebelt.dk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
	DEFINE VARIABLES AS CONSTANTS
*/
define("WP_COMPARE_TABLES_ABSPATH", plugin_dir_path( __FILE__ ));
define("WP_COMPARE_TABLES_BASENAME", plugin_basename( __FILE__ ));

define("WP_COMPARE_TABLES_TABLE_MAIN", 		"compare_tables");
define("WP_COMPARE_TABLES_TABLE_COLUMNS", 	"compare_tables_columns");
define("WP_COMPARE_TABLES_TABLE_ROWS", 		"compare_tables_rows");
define("WP_COMPARE_TABLES_TABLE_VALUES", 	"compare_tables_values");

define("WP_COMPARE_TABLES_TEXTDOMAIN", 		"wp-compare-tables");

define("WP_COMPARE_TABLES_VERSION", "1.0.5");
define("WP_COMPARE_TABLES_DB_VERSION", "1.4");

/*
 	RUN INSTALL SCRIPT ON ACTIVATION
*/
include_once(WP_COMPARE_TABLES_ABSPATH . "helpers/wp-compare-tables-install.php");
register_activation_hook(__FILE__, 'wp_compare_tables_install');

/*
	RUN UNINSTALLER SCRIPT ON DEACTIVATION
*/	
include_once(WP_COMPARE_TABLES_ABSPATH . "helpers/wp-compare-tables-uninstall.php");
register_deactivation_hook(__FILE__, 'wp_compare_tables_uninstall');

/* 	
	LOADERS
*/

// Global loaders
load_plugin_textdomain("wp-compare-tables", false, dirname( plugin_basename( __FILE__ ) ) . "/languages/");

// Load admin
if( is_admin() ) {

	include_once(WP_COMPARE_TABLES_ABSPATH . "helpers/wp-compare-tables-admin-loader.php");
	include_once(WP_COMPARE_TABLES_ABSPATH . "helpers/wp-compare-tables-misc.php");
	include_once(WP_COMPARE_TABLES_ABSPATH . "helpers/wp-compare-tables-global-html.php");

	add_action('admin_menu', 'wp_compare_tables_menu');

	add_action('admin_print_scripts', 'wp_compare_tables_loadscripts');
	add_action('admin_print_styles', 'wp_compare_tables_loadstyles');
	
	include_once(WP_COMPARE_TABLES_ABSPATH . "wp-compare-tables-admin.php");	
	include_once(WP_COMPARE_TABLES_ABSPATH . "wp-compare-tables-admin-options.php");	

// Load frontend	
} else {
	
	include_once(WP_COMPARE_TABLES_ABSPATH . "wp-compare-tables-frontend.php");
	
	add_shortcode('wpc_table', 'wp_compare_tables_return_table');
	add_shortcode('table', 'wp_compare_tables_return_table');

}