<?php

/**
 * Defines a custom upload template
 * 
 * This is a very simple example of creation of a custom upload template by
 * extending the original template.
 * 
 * This custom template is a child of the original template class, so it is not
 * required to declare all functions of the template but only those that are
 * different.
 */
class WFU_UploaderTemplate_AdvancedFileList extends WFU_Original_Template {

private static $instance = null;

function wfu_filelist_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of file list element
 *  @var $height string assigned height of file list element
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $index int the index of occurrence of the element inside the plugin,
 *       in case that it appears more than once
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
	$styles = "";
	//for responsive plugin adjust container's widths if a % width has been defined
	if ( $responsive && strlen($width) > 1 && substr($width, -1, 1) == "%" ) $styles = 'width: 100%;';
	elseif ( $width != "" ) $styles = "width: $width; ";
	if ( $height != "" ) $styles .= "height: $height;";
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
div.file_filelist
{
	display: block;	
	margin: 0px;
	padding: 0px;
	border-style: none;
	background: none;
	color: black; /*relax*/
	width: 150px;
}

div.file_filelist_totalprogress_div
{
	display: block;
	position: relative;
	width: auto; 
	border-top: 1px solid #333333;
	border-right: 1px solid #333333;
	border-bottom: 1px solid #333333;
	border-left: 1px solid #333333;
	margin: 0;
	padding: 4px 22px 4px 4px;
}

div.file_filelist_totalprogress_div_with_remove
{
	display: block;
	position: relative;
	width: auto; 
	border-top: 1px solid #333333;
	border-right: 1px solid #333333;
	border-bottom: 1px solid #333333;
	border-left: 1px solid #333333;
	margin: 0;
	padding: 4px 39px 4px 4px;
}

div.file_filelist_totalprogress_inner
{
	display: block;	
	width: 100%;
	height: 6px;
	margin: 0;
	padding: 0;
	border: 1px solid silver;
	background-color: white;
}

img.file_filelist_totalprogress_imagesafe
{
	width: 100%;
	height: 6px;
}

span.file_filelist_totalprogress_noanimation
{
	display: block;	
	width: 0%; 
	height: 6px;
	margin: 0;
	padding: 0;
	border-style: none;
	background-color: rgb(43,194,83);
	position: relative;
	overflow: hidden;
}

span.file_filelist_totalprogress_progressive
{
	display: block;	
	width: auto; 
	height: 6px;
	margin: 0;
	padding: 0;
	border-style: none;
	background-color: rgb(43,194,83);
	position: relative;
	overflow: hidden;
}

span.file_filelist_totalprogress_shuffle
{
	display: block;	
	width: 25%; 
	height: 6px;
	margin: 0;
	padding: 0;
	border-style: none;
	background-color: rgb(43,194,83);
	position: relative;
	-webkit-animation: shuffle 1s linear infinite alternate;
	-moz-animation: shuffle 1s linear infinite alternate;
	-o-animation: shuffle 1s linear infinite alternate;
	animation: shuffle 1s linear infinite alternate;
	overflow: hidden;
}

span.file_filelist_totalprogress_progressive:after
{
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	bottom: 0;
	right: 0;
	background-image: -webkit-gradient(linear, 0 0, 100% 100%, color-stop(.25, rgba(255, 255, 255, .2)), color-stop(.25, transparent), color-stop(.5, transparent), color-stop(.5, rgba(255, 255, 255, .2)), color-stop(.75, rgba(255, 255, 255, .2)), color-stop(.75, transparent), to(transparent) );
	background-image: -moz-linear-gradient( -45deg, rgba(255, 255, 255, .2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .2) 50%, rgba(255, 255, 255, .2) 75%, transparent 75%, transparent );
	z-index: 1;
	-webkit-background-size: 30px 30px;
	-moz-background-size: 30px 30px;
	background-size: 30px 30px;
	-webkit-animation: lengthen 2s linear infinite;
	-moz-animation: lengthen 2s linear infinite;
	-o-animation: lengthen 2s linear infinite;
	animation: lengthen 2s linear infinite;
	overflow: hidden;
}

div.file_filelist_totalprogress_arrow
{
	border-left: 1px solid #999999;
	height: 16px;
	position: absolute;
	right: 0;
	top: 0;
	width: 16px;
	background: none;
}

div.file_filelist_totalprogress_arrow_with_remove
{
	border-left: 1px solid #999999;
	height: 16px;
	position: absolute;
	right: 17px;
	top: 0;
	width: 16px;
	background: none;
}

div.file_filelist_totalprogress_arrow_up
{
	width: 0; 
	height: 0; 
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-bottom: 5px solid #555555;
	margin: 5px 1px 1px 3px;	
	/* ie6 height fix */
	font-size: 0;
	line-height: 0;
	/* ie6 transparent fix */
	_border-right-color: pink;
	_border-left-color: pink;
	_filter: chroma(color=pink);
}

div.file_filelist_totalprogress_arrow_down
{
	width: 0; 
	height: 0; 
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-top: 5px solid #555555;
	margin: 5px 1px 1px 3px;
	/* ie6 height fix */
	font-size: 0;
	line-height: 0;
	/* ie6 transparent fix */
	_border-right-color: pink;
	_border-left-color: pink;
	_filter: chroma(color=pink);
}

div.file_filelist_totalprogress_arrow:hover
{
	border-left: 1px solid #999999;
	height: 16px;
	position: absolute;
	right: 0;
	top: 0;
	width: 16px;
	background-color: #CCCCCC;
}

div.file_filelist_totalprogress_arrow_with_remove:hover
{
	border-left: 1px solid #999999;
	height: 16px;
	position: absolute;
	right: 17px;
	top: 0;
	width: 16px;
	background-color: #CCCCCC;
}

div.file_filelist_totalprogress_arrow:hover div.file_filelist_totalprogress_arrow_up,
div.file_filelist_totalprogress_arrow_with_remove:hover div.file_filelist_totalprogress_arrow_up
{
	width: 0; 
	height: 0; 
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-bottom: 5px solid #555555;
	margin: 6px 1px 1px 3px;
	/* ie6 height fix */
	font-size: 0;
	line-height: 0;
	/* ie6 transparent fix */
	_border-right-color: pink;
	_border-left-color: pink;
	_filter: chroma(color=pink);
}

div.file_filelist_totalprogress_arrow:hover div.file_filelist_totalprogress_arrow_down,
div.file_filelist_totalprogress_arrow_with_remove:hover div.file_filelist_totalprogress_arrow_down
{
	width: 0; 
	height: 0; 
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-top: 5px solid #555555;
	margin: 6px 1px 1px 3px;
	/* ie6 height fix */
	font-size: 0;
	line-height: 0;
	/* ie6 transparent fix */
	_border-right-color: pink;
	_border-left-color: pink;
	_filter: chroma(color=pink);
}

div.file_filelist_totalprogress_removeall, 
div.file_filelist_totalprogress_cancelall
{
	border-left: 1px solid #999999;
	height: 16px;
	position: absolute;
	right: 0;
	top: 0;
	width: 16px;
	background: none;
}

div.file_filelist_totalprogress_removeall:hover, 
div.file_filelist_totalprogress_cancelall:hover
{
	border-left: 1px solid #999999;
	height: 16px;
	position: absolute;
	right: 0;
	top: 0;
	width: 16px;
	background-color: #CCCCCC;
}

img.file_filelist_totalprogress_removeall_img, 
img.file_filelist_totalprogress_cancelall_img
{
	display: block;	
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	margin: auto;
	border: none;
}

div.file_filelist_totalprogress_removeall:hover img.file_filelist_totalprogress_removeall_img, 
div.file_filelist_totalprogress_removeall:hover img.file_filelist_totalprogress_cancelall_img
{
	display: block;	
	position: absolute;
	top: 2px;
	right: 0;
	bottom: 0;
	left: 0;
	margin: auto;
	border: none;
}

div.file_filelist_list_div
{
	display: block;	
	margin: 0px;
	padding: 0px;
	line-height: 1;
	border-top: none;
	border-right: 1px solid #333333;
	border-bottom: 1px solid #333333;
	border-left: 1px solid #333333;
	background: none;
	color: black; /*relax*/
}

label.file_filelist_filelabel_label0
{
	font-size: 12px;
	font-weight: normal;
	line-height: 1;
	margin: 2px 0 2px 4px;
	padding: 0;
}

div.file_filelist_file_div
{
	display: block;	
	position: relative;
	min-height: 20px;
	margin: 0px;
	padding: 2px 2px 2px 4px;
	border-style: none;
	color: black; /*relax*/
}

div.file_filelist_file_div:nth-child(even)
{
	background-color: rgba(0,0,0,0.1);
}

div.file_filelist_file_div:nth-child(odd)
{
	background-color: rgba(0,0,0,0);
}

div.file_filelist_file_div_with_remove
{
	display: block;	
	position: relative;
	min-height: 20px;
	margin: 0px;
	padding: 2px 20px 2px 4px;
	border-style: none;
	color: black; /*relax*/
}

div.file_filelist_file_div_with_remove:nth-child(even)
{
	background-color: rgba(0,0,0,0.1);
}

div.file_filelist_file_div_with_remove:nth-child(odd)
{
	background-color: rgba(0,0,0,0);
}

table.file_filelist_file_table
{
	width: 100%;
	height:	100%;
	table-layout: fixed;
	margin: 0;
	padding: 0;
	border: none;
	border-spacing: 0;
}

table.file_filelist_file_table td
{
	margin: 0;
	padding: 0 4px 0 0;
	border: none;
	border-spacing: 0;
	vertical-align: middle;
}

label.file_filelist_filelabel_label
{
	font-size: 12px;
	width: 100%;
	white-space: normal;
	text-overflow: ellipsis;
	overflow: hidden;
	display: table-cell;
	padding-left: 2ch;
}

div.file_filelist_fileprogress_inner
{
	display: block;	
	width: 100%;
	height: 6px;
	margin: 0;
	padding: 0;
	border: 1px solid silver;
	background-color: white;
}

span.file_filelist_fileprogress_noanimation
{
	display: block;	
	width: 0%; 
	height: 6px;
	margin: 0;
	padding: 0;
	border-style: none;
	background-color: rgb(43,83,194);
	position: relative;
	overflow: hidden;
}

span.file_filelist_fileprogress_progressive
{
	display: block;	
	width: auto; 
	height: 6px;
	margin: 0;
	padding: 0;
	border-style: none;
	background-color: rgb(43,83,194);
	position: relative;
	overflow: hidden;
}

span.file_filelist_fileprogress_shuffle
{
	display: block;	
	width: 25%; 
	height: 6px;
	margin: 0;
	padding: 0;
	border-style: none;
	background-color: rgb(43,83,194);
	position: relative;
	-webkit-animation: shuffle 1s linear infinite alternate;
	-moz-animation: shuffle 1s linear infinite alternate;
	-o-animation: shuffle 1s linear infinite alternate;
	animation: shuffle 1s linear infinite alternate;
	overflow: hidden;
}

span.file_filelist_fileprogress_progressive:after
{
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	bottom: 0;
	right: 0;
	background: -moz-linear-gradient(-45deg, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0) 25%, rgba(255,255,255,0) 50%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.2) 75%, rgba(255,255,255,0) 75%, rgba(255,255,255,0) 100%);
	background: -webkit-gradient(linear, left top, right bottom, color-stop(25%,rgba(255,255,255,0.2)), color-stop(25%,rgba(255,255,255,0)), color-stop(50%,rgba(255,255,255,0)), color-stop(50%,rgba(255,255,255,0.2)), color-stop(75%,rgba(255,255,255,0.2)), color-stop(75%,rgba(255,255,255,0)), color-stop(100%,rgba(255,255,255,0)));
	background: -webkit-linear-gradient(-45deg, rgba(255,255,255,0.2) 25%,rgba(255,255,255,0) 25%,rgba(255,255,255,0) 50%,rgba(255,255,255,0.2) 50%,rgba(255,255,255,0.2) 75%,rgba(255,255,255,0) 75%,rgba(255,255,255,0) 100%);
	background: -o-linear-gradient(-45deg, rgba(255,255,255,0.2) 25%,rgba(255,255,255,0) 25%,rgba(255,255,255,0) 50%,rgba(255,255,255,0.2) 50%,rgba(255,255,255,0.2) 75%,rgba(255,255,255,0) 75%,rgba(255,255,255,0) 100%);
	background: -ms-linear-gradient(-45deg, rgba(255,255,255,0.2) 25%,rgba(255,255,255,0) 25%,rgba(255,255,255,0) 50%,rgba(255,255,255,0.2) 50%,rgba(255,255,255,0.2) 75%,rgba(255,255,255,0) 75%,rgba(255,255,255,0) 100%);
	background: linear-gradient(135deg, rgba(255,255,255,0.2) 25%,rgba(255,255,255,0) 25%,rgba(255,255,255,0) 50%,rgba(255,255,255,0.2) 50%,rgba(255,255,255,0.2) 75%,rgba(255,255,255,0) 75%,rgba(255,255,255,0) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#33ffffff', endColorstr='#00ffffff',GradientType=1 );
	z-index: 1;
	-webkit-background-size: 30px 30px;
	-moz-background-size: 30px 30px;
	-o-background-size: 30px 30px;
	background-size: 30px 30px;
	-webkit-animation: lengthen 2s linear infinite;
	-moz-animation: lengthen 2s linear infinite;
	-o-animation: lengthen 2s linear infinite;
	animation: lengthen 2s linear infinite;
	overflow: hidden;
}

div.file_filelist_fileremove_div
{
	position: absolute;
	width: 20px; 
	height: 100%;
	top: 0;
	right: 0;
	border: none;
	background: none;
}

div.file_filelist_fileremove_div:hover
{
	position: absolute;
	width: 20px; 
	height: 100%;
	top: 0;
	right: 0;
	border: none;
	background-color: #ccc;
}

img.file_filelist_fileremove_img
{
	display: block;	
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	margin: auto;
	border: none;
}

div.file_filelist_fileremove_div:hover img.file_filelist_fileremove_img
{
	display: block;	
	position: absolute;
	top: 0;
	right: 1px;
	bottom: 1px;
	left: 0;
	margin: auto;
	border: none;
}

img.file_filelist_filecancel_img
{
	margin: 0;
	cursor: pointer;
}

div.file_filelist_fileimage_inner {
	font-family: monospace;
	font-size: 14px;
	white-space: nowrap;
	padding-right: 4ch;
}

div.file_filelist_fileimage_btn {
	display: inline-block;
	width: 1.5ch;
	text-align: center;
	cursor: default;
}

div.file_filelist_fileimage_btn:hover {
	background-color: #eee;
}

img.file_filelist_fileimage_image {
	margin-left: -0.5ch;
	margin-right: -0.5ch;
	vertical-align: middle;
}

@-webkit-keyframes shuffle { from { left: 0%; } to { left: 75%; } }

@-moz-keyframes shuffle { from { left: 0%; } to { left: 75%; } }

@-o-keyframes shuffle { from { left: 0%; } to { left: 75%; } }

@keyframes shuffle { from { left: 0%; } to { left: 75%; } }

@-webkit-keyframes lengthen { from { background-position: 0 0; } to { background-position: 30px 30px; } }

@-moz-keyframes lengthen { from { background-position: 0 0; } to { background-position: 30px 30px; } }

@-o-keyframes lengthen { from { background-position: 0 0; } to { background-position: 30px 30px; } }

@keyframes lengthen { from { background-position: 0 0; } to { background-position: 30px 30px; } }
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].filelist.init = function() {
/***
 *  The following file list methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method setVisibility shows or hides the file list items
 *  @method visible gets the visible state of the file list items
 *  @method updateList updates the list of file items
 *  @method enableEditing enables editing of the file list items
 *  @method disableEditing disables editing of the file list items
 *  @method afterUploadStart executes custom file list actions after upload has
 *          started
 *  @method attachCancelHandlers attaches cancel handlers to all filelist items
 *  @method dettachCancelHandlers dettaches the cancel handlers
 *  @method updateItemProgress updates the upload progress position of an
 *          individual item of the file list
 *  @method updateTotalProgress updates the total upload progress position
 *  @method resetTotalProgress resets the total upload progress element
 *  @method updateItemStatus updates the status of an individual item of the
 *          file list
 */
/**
 *  show or hide filelist items
 *  
 *  @param show bool flag to show or hide the file list
 *  
 *  @return void
 */
this.setVisibility = function(show) {
	var item1 = document.getElementById('filelist_$ID_totalprogress');
	var item2 = document.getElementById('filelist_$ID_totalprogress_arrow_up');
	var item3 = document.getElementById('filelist_$ID_totalprogress_arrow_down');
	var item4 = document.getElementById('filelist_$ID_list_div');
	if (show) {
		item2.style.display = "";
		item3.style.display = "none";
		item1.style.borderBottom = "1px solid #999999";
		item4.style.display = "block";
	}
	else {
		item2.style.display = "none";
		item3.style.display = "";
		item1.style.borderBottom = "1px solid #333333";
		item4.style.display = "none";
	}
}

/**
 *  get visible state of filelist items
 *  
 *  @return bool true if items are visible, false if they are hidden
 */
this.visible = function() {
	return ( document.getElementById('filelist_$ID_totalprogress_arrow_up').style.display != "none" );
}

/**
 *  updates the list of file items
 *  
 *  This function receives an array of selected files and adjusts the items of
 *  the filelist block. The files array contains information about the name and
 *  size of each file. An additional parameter, is_formupload, determines if the
 *  upload is done using the classic HTML Forms or the modern AJAX requests. 
 *  This is important information because in classic HTML Forms files cannot be
 *  treated separately.
 *  
 *  @param files array holds the array of selected files; every item of the
 *         array is a File object
 *  @param is_formupload bool defines if this is an upload using classic HTML
 *         forms or modern AJAX requests. If it is true (classic HTML forms)
 *         then files cannot be treated separately but all together.
 *  
 *  @return void
 */
this.updateList = function(files, is_formupload) {
	if (!!GlobalData.WFU[$ID].filelist_exist) {
		var htmlitem = document.getElementById("filelist_$ID_itemhtml");
		var htmlitemempty = document.getElementById("filelist_$ID_itemhtmlempty");
		var html_item = wfu_plugin_decode_string(htmlitem.value);
		var html = "";
		var html_total = "";
		var ii = 0;
		if (is_formupload && files.length > 0) {
			document.getElementById("filelist_$ID_totalprogress").className = "file_filelist_totalprogress_div_with_remove";
			document.getElementById("filelist_$ID_totalprogress_arrow").className = "file_filelist_totalprogress_arrow_with_remove";
			document.getElementById("filelist_$ID_totalprogress_removeall").style.display = "block";
			document.getElementById("filelist_$ID_totalprogress_cancelall").style.display = "none";
		}
		else {
			document.getElementById("filelist_$ID_totalprogress").className = "file_filelist_totalprogress_div";
			document.getElementById("filelist_$ID_totalprogress_arrow").className = "file_filelist_totalprogress_arrow";
			document.getElementById("filelist_$ID_totalprogress_removeall").style.display = "none";
			document.getElementById("filelist_$ID_totalprogress_cancelall").style.display = "none";
		}
		if (files.length == 0) {
			html_total = wfu_plugin_decode_string(htmlitemempty.value);
		}
		else {
			var props = GlobalData.WFU[$ID].filearrayprops;
			for (var i = 0; i < files.length; i++) {
				ii = i + 1;
				if (!props[i].copies) props[i].copies = 1;
				html = html_item.replace(/\[id\]/g, ii);
				html = html.replace(/\[fileid\]/g, i);
				html = html.replace(/\[name\]/g, files[i].name);
				html = html.replace(/\[copies\]/g, props[i].copies);
				if (is_formupload) {
					html = html.replace("[additional class]", "");
					html = html.replace("[removebutton visibility]", " style=\"display:none;\"");
				}
				else {
					html = html.replace("[additional class]", "_with_remove");
					html = html.replace("[removebutton visibility]", "");
				}
				html_total = html_total + html;
			}
		}
		document.getElementById("filelist_$ID_list_div").innerHTML = html_total;
		this._readImageURL(files);
	}
}

/**
 *  enable editing of the filelist items
 *  
 *  This function is executed after an upload has finished, either successfully
 *  or unsuccessfully. It makes the file list editable. This means that the user
 *  can remove items from the file list. So this function hides the progress bar
 *  of each item, the total progress bar and displays a remove button for each
 *  item. A parameter, is_formupload, determines if the upload is done using the
 *  classic HTML Forms or the modern AJAX requests. This is important
 *  information because in classic HTML Forms files cannot be treated
 *  separately. In this case this function should not show a remove button for
 *  each item, but only one remove button for all items.
 *  
 *  @param is_formupload bool defines if this is an upload using classic HTML
 *         forms or modern AJAX requests. If it is true (classic HTML forms)
 *         then files cannot be treated separately but all together.
 *  
 *  @return void
 */
this.enableEditing = function(is_formupload) {
	var bar = document.getElementById('filelist_$ID_totalprogress_animation');
	var barsafe = document.getElementById('filelist_$ID_totalprogress_imagesafe');
	bar.style.width = "0%";
	bar.className = "file_filelist_totalprogress_noanimation";
	barsafe.style.display = "none";
	bar.style.display = "block";
	if (is_formupload) {
		document.getElementById('filelist_$ID_totalprogress_arrow').className = "file_filelist_totalprogress_arrow_with_remove";
		document.getElementById('filelist_$ID_totalprogress').className = "file_filelist_totalprogress_div_with_remove";
		document.getElementById('filelist_$ID_totalprogress_removeall').style.display = "block";
		document.getElementById('filelist_$ID_totalprogress_cancelall').style.display = "none";
	}
	else {
		var items = document.querySelectorAll("#filelist_$ID table.file_filelist_file_table");
		for (var i = 1; i <= items.length; i++) {
			document.getElementById('filelist_$ID_file' + i).className = "wfu_unlock_progressbar";
			document.getElementById('filelist_$ID_fileremove' + i).style.display = "";
			document.getElementById('filelist_$ID_fileprogress_td' + i).style.display = "none";
			document.getElementById('filelist_$ID_filecancel_td' + i).style.display = "none";
			document.getElementById('filelist_$ID_fileimage_btndec' + i).style.visibility = "visible";
			document.getElementById('filelist_$ID_fileimage_btninc' + i).style.visibility = "visible";
		}
	}
}

/**
 *  disable editing of filelist items
 *  
 *  After execution of this function no items can be removed from the list. This
 *  happens when the user presses the Upload button and upload of selected files
 *  is about to start.
 *  
 *  @param is_formupload bool defines if this is an upload using classic HTML
 *         forms or modern AJAX requests. If it is true (classic HTML forms)
 *         then files cannot be treated separately but all together.
 *  
 *  @return void
 */
this.disableEditing = function(is_formupload) {
	var bar = document.getElementById('filelist_$ID_totalprogress_animation');
	var barsafe = document.getElementById('filelist_$ID_totalprogress_imagesafe');
	bar.style.width = "0%";
	bar.className = "file_filelist_totalprogress_noanimation";
	barsafe.style.display = "none";
	bar.style.display = "block";
	document.getElementById('filelist_$ID_totalprogress_removeall').style.display = "none";
	document.getElementById('filelist_$ID_totalprogress_cancelall').style.display = "none";
	document.getElementById('filelist_$ID_totalprogress').className = "file_filelist_totalprogress_div";
	document.getElementById('filelist_$ID_totalprogress_arrow').className = "file_filelist_totalprogress_arrow";
	var items = document.querySelectorAll("#filelist_$ID table.file_filelist_file_table");
	for (var i = 1; i <= items.length; i++) {
		document.getElementById('filelist_$ID_fileremove' + i).style.display = "none";
		document.getElementById('filelist_$ID_file' + i).className = "file_filelist_file_div";
		document.getElementById('filelist_$ID_fileprogress_td' + i).style.display = "none";
		document.getElementById('filelist_$ID_filecancel_td' + i).style.display = "none";
		document.getElementById('filelist_$ID_fileimage_btndec' + i).style.visibility = "hidden";
		document.getElementById('filelist_$ID_fileimage_btninc' + i).style.visibility = "hidden";
	}
}

/**
 *  filelist actions after upload has started
 *  
 *  This function is executed after an upload has started. It displays the
 *  progress bar for each item and also a cancel button, so that each item's 
 *  upload can be cancelled at any moment. A parameter, effect, determines if
 *  the upload process supports progress information or not. If it supports,
 *  then effect has the value 'progressive' otherwise effect is 'shuffle'.
 *  
 *  @param effect string defines if the upload process supports progress
 *         information. If can take the value 'progressive' (when progress
 *         information is supported) or 'shuffle' (when progress information is
 *         not supported).
 *  @param is_formupload bool defines if this is an upload using classic HTML
 *         forms or modern AJAX requests. If it is true (classic HTML forms)
 *         then files cannot be treated separately but all together.
 *  
 *  @return void
 */
this.afterUploadStart = function(effect, is_formupload) {
	var bar = document.getElementById('filelist_$ID_totalprogress_animation');
	var barsafe = document.getElementById('filelist_$ID_totalprogress_imagesafe');
	if (wfu_BrowserCaps.supportsAnimation && effect == "progressive") {
		bar.style.width = "0%";
		bar.className = "file_filelist_totalprogress_progressive";
		barsafe.style.display = "none";
		bar.style.display = "block";
	}
	else if (wfu_BrowserCaps.supportsAnimation) {
		bar.style.width = "25%";
		bar.className = "file_filelist_totalprogress_shuffle";
		barsafe.style.display = "none";
		bar.style.display = "block";
	}
	else {
		bar.style.width = "0%";
		bar.className = "file_filelist_totalprogress_noanimation";
		bar.style.display = "none";
		barsafe.style.display = "block";
	}
	document.getElementById('filelist_$ID_totalprogress_removeall').style.display = "none";
	document.getElementById('filelist_$ID_totalprogress_cancelall').style.display = (is_formupload ? "block" : "none");
	document.getElementById('filelist_$ID_totalprogress').className = "file_filelist_totalprogress_div";
	document.getElementById('filelist_$ID_totalprogress_arrow').className = "file_filelist_totalprogress_arrow";
	var items = document.querySelectorAll("#filelist_$ID table.file_filelist_file_table");
	for (var i = 1; i <= items.length; i++) {
		bar = document.getElementById('filelist_$ID_fileprogress_animation' + i);
		document.getElementById('filelist_$ID_fileremove' + i).style.display = "none";
		document.getElementById('filelist_$ID_file' + i).className = "file_filelist_file_div";
		if (effect == "progressive") {
			document.getElementById('filelist_$ID_fileprogress_td' + i).style.display = "";
			bar.style.width = "0%";
			bar.className = "file_filelist_fileprogress_progressive";
		}
		document.getElementById('filelist_$ID_filecancel_td' + i).style.display = (is_formupload ? "none" : "");
	}
}

/**
 *  attach cancel handler to all filelist items
 *  
 *  This function attaches handlers to the appropriate elements (usually onclick
 *  events of cancel buttons or images) that cancel the upload of each
 *  file item of the filelist. The handlers need to execute the received
 *  cancel_function passing their item's zero-based index as parameter.
 *  
 *  @param cancel_function function it holds a function object that must be
 *         executed when the upload of a file item is cancelled passing
 *         file item's zero-based index as parameter
 *  
 *  @return void
 */
this.attachCancelHandlers = function(cancel_function) {
	var items = document.querySelectorAll("#filelist_$ID table.file_filelist_file_table");
	for (var i = 0; i < items.length; i++) {
		var obj = document.getElementById('filelist_$ID_filecancel_imgx' + (i + 1));
		obj.item_index = i;
		if (obj) obj.onclick = function(e) { cancel_function(this.item_index); }
	}
	var elem = document.getElementById("filelist_$ID_totalprogress_cancelall");
	elem.onclick = function(e) { cancel_function(0); }
}

/**
 *  dettach cancel handlers from all filelist item
 *  
 *  @return void
 */
this.dettachCancelHandlers = function() {
	var items = document.querySelectorAll("#filelist_$ID table.file_filelist_file_table");
	for (var i = 0; i < items.length; i++) {
		var obj = document.getElementById('filelist_$ID_filecancel_imgx' + (i + 1));
		if (obj) obj.onclick = null;
	}
}

/**
 *  update the upload progress position of an individual item of the filelist
 *  
 *  @param index integer holds the zero-based index of the file item
 *  @param position float the new progress position, which is a number between
 *         0 and 100
 *  
 *  @return void
 */
this.updateItemProgress = function(index, position) {
	var obj = document.getElementById('filelist_$ID_fileprogress_animation' + (index + 1));
	if (obj) obj.style.width = position.toString() + '%';
}

/**
 *  update the total upload progress position
 *  
 *  @param position float the new total progress position, which is a number
 *         between 0 and 100
 *  
 *  @return void
 */
this.updateTotalProgress = function(position) {
	document.getElementById('filelist_$ID_totalprogress_animation').style.width = position.toString() + '%';
}

/**
 *  resets the filelist total progress element
 *  
 *  @return void
 */
this.resetTotalProgress = function() {
	var bar = document.getElementById('filelist_$ID_totalprogress_animation');
	bar.style.width = "0%";
	bar.className = "file_filelist_totalprogress_noanimation";
}

/**
 *  update the status of an individual item of the filelist
 *  
 *  @param index integer holds the zero-based index of the file item
 *  @param status string the status of the file, it can be 'success' (file
 *         uploaded successfully), 'warning' (file uploaded successfully though
 *         there are warnings), 'error' (file not uploaded) or 'unknown'
 *         (unknown status of file).
 *  
 *  @return void
 */
this.updateItemStatus = function(index, status) {
	if (document.getElementById('filelist_$ID_fileprogress_animation' + (index + 1))) {
		document.getElementById('filelist_$ID_file' + (index + 1)).className = "file_filelist_file_div_with_remove";
		document.getElementById('filelist_$ID_fileremove_imgx' + (index + 1)).style.display = "none";
		document.getElementById('filelist_$ID_fileremove_imgok' + (index + 1)).style.display = ((status == 'success' || status == 'warning') ? "" : "none");
		document.getElementById('filelist_$ID_fileremove_imgunknown' + (index + 1)).style.display = (status == 'unknown' ? "" : "none");
		document.getElementById('filelist_$ID_fileremove_imgfail' + (index + 1)).style.display = (status == 'error' ? "" : "none");
		document.getElementById('filelist_$ID_fileremove' + (index + 1)).style.display = "";
		document.getElementById('filelist_$ID_fileremove' + (index + 1)).onclick = null;
		document.getElementById('filelist_$ID_fileprogress_td' + (index + 1)).style.display = "none";
		document.getElementById('filelist_$ID_filecancel_td' + (index + 1)).style.display = "none";
	}
}

/**
 *  load image previews of the filelist items
 *  
 *  @param files array the array of selected files
 *  
 *  @return void
 */
this._readImageURL = function(files) {
	for (var i = 0; i < files.length; i++) {
		ii = i + 1;
		var imgid = 'filelist_$ID_fileimage_image' + ii;
		if (!document.getElementById(imgid)._src) {
			var reader = new FileReader();
			reader.onload = new Function('e', 'var img = document.getElementById("' + imgid + '"); img._src = img.src = e.target.result;');
			reader.readAsDataURL(files[i]);
		}
	}
}

/**
 *  increase/decrease copies of a filelist item
 *  
 *  @param ii integer the id of the file
 *  @param dif integer the amount of increase/decrease of copies
 *  
 *  @return void
 */
this._changeCopies = function(ii, dif) {
	var i = ii - 1;
	var props = GlobalData.WFU[$ID].filearrayprops;
	if (!props[i].copies) props[i].copies = 1;
	if (props[i].copies + dif > 0) props[i].copies += dif;
	document.getElementById('filelist_$ID_filelabel_copies' + ii).innerHTML = props[i].copies + "x";
}
/* do not change this line */}
</script><?php /****************************************************************
        the following lines contain the HTML template of the file list
****************************************************************************/ ?>
<div id="filelist_$ID" class="file_filelist" style="<?php echo $styles; ?>">
	<div id="filelist_$ID_totalprogress" class="file_filelist_totalprogress_div">
		<div id="filelist_$ID_totalprogress_inner" class="file_filelist_totalprogress_inner">
			<span id="filelist_$ID_totalprogress_animation" class="file_filelist_totalprogress_noanimation">&nbsp;</span>
			<img id="filelist_$ID_totalprogress_imagesafe" class="file_filelist_totalprogress_imagesafe" src="<?php echo WFU_IMAGE_FILELIST_PROGBAR; ?>" style="display:none;" />
		</div>
		<div id="filelist_$ID_totalprogress_arrow" class="file_filelist_totalprogress_arrow" onclick="wfu_filelist_toggle($ID);">
			<div id="filelist_$ID_totalprogress_arrow_up" class="file_filelist_totalprogress_arrow_up" style="display:none;"></div>
			<div id="filelist_$ID_totalprogress_arrow_down" class="file_filelist_totalprogress_arrow_down"></div>
		</div>
		<div id="filelist_$ID_totalprogress_removeall" class="file_filelist_totalprogress_removeall" style="display:none;" onclick="wfu_filelist_removeall($ID)">
			<img id="filelist_$ID_totalprogress_removeall_img" class="file_filelist_totalprogress_removeall_img" src="<?php echo WFU_IMAGE_FILELIST_REMOVE; ?>" />
		</div>
		<div id="filelist_$ID_totalprogress_cancelall" class="file_filelist_totalprogress_cancelall" style="display:none;">
			<img id="filelist_$ID_totalprogress_cancelall_img" class="file_filelist_totalprogress_cancelall_img" src="<?php echo WFU_IMAGE_FILE_CANCEL; ?>" />
		</div>
	</div>
	<div id="filelist_$ID_list_div" class="file_filelist_list_div" style="display:none;">
		<label id="filelist_$ID_filelabel_label0" class="file_filelist_filelabel_label0"><?php echo WFU_WARNING_NOFILES_SELECTED; ?></label>
	</div>
	<input type="hidden" id="filelist_$ID_itemhtml" value="[item_template]" />
	<input type="hidden" id="filelist_$ID_itemhtmlempty" value="[item_template_empty]" />
	<input type="hidden" id="filelist_confirm_clearlist_$ID" value="<?php echo WFU_CONFIRM_CLEARFILES; ?>" />
</div>
<?php /*************************************************************************
      the following lines contain the HTML template of each file list item
*************************************************************/ ?><item_template>
<div id="filelist_$ID_file[id]" class="file_filelist_file_div[additional class]">
	<table id="filelist_$ID_filetable[id]" class="file_filelist_file_table"><tbody><tr>
		<td id="filelist_$ID_fileinc_td[id]" style="width:2ch;">[id].</td>
		<td id="filelist_$ID_fileimage_td[id]" style="width:60px;">
			<div id="filelist_$ID_fileimage_inner[id]" class="file_filelist_fileimage_inner">
				<div id="filelist_$ID_fileimage_btndec[id]" class="file_filelist_fileimage_btn" onclick="GlobalData.WFU[$ID].filelist._changeCopies([id], -1);">-</div>
				<img id="filelist_$ID_fileimage_image[id]" class="file_filelist_fileimage_image" src="#" />
				<div id="filelist_$ID_fileimage_btninc[id]" class="file_filelist_fileimage_btn" onclick="GlobalData.WFU[$ID].filelist._changeCopies([id], 1);">+</div>
			</div>
		</td>
		<td id="filelist_$ID_filelabel_td[id]"><label id="filelist_$ID_filelabel_label[id]" class="file_filelist_filelabel_label" title="[name]"><span id="filelist_$ID_filelabel_copies[id]" class="file_filelist_filelabel_copies">[copies]x</span> - [name]</label></td>
		<td id="filelist_$ID_fileprogress_td[id]" style="width:25%; display:none;">
			<div id="filelist_$ID_fileprogress_inner[id]" class="file_filelist_fileprogress_inner">
				<span id="filelist_$ID_fileprogress_animation[id]" class="file_filelist_fileprogress_noanimation">&nbsp;</span>
			</div>
		</td>
		<td id="filelist_$ID_filecancel_td[id]" style="width:16px; padding:0; line-height:0; display:none;">
			<img id="filelist_$ID_filecancel_imgx[id]" class="file_filelist_filecancel_img" src="<?php echo WFU_IMAGE_FILE_CANCEL; ?>" title="<?php echo WFU_FILE_CANCEL_HINT; ?>" />
		</td>
	</tr></tbody></table>
	<div id="filelist_$ID_fileremove[id]" class="file_filelist_fileremove_div" onclick="wfu_filelist_removefile($ID, [fileid]);"[removebutton visibility]>
		<img id="filelist_$ID_fileremove_imgx[id]" class="file_filelist_fileremove_img" src="<?php echo WFU_IMAGE_FILELIST_REMOVE; ?>" />
		<img id="filelist_$ID_fileremove_imgok[id]" class="file_filelist_fileremove_img" src="<?php echo WFU_IMAGE_FILELIST_OK; ?>" style="display:none;" />
		<img id="filelist_$ID_fileremove_imgunknown[id]" class="file_filelist_fileremove_img" src="<?php echo WFU_IMAGE_FILELIST_UNKNOWN; ?>" style="display:none;" />
		<img id="filelist_$ID_fileremove_imgfail[id]" class="file_filelist_fileremove_img" src="<?php echo WFU_IMAGE_FILELIST_FAIL; ?>" style="display:none;" />
	</div>
</div>
</item_template><?php /*********************************************************
      the following lines contain the HTML template of an empty list item
*******************************************************/ ?><item_template_empty>
<label id="filelist_$ID_filelabel_label0" class="file_filelist_filelabel_label0"><?php echo WFU_WARNING_NOFILES_SELECTED; ?></label>
</item_template_empty><?php /***************************************************
                            end of HTML output 
*****************************************************************************/ }

}