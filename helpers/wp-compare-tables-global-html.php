<?php

// HEADER 
function tabels_header ($page = "") 
{
?>
	
	<div class="wrap" id="tables">
		
		<div id="icon-tools" class="icon32"></div>	
		<h2>WP Compare Tables</h2>
		
		<ul class="subsubsub">
			<li><a href="admin.php?page=wp_compare_tables"<?php if($page == '') : ?> class="current"<?php endif; ?>><?php _e('Tables', 'wp-compare-tables'); ?></a> | </li>
			<li><a href="admin.php?page=wp_compare_tables_add"<?php if($page == 'add') : ?> class="current"<?php endif; ?>><?php _e('Add Table', 'wp-compare-tables'); ?></a> | </li>
			<li><a href="admin.php?page=wp_compare_tables_options"<?php if($page == 'options') : ?> class="current"<?php endif; ?>><?php _e('Configuration', 'wp-compare-tables'); ?></a></li>
		</ul>
		
		<br class="clear" />
		
<?php
}

// FOOTER
function tabels_footer () 
{
?>
	
	</div>	
	
<?php
}