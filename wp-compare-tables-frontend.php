<?php
function wp_compare_tables_get_style()  {
    wp_register_style('wp-compare-tables-table-design', WP_PLUGIN_URL . '/wp-compare-tables/templates/stylesheets/css.'.get_option("wp-compare-tables-designtype").'.css');
    wp_enqueue_style('wp-compare-tables-table-design');

};

switch(get_option("wp-compare-tables-designtype")) {
	case "none":
	break;
	
	case "":
	break;
	
	default:
	add_action ('init', 'wp_compare_tables_get_style');
	break;
}

function wp_compare_tables_return_table($atts){

	global $wpdb;

	extract(shortcode_atts(array(
		'id' => 'default'
	), $atts));
	
	if(is_numeric($id))	{
	
		$Table = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_MAIN." WHERE tab_id = '".$id."'");
		
		if($wpdb->num_rows) {
		
			$TableHTML = '<table class="wp_compare_tables wp_compare_tables-table-id-'.$Table->tab_id.'" cellspacing="0"><thead><tr>';
			
			
			$columns = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS." WHERE col_table = '".$id."' ORDER BY col_order");
			if($wpdb->num_rows) {	
				$nr = 1;
				foreach($columns as $column) {
					$extra_class = "";
					if($nr == 1)
						$extra_class = ' wp_compare_tables-thead-first-th';
						
					if($nr == $wpdb->num_rows)
						$extra_class = ' wp_compare_tables-thead-last-th';
						
					$TableHTML .= '<th class="wp_compare_tables-thead-th-nr-'.$nr.''.$extra_class.'">'.$column->col_name.'</th>';						
					$nr++;
					unset($extra_class);
				}
			}	
			
			$TableHTML .= "</tr></thead><tbody>";
			
			$values = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_ROWS." WHERE row_table = '".$id."' ORDER BY row_order ASC");
			$count_values = $wpdb->num_rows;
			
				$nr_rows = 1;
				foreach ($values as $row) {
					
				$extra_class = "";	
					
				if($nr_rows == 1)
					$extra_class = ' wp_compare_tables-tbody-first-tr';
						
				if($nr_rows == $count_values)
					$extra_class = ' wp_compare_tables-tbody-last-tr';
				
				$Even_Odd = ($nr_rows%2==1) ? 'odd' : 'even';
				
				$TableHTML .= '<tr class="wp_compare_tables-tbody-tr-'.$Even_Odd.$extra_class.'">';
				
					$columns = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS." WHERE col_table = '".$id."' ORDER BY col_order");
					$count_columns = $wpdb->num_rows;
					if($count_columns) {	
						
						$nr = 1;
						foreach($columns as $column) {	
							$extra_class_td = "";
							
							if($nr == 1)
								$extra_class_td = ' wp_compare_tables-tbody-first-td';

							if($nr == $count_columns)
								$extra_class_td = ' wp_compare_tables-tbody-last-td';					
						
								
							$value = $wpdb->get_var($wpdb->prepare("
										SELECT 
											val_value 
										FROM 
											".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_VALUES." 
										WHERE 
												val_table = '%d' 
											AND 
												val_col = '%d' 
											AND 
												val_row = '%d'
										", $id, $column->col_id, $row->row_id));
											
							switch($column->col_type) 
							{
							
								case "logo":
									$TableHTML .= '<td class="'.$extra_class_td.'"><img class="wp_compare_tables_table-image" src="'.$value.'" alt="Image"></td>';
								break;
								
								case "text":
									$TableHTML .= '<td class="'.$extra_class_td.'"><span class="wp_compare_tables_table-text">'.$value.'</span></td>';
								break;
								
								case "readmore":
									$TableHTML .= '<td class="'.$extra_class_td.'"><a class="wp_compare_tables_table-button" href="'.$value.'" '.$Table->tab_button_html.'>'.$Table->tab_button_text.'</a></td>';
								break;
								
							}
						$nr++;
						unset($extra_class_td);
						}	
											
					}
					
				$TableHTML .= '</tr>';
				
				$nr_rows++;
				
				unset($extra_class);
				
				}
			
			$TableHTML .= "</tbody></table>";
			
			return $TableHTML;			
		
		} else {
		
			return false;
		
		}
	
	
	} else {
		
		return false;
		
	}
	
}