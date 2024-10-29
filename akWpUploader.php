<?php
/*
Plugin Name: Alternative Wordpress Image Uploader Using Flickr
Plugin URI: http://amiworks.co.in/talk/akwpupoader-alternative-wordpress-image-uploader/
Description: It uses flickr image uploading capabilites to attach images to the post instead of using the the default image uploader. It uses the capablities of  phpFlickr.
Author: Amit kumar singh
Version: 1.1.0
Author URI: http://amiworks.co.in/talk
	This is a WordPress plugin (http://wordpress.org).
*/
define('AKWPUPLOADERDIR', 'akwpuploader-alternative-wordpress-image-uploader');                
add_action('edit_form_advanced', 'initTheForm');
add_action('edit_page_form', 'initTheForm');
add_action('wp_ajax_akwpuploader_attach', 'createAttachment');
add_action('admin_head','loadJquery');

function loadJquery()
{
	wp_enqueue_script('jquery','/wp-content/plugins/'.AKWPUPLOADERDIR.'/js/jquery-1.2.3.min.js');
}

function initTheForm()
{
	if (current_user_can('upload_files'))
	{
		echo '
		<script src="'.get_option('siteurl').'/wp-content/plugins/'.AKWPUPLOADERDIR.'/js/akuploader.js"></script>
		<div class="meta-box-sortables ui-sortable">
		<div class="postbox">
		<div title="Click to toggle" class="handlediv"><br/></div>
		<h3 class="hndle"><span>akWpUploader: Alternative Wordpress Image Uploader Using Flickr</span></h3>

		<div class="inside">
		<div class="dbx-content">
			
				Your Flickr ID: <input type="text" name="flickr_user_id" id="flickid">
				<select id="tag_sets" style="display:none"></select>
				<input type="button" id="tags_button" value="Fetch Tags and Sets" onclick="getTagsAndSets(\''.get_option('siteurl').'\',\'/wp-content/plugins/'.AKWPUPLOADERDIR.'/upload.php\');">
				<input type="button" value="Fetch Images from Flickr" onclick="submitForm(\''.get_option('siteurl').'\',\'/wp-content/plugins/'.AKWPUPLOADERDIR.'/upload.php\');" style="display:none" id="img_button" >
				<input type="hidden" name="akmodes" value="getFlist">
			
		';

		echo '</div>
		<div id="akuploadspace" class="dbx-content">
			<span id="akloader" style="display:none"><img src="'.get_option('siteurl').'/wp-content/plugins/'.AKWPUPLOADERDIR.'/akloader.gif" border="0"> Please wait, fetching images from flickr.</span>
			<div id="akimglist"></div>
			<div id="akImgOption"></div>
		</div>
		</div>
		</div>
		<p>&nbsp;</p>
		';
		
	}
	else
	{
		echo 'Not Authorised To Use akWpUploader';
	}
}

function createAttachment(){
	global $wpdb;
	global $userdata;
	
    get_currentuserinfo();
	//echo bloginfo('name').'<br>'.bloginfo('description');
	$date =  date('Y-m-d H:i:s', $timestamp) ;
    $postid = wp_insert_post(array(
								'post_title' 	    => $_POST['title'],
								'post_content'  	=> $_POST['content'],
								'post_category'   => '0',
								'post_status' 	  => 'inherit',
								'post_author'     => $userdata->ID,
								'post_date'       => $date,
								'post_type'       => 'attachment',
								'post_mime_type'       => 'image/jpeg',
								'comment_status'  => 'open',
								'ping_status'     => 'open'
    ));
	$wpdb->query("UPDATE $wpdb->posts SET guid = '" .$_POST['iurl']. "' WHERE ID = '$postid'");
	$result = $wpdb->query( "INSERT INTO `$wpdb->postmeta` (post_id,meta_key,meta_value ) " 
					                . " VALUES ('$postid','_wp_attached_file','".$_POST['iurl']."') ");

	echo $postid;
	exit;
}
