<?php
// MENU FUNCTION
function wp_compare_tables_menu() {

  	add_menu_page(__('Tables', 'wp-compare-tables'), __('Tables', 'wp-compare-tables'), 'edit_themes', 'wp_compare_tables', 'wp_compare_tables_admin_index');
 	add_submenu_page('wp_compare_tables', __('Add Table', 'wp-compare-tables'), __('Add Table', 'wp-compare-tables'), 'edit_themes', 'wp_compare_tables_add', 'wp_compare_tables_add_form');
	add_submenu_page('wp_compare_tables', __('Configuration', 'wp-compare-tables'), __('Configuration', 'wp-compare-tables'), 'edit_themes', 'wp_compare_tables_options', 'wp_compare_tables_options');
  
}

// LOAD SCRIPTS
function wp_compare_tables_loadscripts () {

	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('dashboard');
	
	wp_register_script('wp_compare_tables_uploader', plugins_url('javascripts/jquery.uploader.js', __FILE__), array('jquery','media-upload','thickbox'));
	wp_enqueue_script('wp_compare_tables_uploader');
	
	add_action('admin_head', 'wp_compare_tables_admin_head');	

}

// LOAD STYLES
function wp_compare_tables_loadstyles () {
	wp_enqueue_style('dashboard');
	wp_enqueue_style('thickbox');
	
	wp_register_style('wp_compare_tables', plugins_url('stylesheets/wp-compare-tables.css', __FILE__));
	wp_enqueue_style('wp_compare_tables');

}

function wp_compare_tables_admin_head()
{
?>
<script type='text/javascript'>
		function addEvent() {
		  var ni = document.getElementById('myDiv');
		  var numi = document.getElementById('theValue');
		  var num = (document.getElementById("theValue").value -1)+ 2;
		  numi.value = num;
		  var divIdName = "my"+num+"Div";
		  var newdiv = document.createElement('div');
		  newdiv.setAttribute("id",divIdName);
		  
		  newdiv.innerHTML = "<table class=\"form-table\"><tr><th><?php _e('Column', 'wp-compare-tables'); ?>: (<a href=\"javascript:;\" onclick=\"removeElement(\'"+divIdName+"\')\"><?php _e('remove', 'wp-compare-tables'); ?></a>)</th><td><select name=\"coltype[]\"><option style=\"padding-right: 10px;\" value='logo'><?php _e('Image', 'wp-compare-tables'); ?></option><option style=\"padding-right: 10px;\" value='text'><?php _e('Text', 'wp-compare-tables'); ?></option><option style=\"padding-right: 10px;\" value='readmore'><?php _e('Button', 'wp-compare-tables'); ?></option></select> <?php _e('Name', 'wp-compare-tables'); ?>: <input name=\"colname[]\" type=\"text\" value=\"\" /></td></tr></table>";
		  ni.appendChild(newdiv);
		}
		function removeElement(divNum) {
		  var d = document.getElementById('myDiv');
		  var olddiv = document.getElementById(divNum);
		  d.removeChild(olddiv);	
		}
		
			jQuery(document).ready(function(){
				jQuery('input[name=doaction]').click(function(){			
					if(jQuery('select[name=action], select[name=action]', jQuery(this).parent()).val() == 'delete' ) {
						if(confirm('<?php _e('WARNING: You will lose any data stored in those tables.', 'wp-compare-tables'); ?>')) {
							return true;
						} else {
							return false;
						}	
					}		
				});
			});
			
			
</script>		
<?php	
}