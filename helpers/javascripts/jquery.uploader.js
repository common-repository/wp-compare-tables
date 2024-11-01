jQuery(document).ready(function() {
 
	jQuery('input[name=clickonthis]').click(function() {
	
	var currentId = jQuery(this).attr('id');

	window.send_to_editor = function(html) {
	 imgurl = jQuery('img',html).attr('src');
	 jQuery('#upload_image_'+currentId).val(imgurl);
	 tb_remove(); 
	}
	 
	 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	 return false;
	});
	
});