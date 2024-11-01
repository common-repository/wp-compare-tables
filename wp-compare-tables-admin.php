<?php

function wp_compare_tables_admin_index()
{
	global $wpdb;
	
		if(isset($_POST['submit-tabel-add'])) {
		
			$wpdb->insert( $wpdb->prefix.WP_COMPARE_TABLES_TABLE_MAIN, array( 
				'tab_name' => stripslashes_deep( $_POST['tabelname'] ),
				'tab_button_text' => stripslashes_deep( $_POST['buttontext'] ),
				'tab_button_html' => stripslashes_deep( $_POST['buttontags'] )
				));
				
			$TableID = $wpdb->insert_id;
			
			$NextOrder = 1;
			
			foreach($_POST['coltype'] as $Number => $Value) {
			
				$wpdb->insert( $wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS, 
							array( 
								'col_table' => $TableID, 
								'col_type' => $Value, 
								'col_name' => stripslashes_deep( $_POST['colname'][$Number] ),
								'col_order' => $NextOrder
								)
							);
				
				$NextOrder++;			
			
			}
		
		}
		
		if(isset($_POST['submit-tabel-edit']) AND is_numeric($_POST['tableid'])) {
					
			$wpdb->update( $wpdb->prefix.WP_COMPARE_TABLES_TABLE_MAIN, 
				array( 
					'tab_name' => stripslashes_deep( $_POST['tabelname'] ),
					'tab_button_text' => stripslashes_deep( $_POST['buttontext'] ),
					'tab_button_html' => stripslashes_deep( $_POST['buttontags'] )
					), 
				array( 
					'tab_id' => $_POST['tableid'] 
					));
			
			foreach($_POST['coltype'] as $Number => $Value) {
				
				if(!isset($_POST['colid'][$Number]))
					$_POST['colid'][$Number] = 0;
				
				$column = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS." WHERE col_id = '".$_POST['colid'][$Number]."'");
				
				if($wpdb->num_rows > 0) {
		
					$wpdb->update( $wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS, 
						array( 
							'col_type' => stripslashes_deep( $Value ), 
							'col_name' => stripslashes_deep( $_POST['colname'][$Number] ), 
							'col_order' => $_POST['colorder'][$Number] 
							), 
						array( 
							'col_id' => $Number 
							));
			
				} else {
					
					$NextOrder = $wpdb->get_var($wpdb->prepare("SELECT col_order FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS." WHERE col_table = '%d' ORDER BY col_order DESC", $_POST['tableid']));
					$NextOrder++;
			
					$wpdb->insert( $wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS, 
							array( 
								'col_table' => $_POST['tableid'], 
								'col_type' => $Value, 
								'col_name' => stripslashes_deep( $_POST['colname'][$Number] ),
								'col_order' => $NextOrder
								));
				
				}
				
			}
			
			if(isset($_POST['del'])) {
				foreach($_POST['del'] as $ID => $Value) {
					
				 $wpdb->query("DELETE FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS." WHERE col_id = '".$ID."'");
				
				}
			}
		
		}
		
		if(isset($_POST['submit-edit-data-cols']) AND is_numeric($_POST['tableid'])) {
			
			$DeleteLater = false;
				
			if($_POST['new_value']) {
					
			$NextOrder = $wpdb->get_var($wpdb->prepare("SELECT row_order FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_ROWS." WHERE row_table = '%d' ORDER BY row_order DESC", $_POST['tableid']));
			$NextOrder++;
			
			$wpdb->insert( $wpdb->prefix.WP_COMPARE_TABLES_TABLE_ROWS, array( 'row_table' => $_POST['tableid'], 'row_order' => $NextOrder ) );
			$RowID = $wpdb->insert_id;	
				
				foreach($_POST['new_value'] as $ColType => $Value) {
				
				if($Value == "")
				 $DeleteLater = true;
				
				$wpdb->insert( $wpdb->prefix.WP_COMPARE_TABLES_TABLE_VALUES, array( 
					'val_table' => $_POST['tableid'], 
					'val_row' => $RowID, 
					'val_col' => $ColType, 
					'val_value' => stripslashes_deep( $Value ) ) );
				
				}
			
				if($DeleteLater) {
					$wpdb->query("DELETE FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_ROWS." WHERE row_id = '".$RowID."'");
					$wpdb->query("DELETE FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_VALUES." WHERE val_row = '".$RowID."'");
				}
				
			}
			
			if(isset($_POST['oldvalue'])) {
				
				foreach($_POST['oldvalue'] as $ColID => $Value) {
				
					foreach($Value as $RowID => $Value) {
					
						$values = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_VALUES." WHERE val_table = '".$_POST['tableid']."' AND val_col = '".$ColID."' AND val_row = '".$RowID."'");
						
						if($wpdb->num_rows) {
						
							$wpdb->update( $wpdb->prefix.WP_COMPARE_TABLES_TABLE_VALUES, array( 'val_value' => stripslashes_deep( $Value ) ), array( 'val_table' => $_POST['tableid'], 'val_col' => $ColID, 'val_row' => $RowID ) );
				
						} else {
						
							$wpdb->insert( $wpdb->prefix.WP_COMPARE_TABLES_TABLE_VALUES, array( 'val_table' => $_POST['tableid'], 'val_row' => $RowID, 'val_col' => $ColID, 'val_value' => stripslashes_deep($Value) ) );
						
						}
			
					}
				
				}
				
			}
			
			if(isset($_POST['coldel'])) {
				
				foreach($_POST['coldel'] as $RowID => $Value) {
					
					$wpdb->query("DELETE FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_ROWS." WHERE row_id = '".$RowID."'");
					$wpdb->query("DELETE FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_VALUES." WHERE val_row = '".$RowID."'");
				
				}
				
			}
			
			if(isset($_POST['order'])) {
				
				foreach($_POST['order'] as $RowID => $Value) {
					
					$wpdb->update( $wpdb->prefix.WP_COMPARE_TABLES_TABLE_ROWS, array( 'row_order' => $Value ), array( 'row_id' => $RowID ) );
				
				}
				
			}
		
			tabels_add_rows($_POST['tableid']);
		
		} else {
	
	
			if(tabels_return_get('action') == 'add') {

				wp_compare_tables_add_form();

			} elseif(tabels_return_get('action') == 'addrows' && tabels_return_get('id')) {

				tabels_add_rows(tabels_return_get('id'));

			} elseif(tabels_return_get('action') == 'edit' && tabels_return_get('id')) {

				tabels_form('edit', tabels_return_get('id'));

			} elseif(tabels_return_get('action') == 'delete' && tabels_return_get('id')) {

				if(tabels_delete(tabels_return_get('id')))
					tabels_show_index();

			} else {

				tabels_show_index();

			}
	
		}
}


function wp_compare_tables_add_form()
{
	tabels_form('add', tabels_return_get('copyfrom'));
}

function tabels_show_index () 
{

	global $wpdb;
	tabels_header();
	
	if(isset($_POST['action'])) {
		
		if($_POST['action'] == 'delete') {
			
			foreach($_POST['table'] as $Key => $ID) {
				
				tabels_delete($ID);
				
			}
			
		}
		
	}
?>

<div class="tablenav">
	<div class="alignleft actions">
		<form method="post" action="admin.php?page=wp_compare_tables">
		<select name="action">
			<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
			<option value="delete"><?php _e('Delete'); ?></option>
		</select>
		<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
	</div>
</div>

<div class="clear"></div>

	<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /</th>
				<th scope="col"><?php _e('Table Name', 'wp-compare-tables'); ?></th>
				<th scope="col"><?php _e('Table Shortcode', 'wp-compare-tables'); ?></th>
			</tr>
		</thead>
	
		<tfoot>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /</th>
				<th scope="col"><?php _e('Table Name', 'wp-compare-tables'); ?></th>
				<th scope="col"><?php _e('Table Shortcode', 'wp-compare-tables'); ?></th>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$tabels = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_MAIN);
		if($wpdb->num_rows) {
		
			foreach ($tabels as $tabel) {
		?>
				<tr valign="top" class="alternate iedit">
					<th class="check-column" scope="row"><input type="checkbox" name="table[]" value="<?php echo $tabel->tab_id; ?>" /></th>
					<td class="post-title page-title column-title"><strong><?php echo $tabel->tab_name; ?></strong>
					
					<div class="row-actions">
						<span class='edit'><a href="admin.php?page=wp_compare_tables&amp;action=addrows&amp;id=<?php echo $tabel->tab_id; ?>"><?php _e('add / edit rows', 'wp-compare-tables'); ?></a> | </span>
						<span class='edit'><a href="admin.php?page=wp_compare_tables&amp;action=edit&amp;id=<?php echo $tabel->tab_id; ?>"><?php _e('edit table', 'wp-compare-tables'); ?></a> | </span>
						<span class='edit'><a href="admin.php?page=wp_compare_tables_add&amp;copyfrom=<?php echo $tabel->tab_id; ?>"><?php _e('copy to new', 'wp-compare-tables'); ?></a> |</span>
						<span class='trash'><a class='submitdelete' 
												onclick="return confirm('<?php _e('WARNING: You will lose any data stored in this table.', 'wp-compare-tables'); ?>')" 
												href='<?php echo wp_nonce_url("admin.php?page=wp_compare_tables&amp;action=delete&amp;id=".$tabel->tab_id.""); ?>'>
											<?php _e('delete', 'wp-compare-tables'); ?>
											</a></span>
					</div>	
						
					</td>
					<td style="white-space: nowrap;">[wpc_table id=<?php echo $tabel->tab_id; ?>]</td>
				</tr>
		<?php
			}
		
		} else {
			echo '<tr><td colspan="4">'.__('No tables found.', 'wp-compare-tables').' <a href="admin.php?page=wp_compare_tables_add">'.__('Add a new table.', 'wp-compare-tables').'</a></td></tr>';
		}		
		?>
		</tbody>
	</table>
	
<div class="tablenav">
	<div class="alignleft actions">
		<select name="action">
			<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
			<option value="delete"><?php _e('Delete'); ?></option>
		</select>
		<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
		<form>
	</div>
</div>

<div class="clear"></div>
	
	<?php
	tabels_footer();	
	
}

function tabels_form($action = 'add', $id = false) 
{

	global $wpdb;

	tabels_header($action);
	
	$Cols = "";
	
	if(is_numeric($id)) {
	
		$tabel = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_MAIN." WHERE tab_id = '".$id."'");
		if($wpdb->num_rows) {
			
			$columns = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS." WHERE col_table = '".$tabel->tab_id."' ORDER BY col_order");
			
			if($wpdb->num_rows) {
			
				foreach($columns as $column) {				
					
					$Cols .=  '<table class="form-table"><tr><th>'.__('Column', 'wp-compare-tables').'</th><td>';
						
					$Cols .= '	<input type="hidden" name="colid['.$column->col_id.']" value="'.$column->col_id.'" />
								<select name="coltype['.$column->col_id.']">
									<option style="padding-right: 10px;" value="logo"'.($column->col_type == 'logo' ? ' selected="selected"' : '').'>'.__('Image', 'wp-compare-tables').'</option>
									<option style="padding-right: 10px;" value="text"'.($column->col_type == 'text' ? ' selected="selected"' : '').'>'.__('Text', 'wp-compare-tables').'</option>
									<option style="padding-right: 10px;" value="readmore"'.($column->col_type == 'readmore' ? ' selected="selected"' : '').'>'.__('Button', 'wp-compare-tables').'</option>
								</select> 
								'.__('Name', 'wp-compare-tables').': <input name="colname['.$column->col_id.']" type="text" value="'.$column->col_name.'" /> 
								'.__('Order', 'wp-compare-tables').' <input type="text" name="colorder['.$column->col_id.']" size="5" value="'.$column->col_order.'" /> 
								<input type="checkbox" name="del['.$column->col_id.']" value="true" /> '.__('Remove?', 'wp-compare-tables').'</td>';
					
					$Cols .=  "</tr></table>";
				
				}
			
			}
	
		} else {
		
			return tabels_show_index();			
		
		}
		
	}
?>

	<form action="admin.php?page=wp_compare_tables" method="post">
	<input type="hidden" id="theValue" value="1" />
	<input type="hidden" name="tableid" value="<?php echo $tabel->tab_id; ?>" />

    <div class="postbox-container" style="width: 100%">
        <div class="metabox-holder">
            <div class="meta-box-sortables">
                <div class="postbox" id="first">
                    <div class="handlediv" title="Click to toggle"><br /></div>
                    <h3 class="hndle"><span><?php _e('Table Settings', 'wp-compare-tables'); ?></span></h3>
                    <div class="inside">
						<table class="form-table">				
							<tr>
								<th><?php _e('Table Name', 'wp-compare-tables'); ?></th>
								<td>
									<input name="tabelname" type="text" value="<?php echo esc_html($wpdb->get_var($wpdb->prepare("SELECT tab_name FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_MAIN." WHERE tab_id = '%d'", $id))); ?>" /> 
									<span class="description"><?php _e('Choose a name, which makes it possible for you to identify this table.', 'wp-compare-tables'); ?></span>
								</td>
							</tr>	
						</table>
                    </div>
                </div>
                <div class="postbox" id="second">
                    <div class="handlediv" title="Click to toggle"><br /></div>
                    <h3 class="hndle"><span><?php _e('Table Columns', 'wp-compare-tables'); ?></span></h3>
                    <div class="inside">
                    
						<?php if($action == 'add' && !is_numeric($id)) : ?>
						<table class="form-table">				
							<tr>
								<th><?php _e('Column', 'wp-compare-tables'); ?></th>
								<td>
										<select name="coltype[]">
											<option style="padding-right: 10px;" value='logo'><?php _e('Image', 'wp-compare-tables'); ?></option>
											<option style="padding-right: 10px;" value='text'><?php _e('Text', 'wp-compare-tables'); ?></option>
											<option style="padding-right: 10px;" value='readmore'><?php _e('Button', 'wp-compare-tables'); ?></option>
										</select> 
										<?php _e('Name', 'wp-compare-tables'); ?>: <input name="colname[]" type="text" value="" />
								</td>
							</tr>		
						</table>
						<?php endif; ?>
						
						<div id="myDiv">
								<?php echo $Cols; ?>
						</div>
							
						<div style="margin-top: 20px;">
							<a href="javascript:;" onclick="addEvent();" class="button-secondary"><?php _e('Add new column', 'wp-compare-tables'); ?></a> 
							<div class="clear"> </div>
						</div>
						
                    </div>
                </div>
                <div class="postbox" id="third">
                    <div class="handlediv" title="Click to toggle"><br /></div>
                    <h3 class="hndle"><span><?php _e('Advanced settings', 'wp-compare-tables'); ?></span></h3>
                    <div class="inside">
                    
						<table class="form-table">		
							<tr>
								<th><?php _e('Table Button Text', 'wp-compare-tables'); ?></th>
								<td>
									<input name="buttontext" type="text" value="<?php echo esc_html($wpdb->get_var($wpdb->prepare("SELECT tab_button_text FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_MAIN." WHERE tab_id = '%d'", $id))); ?>" /> 
									<span class="description"><?php _e('Choose an anchor-text to use on buttons created with this plugin. For example: "Read more"', 'wp-compare-tables'); ?></span>
								</td>
							</tr>	
							<tr>
								<th><?php _e('Custom Button Tags', 'wp-compare-tables'); ?></th>
								<td>
									<input name="buttontags" type="text" value="<?php echo esc_html($wpdb->get_var($wpdb->prepare("SELECT tab_button_html FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_MAIN." WHERE tab_id = '%d'", $id))); ?>" /> 
									<span class="description"><?php _e('You can add some additional tags to the buttons you create. Ex. rel="nofollow", target="_blank", a combination of those, or something similar.', 'wp-compare-tables'); ?></span>
								</td>
							</tr>	
						</table>
						
                    </div>
                </div>


            </div>
        </div>
        
    </div>	
    
	<p class="submit">
		<input name="submit-tabel-<?php echo $action; ?>" type="submit" value="<?php _e('Save Table', 'wp-compare-tables'); ?>" class="button-primary">
	</p>
		
	</form>
		
<?php

	tabels_footer();

}

function tabels_delete ($id = false) {

	global $wpdb;
	
	if(!$id)
	 return false;
	 
	$wpdb->query("DELETE FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_MAIN." WHERE tab_id = '".$id."'");
	$wpdb->query("DELETE FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS." WHERE col_table = '".$id."'");
	$wpdb->query("DELETE FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_ROWS." WHERE row_table = '".$id."'");
	$wpdb->query("DELETE FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_VALUES." WHERE val_table = '".$id."'");
	 
	return true; 

}

function tabels_add_rows ($id = false) {

	global $wpdb;
	
	if(!$id)
	 return tabels_show_index();
	
	tabels_header();
	
	$tabel = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_MAIN." WHERE tab_id = '".$id."'");
	
	if($wpdb->num_rows) {
		
		?>
		
    <div class="postbox-container" style="width: 100%">
        <div class="metabox-holder">
            <div class="meta-box-sortables">
                <div class="postbox" id="first">
                    <div class="handlediv" title="Click to toggle"><br /></div>
                    <h3 class="hndle"><span><?php _e('Add/edit rows', 'wp-compare-tables'); ?></span></h3>
                    <div class="inside">

		<form action="admin.php?page=wp_compare_tables" method="post">
			<input name="tableid" type="hidden" value="<?php echo $id; ?>">
			
			<table id="table-1">				
				<tr>
				<?php	
					$columns = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS." WHERE col_table = '".$id."' ORDER BY col_order");
					if($wpdb->num_rows) {					
						foreach($columns as $column) {
							echo "<th>".$column->col_name."</th>";						
						}					
					}	
				?>
				<th></th>
				</tr>	
				
				<?php
				
				$values = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_ROWS." WHERE row_table = '".$id."' ORDER BY row_order ASC");
				
				$i = 1;
				foreach ($values as $row) {
				
				echo '<tr id="'.$i.'">';
				
					$columns = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS." WHERE col_table = '".$id."' ORDER BY col_order");
					if($wpdb->num_rows) {					
						foreach($columns as $column) {
						
						if($column->col_type == 'logo') {
						
							$RandomV = mt_rand(00000,99999);
								
							echo '<td><input id="upload_image_'.$RandomV.'" type="text" size="36" name="oldvalue['.$column->col_id.']['.$row->row_id.']" value="'.esc_html($wpdb->get_var($wpdb->prepare("SELECT val_value FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_VALUES." WHERE val_table = '%d' AND val_col = '%d' AND val_row = '%d'", $id, $column->col_id, $row->row_id))).'"><input id="'.$RandomV.'" name="clickonthis" type="button" value="Upload image" /></td>';						
						
						} else {
						
							echo '<td><input type="text" name="oldvalue['.$column->col_id.']['.$row->row_id.']" value="'.esc_html($wpdb->get_var($wpdb->prepare("SELECT val_value FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_VALUES." WHERE val_table = '%d' AND val_col = '%d' AND val_row = '%d'", $id, $column->col_id, $row->row_id))).'"></td>';
								
						}
						
						}					
					}
					
				echo '<td><input type="hidden" name="pos['.$i.']" value="" /> '.__('Order', 'wp-compare-tables').':<input type="text" size="3" name="order['.$row->row_id.']" value="'.$row->row_order.'"> <input type="checkbox" name="coldel['.$row->row_id.']" value="1"> '.__('Remove?', 'wp-compare-tables').'</td></tr>';
				
				$i++;
				}
				?>
				
				<tr>	
				<?php	
					$columns = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.WP_COMPARE_TABLES_TABLE_COLUMNS." WHERE col_table = '".$id."' ORDER BY col_order");
					if($wpdb->num_rows) {
						foreach($columns as $column) {
					
							$prevalue = ($column->col_type == 'readmore' OR $column->col_type == 'logo') ? 'http://' : '' ;
							
							if($column->col_type == 'logo') {
							
								$RandomV = mt_rand(00000,99999);
							
								echo '<td><input id="upload_image_'.$RandomV.'" type="text" size="36" name="new_value['.$column->col_id.']" value="" /><input id="'.$RandomV.'" name="clickonthis" type="button" value="'.__('Upload image', 'wp-compare-tables').'" /></td>';
							
							} else {
						
								echo '<td><input type="text" name="new_value['.$column->col_id.']" value="'.$prevalue.'"></td>';
							
							}
							
						}						
					}	
				?>
				<td></td>
				</tr>		
			</table>	
			<div>
				<input type="hidden" name="neworder" id="neworder" value="" />
				<input name="submit-edit-data-cols" type="submit" value="<?php _e('Save table and add a new row', 'wp-compare-tables'); ?>" class="button-secondary">
				<div class="clear"> </div>
			</div>
		</form>
                    </div>
                </div>
            </div>
        </div>
    </div>		
		
		<?php 
		
	} else {
	
		tabels_show_index();
	
	}	
	
	
	tabels_footer();

}