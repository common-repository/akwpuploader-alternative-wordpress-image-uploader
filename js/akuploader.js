function getTagsAndSets(siteurl,path)
{
	flickr_user=jQuery("#flickid").val();
	optval="<option value='-1'>All Images</option>";
	jQuery("#tag_sets").html(optval);
	
	jQuery.post(siteurl+path, {akmodes:"getTags",flickr_user_id:flickr_user}, function(str) 
	{
		optval=jQuery("#tag_sets").html();
		optval=optval+"<optgroup label='Tags'>";
	    jQuery.each(str.tags, function(i,item)
	    {
			optval=optval+"<option value='"+item.tag+"'>"+item.tag+"</option>";
		});
		optval=optval+"</optgroup>";
		jQuery("#tag_sets").html(optval);;
	},"json");
	jQuery.post(siteurl+path, {akmodes:"getSets",flickr_user_id:flickr_user}, function(str) 
	{
		optval=jQuery("#tag_sets").html();
		optval=optval+"<optgroup label='Sets'>";
	    jQuery.each(str.sets, function(i,item)
	    {
			optval=optval+"<option value='"+item.id+"'>"+item.title+"</option>";
		 
		});
		optval=optval+"</optgroup>";
		jQuery("#tag_sets").html(optval);
	},"json");
	jQuery("#tag_sets").show();jQuery("#tags_button").hide();jQuery("#img_button").show();
	
}

function submitForm(siteurl,path)
{
	flickr_user=jQuery("#flickid").val();
	jQuery("#akloader").show();
	switchWindow();
	choice=jQuery("#tag_sets option:selected").val();
	if(choice=='-1')
	{
	jQuery.post(siteurl+path, {akmodes:"getFlist",flickr_user_id:flickr_user}, function(str) 
	{
	   jQuery("#akloader").hide();
	   jQuery("#akimglist").html("");
	 
	   jQuery.each(str.photos, function(i,item)
	   {
			jQuery("<img/>").attr("src", item.src).attr("id", item.id).attr("border", "0").attr("alt", item.title).appendTo("#akimglist")
			  .wrap("<a href='" + item.url + "' title='"+item.title +"' style='margin:0 0 15px 15px' onClick=\"return showOptionsDiv("+item.id+", this.href,'"+siteurl+"','"+path+"');\"></a>");
			 
		});
	},"json");
	}
	else
	{
		choicegroup=jQuery("#tag_sets option:selected").parent().attr("label");
		if(choicegroup=="Tags")
		{
			jQuery.post(siteurl+path, {akmodes:"getTlist",flickr_user_id:flickr_user,tag:choice}, function(str) 
			{
			   jQuery("#akloader").hide();
			   jQuery("#akimglist").html("");
			 
			   jQuery.each(str.photos, function(i,item)
			   {
					jQuery("<img/>").attr("src", item.src).attr("id", item.id).attr("border", "0").attr("alt", item.title).appendTo("#akimglist")
					  .wrap("<a href='" + item.url + "' title='"+item.title +"' style='margin:0 0 15px 15px' onClick=\"return showOptionsDiv("+item.id+", this.href,'"+siteurl+"','"+path+"');\"></a>");
					 
				});
			},"json");

		}
		else if(choicegroup=="Sets")
		{
			jQuery.post(siteurl+path, {akmodes:"getSlist",flickr_user_id:flickr_user,set:choice}, function(str) 
			{
			   jQuery("#akloader").hide();
			   jQuery("#akimglist").html("");
			 
			   jQuery.each(str.photos, function(i,item)
			   {
					jQuery("<img/>").attr("src", item.src).attr("id", item.id).attr("border", "0").attr("alt", item.title).appendTo("#akimglist")
					  .wrap("<a href='" + item.url + "' title='"+item.title +"' style='margin:0 0 15px 15px' onClick=\"return showOptionsDiv("+item.id+", this.href,'"+siteurl+"','"+path+"');\"></a>");
					 
				});
			},"json");
		}
	}	
}	

function showOptionsDiv(itemid,url,siteurl,path)
{
	jQuery("#akimglist").hide();
	jQuery("#akloader").show();
	jQuery("#akImgOption").empty();
	var src=jQuery("#"+itemid).attr("src");
	var title=jQuery("#"+itemid).attr("alt");
	jQuery.post(siteurl+path, {akmodes:"getMeta",photo_id:itemid}, 
		function(str) 
		{

			jQuery("#akloader").hide();
			jQuery('<div id="file-title"><span><a onclick="return switchWindow();" href="#"> << Back </a> </span> <span style="font-weight:bold">'+ str.photo.title +'</span> </div>').appendTo("#akImgOption");
			
			jQuery("<img/>").attr("src", str.photo.small).attr("border", "0").attr("alt", str.photo.title).appendTo("#akImgOption").wrap("<p></p>").wrap("<a href='" + url + "' title='"+str.photo.title +"' style='margin:0 0 15px 15px' target='_blank' ></a>");
			
			var tblstr='<p>  <span style="font-weight:bold">SHOW:</span><input type="radio" value="'+str.photo.Square+'"  name="akdisplay" /> Square <input type="radio" value="'+str.photo.thumb+'"  name="akdisplay" checked/> Thumbnail <input type="radio" value="'+str.photo.small+'"  name="akdisplay"/> Small <input type="radio" value="'+str.photo.medium+'"  name="akdisplay"/> Medium <input type="radio" value="'+str.photo.large+'"  name="akdisplay"/> Large <input type="radio" value="'+str.photo.original+'"  name="akdisplay"/> Original <br>  <span style="font-weight:bold">LINK:</span> <input type="radio" value="flickr" id="link-page" name="aklinkto" checked/> Flickr	<input type="radio" value="page" id="link-page" name="aklinkto"/> Page <input type="hidden" value="'+str.photo.desc+'" name="desc" id="img_desc"><input type="hidden" value="'+url+'"  id="img_flikr_url"><input type="button" value="Send to editor >>" onclick=\'toEditor('+itemid+',"'+siteurl+'")\' name="send" class="button"/> </p>'; 
			jQuery(""+tblstr).appendTo("#akImgOption");
			jQuery("#akImgOption").show();

		},"json");
	return false;
}	

function switchWindow(){
	jQuery("#akimglist").show();jQuery("#akImgOption").hide();return false;
}

function toEditor(imgid,siteurl)
{
    var url;
	switch(jQuery("input[name='aklinkto']:checked").val())
	{	
		case "flickr":
			url="href='"+jQuery("#img_flikr_url").val()+"'";
			appendToEditor(imgid,url);
			break;
		case "page":
				jQuery.post(siteurl+"/wp-admin/admin-ajax.php", {action:"akwpuploader_attach",'cookie': encodeURIComponent(document.cookie),'title':jQuery("#"+imgid).attr("alt"),'hurl':jQuery("#img_flikr_url").val(),'iurl':jQuery("input[name='akdisplay']:checked").val(),'content':jQuery("#img_desc").val() }, 
				function(str) 
				{
					
					var url="href='"+siteurl+"/?attachment_id="+str+"'";
					appendToEditor(imgid,url);
				});
			break;
		default:
			//url="href='"+jQuery("#img_flikr_url").val()+"'";
	}
	return false;
}

function appendToEditor(imgid,url)
{
	h="<a "+ url +" title='"+jQuery("#"+imgid).attr("alt")+"'><img src='"+jQuery("input[name='akdisplay']:checked").val()+"' alt='"+jQuery("#"+imgid).attr("alt")+"'></a>";
	tinyMCE = window.tinyMCE;
	if ( typeof tinyMCE != "undefined" && tinyMCE.getInstanceById("content") ) {
		tinyMCE.selectedInstance.getWin().focus();
		tinyMCE.execCommand("mceInsertContent", false, h);
	} else
		window.edInsertContent(window.edCanvas, h);

}