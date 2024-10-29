<?php
   	require_once("phpFlickr/phpFlickr.php");
	// Create new phpFlickr object
	$f = new phpFlickr("0ecb7bc85eeb448ab5d510d14430af51");


	switch(htmlentities($_POST['akmodes']))
	{
		case "getFlist":
				getUserImages();
				break;
		case "getTags":
				getUserTags();
				break;
		case "getSets":
				getUserSets();
				break;
		case "getTlist":
				getUserImages('tags');
				break;
		case "getSlist":
				getUserImages('sets');
				break;
		case "getMeta":
				getMetaData();
				break;
		default :		
			echo '
				unauthorised
			';
	}

	function getUserImages($opt='')
	{	global $f;
		
		$i = 0;
		if (!empty($_POST['flickr_user_id'])) {
		    // Find the NSID of the username inputted via the form
		    $person = $f->people_findByUsername($_POST['flickr_user_id']);

			switch($opt){
				case'tags':
						//get users photo based on tag
						$tag=$_POST['tag'];
						$photos = $f->photos_search(array("user_id"=>$person['id'], "tags"=>$tag,"per_page"=>500));
						break;
				case'sets':
						//get user's photo based on the set						
						$photo_set_id=$_POST['set'];
						$photos = $f->photosets_getPhotos($photo_set_id,NULL,NULL, 500);	
						break;
				default:		
				// Get the user's first 500 public photos
				$photos = $f->people_getPublicPhotos($person['id'], NULL, 500);
			}	
			
		    // Get the friendly URL of the user's photos
		    $photos_url = $f->urls_getUserPhotos($person['id']);
		    
			$json='{"photos":[';
			
		    // Loop through the photos and output the json
		    foreach ((array)$photos['photo'] as $photo) {
			
				$json=$json.'{"title":"'.$photo['title'].'","src":"'.$f->buildPhotoURL($photo, "Square").'","url":"'.$photos_url.$photo['id'].'","id":"'.$photo['id'].'"},';
		    }
			$json=substr($json,0,-1);
			$json=$json."]}";
			echo $json;
		}

	}
	
	function getUserSets()
	{	global $f;
		
		$i = 0;
		if (!empty($_POST['flickr_user_id'])) {
		    // Find the NSID of the username inputted via the form
		    $person = $f->people_findByUsername($_POST['flickr_user_id']);
		    $pset = $f->photosets_getList($person['id']);
			$json='{"sets":[';
			foreach($pset['photoset'] as $set)
			{
				$json=$json.'{"id":"'.$set['id'].'","title":"'.$set['title'].'"},';
			}	
		    
			$json=substr($json,0,-1);
			$json=$json."]}";
			echo $json;
		}

	}

	function getUserTags()
	{	global $f;
		
		$i = 0;
		if (!empty($_POST['flickr_user_id'])) {
		    // Find the NSID of the username inputted via the form
		    $person = $f->people_findByUsername($_POST['flickr_user_id']);
		    $pset = $f->tags_getListUser($person['id']);
			
			$json='{"tags":[';
			foreach($pset as $key=>$tag)
			{
				$json=$json.'{"tag":"'.$tag.'"},';
			}	
			$json=substr($json,0,-1);
			$json=$json."]}";
			echo $json;
		}

	}	
	function getMetaData()
	{
		global $f;
		error_reporting(0);
		$photo_id=$_POST['photo_id'];
		$meta_data=$f->photos_getInfo($photo_id);
		$meta_sizes=$f->photos_getSizes($photo_id);
		foreach($meta_sizes as $size)
		{
			$imgsize[strtolower($size['label'])]=$size['source'];
		}
		//write code for description etc.
		$json='{"photo":{"title":"'.$meta_data['title'].'","thumb":"'.$imgsize['thumbnail'].'","small":"'.$imgsize['small'].'","square":"'.$imgsize['square'].'","medium":"'.$imgsize['medium'].'","large":"'.$imgsize['large'].'","original":"'.$imgsize['original'].'","desc":"'.$meta_data['description'].'"}}';
		echo $json;
	}
