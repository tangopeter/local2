<?php

class WFU_Original_Template {
	
private static $instance = array();

public static function get_instance() {
	$that = get_called_class();
	if ( !isset(self::$instance[$that]) ) {
		self::$instance[$that] = new $that();
	}

	return self::$instance[$that];
}

public static function get_name() {
	return get_called_class();
}

function wfu_base_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
	if ( $testmode ) {}
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
div.wfu_container
{
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
var dummy = 0;
</script><?php /****************************************************************
               the following lines contain additional HTML output 
****************************************************************************/ ?>
<!-- init -->
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_row_container_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $items an array if block items contained in row
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
	$items_count = count($items);
	$item_props = array();
	for ( $i = 0; $i < $items_count; $i++ ) {
		$item_prop["title"] = $items[$i]["title"];
		$item_prop["is_first"] = ( $i == 0 );
		$item_prop["is_last"] = ( $i == $items_count - 1 );
		$style = "";
		if ( $items[$i]["width"] != "" ) $style .= 'width: '.$items[$i]["width"].'; ';
		if ( $items[$i]["hidden"] ) $style .= 'display: none; ';
		$item_prop["style"] = $style;
		$item_prop["lines"] = array();
		$k = 1;
		while ( isset($items[$i]["line".$k]) ) {
			if ( $items[$i]["line".$k] != "" )
				array_push($item_prop["lines"], $items[$i]["line".$k]);
			$k++;
		}
		if ( isset($items[$i]["object"]) ) $item_prop["object"] = $items[$i]["object"];
		array_push($item_props, $item_prop);
	}
/*******************************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
<?php if ( $responsive ): ?>
<?php foreach ( $item_props as $p ): ?>
	<div id="<?php echo $p["title"]; ?>" class="file_div_clean_responsive" style="<?php echo esc_html($p["style"]); ?>">
	<?php foreach ( $p["lines"] as $line ): ?>
		<?php echo $line; ?>
	<?php endforeach ?>
		<div class="file_space_clean"></div>
	<?php if ( isset($p["object"]) ): ?>
		<script type="text/javascript">wfu_run_js("<?php echo $p["object"]; ?>", "init");</script>
	<?php endif ?>
	</div>
<?php endforeach ?>
	<br />
<?php else: ?>
	<div class="file_div_clean">
		<table class="file_table_clean">
			<tbody>
				<tr>
				<?php foreach ( $item_props as $p ): ?>
					<td class="file_td_clean" style="<?php echo ( $p["is_last"] ? "" : "padding: 0 4px 0 0;" ); ?>">
						<div id="<?php echo $p["title"]; ?>" class="file_div_clean" style="<?php echo esc_html($p["style"]); ?>">
						<?php foreach ( $p["lines"] as $line ): ?>
							<?php echo $line; ?>
						<?php endforeach ?>
							<div class="file_space_clean"></div>
						<?php if ( isset($p["object"]) ): ?>
							<script type="text/javascript">wfu_run_js("<?php echo $p["object"]; ?>", "init");</script>
						<?php endif ?>
						</div>
					</td>
				<?php endforeach ?>
				</tr>
			</tbody>
		</table>
	</div>
<?php endif ?>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_visualeditorbutton_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the shortcode ID
 *  @var $shortcode_tag string the shortcode tag
 *  @var $JS_Object string the Javascript object of the visual editor button
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
div.wfu_overlay_editor {
	width: 18px;
	height: 18px;
	padding: 2px;
	box-shadow: 1px 1px 2px #aaa;
	position: absolute;
	top: 0;
	left: 0;
	z-index: 2;
	display: none;
	background-color: white;
	line-height: 1;
}

.wfu_container:hover div.wfu_overlay_editor, .wfu_browser_container:hover div.wfu_overlay_editor {
	display: block;
}

div.wfu_overlay_editor:hover {
	background-color: yellow;
}

button.wfu_overlay_editor_button, button.wfu_overlay_editor_button:focus {
	background: none;
	border: none;
	margin: 0;
	padding: 0;
	width: 100%;
	height: 100%;
	outline: none;
	display: block;
}

img.wfu_overlay_editor_img
{
	vertical-align: top;
}

div.wfu_overlay_container {
	position: absolute;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	margin: 0;
	padding: 0;
	background-color: rgba(255, 255, 255, 0.7);
	z-index: 2;
	display: none;
}

table.wfu_overlay_table, table.wfu_overlay_table tr, table.wfu_overlay_table td {
	border: none;
	margin: 0;
	padding: 0;
	background: none;
	width: 100%;
	height: 100%;
}

table.wfu_overlay_table td {
	text-align: center;
	vertical-align: middle;
}

div.wfu_overlay_container_inner {
	position: absolute;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	margin: 0;
	padding: 0;
	background: none;
}

div.wfu_overlay_container label {
	margin-left: 4px;
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */<?php echo $JS_Object; ?>.init = function() {
/***
 *  The following visual editor button params have been defined and can be used 
 *  in Javascript code:
 *
 *  @var shortcode_tag string the shortcode tag, can be either
 *       "wordpress_file_upload" or "wordpress_file_upload_browser"
 *  
 *  The following visual editor button methods can be defined by the template,
 *  together with other initialization actions:
 *
 *  @method attachInvokeHandler attaches the handler that opens the visual
 *          editor
 *  @method update updates the button status
 *  @method onInvoke executes custom actions when the button is pressed
 *  @method afterInvoke executes custom actions after the button is pressed and
 *          the visual editor has opened
 */
/**
 *  attaches the handler that opens the visual editor
 *  
 *  @param invoke_function function the function that must be run when the
 *         button is clicked in order to open the visual editor
 *  
 *  @return void
 */
this.attachInvokeHandler = function(invoke_function) {
	var btn = document.querySelector("#" + this.shortcode_tag + "_editor_$ID > button");
	if (btn) btn.onclick = function() { invoke_function(); }
}

/**
 *  updates the button status
 *  
 *  @param status string the status of the button, it can have these values:
 *         "on_invoke": runs right after the visual editor button has been
 *             pressed in order to execute custom actions, such as lock the
 *             form until the visual editor opens
 *         "on_open": runs right after the visual editor has opened in order
 *             to execute custom actions, such as unlock the form
 *  
 *  @return void
 */
this.update = function(status) {
	if (status == "on_invoke") {
		document.getElementById(this.shortcode_tag + "_editor_$ID").style.display = "none";
		document.getElementById(this.shortcode_tag + "_overlay_$ID").style.display = "block";
	}
	else if (status == "on_open") {
		document.getElementById(this.shortcode_tag + "_overlay_$ID").style.display = "none";
		document.getElementById(this.shortcode_tag + "_editor_$ID").removeAttribute("style");
	}
}
/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
	<div id="<?php echo $shortcode_tag; ?>_editor_$ID" class="wfu_overlay_editor">
		<button class="wfu_overlay_editor_button" title="<?php echo WFU_PAGE_PLUGINEDITOR_BUTTONTITLE; ?>"><img src="<?php echo WFU_IMAGE_OVERLAY_EDITOR; ?>" class="wfu_overlay_editor_img" width="20px" height="20px" /></button>
	</div>
	<div id="<?php echo $shortcode_tag; ?>_overlay_$ID" class="wfu_overlay_container">
		<table class="wfu_overlay_table"><tbody><tr><td><img src="<?php echo WFU_IMAGE_OVERLAY_LOADING; ?>" /><label><?php echo WFU_PAGE_PLUGINEDITOR_LOADING; ?></label></td></tr></tbody></table>
		<div class="wfu_overlay_container_inner"></div>
	</div>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_dragdrop_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the shortcode ID
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
div.file_drag_target
{
	position: absolute;
	width: 100%;
	height: 100%;
	background-color: rgba(245,245,245,0.9);
	z-index: 1;
	border: 4px dashed silver;
	color: silver;
	font-size: 30px;
	font-weight: bold;
	text-align: center;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

div.file_drag_target_over
{
	position: absolute;
	width: 100%;
	height: 100%;
	background-color: rgba(245,245,245,0.9);
	z-index: 1;
	border: 4px dashed gray;
	color: gray;
	font-size: 30px;
	font-weight: bold;
	text-align: center;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

div.file_drag_target > div, div.file_drag_target_over > div
{
	display: table;
	width: 100%;
	height: 100%;
}

div.file_drag_target > div > div, div.file_drag_target_over > div > div
{
	display: table-cell;
	vertical-align: middle;
}
div.file_drag_target_off {
	display: none;
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].dragdrop.init = function() {
/***
 *  The following visual editor button methods can be defined by the template,
 *  together with other initialization actions:
 *
 *  @method attachHandlers attaches handlers that must be run when files are
 *          dragged and dropped on the upload form
 *  @method disableDragDrop disables drag-drop operation
 *  @method reEnableDragDrop re-enables drag-drop operation
 */
/**
 *  attaches handlers that must be run when files are dragged and dropped on the
 *  upload form
 *  
 *  @param handlers object it contains a list of functions, as object properties
 *         that must be executed on various drag/drop operations:
 *             dragenter: function that must be run when files are dragged
 *                 inside the upload form, takes no parameters
 *             dragover: function that must be run when files are dragged over
 *                 the upload form, takes no parameters
 *             dragleave: function that must be run when files are dragged off
 *                 the upload form, takes no parameters
 *             drop: function that must be run when files are dropped inside the
 *                 upload form, takes one parameter, the list of dropped files
 *             windowdragenter: function that must be run when files are dragged
 *                 inside the window, takes no parameters
 *             windowdragover: function that must be run when files are dragged
 *                 over the window, takes no parameters
 *             windowdragleave: function that must be run when files are dragged
 *                 off the window, takes no parameters
 *             windowdrop: function that must be run when files are dropped
 *                 inside the window (but outside the upload form), takes no
 *                 parameters
 *         Some of these functions may be null.    
 *  
 *  @return void
 */
this.attachHandlers = function(handlers) {
	var target = document.getElementById('wordpress_file_upload_drag_$ID');
	//upload form dragenter handler
	wfu_addEventHandler(target, 'dragenter', function(e) {
		e = e || window.event; // get window.event if e argument missing (in IE)
		if (e.preventDefault) { e.preventDefault(); }
		var item = document.getElementById('wordpress_file_upload_drag_$ID');
		if (item.className != "file_drag_target_over") item.className = "file_drag_target_over";
		if (handlers.dragenter != null && !item.classList.contains("file_drag_target_off")) handlers.dragenter();
		return false;
	});
	//upload form dragover handler
	wfu_addEventHandler(target, 'dragover', function(e) {
		e = e || window.event; // get window.event if e argument missing (in IE)
		if (e.preventDefault) { e.preventDefault(); }
		var item = document.getElementById('wordpress_file_upload_drag_$ID');
		if (item.className != "file_drag_target_over") item.className = "file_drag_target_over";
		if (handlers.dragover != null && !item.classList.contains("file_drag_target_off")) handlers.dragover();
		return false;
	});
	//upload form dragleave handler
	wfu_addEventHandler(target, 'dragleave', function(e) {
		e = e || window.event; // get window.event if e argument missing (in IE)
		if (e.preventDefault) { e.preventDefault(); }
		var item = document.getElementById('wordpress_file_upload_drag_$ID');
		item.className = "file_drag_target";
		if (handlers.dragleave != null && !item.classList.contains("file_drag_target_off")) handlers.dragleave();
	});
	//upload form drop handler
	wfu_addEventHandler(target, 'drop', function(e) {
		let _getFilesDataTransferItems = function(dataTransferItems) {
			let _traverseFileTreePromise = function(item, path = "", fs) {
				return new Promise(resolve => {
					if (item.isFile) {
						item.file(file => {
							file.filepath = path || "" + file.name;
							fs.push(file);
							resolve(file);
						});
					}
					else if (item.isDirectory && allowdir) {
						let dirReader = item.createReader();
						dirReader.readEntries(entries => {
							let entriesPromises = [];
							for (let entr of entries)
								entriesPromises.push(
									_traverseFileTreePromise(entr, path || ""  + item.name + "/", fs)
								);
							resolve(Promise.all(entriesPromises));
						});
					}
				});
			}
			let files = [];
			return new Promise((resolve, reject) => {
				let entriesPromises = [];
				for (let it of dataTransferItems)
					entriesPromises.push(
						_traverseFileTreePromise(it.webkitGetAsEntry(), null, files)
					);
				Promise.all(entriesPromises).then(entries => {
					resolve(files);
				});
			});
		}
		var allowdir = <?php echo ( $params["multiple"] == "true" && $params["allowdir"] == "true" ? "true" : "false" ); ?>;
		var item = document.getElementById('wordpress_file_upload_drag_$ID');
		if (handlers.drop != null && !item.classList.contains("file_drag_target_off")) {
			e = e || window.event; // get window.event if e argument missing (in IE)
			if (e.preventDefault) { e.preventDefault(); }
			var dt = e.dataTransfer;
			if (dt.items) {
				_getFilesDataTransferItems(dt.items).then(files => {
					handlers.drop(files);
				});
			}
			else {
				var files = dt.files;
				handlers.drop(files);
			}
			return false;
		}
	});
	//window dragenter handler
	wfu_addEventHandler(window, 'dragenter', function(e) {
		e = e || window.event; // get window.event if e argument missing (in IE)
		if (e.preventDefault) { e.preventDefault(); }
		var item = document.getElementById('wordpress_file_upload_drag_$ID');
		if (!item.classList.contains("file_drag_target_off")) {
			item.style.display = "";
			if (handlers.windowdragenter != null) handlers.windowdragenter();
		}
	});
	//window dragover handler
	wfu_addEventHandler(window, 'dragover', function(e) {
		var item = document.getElementById('wordpress_file_upload_drag_$ID');
		if (handlers.windowdragover != null && !item.classList.contains("file_drag_target_off")) {
			e = e || window.event; // get window.event if e argument missing (in IE)
			if (e.preventDefault) { e.preventDefault(); }
			handlers.windowdragover();
		}
	});
	//window dragleave handler
	wfu_addEventHandler(window, 'dragleave', function(e) {
		e = e || window.event; // get window.event if e argument missing (in IE)
		if (e.preventDefault) { e.preventDefault(); }
		var item = document.getElementById('wordpress_file_upload_drag_$ID');
		if (e.x == 0 && e.y == 0) item.style.display = "none";
		if (handlers.windowdragleave != null && !item.classList.contains("file_drag_target_off")) handlers.windowdragleave();
	});
	//window drop handler
	wfu_addEventHandler(window, 'drop', function(e) {
		e = e || window.event; // get window.event if e argument missing (in IE)
		if (e.preventDefault) { e.preventDefault(); }
		var item = document.getElementById('wordpress_file_upload_drag_$ID');
		item.style.display = "none";
		if (handlers.windowdrop != null && !item.classList.contains("file_drag_target_off")) handlers.windowdrop();
	});
}

/**
 *  disables drag-drop operation (e.g. in case the upload form is locked)
 *
 *  @return void
 */
this.disableDragDrop = function() {
	document.getElementById("wordpress_file_upload_drag_$ID").classList.add("file_drag_target_off");
}

/**
 *  re-enables drag-drop operation (e.g. in case the upload form was unlocked)
 *
 *  @return void
 */
this.reEnableDragDrop = function() {
	document.getElementById("wordpress_file_upload_drag_$ID").classList.remove("file_drag_target_off");
	document.getElementById('wordpress_file_upload_drag_$ID').style.display = "none";
}
/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
	<div id="wordpress_file_upload_drag_$ID" class="file_drag_target" style="display:none;"><div><div><?php echo WFU_DROP_HERE_MESSAGE; ?></div></div></div>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_title_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of title element
 *  @var $height string assigned height of title element
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $title string the title text
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
	if ( $height != "" ) $styles .= "height: $height; ";
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
span.file_title_clean
{
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border-style: none; /*relax*/
	background: none; /*relax*/
	color: black; /*relax*/
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].title.init = function() {

/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
<span class="file_title_clean" style="<?php echo esc_html($styles); ?>"><?php echo esc_html($title); ?></span>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_textbox_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of textbox element
 *  @var $height string assigned height of textbox element
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
	if ( $height != "" ) $styles .= "height: $height; ";
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
input[type="text"].file_input_textbox
{
	 position: relative;
	 width: 150px; /*relax*/
	 height: 25px; /*relax*/
	 margin: 0px; /*relax*/
	 padding: 0px; /*relax*/
	 border: 1px solid; /*relax*/
	 border-color: #BBBBBB; /*relax*/
	 background-color: white; /*relax*/
	 color: black; /*relax*/
}

input[type="text"].file_input_textbox:disabled
{
	 position: relative;	
	 width: 150px; /*relax*/
	 height: 25px; /*relax*/
	 margin: 0px; /*relax*/
	 padding: 0px; /*relax*/
	 border: 1px solid; /*relax*/
	 border-color: #BBBBBB; /*relax*/
	 background-color: white; /*relax*/
	 color: silver; /*relax*/
}

input[type="text"].file_input_textbox_nofile
{
	 position: relative;	
	 width: 150px; /*relax*/
	 height: 25px; /*relax*/
	 margin: 0px; /*relax*/
	 padding: 0px; /*relax*/
	 border: 1px solid; /*relax*/
	 border-color: #BBBBBB; /*relax*/
	 background-color: red;
	 color: black; /*relax*/
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].textbox.init = function() {
/***
 *  The following textbox methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method attachCancelHandler attaches a cancel handler to the textbox that
 *            cancels the upload
 *  @method dettachCancelHandler dettaches the cancel handler
 *  @method update updates the textbox contents
 */
/**
 *  attaches a cancel handler to textbox item
 *  
 *  If it is set, this method attaches a cancel handler to the textbox element
 *  which cancels the current upload of files. The handler is executed when the
 *  Esc button is pressed while the cursor is inside the textbox.
 *  
 *  @param cancel_function function it holds a function object that must be
 *         executed when the user presses the Esc button while the cursor is
 *         inside the textbox. The function returns true if the upload has been
 *         cancelled or false if cancellation has been aborted for some reason.
 *  
 *  @return void
 */
this.attachCancelHandler = function(cancel_function) {
	var textbox = document.getElementById('fileName_$ID');
	textbox.onkeyup = function(e) {
		var result = false;
		if (e.keyCode == 27) result = cancel_function();
		//if cancellation was executed then detach the handler
		if (result) textbox.onkeyup = null;
	}
}

/**
 *  dettaches cancel handler from textbox item
 *  
 *  If attachCancelHandler is set, then dettachCancelHandler must also be set as
 *  a function to dettach the cancel handler.
 *  
 *  @return void
 */
this.dettachCancelHandler = function() {
	var textbox = document.getElementById('fileName_$ID');
	textbox.onkeyup = null;
}

/**
 *  updates textbox status depending on action
 *  
 *  If it is set, this method adjusts the textbox contents and appearance
 *  depending on action variable. If action is 'clear' then the textbox contents
 *  are cleared. If action is 'set' then the contents are set as a comma-
 *  separated list of file names. If action is 'nofile' then the textbox becomes
 *  red notifying that no file has been selected. If action is 'lock' or
 *  'unlock' then the textbox is prepared for locking of plugin elements right
 *  before upload and unlocking right after upload. The default textbox does not
 *  change anything on 'lock' or 'unlock' actions. There is also 'init' action
 *  which occurs when the 'Select Files' button of the upload form is pressed.
 *  At the moment 'init' action resets the textbox class.
 *  
 *  @param action string the update action. Can be 'init', 'clear', 'set',
 *         'nofile', 'lock' and 'unlock'.
 *  @param filenames array it holds an array of names of the uploaded files if
 *         action is 'set'.
 *  
 *  @return void
 */
this.update = function(action, filenames) {
	var textbox = document.getElementById('fileName_$ID');
	if (action == "init" && textbox.className == "file_input_textbox_nofile") {
		textbox.value = "";
		textbox.className = "file_input_textbox";
	}
	else if (action == "clear") {
		textbox.value = "";
		textbox.className = "file_input_textbox";
	}
	else if (action == "set") {
		var txt = '';
		for (var i = 0; i < filenames.length; i++) {
			if (txt != '') txt += ', ';
//			txt += filenames[i].replace(/c:\\fakepath\\/i, "");
			var dbs = String.fromCharCode(92);
			txt += filenames[i].replace(new RegExp('c:' + dbs + dbs + 'fakepath' + dbs + dbs, 'i'), "");
		}
		textbox.value = txt;	
		textbox.className = "file_input_textbox";
	}
	else if (action == "nofile") {
		textbox.value = GlobalData.consts.nofilemessage;
		textbox.className = "file_input_textbox_nofile";
	}
	else if (action == "lock") {
	}
	else if (action == "unlock") {
	}
}
/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
<input type="text" id="fileName_$ID" class="file_input_textbox" style="<?php echo esc_html($styles); ?>" readonly="readonly" />
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_progressbar_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of progress bar element
 *  @var $height string assigned height of progress bar element
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $index int the index of occurrence of the element inside the plugin,
 *       in case that it appears more than once
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
	$styles = "";
	$styles2 = "";
	//for responsive plugin adjust container's widths if a % width has been defined
	if ( $responsive && strlen($width) > 1 && substr($width, -1, 1) == "%" ) $styles = 'width: 100%;';
	elseif ( $width != "" ) $styles = "width: $width;";
	if ( $width != "" ) $styles2 .= 'width: auto; ';
	if ( $height != "" ) $styles2 .= "height: $height; ";
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
div.file_progress_bar
{
	display: block;
	position: relative;
	width: 100px; 
	border: 1px solid #333333;
	margin: 0;
	padding: 4px;
}

div.file_progress_inner
{
	display: block;	
	width: 100%;
	height: 6px;
	margin: 0;
	padding: 0;
	border: 1px solid silver;
	background-color: white;
}

img.file_progress_imagesafe
{
	width: 100%;
	height: 6px;
}

span.file_progress_noanimation
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

span.file_progress_progressive
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

span.file_progress_shuffle
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

span.file_progress_progressive:after
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
/* do not change this line */GlobalData.WFU[$ID].progressbar.init = function() {
/***
 *  The following progress bar methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method show shows the progress bar in its initial state
 *  @method hide hides the progress bar
 *  @method update updates the progress bar position
 */
/**
 *  shows the progress bar in initial state
 *  
 *  @param string mode defines whether the upload provides progress
 *         information (mode: progressive) or not (mode: shuffle)
 *
 *  @return void
 */
this.show = function(mode) {
	var bar = document.getElementById('progressbar_$ID_animation');
	var barsafe = document.getElementById('progressbar_$ID_imagesafe');
	if (bar) {
		if (mode == "progressive") {
			bar.style.width = "0%";
			bar.className = "file_progress_progressive";
			barsafe.style.display = "none";
			bar.style.display = "block";
		}
		else if (wfu_BrowserCaps.supportsAnimation) {
			bar.style.width = "25%";
			bar.className = "file_progress_shuffle";
			barsafe.style.display = "none";
			bar.style.display = "block";
		}
		else {
			bar.style.width = "0%";
			bar.className = "file_progress_noanimation";
			bar.style.display = "none";
			barsafe.style.display = "block";
		}
		document.getElementById('wordpress_file_upload_progressbar_$ID').style.display = "block";
	}
}

/**
 *  hides the progress bar
 *  
 *  @return void
 */
this.hide = function() {
	var bar = document.getElementById('progressbar_$ID_animation');
	var barsafe = document.getElementById('progressbar_$ID_imagesafe');
	if (bar) {
		document.getElementById('wordpress_file_upload_progressbar_$ID').style.display = "none";
		bar.style.width = "0%";
		bar.className = "file_progress_noanimation";
		barsafe.style.display = "none";
		bar.style.display = "block";
	}
}

/**
 *  updates the progress position of the progress bar
 *  
 *  @param float position the new progress position of the progress bar, which
 *         is a number between 0 and 100
 *
 *  @return void
 */
this.update = function(position) {
	document.getElementById('progressbar_$ID_animation').style.width = position.toString() + '%';
}
/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
<div id="progressbar_$ID" class="file_div_clean<?php echo ( $responsive ? '_responsive' : '' ); ?>" style="<?php echo esc_html($styles); ?>">
	<div id="progressbar_$ID_outer" class="file_progress_bar" style="<?php echo esc_html($styles2); ?>">
		<div id="progressbar_$ID_inner" class="file_progress_inner">
			<span id="progressbar_$ID_animation" class="file_progress_noanimation">&nbsp;</span>
			<img id="progressbar_$ID_imagesafe" class="file_progress_imagesafe" src="<?php echo WFU_IMAGE_SIMPLE_PROGBAR; ?>" style="display:none;" />
		</div>
	</div>
</div>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

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
	white-space: nornal;
	text-overflow: ellipsis;
	overflow: hidden;
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
			for (var i = 0; i < files.length; i++) {
				ii = i + 1;
				html = html_item.replace(/\[id\]/g, ii);
				html = html.replace(/\[fileid\]/g, i);
				html = html.replace(/\[name\]/g, files[i].name);
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
/* do not change this line */}
</script><?php /****************************************************************
        the following lines contain the HTML template of the file list
****************************************************************************/ ?>
<div id="filelist_$ID" class="file_filelist" style="<?php echo esc_html($styles); ?>">
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
		<td id="filelist_$ID_filelabel_td[id]"><label id="filelist_$ID_filelabel_label[id]" class="file_filelist_filelabel_label" title="[name]">[name]</label></td>
		<td id="filelist_$ID_fileprogress_td[id]" style="width:50%; display:none;">
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

function wfu_subfolders_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $widths array assigned widths of subfolder elements
 *  @var $heights array assigned heights of subfolder elements
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $show_uploadfolder bool true if the upload folder must be shown
 *  @var $show_subfolders bool true if a list of subfolders must be shown
 *  @var $editable bool true if the list of subfolders must be editable
 *  @var $uploadfolder string the upload folder path
 *  @var $uploadfolder_title string the title of the upload folder element
 *  @var $subfolders array holds the list of subfolders; it contains the
 *       following items:
 *         'path' array of the paths of the subfolders
 *         'label' array of the labels of the subfolders
 *         'level' array of the levels of the subfolders below the base folder
 *            (the one defined in uploadpath attribute)
 *         'default' array of boolean values of the subfolders, it is true for
 *             the subfolder that it is the default one
 *  @var $subfolders_title string the title of the subfolders element
 *  @var $index int the index of occurrence of the element inside the plugin,
 *       in case that it appears more than once
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
	//get Relax CSS option value from the plugin's Settings
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$relaxcss = false;
	if ( isset($plugin_options['relaxcss']) ) $relaxcss = ( $plugin_options['relaxcss'] == "1" );

	$width1 = $widths['uploadfolder_label'];
	$height1 = $heights['uploadfolder_label'];
	$width2 = $widths['subfolders_label'];
	$height2 = $heights['subfolders_label'];
	$width3 = $widths['subfolders_select'];
	$height3 = $heights['subfolders_select'];
	$width4 = $widths['subfolders'];

	$styles1 = "";
	$styles2 = "";
	$styles3 = "border: 1px solid; border-color: #BBBBBB;";
	$styles4 = "";
	if ( $width1 != "" ) $styles1 .= 'width: '.$width1.'; display:inline-block;';
	if ( $height1 != "" ) $styles1 .= 'height: '.$height1.'; ';
	if ( $width2 != "" ) $styles2 .= 'width: '.$width2.'; display:inline-block;';
	if ( $height2 != "" ) $styles2 .= 'height: '.$height2.'; ';
	if ( $width3 != "" ) $styles4 .= 'width: '.$width3.'; ';
	if ( $height3 != "" ) $styles4 .= 'height: '.$height3.'; ';
	$styles3 = ( $relaxcss ? '' : $styles3 ).$styles4;

	//detect the default subfolder
	$default = -1;
	foreach ($subfolders['path'] as $ind => $subfolder)
		if ( $subfolders['default'][$ind] ) $default = intval($ind) + 1;	
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
span.subfolder_dir
{
}

span.subfolder_label
{
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border-style: none; /*relax*/
	background: none; /*relax*/
	color: black; /*relax*/
}

div.subfolder_container
{
	margin: 0px;
	padding: 0px;
	height: 25px; /*relax*/
	border-style: none;
	background: none;
	color: black; /*relax*/
	position: relative;
	display: inline-block;
}

div.subfolder_autoplus_container
{
	margin: 0;
	padding: 0 20px 0 0;
	border-style: none;
	background: none;
	display: inline-block;
	width: 100%;
	height: 100%;
}

div.subfolder_autoplus_select_container
{
	position: absolute;
	width: 100%;
	height: 100%;
	top: 0;
	left: 100%;
	margin: 0 0 0 -20px;
	padding: 0;
	border: none;
	background: none;
	overflow: hidden;
}

input[type="text"].subfolder_autoplus
{
	width: 100%;
	height: 100%; /*relax*/
	border: none; /*relax*/
	box-shadow: none; /*relax*/
	padding: 0; /*relax*/
	margin: 0; /*relax*/
}

input[type="text"].subfolder_autoplus:disabled
{
	width: 100%;
	height: 100%; /*relax*/
	border: none; /*relax*/
	box-shadow: none; /*relax*/
	padding: 0; /*relax*/
	margin: 0; /*relax*/
	color: silver; /*relax*/
}

input[type="text"].subfolder_autoplus_match
{
	width: 100%;
	height: 100%; /*relax*/
	border: none; /*relax*/
	box-shadow: none; /*relax*/
	padding: 0; /*relax*/
	margin: 0; /*relax*/
	font-weight: bold;
	font-style: italic;
}

input[type="text"].subfolder_autoplus_match:disabled
{
	width: 100%;
	height: 100%; /*relax*/
	border: none; /*relax*/
	box-shadow: none; /*relax*/
	padding: 0; /*relax*/
	margin: 0; /*relax*/
	font-weight: bold;
	font-style: italic;
	color: silver; /*relax*/
}

input[type="text"].subfolder_autoplus_empty
{
	width: 100%;
	height: 100%; /*relax*/
	border: none; /*relax*/
	box-shadow: none; /*relax*/
	padding: 0; /*relax*/
	margin: 0; /*relax*/
	color: silver; /*relax*/
	font-style: italic;
}

input[type="text"].subfolder_autoplus_prompt
{
	width: 100%;
	height: 100%; /*relax*/
	border: none; /*relax*/
	box-shadow: none; /*relax*/
	padding: 0; /*relax*/
	margin: 0; /*relax*/
	background: red;
}

select.subfolder_dropdown
{
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	height: 25px; /*relax*/
	border: none; /*relax*/
	background: none; /*relax*/
	color: black; /*relax*/
}

select.subfolder_autoplus_dropdown
{
	width: 100%;
    height: 100%;
    left: -100%;
    position: absolute;
    margin-left: 20px;
	margin-top: 0px; /*relax*/
	margin-bottom: 0px; /*relax*/
	margin-right: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: none; /*relax*/
	background: none; /*relax*/
	color: black; /*relax*/
}

select.subfolder_dropdown_prompt
{
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	height: 25px; /*relax*/
	border: none; /*relax*/
	background: red;
	color: black; /*relax*/
}

select.subfolder_autoplus_dropdown_prompt
{
	width: 100%;
    height: 100%;
    left: -100%;
    position: absolute;
    margin-left: 20px;
	background: red;
	margin-top: 0px; /*relax*/
	margin-bottom: 0px; /*relax*/
	margin-right: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: none; /*relax*/
	color: black; /*relax*/
}

select.subfolder_dropdown:disabled
{
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	height: 25px; /*relax*/
	border: none; /*relax*/
	background: none; /*relax*/
	color: silver; /*relax*/
}

select.subfolder_autoplus_dropdown:disabled
{
	width: 100%;
    height: 100%;
    left: -100%;
    position: absolute;
    margin-left: 20px;
	margin-top: 0px; /*relax*/
	margin-bottom: 0px; /*relax*/
	margin-right: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: none; /*relax*/
	background: none; /*relax*/
	color: silver; /*relax*/
}

select.subfolder_dropdown option, select.subfolder_dropdown_prompt option
{
	background: white;
	color: black; /*relax*/
}

select.subfolder_autoplus_dropdown option, select.subfolder_autoplus_dropdown_prompt option
{
	background: white;
	color: black; /*relax*/
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].subfolders.init = function() {
/***
 *  The following subfolder methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method check checks if a subfolder has been selected
 *  @method index returns the index of the selected subfolder
 *  @method reset resets the subfolder item to its initial state
 *  @method toggle activates or deactivates the subfolder item
 */
/**
 *  checks if a subfolder has been selected
 *  
 *  If the subfolders feature is activated, then the user must select a
 *  subfolder before the upload. This function checks if a subfolder has been
 *  selected, returning true or false. During the check, the function must also
 *  update the internal value, kept by the plugin, with the selected subfolder.
 *  To do this, the function must call this.update_handler(), passing the
 *  selected subfolder as parameter.
 *  
 *  @return bool true if a subfolder has been selected, false if not.
 */
this.check = function() {
	//synchronize editbox with selected value
	if (this._editable && this._sel.selectedIndex > 0) {
		this._editbox.value = this._sel.value.replace(/^\s+/,"");
		this._set_editbox_status("match");
		this._editbox_changed();
	}
	
	if (this._editable) this.update_handler(this._editbox.value);
	else this.update_handler(this._sel.selectedIndex);
	
	if ((!this._editable && this._sel.selectedIndex == 0) || (this._editable && (this._editbox.value == '' || this._get_editbox_status() == "empty"))) {
		if (this._editable) this._editbox.value = "";
		this._set_select_status("prompt");
		return false;
	}
	else {
		this._set_select_status("normal");
		this._sel.options[0].style.display = "none";
		return true;
	}
}

/**
 *  returns the index of the selected subfolder
 *  
 *  It returns the index of the selected subfolder. If the subfolder item is
 *  editable then the function returns the selected subfolder, not its index.
 *  
 *  @return string the index of the selected subfolder.
 */
this.index = function() {
	if (this._editable) return this._editbox.value;
	else return this._sel.selectedIndex;
}

/**
 *  resets the subfolder item to its initial state
 *  
 *  This function runs after un uploaded has been completed, in order to clear
 *  the subfolder item and return it to its original state.
 *  
 *  @return void
 */
this.reset = function() {
	if (!this._editable) {
		this._sel.options[0].style.display = "block";
		this._sel.selectedIndex = parseInt(document.getElementById('selectsubdirdefault_$ID').value);
		if (this._sel.selectedIndex < 0) this._sel.selectedIndex = 0;
	}
	else {
		this._sel.selectedIndex = -1;
		this._editbox.value = '';
		this._editbox_exit();
	}
}

/**
 *  activates or deactivates the subfolder item
 *  
 *  This function activates or deactivates the subfolder item, based on the
 *  state of the 'enabled' parameter (true or false).
 *  
 *  @param enabled bool true if the subfolder item must be enabled, false if the
 *         subfolder item must be disabled.
 *  
 *  @return void
 */
this.toggle = function(enabled) {
	this._sel.disabled = !enabled;
	if (this._editable) this._editbox.disabled = !enabled;
}

//************* Internal Function Definitions **********************************

/**
 *  editbox onfocus event
 *  
 *  This function runs when the subfolders are editable and the editbox receives
 *  focus.
 *  
 *  @return void
 */
this._editbox_enter = function() {
	if (this._get_editbox_status() == "empty") this._editbox.value = "";
	this._set_select_status("editing");
}
 
/**
 *  editbox onblur event
 *  
 *  This function runs when the subfolders are editable and the editbox loses
 *  focus.
 *  
 *  @return void
 */
this._editbox_exit = function() {
	if (this._editbox.value == "") {
		this._editbox.value = GlobalData.consts.wfu_subdir_typedir;
		this._set_editbox_status("empty");
	}
}

/**
 *  editbox onchange event
 *  
 *  This function runs when the subfolders are editable and the editbox value
 *  changes.
 *  
 *  @return void
 */
this._editbox_changed = function() {
	if (!!this._freeze_editbox) return; 
	var editbox_status = this._get_editbox_status();
	if (editbox_status == "disabled") return;
	if (editbox_status == "empty") {
		this._sel.selectedIndex = 0;
		this.update_handler('');
		return;
	}
	this.update_handler(this._editbox.value);
	var found = false, opt;
	for (var i = 1; i < this._sel.options.length; i++) {
		opt = this._sel.options[i].value.replace(/^\s+/,"").toLowerCase();
		if (this._editbox.value.length >= 3) {
			if (opt.substr(0, this._editbox.value.length) == this._editbox.value.toLowerCase()) this._sel.options[i].style.display = 'block';
			else this._sel.options[i].style.display = 'none';
		}
		else this._sel.options[i].style.display = 'block';
		if (this._sel.options[i].value.replace(/^\s+/,"") == this._editbox.value) {
			this._sel.selectedIndex = i;
			this._set_editbox_status("match");
			found = true;
		}
	}
	if (!found) {
		this._sel.selectedIndex = 0;
		this._set_editbox_status("normal");
	}
}

/**
 *  set status of subfolder item
 *  
 *  This function changes the appearance of the subfolder item based on the new
 *  status.
 *  
 *  @return void
 */
this._set_select_status = function(status) {
	if (this._editable) {
		if (status == "prompt") {
			this._sel.className = 'subfolder_autoplus_dropdown_prompt';
			this._freeze_editbox = true;
			this._set_editbox_status("prompt");
			this._freeze_editbox = false;
		}
		else if (status == "normal") {
			this._sel.className = 'subfolder_autoplus_dropdown';
			if (this._get_editbox_status() == "prompt") this._set_editbox_status("normal");
		}
		else if (status == "editing") {
			this._sel.className = 'subfolder_autoplus_dropdown';
			this._set_editbox_status("normal");
		}
	}
	else {
		if (status == "prompt") this._sel.className = 'file_item_clean_prompt subfolder_dropdown_prompt';
		else if (status == "normal") this._sel.className = 'file_item_clean subfolder_dropdown';
	}
}

/**
 *  get status of editbox item
 *  
 *  This function gets the status of the editbox item, that is used when the
 *  subfolders are editable.
 *  
 *  @return string the editbox status
 */
this._get_editbox_status = function() {
	if (this._editbox.style.display == "none") return "disabled";
	if (this._editbox.className == "file_item_clean_empty subfolder_autoplus_empty") return "empty";
	if (this._editbox.className == "file_item_clean_match subfolder_autoplus_match") return "match";
	if (this._editbox.className == "file_item_clean_prompt subfolder_autoplus_prompt") return "prompt";
	if (this._editbox.className == "file_item_clean subfolder_autoplus") return "normal";
	return "normal";
}

/**
 *  set status of editbox item
 *  
 *  This function changes the appearance of the editbox item based on the new
 *  status.
 *  
 *  @return void
 */
this._set_editbox_status = function(status) {
	if (this._editable) {
		if (status == "empty") this._editbox.className = "file_item_clean_empty subfolder_autoplus_empty";
		else if (status == "match") this._editbox.className = "file_item_clean_match subfolder_autoplus_match";
		else if (status == "prompt") this._editbox.className = "file_item_clean_prompt subfolder_autoplus_prompt";
		else if (status == "normal") this._editbox.className = "file_item_clean subfolder_autoplus";
	}
}

//************* Additional Initialization Actions ******************************

this._sel = document.getElementById("selectsubdir_$ID");
this._editbox = document.getElementById("selectsubdiredit_$ID");
if (this._editbox) {
	this._editable = (this._get_editbox_status() != "disabled");
	//attach subfolder edit box handlers if it is editable
	if (this._editable) wfu_attach_element_handlers(this._editbox, new Function("GlobalData.WFU[$ID].subfolders._editbox_changed();"));
}

/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
<?php if ( $show_uploadfolder ): ?>
<span class="subfolder_dir" style="<?php echo esc_html($styles1); ?>"><?php echo esc_html($uploadfolder_title); ?>: <strong><?php echo esc_html($uploadfolder); ?></strong></span><br />
<?php endif ?>
<?php if ( $show_subfolders ): ?>
<span class="file_item_clean subfolder_label" style="<?php echo esc_html($styles2); ?>"><?php echo esc_html($subfolders_title); ?> </span>
<div class="file_item_clean subfolder_container" style="<?php echo esc_html($styles4); ?>">
	<div class="file_item_clean_inner subfolder_autoplus_container" style="<?php echo ( $editable ? '' : 'display:none;' ); ?>">
		<input type="text" id="selectsubdiredit_$ID" class="file_item_clean_empty subfolder_autoplus_empty" value="<?php echo WFU_SUBDIR_TYPEDIR; ?>" style="<?php echo ( $editable ? '' : 'display:none;' ); ?>" onchange="GlobalData.WFU[$ID].subfolders._editbox_changed();" onfocus="GlobalData.WFU[$ID].subfolders._editbox_enter();" onblur="GlobalData.WFU[$ID].subfolders._editbox_exit();" />
	</div>
	<?php if ( $editable ): ?>
	<div class="subfolder_autoplus_select_container">
	<?php endif ?>
	<select class="<?php echo ( $editable ? 'subfolder_autoplus_dropdown' : 'file_item_clean subfolder_dropdown' ); ?>" style="<?php echo esc_html($styles3); ?>" id="selectsubdir_$ID" onchange="GlobalData.WFU[$ID].subfolders.check();">
	<?php if ( $testmode ): ?>
		<option><?php echo WFU_NOTIFY_TESTMODE; ?></option>
	<?php else: ?>
		<option style="<?php echo ( $editable || $default != -1 ? 'display:none;' : '' ); ?>"><?php echo WFU_SUBDIR_SELECTDIR; ?></option>
	<?php endif ?>
	<?php foreach( $subfolders['path'] as $ind => $subfolder ): ?>
		<option<?php echo ( $subfolders['default'][$ind] ? ' selected="selected"' : '' ); ?>><?php echo str_repeat("&nbsp;&nbsp;&nbsp;", intval($subfolders['level'][$ind])).esc_html($subfolders['label'][$ind]); ?></option>
	<?php endforeach ?>
	</select>
	<?php if ( $editable ): ?>
	</div>
	<?php endif ?>
</div>
<input id="selectsubdirdefault_$ID" type="hidden" value="<?php echo $default; ?>" />
<?php endif ?>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_uploadform_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of select button element
 *  @var $height string assigned height of select button element
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $multiple bool true if multiple files can be selected for upload
 *  @var $allowdir bool true if directories can be selected for upload
 *  @var $label string the title of the select button element
 *  @var $filename string the name that the selected file must have when
 *       submitted for upload by the form; it must be passed to the 'name'
 *       attribute of the form's input element of 'file' type
 *  $var hidden_elements array holds an array of hidden elements that must be
 *       added in the HTML form so that the plugin works correctly; every item
 *       of the array has three properties, the 'id', the 'name' and the 'value'
 *       of the hidden element
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
	if ( $height != "" ) $styles .= "height: $height; ";
	$styles_form = $styles;
	if ( $testmode ) $styles .= 'z-index: 500;';
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
form.file_input_uploadform
{
	position: relative; 
	width: 100px; /*relax*/
	height: 27px; /*relax*/
	overflow: hidden;
	margin: 0px;
	padding: 0px;
}

input[type="button"].file_input_button
{
	width: 100px; /*relax*/
	height: 27px; /*relax*/
	position: absolute; /*relax*/
	top: 0px; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	background-color: #EEEEEE; /*relax*/
	color: #555555; /*relax*/
	background-image: url("<?php echo WPFILEUPLOAD_DIR; ?>images/white-grad-active.png"); /*relax*/
	background-position: left top; /*relax*/
	background-repeat: repeat-x; /*relax*/
	border-style: solid; /*relax*/
	border-width: 1px; /*relax*/
	border-color: #BBBBBB; /*relax*/
	-webkit-border-radius: 2px; /*relax*/
	-moz-border-radius: 2px; /*relax*/
	-khtml-border-radius: 2px; /*relax*/
	border-radius: 2px; /*relax*/
}

input[type="button"].file_input_button_hover
{
	width: 100px; /*relax*/
	height: 27px; /*relax*/
	position: absolute; /*relax*/
	top: 0px; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	background-color: #EEEEEE; /*relax*/
	color: #111111; /*relax*/
	background-image: url("<?php echo WPFILEUPLOAD_DIR; ?>images/white-grad-active.png"); /*relax*/
	background-position: left top; /*relax*/
	background-repeat: repeat-x; /*relax*/
	border-style: solid; /*relax*/
	border-width: 1px; /*relax*/
	border-color: #333333; /*relax*/
	-webkit-border-radius: 2px; /*relax*/
	-moz-border-radius: 2px; /*relax*/
	-khtml-border-radius: 2px; /*relax*/
	border-radius: 2px; /*relax*/
}

input[type="button"].file_input_button.directory_upload, input[type="button"].file_input_button_hover.directory_upload {
	background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3Csvg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='8' height='8' viewBox='0 0 8 8'%3E%3Ccircle cx='4' cy='4' r='4' fill='blue' /%3E%3C/svg%3E%0A");
	background-repeat: no-repeat no-repeat;
	background-position: 1px 1px;
}

input[type="button"].file_input_button:disabled, input[type="button"].file_input_button_hover:disabled
{
	width: 100px; /*relax*/
	height: 27px; /*relax*/
	position: absolute; /*relax*/
	top: 0px; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	background-color: #EEEEEE; /*relax*/
	color: silver; /*relax*/
	background-image: url("<?php echo WPFILEUPLOAD_DIR; ?>images/white-grad-active.png"); /*relax*/
	background-position: left top; /*relax*/
	background-repeat: repeat-x; /*relax*/
	border-style: solid; /*relax*/
	border-width: 1px; /*relax*/
	border-color: #BBBBBB; /*relax*/
	-webkit-border-radius: 2px; /*relax*/
	-moz-border-radius: 2px; /*relax*/
	-khtml-border-radius: 2px; /*relax*/
	border-radius: 2px; /*relax*/
}

input[type="file"].file_input_hidden
{
	display: none;
	font-size: 45px; 
	position: absolute;
	right: 0px; 
	top: 0px; 
	margin: 0px;
	padding: 0px;
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
	filter: alpha(opacity=0);
	-moz-opacity: 0;
	-khtml-opacity: 0;
	opacity: 0;
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].uploadform.init = function() {
/***
 *  The following uploadform methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method attachActions attaches necessary actions of the plugin that must be
 *          run when the select button is clicked or when the user changes the
 *          selected file
 *  @method selectClickAction attached click action to select button
 *  @method fileDialogClosed runs when file select dialog closes
 *  @method reset resets the upload form
 *  @method submit submits the upload form
 *  @method lock locks the upload form
 *  @method unlock unlocks the upload form
 *  @method changeFileName changes the name that the selected file must have
 *  @method files returns the list of files selected by the user
 */
/**
 *  attaches necessary actions of the plugin
 *  
 *  This function attaches necessary actions of the plugin that must be run when
 *  the select button of the form is clicked or when the user changes the
 *  selected file.
 *  
 *  @param clickaction object this is a function that must be called when the
 *         user clicks the select button in order to select a file; it takes no
 *         parameters
 *  @param changeaction object this is a function that must be called when the
 *         user selects a file; a boolean true or false must be passed as
 *         parameter, denoting if a file has been selected or not
 *  
 *  @return void
 */
this.attachActions = function(clickaction, changeaction) {
	var WFUUF = GlobalData.WFU[$ID].uploadform;
	WFUUF._fileDialogOpen = false;
	// monitor focus event of window object; if file select dialog is open and
	// closes, window focus event will be fired; this is the only way to detect
	// when file select dialog closes in case Cancel button is pressed;
	jQuery(window).focus(function (event) {
		var WFUUF = GlobalData.WFU[$ID].uploadform;
		if (WFUUF._fileDialogOpen) WFUUF.fileDialogClosed();
	});
	// the way input file element is fired has changed; the select button does
	// not need to be under the input file element anymore, so it responds
	// normally to mouse events and :hover pseudoselector works; however for
	// backward compatibility the class "file_input_button_hover" needs to
	// remain;
	jQuery("#input_$ID").hover(
		function () {
			document.getElementById("input_$ID").classList.remove("file_input_button");
			document.getElementById("input_$ID").classList.add("file_input_button_hover");
		},
		function () {
			document.getElementById("input_$ID").classList.remove("file_input_button_hover");
			document.getElementById("input_$ID").classList.add("file_input_button");
		}
	);
	/**
	 *  Directory upload notes: the browsers do not support concurrent selection
	 *  of files and folders. The select dialog can either select files or
	 *  folders. The plugin has incorporated several ways so that the user can
	 *  choose to select files or folders using the same Select button:
	 *    - first of all there is a visual differentiation of the Select button
	 *      when it is intended for folder selection; it has a blue dot on its
	 *      top-left corner.
	 *    - the user can select folders by pressing and holding Ctrl key and
	 *      then pressing the Select button.
	 *    - the user can force Select button to always select folders by
	 *      pressing and releasing Ctrl and Alt keys at the same time
	 *      (workaround for Mac browsers). This can be undone by pressing and
	 *      releasing Ctrl and Alt keys again.
	 *    - the user can force Select button to always select folders by tapping
	 *      with two or three fingers on it (workaround for mobiles and tablets
	 *      that do not have a physical keyboard). This can be undone by tapping
	 *      with two or three fingers again.
	 *    - the admin can force Select button to always select folders,
	 *      regardless of whether the user presses Ctrl, Alt or taps, by adding
	 *      shortcode attribute forcedir = "true".
	 */
	/**
	 *  updates Select button directory status
	 *  
	 *  This function toggles the class "directory_upload" in Select button if
	 *  directory selection is forced or enabled by the user.
	 *  
	 *  @param reset boolean if true then Ctrl key will be reset.
	 *  
	 *  @return void
	 */
	WFUUF.updateDirStatus = function(reset) {
		var WFUUF = GlobalData.WFU[$ID].uploadform;
		if (reset) {
			WFUUF._ctrlPressed = false;
		}
		var dirEnabled = (WFUUF.forceDirectory || WFUUF._ctrlPressed || WFUUF._multiFingerTapped || WFUUF._forceDirectory);
		if (dirEnabled != WFUUF._dirEnabled) {
			WFUUF._dirEnabled = dirEnabled;
			document.getElementById("input_$ID").classList.toggle("directory_upload", dirEnabled);
		}
	};
	WFUUF.allowdir = <?php echo ( $multiple && $allowdir ? "true" : "false" ); ?>;
	WFUUF.forceDirectory = <?php echo ( $forcedir ? "true" : "false" ); ?>;
	WFUUF.tapThreshold = 10;
	WFUUF._ctrlPressed = false;
	WFUUF._altPressed = false;
	WFUUF._multiFingerTapped = false;
	WFUUF._forceDirectory = false;
	WFUUF._dirEnabled = false;
	WFUUF._lastTapTime = 0;
	if (WFUUF.allowdir) {
		WFUUF.updateDirStatus();
		// if forceDirectory is active then we do not need to monitor keys and
		// gestures to identify if select button status should be updated
		if (!WFUUF.forceDirectory) {
			// monitor when Ctrl and Alt keys are pressed;
			// while Ctrl is pressed then select button will select directories;
			jQuery(document).keydown(function (event) { 
				if (!(event.which == "17" || event.which == "18")) return;
				var WFUUF = GlobalData.WFU[$ID].uploadform;
				if (event.which == "17") {
					WFUUF._ctrlPressed = true;
					WFUUF.updateDirStatus();
				}
				else WFUUF._altPressed = true;
			});
			// monitor when Ctrl and Alt keys are released;
			// when Ctrl is released then select button will select files;
			// when both Ctrl and Alt keys are pressed and one of them is
			// released then select button will be permanently forced to select
			// directories, until Ctrl and Alt are pressed again;
			jQuery(document).keyup(function (event) {
				if (!(event.which == "17" || event.which == "18")) return;
				var WFUUF = GlobalData.WFU[$ID].uploadform;
				if (!(!WFUUF._ctrlPressed || !WFUUF._altPressed))
					WFUUF._forceDirectory = !WFUUF._forceDirectory;
				WFUUF._altPressed = false;
				WFUUF._ctrlPressed = false;
				WFUUF.updateDirStatus();
			});
			// monitor tapping on select button;
			// when two or three fingers tap on the select button it will be
			// permanently forced to select directories, until tapped again;
			jQuery("#input_$ID").on("touchstart", function (event) {
				if (event.touches.length < 2 || event.touches.length > 3) return;
				var WFUUF = GlobalData.WFU[$ID].uploadform;
				if (event.timeStamp - WFUUF._lastTapTime < WFUUF.tapThreshold) return;
				WFUUF._lastTapTime = event.timeStamp;
				WFUUF._multiFingerTapped = !WFUUF._multiFingerTapped;
				WFUUF.updateDirStatus();
			});
		}
	}
	document.getElementById("upfile_$ID").onclick = function() { clickaction(); GlobalData.WFU[$ID].uploadform._fileDialogOpen = true; };
	document.getElementById("upfile_$ID").onchange = function() { changeaction(document.getElementById("upfile_$ID").value != ""); };
	document.getElementById("input_$ID").onclick = function() { GlobalData.WFU[$ID].uploadform.selectClickAction(); };
}

/**
 *  attaches click action event on select button
 *  
 *  This function attaches a click action event on select button in order to
 *  invoke the click action of the file input element.
 *  
 *  @return void
 */
this.selectClickAction = function() {
	var WFUUF = GlobalData.WFU[$ID].uploadform;
	if (!WFUUF.allowdir || !WFUUF._dirEnabled)
		document.getElementById("upfile_$ID").removeAttribute("webkitdirectory");
	else document.getElementById("upfile_$ID").setAttribute("webkitdirectory", "");
	document.getElementById("upfile_$ID").click();
}

/**
 *  action when file selection dialog closes
 *  
 *  This function runs when the file selection dialog closes.
 *  
 *  @return void
 */
this.fileDialogClosed = function() {
	var WFUUF = GlobalData.WFU[$ID].uploadform;
	WFUUF._fileDialogOpen = false;
	if (!(!WFUUF.allowdir || WFUUF.forceDirectory)) WFUUF.updateDirStatus(true);
}

/**
 *  resets the upload form
 *  
 *  This function runs right after an upload has finished, in order to clear the
 *  list of files. It has a meaning only when the upload is done using classic
 *  HTML Forms and not AJAX.
 *  
 *  @return void
 */
this.reset = function() {
	document.getElementById("uploadform_$ID").reset();
}

/**
 *  resets the dummy form
 *  
 *  The dummy form is a second form element created only for acting as the form
 *  element of userdata fields. This way userdata fields autofill attribute can
 *  work normally. When userdata fields need to be cleared, the dummy form needs
 *  to be reset as well, so that any autofill styling (like yellow background in
 *  input elements) is cleared.
 *  
 *  @return void
 */
this.resetDummy = function() {
	document.getElementById("dummy_$ID").reset();
}

/**
 *  submits the upload form
 *  
 *  This function runs when the upload starts, in order to submit the files
 *  using the classic HTML Forms and not AJAX.
 *  
 *  @return void
 */
this.submit = function() {
	document.getElementById("upfile_$ID").disabled = false;
	document.getElementById("uploadform_$ID").submit();
}

/**
 *  locks the upload form
 *  
 *  This function runs right before an upload starts, in order to disable the
 *  form and select button elements, so that the user cannot select files while
 *  an upload is on progress.
 *  
 *  @return void
 */
this.lock = function() {
	document.getElementById("input_$ID").disabled = true;
	document.getElementById("upfile_$ID").disabled = true;
}

/**
 *  unlocks the upload form
 *  
 *  This function runs right after finish of an upload, in order to re-enable
 *  the form and the select button.
 *  
 *  @return void
 */
this.unlock = function() {
	document.getElementById("input_$ID").disabled = false;
	document.getElementById("upfile_$ID").disabled = false;
}

/**
 *  changes the name that the selected file must have
 *  
 *  This function changes the name that the selected file must have when
 *  submitted for upload by the form. it must be passed to the 'name' attribute
 *  of the form's input element of 'file' type.
 *  
 *  @param new_filename string the new name of the file
 *  
 *  @return void
 */
this.changeFileName = function(new_filename) {
	document.getElementById("upfile_$ID").name = new_filename;
}

/**
 *  returns the list of files selected by the user
 *  
 *  This function returns the list of files selected by the user, which are
 *  stored in the input element of type "file" of the form.
 *  
 *  @return object
 */
this.files = function() {
	var inputfile = document.getElementById("upfile_$ID");
	var farr = inputfile.files;
	//fix in case files attribute is not supported
	if (!farr) { if (inputfile.value) farr = [{name:inputfile.value}]; else farr = []; }
	return farr;
}
/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
<form id="dummy_$ID" style="display:none;"></form>
<form class="file_input_uploadform" id="uploadform_$ID" name="uploadform_$ID" method="post" enctype="multipart/form-data" style="<?php echo esc_html($styles_form); ?>">
<?php if ( $testmode ): ?>
	<input align="center" type="button" id="input_$ID" value="<?php echo esc_html($label); ?>" class="file_input_button" style="<?php echo esc_html($styles); ?>" onmouseout="javascript: document.getElementById('input_$ID').className = 'file_input_button';" onmouseover="javascript: document.getElementById('input_$ID').className = 'file_input_button_hover';" onclick="alert('<?php echo WFU_NOTIFY_TESTMODE; ?>');" />
<?php else: ?>
	<input align="center" type="button" id="input_$ID" value="<?php echo esc_html($label); ?>" class="file_input_button" style="<?php echo esc_html($styles); ?>" />
<?php endif ?>
	<input type="file" class="file_input_hidden" name="<?php echo $filename; ?>" id="upfile_$ID" tabindex="1" onmouseout="javascript: document.getElementById('input_$ID').className = 'file_input_button';" onmouseover="javascript: document.getElementById('input_$ID').className = 'file_input_button_hover';"<?php echo "".( $multiple ? ' multiple="multiple"' : '' ); ?> />
<?php foreach( $hidden_elements as $elem ): ?>
	<input type="hidden" id="<?php echo $elem["id"]; ?>" name="<?php echo $elem["name"]; ?>" value="<?php echo $elem["value"]; ?>" />
<?php endforeach ?>
</form>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_submit_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of upload button element
 *  @var $height string assigned height of upload button element
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $allownofile bool true if it is allowed to submit the upload form
 *       without any file selected
 *  @var $multiple bool true if multiple files can be selected for upload
 *  @var $label string the title of the upload button element
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
	if ( $height != "" ) $styles .= "height: $height; ";
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
input[type="button"].file_input_submit
{
	width: 100px; /*relax*/
	height: 27px; /*relax*/
	position: relative; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	background-color: #EEEEEE; /*relax*/
	color: #555555; /*relax*/
	background-image: url("<?php echo WPFILEUPLOAD_DIR; ?>images/white-grad-active.png"); /*relax*/
	background-position: left top; /*relax*/
	background-repeat: repeat-x; /*relax*/
	border-style: solid; /*relax*/
	border-width: 1px; /*relax*/
	border-color: #BBBBBB; /*relax*/
	-webkit-border-radius: 2px; /*relax*/
	-moz-border-radius: 2px; /*relax*/
	-khtml-border-radius: 2px; /*relax*/
	border-radius: 2px; /*relax*/
}

input[type="button"].file_input_submit:hover, input[type="button"].file_input_submit:focus
{
	width: 100px; /*relax*/
	height: 27px; /*relax*/
	position: relative; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	background-color: #EEEEEE; /*relax*/
	color: #111111; /*relax*/
	background-image: url("<?php echo WPFILEUPLOAD_DIR; ?>images/white-grad-active.png"); /*relax*/
	background-position: left top; /*relax*/
	background-repeat: repeat-x; /*relax*/
	border-style: solid; /*relax*/
	border-width: 1px; /*relax*/
	border-color: #333333; /*relax*/
	-webkit-border-radius: 2px; /*relax*/
	-moz-border-radius: 2px; /*relax*/
	-khtml-border-radius: 2px; /*relax*/
	border-radius: 2px; /*relax*/
}

input[type="button"].file_input_submit:disabled
{
	width: 100px; /*relax*/
	height: 27px; /*relax*/
	position: relative; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	background-color: #EEEEEE; /*relax*/
	color: silver; /*relax*/
	background-image: url("<?php echo WPFILEUPLOAD_DIR; ?>images/white-grad-active.png"); /*relax*/
	background-position: left top; /*relax*/
	background-repeat: repeat-x; /*relax*/
	border-style: solid; /*relax*/
	border-width: 1px; /*relax*/
	border-color: #BBBBBB; /*relax*/
	-webkit-border-radius: 2px; /*relax*/
	-moz-border-radius: 2px; /*relax*/
	-khtml-border-radius: 2px; /*relax*/
	border-radius: 2px; /*relax*/
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].submit.init = function() {
/***
 *  The following upload button methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method attachClickAction attaches necessary action of the plugin that must
 *          be run when the upload button is clicked
 *  @method updateLabel updates the label of the upload button
 *  @method toggle enables or disables the upload button
 */
/**
 *  attaches necessary click action of the plugin
 *  
 *  This function attaches necessary action of the plugin that must be ran when
 *  the upload button is clicked.
 *  
 *  @param clickaction object this is a function that must be called when the
 *         user clicks the upload button in order to upload the selected file
 *  
 *  @return void
 */
this.attachClickAction = function(clickaction) {
	document.getElementById("upload_$ID").onclick = function() { clickaction(); };
}

/**
 *  updates the label of the upload button
 *  
 *  @param new_label string the new label of the upload button
 *  
 *  @return void
 */
this.updateLabel = function(new_label) {
	document.getElementById("upload_$ID").value = new_label;
}

/**
 *  enables or disables the upload button
 *  
 *  @param status bool if true the the upload button must be enabled, if false
 *         then the upload button must be disabled
 *  
 *  @return void
 */
this.toggle = function(status) {
	document.getElementById("upload_$ID").disabled = !status;
}
/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
<?php if ( $testmode ): ?>
<input align="center" type="button" id="upload_$ID" name="upload_$ID" value="<?php echo esc_html($label); ?>" class="file_input_submit" style="<?php echo esc_html($styles); ?>" />
<?php else: ?>
<input align="center" type="button" id="upload_$ID" name="upload_$ID" value="<?php echo esc_html($label); ?>" class="file_input_submit" style="<?php echo esc_html($styles); ?>"<?php echo ( $allownofile ? '' : ' disabled="disabled"' ); ?> />
<?php endif ?>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_captcha_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of captcha element
 *  @var $height string assigned height of captcha element
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $type string the captcha type, it can be "RecaptchaV1", "RecaptchaV2",
 *       "RecaptchaV1 (no account)" or "RecaptchaV2 (no account)"
 *  @var $V2unsupported bool true if the website does not support the new
 *       version 2 of Google Recaptcha due to old PHP version (lower than 5.3.0)
 *  @var $sitekey string the public (site) key of Google Recaptcha; applicable
 *       when captcha type is "RecaptchaV1" or "RecaptchaV2"
 *  @var $frameURL string the URL of the iframe element that will load captcha
 *       element when captcha type is "RecaptchaV1 (no account)" or
 *       "RecaptchaV2 (no account)"
 *  @var $index int the index of occurrence of the element inside the plugin,
 *       in case that it appears more than once
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
	$styles = "";
	if ( $width != "" ) $styles .= "width: $width; ";
	if ( $height != "" ) $styles .= "height: $height;";
	$user_is_admin = current_user_can( 'manage_options' );
	$error_text = "";
	if ( $type == "RecaptchaV1" )
		$error_text = ( $sitekey == '' ? ( $user_is_admin ? WFU_ERROR_CAPTCHA_NOSITEKEY_ADMIN : WFU_ERROR_CAPTCHA_NOSITEKEY ) : WFU_ERROR_CAPTCHA_UNKNOWNERROR );
	elseif ( $type == "RecaptchaV2" ) {
		$error_text = ( !$V2unsupported ? ( $sitekey == '' ? ( $user_is_admin ? WFU_ERROR_CAPTCHA_NOSITEKEY_ADMIN : WFU_ERROR_CAPTCHA_NOSITEKEY ) : WFU_ERROR_CAPTCHA_UNKNOWNERROR ) : WFU_ERROR_CAPTCHA_OLDPHP );
	}
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
span.file_captcha_prompt
{
	display: inline-block;
}

input[type="text"].file_captcha_input
{
	position: relative;	
	width: 150px; /*relax*/
	height: 25px; /*relax*/
	color: black; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: 1px solid; /*relax*/
	border-color: #BBBBBB; /*relax*/
	background-color: white; /*relax*/
	background-image: none; /*relax*/
}

input[type="text"].file_captcha_input:disabled
{
	position: relative;	
	width: 150px; /*relax*/
	height: 25px; /*relax*/
	color: silver; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: 1px solid; /*relax*/
	border-color: #BBBBBB; /*relax*/
	background-color: white; /*relax*/
	background-image: none; /*relax*/
}

input[type="text"].file_captcha_input_empty
{
	position: relative;	
	width: 150px; /*relax*/
	height: 25px; /*relax*/
	color: black; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: 1px solid; /*relax*/
	border-color: #BBBBBB; /*relax*/
	background-color: red;
	background-image: none; /*relax*/
}

input[type="text"].file_captcha_input_checking
{
	position: relative;	
	width: 150px; /*relax*/
	height: 25px; /*relax*/
	color: black; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: 1px solid; /*relax*/
	border-color: #BBBBBB; /*relax*/
	background-color: white; /*relax*/
	background-image: url("<?php echo WPFILEUPLOAD_DIR; ?>images/refresh_16.gif");
	background-repeat: no-repeat;
	background-position: right;
}

input[type="text"].file_captcha_input_checking:disabled
{
	position: relative;	
	width: 150px; /*relax*/
	height: 25px; /*relax*/
	color: silver; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: 1px solid; /*relax*/
	border-color: #BBBBBB; /*relax*/
	background-color: white; /*relax*/
	background-image: url("<?php echo WPFILEUPLOAD_DIR; ?>images/refresh_16.gif");
	background-repeat: no-repeat;
	background-position: right;
}

input[type="text"].file_captcha_input_Ok
{
	position: relative;	
	width: 150px; /*relax*/
	height: 25px; /*relax*/
	color: black; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: 1px solid; /*relax*/
	border-color: #BBBBBB; /*relax*/
	background-color: white; /*relax*/
	background-image: url("<?php echo WPFILEUPLOAD_DIR; ?>images/ok_16.png");
	background-repeat: no-repeat;
	background-position: right;
}

input[type="text"].file_captcha_input_Ok:disabled
{
	position: relative;	
	width: 150px; /*relax*/
	height: 25px; /*relax*/
	color: silver; /*relax*/
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: 1px solid; /*relax*/
	border-color: #BBBBBB; /*relax*/
	background-color: white; /*relax*/
	background-image: url("<?php echo WPFILEUPLOAD_DIR; ?>images/ok_16.png");
	background-repeat: no-repeat;
	background-position: right;
}

div.file_captcha
{
	display: block;
	position: relative;	
	width: 318px;
	margin: 0px;
	padding: 0px;
	border-style: none;
	background: none;
	color: black;
}

div.file_captcha .recaptchatable {
	table-layout: auto;
}

div.file_captchav2
{
	display: block;
	position: relative;	
	width: 304px;
	margin: 0px;
	padding: 0px;
	border-style: none;
	background: none;
	color: black;
}

div.file_captcha_inner
{
	position: relative;
	min-height: 40px;
}

div.file_captcha_inner iframe
{
	width: 318px;
	height: 129px;
	margin: 0;
}

div.file_captcha_inner_v2
{
	position: relative;
	width: 304px;
	height: 78px;
	overflow: visible;
}

div.file_captcha_inner_v2_empty, div.file_captcha_inner_v2_locked
{
	overflow: hidden;
}

div.file_captcha_inner_v2 iframe
{
	position: absolute;
	width: 304px;
	height: 78px;
/*	max-width: 800px;
	max-height: 800px;*/
	overflow: visible;
	z-index: 1;
	margin: 0;
}

div.file_captcha_inner_v2_empty iframe, div.file_captcha_inner_v2_locked iframe
{
	height: 72px;
}

div.file_captcha_overlay_v1
{
	position: absolute;
	left: 0;
	top: 0;
	right: 0;
	bottom: 0;
	background: none;
	border: 4px solid red;
	z-index: 2;
	display: none;
}

div.file_captcha_inner_empty div.file_captcha_overlay_v1
{
	display: block;
}

div.file_captcha_inner_locked div.file_captcha_overlay_v1
{
	display: block;
	background-color: rgba(255,255,255,0.9);
	border: none;
}

label.file_captcha_overlay_v1_errors
{
	background-color: red;
	color: white;
	font-size: small;
	text-overflow: ellipsis;
	white-space: nowrap;
	overflow: hidden;
}

div.file_captcha_inner_empty label.file_captcha_overlay_v1_errors
{
	display: block;
}

div.file_captcha_inner_locked label.file_captcha_overlay_v1_errors
{
	display: none;
}

div.file_captcha_overlay_v2
{
	position: absolute;
	left: 0;
	top: 0;
	right: 0;
	bottom: 0;
	background: none;
	border: 4px solid red;
	z-index: 2;
	display: none;
}

div.file_captcha_inner_v2_empty div.file_captcha_overlay_v2
{
	display: block;
}

div.file_captcha_inner_v2_locked div.file_captcha_overlay_v2
{
	display: block;
	background-color: rgba(255,255,255,0.9);
	border: none;
}

label.file_captcha_overlay_v2_errors
{
	background-color: red;
	color: white;
	font-size: small;
	text-overflow: ellipsis;
	white-space: nowrap;
	overflow: hidden;
}

div.file_captcha_inner_v2_empty label.file_captcha_overlay_v2_errors
{
	display: block;
}

div.file_captcha_inner_v2_locked label.file_captcha_overlay_v2_errors
{
	display: none;
}

div.file_captcha_overlay_hidden
{
	position: absolute;
	display: none;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	background-color: rgba(255,255,255,0.9);
}

div.file_captcha_loading_outer, div.file_captcha_overlay_outer
{
	position: absolute;
	display: block;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	background-color: rgba(255,255,255,0.9);
}

div.file_captcha_loading_inner
{
	position: relative;
	height: 100%;
}

div.file_captcha_loading_outer img.file_captcha_loading_image
{
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	margin: auto;
}

div.file_captcha_overlay_outer img.file_captcha_loading_image
{
	position: absolute;
	display: none;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	margin: auto;
}

img.file_captcha_inner+img.file_captcha_refresh:hover
{
	display: block;
	position: absolute;
	right: 0px;
	top: 0px;
	padding: 2px;
	border: 1px solid #aaa;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	-khtml-border-radius: 4px;
	border-radius: 4px;
	background-color: #ddd;
	width: 20px;
	height: 20px;
}

img.file_captcha_inner+img.file_captcha_refresh
{
	display: none;
	position: absolute;
	right: 0px;
	top: 0px;
	padding: 2px;
	border: 1px solid #aaa;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	-khtml-border-radius: 4px;
	border-radius: 4px;
	background-color: white;
	width: 20px;
	height: 20px;
}

img.file_captcha_inner:hover+img.file_captcha_refresh
{
	display: block;
	position: absolute;
	right: 0px;
	top: 0px;
	padding: 2px;
	border: 1px solid #aaa;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	-khtml-border-radius: 4px;
	border-radius: 4px;
	background-color: white;
	width: 20px;
	height: 20px;
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].captcha.init = function() {
/***
 *  The following captcha params have been defined and can be used in Javascript
 *  code:
 *
 *  @var type string the captcha type, it can be "RecaptchaV1", "RecaptchaV2",
 *       "RecaptchaV1 (no account)" or "RecaptchaV2 (no account)"
 *  
 *  The following captcha methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method updateStatus updates the status of the captcha
 *  @method isLocked return lock status of captcha
 *  @method core returns the core captcha element that does all the job
 *  @method resize resizes the captcha
 */
/**
 *  updates the status of the captcha
 *  
 *  @param status string the status of the captcha
 *  @param param string optional additional parameter; if status is 'error' then
 *         param represents the error message
 *  
 *  @return void
 */
this.updateStatus = function(status, param) {
	var mode = this.type.substr(9, 2);
	var captcha_empty_overlay = document.getElementById('captcha_$ID_empty_overlay');
	var captcha_error_label = document.getElementById('captcha_$ID_overlay_error_label');
	if (mode == "V1") {
		if (status == "idle") {
			captcha_empty_overlay.parentNode.className = "file_captcha_inner";
		}
		else if (status == "empty") {
			captcha_error_label.innerHTML = GlobalData.consts.captchamessage_empty;
			captcha_error_label.title = GlobalData.consts.captchamessage_empty;
			captcha_empty_overlay.parentNode.className = "file_captcha_inner file_captcha_inner_empty";
		}
		else if (status == "error" && arguments.length > 1) {
			var param = arguments[1];
			captcha_error_label.innerHTML = param;
			captcha_error_label.title = param;
			captcha_empty_overlay.parentNode.className = "file_captcha_inner file_captcha_inner_empty";
		}
		else if (status == "checking") {
			captcha_empty_overlay.parentNode.className = "file_captcha_inner file_captcha_inner_locked";
		}
		else if (status == "locked") {
			captcha_empty_overlay.parentNode.className = "file_captcha_inner file_captcha_inner_locked";
		}
		else if (status == "unlocked") {
			captcha_empty_overlay.parentNode.className = "file_captcha_inner";
		}
		else if (status == "nomultiple") {
			document.getElementById("captcha_$ID_inner").className = "file_captcha_inner file_captcha_inner_empty";
			captcha_empty_overlay.onclick = null;
			captcha_error_label.title = (GlobalData.WFU[$ID].is_admin ? GlobalData.consts.captcha_multiple_notallowed_admin : GlobalData.consts.captcha_multiple_notallowed );
			captcha_error_label.innerHTML = captcha_error_label.title;
		}
	}
	else if (mode == "V2") {
		if (status == "idle") {
			captcha_empty_overlay.parentNode.className = "file_captcha_inner_v2";
		}
		else if (status == "empty") {
			captcha_error_label.innerHTML = GlobalData.consts.captchamessage_empty;
			captcha_error_label.title = GlobalData.consts.captchamessage_empty;
			captcha_empty_overlay.parentNode.className = "file_captcha_inner_v2 file_captcha_inner_v2_empty";
		}
		else if (status == "error" && arguments.length > 1) {
			var param = arguments[1];
			captcha_error_label.innerHTML = param;
			captcha_error_label.title = param;
			captcha_empty_overlay.parentNode.className = "file_captcha_inner_v2 file_captcha_inner_v2_empty";
		}
		else if (status == "checking") {
			captcha_empty_overlay.parentNode.className = "file_captcha_inner_v2 file_captcha_inner_v2_locked";
		}
		else if (status == "locked") {
			captcha_empty_overlay.parentNode.className = "file_captcha_inner_v2 file_captcha_inner_v2_locked";
		}
		else if (status == "unlocked") {
			captcha_empty_overlay.parentNode.className = "file_captcha_inner_v2";
		}
	}
}

/**
 *  return lock status of captcha
 *  
 *  This function returns true if captcha is locked because an upload is on
 *  progress.
 *  
 *  @return bool true if captcha is locked
 */
this.isLocked = function() {
	var mode = this.type.substr(9, 2);
	var captcha_empty_overlay = document.getElementById('captcha_$ID_empty_overlay');
	if (mode == "V1") { 
		return (captcha_empty_overlay.parentNode.className == "file_captcha_inner file_captcha_inner_locked")
	}
	else if (mode == "V2") {
		return (captcha_empty_overlay.parentNode.className == "file_captcha_inner_v2 file_captcha_inner_v2_locked") 
	}
	else return false;
}

/**
 *  returns the core captcha element that does all the job
 *  
 *  For normal Google Recaptcha V1 and V2 versions this is the div element that
 *  will be used as container for Google Recaptcha API to generate the captcha
 *  HTML code. For (no account) versions this is the iframe element, in which
 *  the captcha code is generated.
 *  
 *  @return object the core captcha element
 */
this.core = function() {
	if (this.type == "RecaptchaV1") return document.getElementById("recaptcha_$ID_div");
	else if (this.type == "RecaptchaV1 (no account)") return document.getElementById("captcha_$ID_frame");
	else if (this.type == "RecaptchaV2") return document.getElementById("recaptcha_$ID_div");
	else if (this.type == "RecaptchaV2 (no account)") return document.getElementById("captcha_$ID_frame");
	else return null;
}

/**
 *  resizes the captcha
 *  
 *  Sometimes based on user input the captcha may require resizing. For the
 *  moment this happens only for "RecaptchaV2 (no account)".
 *  
 *  @param width int the new width of the core captcha element
 *  @param height int the new height of the core captcha element
 *  
 *  @return void
 */
this.resize = function(width, height) {
	if (this.type == "RecaptchaV2 (no account)") {
		var frame = document.getElementById('captcha_$ID_frame');
		var inner = document.getElementById('captcha_$ID_inner');
//		frame.style.width = width + 'px';
		frame.style.height = (height + 10) + 'px';
//		inner.style.width = width + 'px';		
	}
}
/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
<?php if ( $type == "RecaptchaV1" ): ?>
<div id="captcha_$ID" class="file_captcha" style="<?php echo esc_html($styles); ?>">
	<input id="captcha_$ID_mode" type="hidden" value="V1" />
	<div id="captcha_$ID_inner" class="file_captcha_inner<?php echo ( $sitekey == '' ? ' file_captcha_inner_empty' : '' ); ?>">
		<div id="captcha_$ID_empty_overlay" class="file_captcha_overlay_v1"<?php echo ( $sitekey == '' ? '' : ' onclick="if (this.parentNode.className == \'file_captcha_inner file_captcha_inner_empty\') wfu_set_captcha_state($ID, \'idle\', \'false\');"' ); ?>>
			<label id="captcha_$ID_overlay_error_label" class="file_captcha_overlay_v1_errors" title="<?php echo $error_text; ?>"><?php echo $error_text; ?></label>
		</div>
		<?php if ( $sitekey != "" ): ?>
		<div id="recaptcha_$ID_div"></div>
		<?php endif ?>
	</div>
</div>
<?php elseif ( $type == "RecaptchaV1 (no account)" ): ?>
<div id="captcha_$ID" class="file_captcha" style="<?php echo esc_html($styles); ?>">
	<input id="captcha_$ID_mode" type="hidden" value="V1 (no account)" />
	<div id="captcha_$ID_inner" class="file_captcha_inner">
		<div id="captcha_$ID_empty_overlay" class="file_captcha_overlay_v1" onclick="if (this.parentNode.className == 'file_captcha_inner file_captcha_inner_empty') wfu_set_captcha_state($ID, 'idle', 'false');">	
			<label id="captcha_$ID_overlay_error_label" class="file_captcha_overlay_v1_errors">Errors: <?php echo WFU_ERROR_CAPTCHA_UNKNOWNERROR; ?></label>
		</div>
		<iframe id="captcha_$ID_frame" src="<?php echo $frameURL; ?>" scrolling="no" width="318" height="129"></iframe>
	</div>
</div>
<?php elseif ( $type == "RecaptchaV2" ): ?>
<div id="captcha_$ID" class="file_captchav2" style="<?php echo esc_html($styles); ?>">
	<input id="captcha_$ID_mode" type="hidden" value="V2" />
	<div id="captcha_$ID_inner" class="file_captcha_inner_v2<?php echo ( $sitekey == '' ? ' file_captcha_inner_v2_empty' : '' ); ?>">
		<div id="captcha_$ID_empty_overlay" class="file_captcha_overlay_v2"<?php echo ( $sitekey == '' ? '' : ' onclick="if (this.parentNode.className == \'file_captcha_inner_v2 file_captcha_inner_v2_empty\') wfu_set_captcha_state($ID, \'idle\', \'false\');"' ); ?>>
			<label id="captcha_$ID_overlay_error_label" class="file_captcha_overlay_v2_errors" title="<?php echo $error_text; ?>"><?php echo $error_text; ?></label>
		</div>
		<?php if ( !$V2unsupported && $sitekey != "" ): ?>
		<div id="recaptcha_$ID_div"></div>
		<?php endif ?>
	</div>
</div>
<?php elseif ( $type == "RecaptchaV2 (no account)" ): ?>
<div id="captcha_$ID" class="file_captchav2" style="<?php echo esc_html($styles); ?>">
	<input id="captcha_$ID_mode" type="hidden" value="V2 (no account)" />
	<div id="captcha_$ID_inner" class="file_captcha_inner_v2">
		<div id="captcha_$ID_empty_overlay" class="file_captcha_overlay_v2" onclick="if (this.parentNode.className == 'file_captcha_inner_v2 file_captcha_inner_v2_empty') wfu_set_captcha_state($ID, 'idle', 'false');">
			<label id="captcha_$ID_overlay_error_label" class="file_captcha_overlay_v2_errors">Errors: <?php echo WFU_ERROR_CAPTCHA_UNKNOWNERROR; ?></label>
		</div>
		<iframe id="captcha_$ID_frame" src="<?php echo $frameURL; ?>" scrolling="no"></iframe>
	</div>
</div>
<?php endif ?>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_webcam_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of webcam element
 *  @var $height string assigned height of webcam element
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $index int the index of occurrence of the element inside the plugin,
 *       in case that it appears more than once
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
	$styles = "";
	if ( $width != "" ) $styles = "width: $width; ";
	if ( $height != "" ) $styles .= "height: $height; ";
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
div.wfu_file_webcam_inner {
	position: relative;
	background: none;
	border: none;
	padding: 0;
	margin: 0;
	width: 100%;
	height: 100%;
}

div.wfu_webcam_notsupported {
	border: 1px inset;
}

div.wfu_webcam_notsupported label.wfu_webcam_notsupported_label {
	display: inline !important;
	font-size: smaller;
	color: red;
}

div.wfu_file_webcam_off {
	width: 100%;
	height: 100%;
	margin: 0;
	padding: 0;
	border: 0;
	background-color: black;
}

div.wfu_file_webcam_off img {
	max-width: 100%;
	max-height: 100%;
	padding: 0;
	margin: 0;
}

div.wfu_file_webcam_off svg {
	position: absolute;
	top: 0;
	left: 0;
	fill: rgba(255, 255, 255, 0.5);
	width: 100%;
	height: 100%;
	padding: 0;
	margin: 0;
}

div.wfu_file_webcam_nav_container {
	position: relative;
	border: none;
	background: none;
	padding: 0;
	margin: 0;
}

div.wfu_file_webcam_nav {
	display: block;
	position: absolute;
	border: none;
	padding: 4px;
	margin: 0;
	left: 0;
	right: 0;
	height: 30px;
	bottom: 0;
	z-index: 1;
	overflow: hidden;
}

div.wfu_rec_ready {
	background-color: transparent;
}

div.wfu_recording {
	background-color: rgba(0, 0, 0, 0.8);
}

div.wfu_stream_ready {
	background-color: rgba(0, 0, 0, 0.8);
	display: none;
}

div.wfu_file_webcam_inner:hover div.wfu_stream_ready {
	display: block;
}

svg.wfu_file_webcam_btn, svg.wfu_file_webcam_btn_disabled {
	float: left;
	height: 100%;
}

svg.wfu_file_webcam_btn:hover {
	border-radius: 4px;
	box-shadow: 0px 0px 4px #aaa;
}

svg.wfu_file_webcam_btn_onoff {
	fill: white;
	position: absolute;
	display: none;
	height: 22px;
	width: 22px;
	top: 2px;
	right: 2px;
	padding: 0 0 2px 3px;
	z-index: 1;
}

div.wfu_file_webcam_inner:hover svg.wfu_file_webcam_btn_onoff {
	display: block;
}

svg.wfu_file_webcam_btn_video {
	fill: white;
	padding: 2px;
}

svg.wfu_file_webcam_btn_video_disabled {
	fill: rgba(255, 255, 255, 0.3);
	padding: 2px;
}

svg.wfu_file_webcam_btn_record {
	fill: red;
}

svg.wfu_recording {
	animation: blink-animation 1s steps(3, start) infinite;
	-webkit-animation: blink-animation 1s steps(3, start) infinite;
}

svg.wfu_recording:hover {
	border-radius: 0px;
	box-shadow: none;
}

@keyframes blink-animation {
	to { visibility: hidden; }
}

@-webkit-keyframes blink-animation {
	to { visibility: hidden; }
}

svg.wfu_file_webcam_btn_stop {
	fill: white;
}

svg.wfu_file_webcam_btn_play {
	fill: limegreen;
}

svg.wfu_file_webcam_btn_play_disabled {
	fill: rgba(255, 255, 255, 0.3);
}

svg.wfu_file_webcam_btn_pause {
	fill: white;
}

svg.wfu_file_webcam_btn_pause_disabled {
	fill: rgba(255, 255, 255, 0.3);
}

div.wfu_file_webcam_btn_pos {
	position: relative;
	float: left;
	background: none;
	border: none;
	margin: 0 8px 0 3px;
	padding: 0;
	width: calc(100% - 200px);
	max-width: 100px;
	height: 100%;
}

svg.wfu_file_webcam_btn_bar {
	position: absolute;
	height: 100%;
	top: 0;
	width: calc(100% + 5px);
	fill: white;
}

svg.wfu_file_webcam_btn_pointer {
	position: absolute;
	top: 4px;
	bottom: 4px;
	width: 5px;
	height: calc(100% - 8px);
	fill: white;
}

svg.wfu_file_webcam_btn_back {
	fill: white;
	padding: 0 2px;
}

svg.wfu_file_webcam_btn_fwd {
	fill: white;
	padding: 0 2px;
}

video.wfu_file_webcam_box {
	max-width: 100%;
	max-height: 100%;
	padding: 0;
	margin: 0;
}

div.wfu_file_webcam_btn_time {
	position: relative;
	float: right;
	background: none;
	border: none;
	margin: 0;
	padding: 0;
	height: 100%;
}

table.wfu_file_webcam_btn_time_tb {
	margin: 0;
	padding: 0;
	border: none;
	border-collapse: collapse;
	background: none;
	height: 100%;
}

tr.wfu_file_webcam_btn_time_tr {
	border: none;
	padding: 0;
	background: none;
}

td.wfu_file_webcam_btn_time_td {
	border: none;
	padding: 0;
	background: none;
	vertical-align: middle;
}

div.wfu_file_webcam_btn_time label {
	color: white;
	font-size: smaller;
	vertical-align: middle;
}

svg.wfu_file_webcam_btn_picture {
	fill: yellow;
	float: right;
	padding: 2px;
	height: calc(100% - 4px);
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].webcam.init = function() {
/***
 *  The following webcam methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method initCallback runs after webcam initialization to perform custom
 *          actions
 *  @method initButtons runs when the webcam buttons need to be initialized
 *  @method updateStatus updates the status of the webcam element
 *  @method updateButtonStatus updates the status of the webcam buttons
 *  @method updateTimer updates the timer of video recording
 *  @method updatePlayProgress updates the playback progress timer
 *  @method setVideoProperties sets various properties of the video element
 *  @method videoSize gets width and height of video
 *  @method readyState gets the ready state of the video element
 *  @method screenshot gets a screenshot of the video stream and saves it
 *  @method play runs when play button is pressed
 *  @method pause runs when pause button is pressed
 *  @method back runs when backward button is pressed during playback
 *  @method fwd runs when forward button is pressed during playback
 *  @method ended runs when video playback has ended
 */
/**
 *  runs after webcam initialization to perform custom actions
 *  
 *  In this template initCallback creates the webcamoff image
 *  
 *  @return void
 */
this.initCallback = function() {
	var container = document.getElementById("webcam_$ID_inner");
	var video = document.getElementById("webcam_$ID_box");
//	console.log(container.clientWidth, container.clientHeight);
//	console.log(video.videoWidth, video.videoHeight);
	var imgdata = '<svg xmlns="http://www.w3.org/2000/svg" width="' + video.videoWidth + '" height="' + video.videoHeight + '"></svg>';
	var imgblob = new Blob([imgdata], {type: 'image/svg+xml;charset=utf-8'});
	var img = document.getElementById("webcam_$ID_webcamoff_img");
	img.src = window.URL.createObjectURL(imgblob);
	img.style.width = container.clientWidth + "px";
	img.style.height = container.clientHeight + "px";
}

/**
 *  runs when the webcam buttons need to be initialized
 *  
 *  In this template initButtons declares SVGInjector object and initializes the
 *  webcam buttons.
 *  
 *  @param mode string the webcam mode, it can be "capture video", "take Photos"
 *         or "both"
 *  
 *  @return void
 */
this.initButtons = function(mode) {
	if (typeof SVGInjector == "undefined") {
		!function(t,e){"use strict";function r(t){t=t.split(" ");for(var e={},r=t.length,n=[];r--;)e.hasOwnProperty(t[r])||(e[t[r]]=1,n.unshift(t[r]));return n.join(" ")}var n="file:"===t.location.protocol,i=e.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure","1.1"),o=Array.prototype.forEach||function(t,e){if(void 0===this||null===this||"function"!=typeof t)throw new TypeError;var r,n=this.length>>>0;for(r=0;n>r;++r)r in this&&t.call(e,this[r],r,this)},a={},l=0,s=[],u=[],c={},f=function(t){return t.cloneNode(!0)},p=function(t,e){u[t]=u[t]||[],u[t].push(e)},d=function(t){for(var e=0,r=u[t].length;r>e;e++)!function(e){setTimeout(function(){u[t][e](f(a[t]))},0)}(e)},v=function(e,r){if(void 0!==a[e])a[e]instanceof SVGSVGElement?r(f(a[e])):p(e,r);else{if(!t.XMLHttpRequest)return r("Browser does not support XMLHttpRequest"),!1;a[e]={},p(e,r);var i=new XMLHttpRequest;i.onreadystatechange=function(){if(4===i.readyState){if(404===i.status||null===i.responseXML)return r("Unable to load SVG file: "+e),n&&r("Note: SVG injection ajax calls do not work locally without adjusting security setting in your browser. Or consider using a local webserver."),r(),!1;if(!(200===i.status||n&&0===i.status))return r("There was a problem injecting the SVG: "+i.status+" "+i.statusText),!1;if(i.responseXML instanceof Document)a[e]=i.responseXML.documentElement;else if(DOMParser&&DOMParser instanceof Function){var t;try{var o=new DOMParser;t=o.parseFromString(i.responseText,"text/xml")}catch(l){t=void 0}if(!t||t.getElementsByTagName("parsererror").length)return r("Unable to parse SVG file: "+e),!1;a[e]=t.documentElement}d(e)}},i.open("GET",e),i.overrideMimeType&&i.overrideMimeType("text/xml"),i.send()}},h=function(e,n,a,u){var f=e.getAttribute("data-src")||e.getAttribute("src");if(!/\.svg/i.test(f))return void u("Attempted to inject a file with a non-svg extension: "+f);if(!i){var p=e.getAttribute("data-fallback")||e.getAttribute("data-png");return void(p?(e.setAttribute("src",p),u(null)):a?(e.setAttribute("src",a+"/"+f.split("/").pop().replace(".svg",".png")),u(null)):u("This browser does not support SVG and no PNG fallback was defined."))}-1===s.indexOf(e)&&(s.push(e),e.setAttribute("src",""),v(f,function(i){if("undefined"==typeof i||"string"==typeof i)return u(i),!1;var a=e.getAttribute("id");a&&i.setAttribute("id",a);var p=e.getAttribute("title");p&&i.setAttribute("title",p);var d=[].concat(i.getAttribute("class")||[],"injected-svg",e.getAttribute("class")||[]).join(" ");i.setAttribute("class",r(d));var v=e.getAttribute("style");v&&i.setAttribute("style",v);var h=[].filter.call(e.attributes,function(t){return/^data-\w[\w\-]*$/.test(t.name)});o.call(h,function(t){t.name&&t.value&&i.setAttribute(t.name,t.value)});var g,m,b,y,A,w={clipPath:["clip-path"],"color-profile":["color-profile"],cursor:["cursor"],filter:["filter"],linearGradient:["fill","stroke"],marker:["marker","marker-start","marker-mid","marker-end"],mask:["mask"],pattern:["fill","stroke"],radialGradient:["fill","stroke"]};Object.keys(w).forEach(function(t){g=t,b=w[t],m=i.querySelectorAll("defs "+g+"[id]");for(var e=0,r=m.length;r>e;e++){y=m[e].id,A=y+"-"+l;var n;o.call(b,function(t){n=i.querySelectorAll("["+t+'*="'+y+'"]');for(var e=0,r=n.length;r>e;e++)n[e].setAttribute(t,"url(#"+A+")")}),m[e].id=A}}),i.removeAttribute("xmlns:a");for(var x,S,k=i.querySelectorAll("script"),j=[],G=0,T=k.length;T>G;G++)S=k[G].getAttribute("type"),S&&"application/ecmascript"!==S&&"application/javascript"!==S||(x=k[G].innerText||k[G].textContent,j.push(x),i.removeChild(k[G]));if(j.length>0&&("always"===n||"once"===n&&!c[f])){for(var M=0,V=j.length;V>M;M++)new Function(j[M])(t);c[f]=!0}var E=i.querySelectorAll("style");o.call(E,function(t){t.textContent+=""}),e.parentNode.replaceChild(i,e),delete s[s.indexOf(e)],e=null,l++,u(i)}))},g=function(t,e,r){e=e||{};var n=e.evalScripts||"always",i=e.pngFallback||!1,a=e.each;if(void 0!==t.length){var l=0;o.call(t,function(e){h(e,n,i,function(e){a&&"function"==typeof a&&a(e),r&&t.length===++l&&r(l)})})}else t?h(t,n,i,function(e){a&&"function"==typeof a&&a(e),r&&r(1),t=null}):r&&r(0)};"object"==typeof module&&"object"==typeof module.exports?module.exports=exports=g:"function"==typeof define&&define.amd?define(function(){return g}):"object"==typeof t&&(t.SVGInjector=g)}(window,document);
	}
	if (document.getElementById("webcam_$ID_btns_converted").value != "1") {
		SVGInjector(document.getElementById("webcam_$ID_btns"));
		document.getElementById("webcam_$ID_btns_converted").value = "1";
	}
	if (mode == "capture video") this.updateButtonStatus("idle_only_video");
	else if (mode == "take photos") this.updateButtonStatus("idle_only_pictures");
	else if (mode == "both") this.updateButtonStatus("idle_video_and_pictures");
	else this.updateButtonStatus("idle_only_video");
}

/**
 *  updates the status of the webcam element
 *  
 *  @param status string the status of the webcam element
 *  
 *  @return void
 */
this.updateStatus = function(status) {
	var container = document.getElementById("webcam_$ID_inner");
	var video = document.getElementById("webcam_$ID_box");
	var webcamoff = document.getElementById("webcam_$ID_webcamoff");
	if (status == "idle") {
		webcamoff.style.display = "none";
		video.style.display = "block";
		video.muted = true;
	}
	else if (status == "off") {
		var img = document.getElementById("webcam_$ID_webcamoff_img");
		img.style.width = container.clientWidth + "px";
		img.style.height = container.clientHeight + "px";
		video.pause();
		video.src = "";
		video.ontimeupdate = null;
		video.onended = null;
		video.onloadeddata = null;
		video.onerror = null;
		video.load();
		this.updateButtonStatus("hidden");
		video.style.display = "none";
		document.getElementById("webcam_$ID_screenshot").src = "";
		webcamoff.style.display = "block";
	}
	else if (status == "video_notsupported") {
		container.className = "wfu_file_webcam_inner wfu_webcam_notsupported";
	}
}

/**
 *  updates the status of the webcam buttons
 *  
 *  @param status string the status of the webcam buttons
 *  
 *  @return void
 */
this.updateButtonStatus = function(status) {
	var onoff = document.getElementById("webcam_$ID_btn_onoff");
	var nav = document.getElementById("webcam_$ID_nav");
	var vid = document.getElementById("webcam_$ID_btn_video");
	var rec = document.getElementById("webcam_$ID_btn_record");
	var play = document.getElementById("webcam_$ID_btn_play");
	var stop = document.getElementById("webcam_$ID_btn_stop");
	var pause = document.getElementById("webcam_$ID_btn_pause");
	var pos = document.getElementById("webcam_$ID_btn_pos");
	var back = document.getElementById("webcam_$ID_btn_back");
	var fwd = document.getElementById("webcam_$ID_btn_fwd");
	var tim = document.getElementById("webcam_$ID_btn_time");
	var pic = document.getElementById("webcam_$ID_btn_picture");
	var screenshot = document.getElementById("webcam_$ID_screenshot");
	
	onoff.style.display = "block";
	//buttons are hidden
	if (status == "hidden") {
		nav.style.display = "none";
	}
	//video recording on progress
	else if (status == "recording") {
		nav.removeAttribute("style");
		nav.className = "wfu_file_webcam_nav wfu_recording";
		vid.style.display = "none";
		rec.style.display = "block";
		rec.style.visibility = "visible";
		rec.setAttribute("class", "wfu_file_webcam_btn wfu_file_webcam_btn_record wfu_recording");
		stop.style.display = "block";
		stop.style.visibility = "visible";
		play.style.display = "block";
		play.style.visibility = "hidden";
		pause.style.display = "block";
		pause.style.visibility = "hidden";
		pos.style.display = "block";
		pos.style.visibility = "hidden";
		back.style.display = "block";
		back.style.visibility = "hidden";
		fwd.style.display = "block";
		fwd.style.visibility = "hidden";
		tim.style.display = "block";
		tim.style.visibility = "visible";
		pic.style.display = "none";
		screenshot.style.display = "none";
	}
	//video recording finished
	else if (status == "after_recording") {
		nav.removeAttribute("style");
		nav.className = "wfu_file_webcam_nav wfu_stream_ready";
		vid.style.display = "block";
		vid.setAttribute("class", "wfu_file_webcam_btn wfu_file_webcam_btn_video");
		rec.style.display = "none";
		stop.style.display = "block";
		stop.style.visibility = "hidden";
		play.style.display = "block";
		play.style.visibility = "hidden";
		pause.style.display = "block";
		pause.style.visibility = "hidden";
		pos.style.display = "block";
		pos.style.visibility = "hidden";
		back.style.display = "block";
		back.style.visibility = "hidden";
		fwd.style.display = "block";
		fwd.style.visibility = "hidden";
		tim.style.display = "block";
		tim.style.visibility = "hidden";
		pic.style.display = "none";
		screenshot.style.display = "block";
	}
	//video is available for playback
	else if (status == "ready_playback") {
		nav.removeAttribute("style");
		nav.className = "wfu_file_webcam_nav wfu_stream_ready";
		vid.style.display = "block";
		vid.setAttribute("class", "wfu_file_webcam_btn wfu_file_webcam_btn_video");
		rec.style.display = "none";
		stop.style.display = "block";
		stop.style.visibility = "hidden";
		play.style.display = "block";
		play.style.visibility = "visible";
		play.setAttribute("class", "wfu_file_webcam_btn wfu_file_webcam_btn_play");
		pause.style.display = "block";
		pause.style.visibility = "visible";
		pause.setAttribute("class", "wfu_file_webcam_btn_disabled wfu_file_webcam_btn_pause_disabled");
		pos.style.display = "block";
		pos.style.visibility = "visible";
		back.style.display = "block";
		back.style.visibility = "visible";
		fwd.style.display = "block";
		fwd.style.visibility = "visible";
		tim.style.display = "block";
		tim.style.visibility = "visible";
		pic.style.display = "none";
		screenshot.style.display = "none";
	}
	//a screenshot has been captured
	else if (status == "after_screenshot") {
		nav.removeAttribute("style");
		nav.className = "wfu_file_webcam_nav wfu_stream_ready";
		vid.style.display = "block";
		vid.setAttribute("class", "wfu_file_webcam_btn wfu_file_webcam_btn_video");
		rec.style.display = "none";
		stop.style.display = "block";
		stop.style.visibility = "hidden";
		play.style.display = "block";
		play.style.visibility = "hidden";
		pause.style.display = "block";
		pause.style.visibility = "hidden";
		pos.style.display = "block";
		pos.style.visibility = "hidden";
		back.style.display = "block";
		back.style.visibility = "hidden";
		fwd.style.display = "block";
		fwd.style.visibility = "hidden";
		tim.style.display = "block";
		tim.style.visibility = "hidden";
		pic.style.display = "none";
		screenshot.style.display = "block";
	}
	//video playback on progress
	else if (status == "playing") {
		nav.removeAttribute("style");
		nav.className = "wfu_file_webcam_nav wfu_stream_ready";
		vid.style.display = "block";
		vid.setAttribute("class", "wfu_file_webcam_btn_disabled wfu_file_webcam_btn_video_disabled");
		rec.style.display = "none";
		stop.style.display = "block";
		stop.style.visibility = "hidden";
		play.style.display = "block";
		play.style.visibility = "visible";
		play.setAttribute("class", "wfu_file_webcam_btn_disabled wfu_file_webcam_btn_play_disabled");
		pause.style.display = "block";
		pause.style.visibility = "visible";
		pause.setAttribute("class", "wfu_file_webcam_btn wfu_file_webcam_btn_pause");
		pos.style.display = "block";
		pos.style.visibility = "visible";
		back.style.display = "block";
		back.style.visibility = "visible";
		fwd.style.display = "block";
		fwd.style.visibility = "visible";
		tim.style.display = "block";
		tim.style.visibility = "visible";
		pic.style.display = "none";
		screenshot.style.display = "none";
	}
	//idle status, waiting for video recording or screenshot capture
	else {
		nav.removeAttribute("style");
		nav.className = "wfu_file_webcam_nav wfu_rec_ready";
		vid.style.display = "none";
		rec.style.display = "none";
		stop.style.display = "none";
		play.style.display = "none";
		pause.style.display = "none";
		pos.style.display = "none";
		back.style.display = "none";
		fwd.style.display = "none";
		tim.style.display = "none";
		pic.style.display = "none";
		screenshot.style.display = "none";
		if (status == "idle_only_video" || status == "idle_video_and_pictures") {
			rec.style.display = "block";
			rec.setAttribute("class", "wfu_file_webcam_btn wfu_file_webcam_btn_record");
		}
		if (status == "idle_only_pictures" || status == "idle_video_and_pictures") {
			pic.style.display = "block";
		}
	}
}

/**
 *  updates the timer of video recording
 *  
 *  @param time float number of seconds of recording; the mantissa provides
 *         information about milliseconds
 *  
 *  @return void
 */
this.updateTimer = function(time) {
	var hours = Math.floor(time / 3600);
	time -= hours * 3600;
	var minutes = Math.floor(time / 60);
	time -= minutes * 60;
	var secs = Math.floor(time);
	var msecs = (time - Math.floor(time)) * 1000;
	document.getElementById("webcam_$ID_btn_time_label").innerHTML = (hours > 0 ? hours + ":" : "") + (minutes < 10 ? "0" : "") + minutes + ":" + (secs < 10 ? "0" : "") + secs;
}

/**
 *  updates the playback progress timer
 *  
 *  @param duration float the duration of the video stream
 *  
 *  @return void
 */
this.updatePlayProgress = function(duration) {
	var video = document.getElementById("webcam_$ID_box");
	var pointer = document.getElementById("webcam_$ID_btn_pointer");
	duration = (isFinite(video.duration) ? video.duration : duration);
	var pos = Math.round(video.currentTime / duration * 100);
	pointer.style.left = pos + "%";
}

/**
 *  sets various properties of the video element
 *  
 *  @param props object contains the properties and their values
 *  
 *  @return void
 */
this.setVideoProperties = function(props) {
	var video = document.getElementById("webcam_$ID_box");
	for (var prop in props) {
		if (props.hasOwnProperty(prop)) {
			if (prop == "srcObject") {
				try {
					video.srcObject = props["srcObject"];
				}
				catch (error) {
					//fallback to the src property if srcObject not supported
					video.srcObject = null;
					video.src = window.URL.createObjectURL(props["srcObject"]);
				}
			}
			else video[prop] = props[prop];
		}
	}
}

/**
 *  gets width and height of video
 *  
 *  @return object returns the width and height of video element as an object
 *          with two properties, width and height
 */
this.videoSize = function() {
	var video = document.getElementById("webcam_$ID_box");
	return {width: video.videoWidth, height: video.videoHeight};
}

/**
 *  gets the ready state of the video element
 *  
 *  @return integer the ready state of the video element
 */
this.readyState = function() {
	var video = document.getElementById("webcam_$ID_box");
	return video.readyState;
}

/**
 *  gets a screenshot of the video stream and saves it
 *  
 *  This function gets a screenshot image of the current video stream and saves
 *  it internally in an img element. If savefunc is defined (not null) then the
 *  function will convert the saved screenshot into an image file object (or
 *  blob) of type image_type and will execute savefunc passing the image as
 *  parameter.
 *  
 *  @return void
 */
this.screenshot = function(savefunc, image_type) {
	var video = document.getElementById("webcam_$ID_box");
	var canvas = document.getElementById("webcam_$ID_canvas");
	var screenshot = document.getElementById("webcam_$ID_screenshot");
	canvas.width = video.clientWidth;
	canvas.height = video.clientHeight;
	var ctx = canvas.getContext('2d');
	ctx.drawImage(video, 0, 0, video.clientWidth, video.clientHeight);
	screenshot.src = canvas.toDataURL('image/webp');
	if (savefunc != null) {
		//the following commands will initialize toBlob function in case that it
		//does not exist; initialization will be executed only once
		if (!window["wfu_toBlob_function_initialized"]) {
			!function(t){"use strict";var e=t.HTMLCanvasElement&&t.HTMLCanvasElement.prototype,o=t.Blob&&function(){try{return Boolean(new Blob)}catch(t){return!1}}(),n=o&&t.Uint8Array&&function(){try{return 100===new Blob([new Uint8Array(100)]).size}catch(t){return!1}}(),r=t.BlobBuilder||t.WebKitBlobBuilder||t.MozBlobBuilder||t.MSBlobBuilder,a=/^data:((.*?)(;charset=.*?)?)(;base64)?,/,i=(o||r)&&t.atob&&t.ArrayBuffer&&t.Uint8Array&&function(t){var e,i,l,u,b,c,d,B,f;if(e=t.match(a),!e)throw new Error("invalid data URI");for(i=e[2]?e[1]:"text/plain"+(e[3]||";charset=US-ASCII"),l=!!e[4],u=t.slice(e[0].length),b=l?atob(u):decodeURIComponent(u),c=new ArrayBuffer(b.length),d=new Uint8Array(c),B=0;B<b.length;B+=1)d[B]=b.charCodeAt(B);return o?new Blob([n?d:c],{type:i}):(f=new r,f.append(c),f.getBlob(i))};t.HTMLCanvasElement&&!e.toBlob&&(e.mozGetAsFile?e.toBlob=function(t,o,n){t(n&&e.toDataURL&&i?i(this.toDataURL(o,n)):this.mozGetAsFile("blob",o))}:e.toDataURL&&i&&(e.toBlob=function(t,e,o){t(i(this.toDataURL(e,o)))})),"function"==typeof define&&define.amd?define(function(){return i}):"object"==typeof module&&module.exports?module.exports=i:t.dataURLtoBlob=i}(window);
			window["wfu_toBlob_function_initialized"] = true;
		}
		if (canvas.toBlob) {
			//convert the captured screenshot into an image file
			canvas.toBlob(
				function(blob) { savefunc(blob); },
				image_type
			);
		}
	}
}

/**
 *  runs when play button is pressed
 *  
 *  @return void
 */
this.play = function() {
	var video = document.getElementById("webcam_$ID_box");
	video.muted = false;
	video.play();	
}

/**
 *  runs when pause button is pressed
 *  
 *  @return void
 */
this.pause = function() {
	var video = document.getElementById("webcam_$ID_box");
	video.pause();	
}

/**
 *  runs when backward button is pressed during playback
 *  
 *  This function takes the recorded video to the beginning
 *  
 *  @return void
 */
this.back = function() {
	var video = document.getElementById("webcam_$ID_box");
	video.src = video.src;
	video.currentTime = 0;
}

/**
 *  runs when forward button is pressed during playback
 *  
 *  This function takes the recorded video to the end
 *  
 *  @param duration float the duration of the video stream
 *  
 *  @return void
 */
this.fwd = function(duration) {
	var video = document.getElementById("webcam_$ID_box");
	video.currentTime = (isFinite(video.duration) ? video.duration : duration * 2);
}

/**
 *  runs when video playback has ended
 *  
 *  @return void
 */
this.ended = function() {
	var video = document.getElementById("webcam_$ID_box");
	video.src = video.src;
}
/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
<div id="webcam_$ID" class="wfu_file_webcam" style="<?php echo esc_html($styles); ?>">
	<div id="webcam_$ID_inner" class="wfu_file_webcam_inner">
		<label id="webcam_$ID_notsupported" class="wfu_webcam_notsupported_label" style="display:none;"><?php echo WFU_ERROR_WEBCAM_NOTSUPPORTED; ?></label>
		<img id="webcam_$ID_btns" src="<?php echo WFU_IMAGE_MEDIA_BUTTONS; ?>" style="display:none;" />
		<svg viewBox="0 0 8 8" id="webcam_$ID_btn_onoff" class="wfu_file_webcam_btn wfu_file_webcam_btn_onoff" onclick="wfu_webcam_onoff($ID);" style="display:none;"><use xlink:href="#power-standby"></use><rect width="8" height="8" fill="transparent"><title><?php echo WFU_WEBCAM_TURNONOFF_BTN; ?></title></rect></svg>
		<img id="webcam_$ID_screenshot" style="display:none; position:absolute; width:100%; height:100%;" />
		<canvas id="webcam_$ID_canvas" style="display:none;"></canvas>
		<video autoplay="true" id="webcam_$ID_box" class="wfu_file_webcam_box"><?php echo WFU_ERROR_WEBCAM_NOTSUPPORTED; ?></video>
		<div class="wfu_file_webcam_nav_container">
			<div id="webcam_$ID_nav" class="wfu_file_webcam_nav wfu_rec_ready" style="display:none;">
				<input id="webcam_$ID_btns_converted" type="hidden" value="" />
				<svg viewBox="0 0 8 8" id="webcam_$ID_btn_video" class="wfu_file_webcam_btn wfu_file_webcam_btn_video" onclick="wfu_webcam_golive($ID);"><use xlink:href="#video"></use><rect width="8" height="8" fill="transparent"><title><?php echo WFU_WEBCAM_GOLIVE_BTN; ?></title></rect></svg>
				<svg viewBox="0 0 8 8" id="webcam_$ID_btn_record" class="wfu_file_webcam_btn wfu_file_webcam_btn_record" onclick="wfu_webcam_start_rec($ID);"><use xlink:href="#media-record"></use><rect width="8" height="8" fill="transparent"><title><?php echo WFU_WEBCAM_RECVIDEO_BTN; ?></title></rect></svg>
				<svg viewBox="0 0 8 8" id="webcam_$ID_btn_stop" class="wfu_file_webcam_btn wfu_file_webcam_btn_stop" onclick="wfu_webcam_stop_rec($ID);"><use xlink:href="#media-stop"></use><rect width="8" height="8" fill="transparent"><title><?php echo WFU_WEBCAM_STOPREC_BTN; ?></title></rect></svg>
				<svg viewBox="0 0 8 8" id="webcam_$ID_btn_play" class="wfu_file_webcam_btn wfu_file_webcam_btn_play" onclick="wfu_webcam_play($ID);"><use xlink:href="#media-play"></use><rect width="8" height="8" fill="transparent"><title><?php echo WFU_WEBCAM_PLAY_BTN; ?></title></rect></svg>
				<svg viewBox="0 0 8 8" id="webcam_$ID_btn_pause" class="wfu_file_webcam_btn wfu_file_webcam_btn_pause" onclick="wfu_webcam_pause($ID);"><use xlink:href="#media-pause"></use><rect width="8" height="8" fill="transparent"><title><?php echo WFU_WEBCAM_PAUSE_BTN; ?></title></rect></svg>
				<div id="webcam_$ID_btn_pos" class="wfu_file_webcam_btn_pos">
					<svg viewBox="0 0 8 8" class="wfu_file_webcam_btn_bar" preserveAspectRatio="none"><use xlink:href="#minus"></use></svg>
					<svg viewBox="1 1 6 6" id="webcam_$ID_btn_pointer" class="wfu_file_webcam_btn_pointer" preserveAspectRatio="none"><use xlink:href="#media-stop" transform="rotate(0)"></use></svg>
				</div>
				<svg viewBox="0 0 8 8" id="webcam_$ID_btn_back" class="wfu_file_webcam_btn wfu_file_webcam_btn_back" onclick="wfu_webcam_back($ID);"><use xlink:href="#media-skip-backward"></use><rect width="8" height="8" fill="transparent"><title><?php echo WFU_WEBCAM_GOBACK_BTN; ?></title></rect></svg>
				<svg viewBox="0 0 8 8" id="webcam_$ID_btn_fwd" class="wfu_file_webcam_btn wfu_file_webcam_btn_fwd" onclick="wfu_webcam_fwd($ID);"><use xlink:href="#media-skip-forward"></use><rect width="8" height="8" fill="transparent"><title><?php echo WFU_WEBCAM_GOFWD_BTN; ?></title></rect></svg>
				<svg viewBox="0 0 8 8" id="webcam_$ID_btn_picture" class="wfu_file_webcam_btn wfu_file_webcam_btn_picture" onclick="wfu_webcam_take_picture($ID);"><use xlink:href="#aperture"></use><rect width="8" height="8" fill="transparent"><title><?php echo WFU_WEBCAM_TAKEPIC_BTN; ?></title></rect></svg>
				<div id="webcam_$ID_btn_time" class="wfu_file_webcam_btn_time">
					<table class="wfu_file_webcam_btn_time_tb">
						<tbody>
							<tr class="wfu_file_webcam_btn_time_tr">
								<td class="wfu_file_webcam_btn_time_td">
									<label id="webcam_$ID_btn_time_label">00:00</label>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div id="webcam_$ID_webcamoff" class="wfu_file_webcam_off" style="display:none;">
			<svg viewBox="-2 -2 12 12"><use xlink:href="#video"></use></svg>
			<img id="webcam_$ID_webcamoff_img" src="" />
		</div>
	</div>
</div>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_message_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of textbox element
 *  @var $height string assigned height of textbox element
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $header_styles array an array of predefined header styles
 *  @var $index int the index of occurrence of the element inside the plugin,
 *       in case that it appears more than once
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
	$styles = "display:".( $testmode ? "table" : "none" ).";";
	//for responsive plugin adjust container's widths if a % width has been defined
	if ( $responsive && strlen($width) > 1 && substr($width, -1, 1) == "%" ) $styles .= 'width: 100%;';
	elseif ( $width != "" ) $styles .= "width: $width; ";
	if ( $height != "" ) $styles .= "height: $height; ";
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
table.file_messageblock_table
{
	margin: 0;
	padding: 0;
	border: none;
}

tr.file_messageblock_header_tr
{
}

td.file_messageblock_header_td
{
	border: 1px solid #dddddd;
	margin: 0;
	padding: 0;
}

div.file_messageblock_header
{
	margin: 0;
	padding: 2px;
}

label.file_messageblock_header_label
{
	font-weight: bold;
	font-size: 12px;
	line-height: 1;
}

td.file_messageblock_arrow_td
{
	border: 1px solid #dddddd;
	margin: 0;
	padding: 0;
	width: 20px;
	vertical-align: middle;
}

div.file_messageblock_header_arrow_up
{
	width: 0; 
	height: 0; 
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-bottom: 5px solid #555555;
	margin: 5px 1px 1px 5px;
	/* ie6 height fix */
	font-size: 0;
	line-height: 0;
	/* ie6 transparent fix */
	_border-right-color: pink;
	_border-left-color: pink;
	_filter: chroma(color=pink);
}

div.file_messageblock_header_arrow_down
{
	width: 0; 
	height: 0; 
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-top: 5px solid #555555;
	margin: 5px 1px 1px 5px;
	/* ie6 height fix */
	font-size: 0;
	line-height: 0;
	/* ie6 transparent fix */
	_border-right-color: pink;
	_border-left-color: pink;
	_filter: chroma(color=pink);
}

tr.file_messageblock_subheader_tr
{
}

td.file_messageblock_subheader_td
{
	margin: 0;
	padding: 0;
	border: 1px solid #dddddd;
}

div.file_messageblock_subheader_message
{
	margin: 0;
	padding: 2px;
	background: none;
}

label.file_messageblock_subheader_messagelabel
{
	font-weight: normal;
	font-size: 12px;
	line-height: 1;
}

div.file_messageblock_subheader_adminmessage
{
	margin: 0;
	padding: 2px;
	background-color: #F7F7F7;
	overflow: scroll;
}

label.file_messageblock_subheader_debugmessage_label
{
	margin: 0;
	padding: 0;
	background: none;
	border: none;
	font-weight: bold;
}

div.file_messageblock_subheader_debugmessage_container
{
	margin: 0 0 0 20px;
	padding: 0px;
	background: none;
	border: none;
	font-size: 10px;
}

label.file_messageblock_subheader_adminmessagelabel
{
	font-weight: normal;
	font-size: 12px;
	font-style: italic;
	line-height: 1;
}

tr.file_messageblock_fileheader_tr
{
}

td.file_messageblock_filenumber_td
{
	width: 30px;
	margin: 0;
	padding: 2px;
	text-align: center;
	vertical-align: middle;
	font-weight: bold;
	font-size: 11px;
	line-height: 1;
	border: 1px solid #dddddd;
}

td.file_messageblock_fileheader_td
{
	margin: 0;
	padding: 0;
	border: 1px solid #dddddd;
}

div.file_messageblock_fileheader
{
	margin: 0;
	padding: 2px;
}

label.file_messageblock_fileheader_label
{
	font-weight: bold;
	font-size: 11px;
	line-height: 1;
}

td.file_messageblock_filearrow_td
{
	border: 1px solid #dddddd;
	margin: 0;
	padding: 0;
	width: 20px;
	border: 1px solid #dddddd;
	vertical-align: middle;
}

div.file_messageblock_file_arrow_up
{
	width: 0; 
	height: 0; 
	border-left: 4px solid transparent;
	border-right: 4px solid transparent;
	border-bottom: 4px solid #555555;
	margin: 5px 1px 1px 6px;
	/* ie6 height fix */
	font-size: 0;
	line-height: 0;
	/* ie6 transparent fix */
	_border-right-color: pink;
	_border-left-color: pink;
	_filter: chroma(color=pink);
}

div.file_messageblock_file_arrow_down
{
	width: 0; 
	height: 0; 
	border-left: 4px solid transparent;
	border-right: 4px solid transparent;
	border-top: 4px solid #555555;
	margin: 5px 1px 1px 6px;
	/* ie6 height fix */
	font-size: 0;
	line-height: 0;
	/* ie6 transparent fix */
	_border-right-color: pink;
	_border-left-color: pink;
	_filter: chroma(color=pink);
}

tr.file_messageblock_filesubheader_tr
{
}

td.file_messageblock_filesubheaderempty_td
{
	width: 30px;
	margin: 0;
	padding: 0;
	border: 1px solid #dddddd;
}

td.file_messageblock_filesubheader_td
{
	margin: 0;
	padding: 0;
	border: 1px solid #dddddd;
}

div.file_messageblock_filesubheader_message
{
	margin: 0;
	padding: 2px;
	background: none;
}

label.file_messageblock_filesubheader_messagelabel
{
	font-weight: normal;
	font-size: 11px;
	line-height: 1;
}

div.file_messageblock_filesubheader_adminmessage
{
	margin: 0;
	padding: 2px;
	background-color: #F7F7F7;
}

label.file_messageblock_filesubheader_adminmessagelabel
{
	font-weight: normal;
	font-size: 11px;
	font-style: italic;
	line-height: 1;
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].message.init = function() {
/***
 *  The following textbox methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method update updates the message block
 *  @method reset resets the message block
 */
/**
 *  updates the message block
 *  
 *  This function will update the message block. The message block may be
 *  updated many times during the upload, depending on the upload status of the
 *  uploaded file(s). The data parameter contains information about the general
 *  state of the upload, as well as information about the individual files that
 *  have been updated since the last update.
 *  
 *  @param data object an object holding data about the overall upload as below:
 *         files_count [int]: the total number of files of this upload
 *         files_processed [int]: number of files that have been processed
 *         state [int]: the state of upload
 *         single [bool]: true if this is a single file upload
 *         color [string]: default font color of message header text
 *         bgcolor [string]: default background color of message header
 *         borcolor [string]: default border color of message header
 *         message1 [string]: a generic message about the overall upload result
 *         message2 [array]: an array of messages providing more details about
 *             the overall upload containing generic error information
 *         message3 [array]: a array of messages providing even more details
 *             (usually for admins) about the overall upload containing more
 *             detailed error information
 *         debug_data [array]: array of objects containing debug information in 
 *             case that debug mode is activated; every object contains two 
 *             properties: title and data representing the title and data of the
 *             debug information respectively
 *         files [array]: an array holding data about the files causing the
 *             update of the message block; every array item is an object
 *             containing various properties:
 *             index [int]: the index of the file
 *             result [string]: the upload result, can be 'success' for
 *                 successful upload, 'warning' if upload was successful but
 *                 there are warnings, 'error' if the upload failed
 *             message1 [string]: a generic message about the upload result of
 *                 this file
 *             message2 [string]: a message providing more details about the
 *                 upload of this file containing generic error information
 *             message3 [string]: a message providing even more details (usually
 *                 for admins) about the upload of this file containing more
 *                 detailed error information
 *  
 *  @return void
 */
this.update = function(data) {
	var message_table = document.getElementById('wfu_messageblock_$ID');
	var subheader_state = document.getElementById('wfu_messageblock_header_$ID_state');
	// adjust header if must be shown
	if (data.single) {
		document.getElementById('wfu_messageblock_header_$ID').style.display = "none";
	}
	else {
		document.getElementById('wfu_messageblock_header_$ID').style.display = "";
		var header_container = document.getElementById('wfu_messageblock_header_$ID_container');
		header_container.innerHTML = this._apply_header_template(data);

		// adjust subheader message
		var subheader_show = false;
		if (data.message2.length > 0) {
			document.getElementById('wfu_messageblock_subheader_$ID_message').style.display = "";
			document.getElementById('wfu_messageblock_subheader_$ID_messagelabel').innerHTML = data.message2.join("<br />");
			subheader_show = true;
		}
		else
			document.getElementById('wfu_messageblock_subheader_$ID_message').style.display = "none";

		//unify admin message and debug data
		for (var i = 0; i < data.debug_data.length; i++)
			data.message3.push(this._format_debug_data(data.debug_data[i]));
		// adjust subheader admin message
		if (data.message3.length > 0) {
			document.getElementById('wfu_messageblock_subheader_$ID_adminmessage').style.display = "";
			document.getElementById('wfu_messageblock_subheader_$ID_adminmessagelabel').innerHTML = data.message3.join("<br />");
			subheader_show = true;
		}
		else
			document.getElementById('wfu_messageblock_subheader_$ID_adminmessage').style.display = "none";

		// adjust subheader
		if (subheader_show)
			document.getElementById('wfu_messageblock_subheader_$ID').style.display = subheader_state.value;
		else
			document.getElementById('wfu_messageblock_subheader_$ID').style.display = "none";

		// adjust header arrow
		if (subheader_show || data.files_processed > 0) {
			header_container.colSpan = 2;
			document.getElementById('wfu_messageblock_arrow_$ID').style.display = "";
		}
		else {
			document.getElementById('wfu_messageblock_arrow_$ID').style.display = "none";
			header_container.colSpan = 3;
		}
	}
	var next_block = document.getElementById('wfu_messageblock_subheader_$ID');
	var next_block_id = 0;

	// insert file blocks
	var file_block = null;
	var file_template_container = document.getElementById('wfu_messageblock_$ID_filetemplate');
	var file_contents = "";
	var door = document.getElementById('wfu_messageblock_$ID_door');
	var headerspan = 1;
	var subheaderspan = 2;
	var file_template = wfu_plugin_decode_string(file_template_container.value.replace(/^\s+|\s+$/g,""));
	for (var i = 0; i < data.files.length; i++) {
		var file = data.files[i];
		// replace template variables with file data
		file_contents = file_template.replace(/\[file_id\]/g, file.index);
		file_contents = file_contents.replace(/\[fileheader_color\]/g, GlobalData.Colors[file.result][0]);
		file_contents = file_contents.replace(/\[fileheader_bgcolor\]/g, GlobalData.Colors[file.result][1]);
		file_contents = file_contents.replace(/\[fileheader_borcolor\]/g, GlobalData.Colors[file.result][2]);
		file_contents = file_contents.replace(/\[fileheader_message\]/g, file.message1);
		file_contents = file_contents.replace(/\[filesubheadermessage_display\]/g, "style=\"display:none;\"");
		file_contents = file_contents.replace(/\[filesubheader_message\]/g, file.message2);
		file_contents = file_contents.replace(/\[filesubheaderadminmessage_display\]/g, "style=\"display:none;\"");
		file_contents = file_contents.replace(/\[filesubheader_adminmessage\]/g, file.message3);
		// put file contents to temp div element to convert them to HTML elements
		var door_table = document.createElement("TABLE");
		var door_tbody = document.createElement("TBODY");
		door_tbody.innerHTML = file_contents;
		door_table.appendChild(door_tbody);
		door.innerHTML = "";
		door.appendChild(door_table);
		// post process created file block to adjust visibility of its contents
		headerspan = 1;
		subheaderspan = 2;
		subheader_show = false;
		file_block = document.getElementById('wfu_messageblock_$ID_' + file.index);
		if (data.files_count == 1) {
			document.getElementById('wfu_messageblock_$ID_filenumber_' + file.index).style.display = "none";
			document.getElementById('wfu_messageblock_subheader_$ID_fileempty_' + file.index).style.display = "none";
			if (data.single) file_block.style.display = "";
			else file_block.style.display = subheader_state.value;
			headerspan ++;
			subheaderspan ++;
		}
		else file_block.style.display = subheader_state.value;
		if (file.message2 != "") {
			document.getElementById('wfu_messageblock_subheader_$ID_message_' + file.index).style.display = "";
			subheader_show = true;
		}
		if (file.message3 != "") {
			document.getElementById('wfu_messageblock_subheader_$ID_adminmessage_' + file.index).style.display = "";
			subheader_show = true;
		}
		if (!subheader_show) {
			document.getElementById('wfu_messageblock_arrow_$ID_' + file.index).style.display = "none";
			headerspan ++;
		}
		document.getElementById('wfu_messageblock_header_$ID_container_' + file.index).colSpan = headerspan;
		document.getElementById('wfu_messageblock_subheader_$ID_container_' + file.index).colSpan = subheaderspan;
		// move file block inside message block
		while (next_block_id < file.index) {
			next_block = next_block.nextSibling;
			if (next_block == null) break;
			if (next_block.nodeType === 1) next_block_id = next_block.id.substr(next_block.id.lastIndexOf("_") + 1);
		}
		message_table.tBodies[0].insertBefore(file_block, next_block);
		next_block = file_block.nextSibling;
		file_block = document.getElementById('wfu_messageblock_subheader_$ID_' + file.index);
		message_table.tBodies[0].insertBefore(file_block, next_block);
		next_block = file_block;
		next_block_id = file.index;
	}
	if (data.single) document.getElementById('wfu_messageblock_$ID_1').style.display = "";
	//show message block
	message_table.style.display = "table";
}

/**
 *  resets the message block
 *  
 *  @return void
 */
this.reset = function() {
	var message_table = document.getElementById('wfu_messageblock_$ID');
	//hide message block
	message_table.style.display = "none";
	// reset header
	document.getElementById('wfu_messageblock_header_$ID').style.display = "";
	var header_container = document.getElementById('wfu_messageblock_header_$ID_container');
	header_container.innerHTML = this._apply_header_template(GlobalData.States["State0"]);
	document.getElementById('wfu_messageblock_header_$ID_state').value = "none";
	document.getElementById('wfu_messageblock_arrow_$ID').style.display = "none";
	header_container.colSpan = 3;
	// reset subheader
	document.getElementById('wfu_messageblock_subheader_$ID_messagelabel').innerHTML = "";
	document.getElementById('wfu_messageblock_subheader_$ID_adminmessagelabel').innerHTML = "";
	document.getElementById('wfu_messageblock_subheader_$ID').style.display = "none";
	document.getElementById('wfu_messageblock_subheader_$ID_message').style.display = "none";
	document.getElementById('wfu_messageblock_subheader_$ID_adminmessage').style.display = "none";
	// reset files
	var file_array = this._get_file_ids();
	for (var i = 1; i <= file_array.length; i++) {
		message_table.tBodies[0].removeChild(document.getElementById('wfu_messageblock_$ID_' + i));
		message_table.tBodies[0].removeChild(document.getElementById('wfu_messageblock_subheader_$ID_' + i));
	}
}

//************* Internal Function Definitions **********************************

/**
 *  create HTML code from header template
 *  
 *  This function creates HTML code from the header template. The parameter data
 *  contains values that will replace template variables, such as [header_safe],
 *  [header_color] etc.
 *  
 *  @param data object contains the properties color, bgcolor, borcolor and
 *         message1 that will replace template variables [header_color],
 *         [header_bgcolor], [header_borcolor] and [header_message] respectively
 *  
 *  @return string the generated HTML code
 */
this._apply_header_template = function(data) {
	var template = wfu_plugin_decode_string(document.getElementById("wfu_messageblock_$ID_headertemplate").value);
	template = template.replace(/\[header_safe\]/g, "");
	template = template.replace(/\[header_color\]/g, data.color);
	template = template.replace(/\[header_bgcolor\]/g, data.bgcolor);
	template = template.replace(/\[header_borcolor\]/g, data.borcolor);
	template = template.replace(/\[header_message\]/g, data.message1);
	return template;
}

/**
 *  create HTML code from header template
 *  
 *  This function creates HTML code from the header template. The parameter data
 *  contains values that will replace template variables, such as [header_safe],
 *  [header_color] etc.
 *  
 *  @param data object contains the properties color, bgcolor, borcolor and
 *         message1 that will replace template variables [header_color],
 *         [header_bgcolor], [header_borcolor] and [header_message] respectively
 *  
 *  @return string the generated HTML code
 */
this._format_debug_data = function(debug_data) {
	var lab = document.createElement("LABEL");
	lab.className = "file_messageblock_subheader_debugmessage_label"
	lab.innerHTML = debug_data.title;
	var div = document.createElement("DIV");
	div.className = "file_messageblock_subheader_debugmessage_container"
	div.innerHTML = debug_data.data;
	return lab.outerHTML+div.outerHTML;
}

/**
 *  get the indices of the files shown in message block
 *  
 *  This function returns an array of the indices of the files already shown in
 *  the message block.
 *  
 *  @return array the array of indices
 */
this._get_file_ids = function() {
	var message_table = document.getElementById('wfu_messageblock_$ID');
	var next_block = document.getElementById('wfu_messageblock_subheader_$ID').nextSibling;
	var prefix = 'wfu_messageblock_$ID_';
	var file_ids = [];
	while (next_block != null) {
		if (next_block.nodeType === 1 && next_block.id.substr(0, prefix.length) == prefix)
			file_ids.push(next_block.id.substr(next_block.id.lastIndexOf("_") + 1));
		next_block = next_block.nextSibling;
	}
	return file_ids;
}

/**
 *  shows or hides detailed information of the overall upload
 *  
 *  @return void
 */
this._headerdetails_toggle = function() {
	var item1 = document.getElementById('wfu_messageblock_arrow_$ID');
	var item2 = document.getElementById('wfu_messageblock_arrow_$ID_up');
	var item3 = document.getElementById('wfu_messageblock_arrow_$ID_down');
	var item4 = document.getElementById('wfu_messageblock_subheader_$ID');
	var item5 = document.getElementById('wfu_messageblock_subheader_$ID_message');
	var item6 = document.getElementById('wfu_messageblock_subheader_$ID_adminmessage');
	var item7 = document.getElementById('wfu_messageblock_header_$ID_state');
	var file_ids = this._get_file_ids();
	var show = (item2.style.display == "none");
	if (show) {
		item2.style.display = "";
		item3.style.display = "none";
		if ( item5.style.display != "none" || item6.style.display != "none" ) item4.style.display = "";
		item7.value = "";
		for (var i = 0; i < file_ids.length; i++) {
			document.getElementById('wfu_messageblock_$ID_' + file_ids[i]).style.display = "";
			document.getElementById('wfu_messageblock_subheader_$ID_' + file_ids[i]).style.display = document.getElementById('wfu_messageblock_header_$ID_state_' + file_ids[i]).value;
		}
	}
	else {
		item2.style.display = "none";
		item3.style.display = "";
		item4.style.display = "none";
		item7.value = "none";
		for (var i = 0; i < file_ids.length; i++) {
			document.getElementById('wfu_messageblock_$ID_' + file_ids[i]).style.display = "none";
			document.getElementById('wfu_messageblock_subheader_$ID_' + file_ids[i]).style.display = "none";
		}
	}
}

/**
 *  shows or hides detailed information of a specific file
 *  
 *  @param fileid int the index of the specific file
 *  
 *  @return void
 */
this._filedetails_toggle = function(fileid) {
	var item1 = document.getElementById('wfu_messageblock_arrow_$ID_' + fileid);
	var item2 = document.getElementById('wfu_messageblock_arrow_$ID_up_' + fileid);
	var item3 = document.getElementById('wfu_messageblock_arrow_$ID_down_' + fileid);
	var item4 = document.getElementById('wfu_messageblock_subheader_$ID_' + fileid);
	var item5 = document.getElementById('wfu_messageblock_header_$ID_state_' + fileid);
	var show = (item2.style.display == "none");
	if (show) {
		item2.style.display = "";
		item3.style.display = "none";
		item4.style.display = "";
		item5.value = "";
	}
	else {
		item2.style.display = "none";
		item3.style.display = "";
		item4.style.display = "none";
		item5.value = "none";
	}
}
/* do not change this line */}
</script><?php /****************************************************************
        the following lines contain the HTML template of the message block
****************************************************************************/ ?>
<table id="wfu_messageblock_$ID" class="file_messageblock_table" style="<?php echo esc_html($styles); ?>">
	<tbody>
		<tr id="wfu_messageblock_header_$ID" class="file_messageblock_header_tr" style="display:<?php echo ( $testmode ? "table-row" : "none" ); ?>;">
			<td colspan="2" id="wfu_messageblock_header_$ID_container" class="file_messageblock_header_td">
<?php if ( $testmode ): ?>
				<div id="wfu_messageblock_header_$ID" class="file_messageblock_header" style="color:<?php echo $header_styles["State9"]["color"]; ?>; background-color:<?php echo $header_styles["State9"]["bgcolor"]; ?>; border:1px solid <?php echo $header_styles["State9"]["borcolor"]; ?>;">
					<label id="wfu_messageblock_header_$ID_label" class="file_messageblock_header_label"><?php echo $header_styles["State9"]["message"]; ?></label>
				</div>
<?php endif ?>
			</td>
			<td id="wfu_messageblock_arrow_$ID" class="file_messageblock_arrow_td" style="display:<?php echo ( $testmode ? "table-cell" : "none" ); ?>;" onclick="GlobalData.WFU[$ID].message._headerdetails_toggle();">
				<input id="wfu_messageblock_header_$ID_state" type="hidden" value="none" />
				<div id="wfu_messageblock_arrow_$ID_up" class="file_messageblock_header_arrow_up" style="display:none;"></div>
				<div id="wfu_messageblock_arrow_$ID_down" class="file_messageblock_header_arrow_down"></div>
			</td>
		</tr>
		<tr id="wfu_messageblock_subheader_$ID" class="file_messageblock_subheader_tr" style="display:none;">
			<td colspan="3" id="wfu_messageblock_subheader_$ID_td" class="file_messageblock_subheader_td">
				<div id="wfu_messageblock_subheader_$ID_message" class="file_messageblock_subheader_message" style="display:<?php echo ( $testmode ? "block" : "none" ); ?>;">
					<label id="wfu_messageblock_subheader_$ID_messagelabel" class="file_messageblock_subheader_messagelabel"><?php echo ( $testmode ? WFU_TESTMESSAGE_MESSAGE : "" ); ?></label>
				</div>
				<div id="wfu_messageblock_subheader_$ID_adminmessage" class="file_messageblock_subheader_adminmessage" style="display:<?php echo ( $testmode ? "block" : "none" ); ?>;">
					<label id="wfu_messageblock_subheader_$ID_adminmessagelabel" class="file_messageblock_subheader_adminmessagelabel"><?php echo ( $testmode ? WFU_TESTMESSAGE_ADMINMESSAGE : "" ); ?></label>
				</div>
			</td>
		</tr>
<?php if ( $testmode ): ?>
	<?php for ( $i = 1; $i <= 2; $i++ ): ?>
		<tr id="wfu_messageblock_$ID_<?php echo $i; ?>" class="file_messageblock_fileheader_tr" style="display:none;">
			<td id="wfu_messageblock_$ID_filenumber_<?php echo $i; ?>" class="file_messageblock_filenumber_td"><?php echo $i; ?></td>
			<td id="wfu_messageblock_header_$ID_container_<?php echo $i; ?>" class="file_messageblock_fileheader_td">
				<div id="wfu_messageblock_header_$ID_<?php echo $i; ?>" class="file_messageblock_fileheader" style="color:<?php echo $header_styles["State9"]["color"]; ?>; background-color:<?php echo $header_styles["State9"]["bgcolor"]; ?>; border:1px solid <?php echo $header_styles["State9"]["borcolor"]; ?>;">
					<label id="wfu_messageblock_header_$ID_label_<?php echo $i; ?>" class="file_messageblock_fileheader_label"><?php echo constant("WFU_TESTMESSAGE_FILE{$i}_HEADER"); ?></label>
					<!-- the following hidden input holds state of arrow (open or close) -->
					<input id="wfu_messageblock_header_$ID_state_<?php echo $i; ?>" type="hidden" value="none" />
				</div>
			</td>
			<!-- add a drop down arrow to the file header (file has always details to be shown) -->
			<td id="wfu_messageblock_arrow_$ID_<?php echo $i; ?>" class="file_messageblock_filearrow_td" onclick="GlobalData.WFU[$ID].message._filedetails_toggle(<?php echo $i; ?>);">
				<div id="wfu_messageblock_arrow_$ID_up_<?php echo $i; ?>" class="file_messageblock_file_arrow_up" style="display:none;"></div>
				<div id="wfu_messageblock_arrow_$ID_down_<?php echo $i; ?>" class="file_messageblock_file_arrow_down"></div>
			</td>
		</tr>
		<!-- add the files subheader block HTML template -->
		<tr id="wfu_messageblock_subheader_$ID_<?php echo $i; ?>" class="file_messageblock_filesubheader_tr" style="display:none;">
			<td id="wfu_messageblock_subheader_$ID_fileempty_<?php echo $i; ?>" class="file_messageblock_filesubheaderempty_td"></td>
			<td colspan="2" id="wfu_messageblock_subheader_$ID_container_<?php echo $i; ?>" class="file_messageblock_filesubheader_td">
				<div id="wfu_messageblock_subheader_$ID_message_<?php echo $i; ?>" class="file_messageblock_filesubheader_message" style="display:block;">
					<label id="wfu_messageblock_subheader_$ID_messagelabel_<?php echo $i; ?>" class="file_messageblock_filesubheader_messagelabel"><?php echo constant("WFU_TESTMESSAGE_FILE{$i}_MESSAGE"); ?></label>
				</div>
				<div id="wfu_messageblock_subheader_$ID_adminmessage_<?php echo $i; ?>" class="file_messageblock_filesubheader_adminmessage" style="display:block;">
					<label id="wfu_messageblock_subheader_$ID_adminmessagelabel_<?php echo $i; ?>" class="file_messageblock_filesubheader_adminmessagelabel"><?php echo constant("WFU_TESTMESSAGE_FILE{$i}_ADMINMESSAGE"); ?></label>
				</div>
			</td>
		</tr>
	<?php endfor ?>
<?php endif ?>
	</tbody>
</table>
<!-- State10 header for the case that JSON parse fails and upload results cannot be decoded -->
<div id="wfu_messageblock_header_$ID_safecontainer" style="display:none;">
				<div id="wfu_messageblock_header_$ID_safe" class="file_messageblock_header" style="color:<?php echo $header_styles["State10"]["color"]; ?>; background-color:<?php echo $header_styles["State10"]["bgcolor"]; ?>; border:1px solid <?php echo $header_styles["State10"]["borcolor"]; ?>;">
					<label id="wfu_messageblock_header_$ID_label_safe" class="file_messageblock_header_label"><?php echo $header_styles["State10"]["message"]; ?></label>
				</div>
</div>
<input id="wfu_messageblock_$ID_headertemplate" type="hidden" value="[header_template]" />
<input id="wfu_messageblock_$ID_filetemplate" type="hidden" value="[file_template]" />
<div id="wfu_messageblock_$ID_door" style="display:none;"></div>
<?php /*************************************************************************
      the following lines contain the HTML template of the header block
***********************************************************/ ?><header_template>
				<div id="wfu_messageblock_header_$ID[header_safe]" class="file_messageblock_header" style="color:[header_color]; background-color:[header_bgcolor]; border:1px solid [header_borcolor];">
					<label id="wfu_messageblock_header_$ID_label[header_safe]" class="file_messageblock_header_label">[header_message]</label>
				</div>
</header_template><?php /*******************************************************
      the following lines contain the HTML template of a file block
*************************************************************/ ?><file_template>
		<tr id="wfu_messageblock_$ID_[file_id]" class="file_messageblock_fileheader_tr" style="display:none;">
			<td id="wfu_messageblock_$ID_filenumber_[file_id]" class="file_messageblock_filenumber_td">[file_id]</td>
			<td id="wfu_messageblock_header_$ID_container_[file_id]" class="file_messageblock_fileheader_td">
				<div id="wfu_messageblock_header_$ID_[file_id]" class="file_messageblock_fileheader" style="color:[fileheader_color]; background-color:[fileheader_bgcolor]; border:1px solid [fileheader_borcolor];">
					<label id="wfu_messageblock_header_$ID_label_[file_id]" class="file_messageblock_fileheader_label">[fileheader_message]</label>
					<!-- the following hidden input holds state of arrow (open or close) -->
					<input id="wfu_messageblock_header_$ID_state_[file_id]" type="hidden" value="none" />
				</div>
			</td>
			<!-- add a drop down arrow to the file header (file has always details to be shown) -->
			<td id="wfu_messageblock_arrow_$ID_[file_id]" class="file_messageblock_filearrow_td" onclick="GlobalData.WFU[$ID].message._filedetails_toggle([file_id]);">
				<div id="wfu_messageblock_arrow_$ID_up_[file_id]" class="file_messageblock_file_arrow_up" style="display:none;"></div>
				<div id="wfu_messageblock_arrow_$ID_down_[file_id]" class="file_messageblock_file_arrow_down"></div>
			</td>
		</tr>
		<!-- add the files subheader block HTML template -->
		<tr id="wfu_messageblock_subheader_$ID_[file_id]" class="file_messageblock_filesubheader_tr" style="display:none;">
			<td id="wfu_messageblock_subheader_$ID_fileempty_[file_id]" class="file_messageblock_filesubheaderempty_td"></td>
			<td colspan="2" id="wfu_messageblock_subheader_$ID_container_[file_id]" class="file_messageblock_filesubheader_td">
				<div id="wfu_messageblock_subheader_$ID_message_[file_id]" class="file_messageblock_filesubheader_message"[filesubheadermessage_display]>
					<label id="wfu_messageblock_subheader_$ID_messagelabel_[file_id]" class="file_messageblock_filesubheader_messagelabel">[filesubheader_message]</label>
				</div>
				<div id="wfu_messageblock_subheader_$ID_adminmessage_[file_id]" class="file_messageblock_filesubheader_adminmessage"[filesubheaderadminmessage_display]>
					<label id="wfu_messageblock_subheader_$ID_adminmessagelabel_[file_id]" class="file_messageblock_filesubheader_adminmessagelabel">[filesubheader_adminmessage]</label>
				</div>
			</td>
		</tr>
</file_template><?php /*********************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_userdata_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of textbox element
 *  @var $height string assigned height of textbox element
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $props array an array of userdata field properties of the fields to be
 *       shown; every array item is an associative array holding field props,
 *       as follows:
 *       @prop key int the zero-based index of the field
 *       @prop type string the field type, the below types are supported:
 *             text: a simple text field
 *             multitext: a multi-line text field
 *             number: a number field that can be validated if it contains a
 *                 valid number
 *             email: an email field that can be validated if it contains a
 *                 valid email address
 *             confirmemail: an email field used to confirm a previously
 *                 defined email field
 *             password: a password field
 *             confirmpassword: a password field used to confirm a previously
 *                 defined password field
 *             checkbox: a field that checks a checkbox
 *             radiobutton: a field with radio button options
 *             date: a field to enter a date
 *             time: a field to enter a time
 *             datetime: a field to enter date and time
 *             list: a field that shows a listbox with options
 *             dropdown: a field that shows a dropdown list with options
 *       @prop label string the title of the field type
 *       @prop labelposition string the position of the label related to the
 *             field, it can be 'left', 'top', 'right', 'bottom' or 'none'
 *       @prop required bool true if the field is required
 *       @prop donotautocomplete bool true if the autcocomplete feature of the
 *             field element must be disabled
 *       @prop validate bool true if the field value will be validated
 *       @prop typehook bool true if the field value will be validated real-time
 *             during typing
 *       @prop hintposition string the position of the hint (a message that
 *             appears after validation to notify that the field is empty or
 *             invalid) related to the field; it can be 'left', 'right', 'top',
 *             'bottom', 'inline' or 'none'
 *       @prop default string a default value for the field
 *       @prop group string a value that is used to group together an email and
 *             a confirmemail field, or a password and a confirmpassword field,
 *             or radio buttons together
 *       @prop format string a format option that depends on the field type:
 *             number: number type, 'd' for integer, 'f' for floating point
 *             checkbox: position of the checkbox description in relation to the
 *                 checkbox, can be 'left', 'top', 'right' or 'bottom'
 *             radiobutton: position of the radio button labels in relation to
 *                 the radio buttons ('left', 'top', 'right' 'bottom') and
 *                 placement of the radio buttons ('horizontal', 'vertical')
 *             date: format of the date field
 *             time: format of the time field
 *             datetime: format of the datetime field
 *       @prop data string data where its meaning depends on the field type:
 *             checkbox: the description of the checkbox (apart from title)
 *             radiobutton: a comma-separated list of radio button items
 *             list: a comma-separated list of listbox items
 *             dropdown: a comma-separated list of dropdown items
 *  @var $index int the index of occurrence of the element inside the plugin,
 *       in case that it appears more than once
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
	$styles1 = "";
	$styles2 = "";
	$styles3 = "";
	//for responsive plugin adjust container's widths if a % width has been defined
	if ( $responsive && strlen($width) > 1 && substr($width, -1, 1) == "%" ) $styles1 = 'width: 100%;';
	elseif ( $width != "" ) $styles1 = "width: $width; ";
	if ( $height != "" ) $styles1 .= "height: $height; ";
	if ( $width_label != "" ) $styles2 = "width: $width_label; ";
	if ( $height_label != "" ) $styles2 .= "height: $height_label; ";
	if ( $width_value != "" ) $styles3 = "width: $width_value; ";
	if ( $height_value != "" ) $styles3 .= "height: $height_value; ";
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
div.file_userdata_fieldwrapper, div.file_userdata_fieldwrapper_required, div.file_userdata_fieldwrapper_required_empty
{
	position: relative;
	display: inline-block;
	width: 60%;
	height: 25px; /*relax*/
	margin: 0;
	padding: 0;
	background: none;
	border: none;
	box-shadow: none;
}

div.file_userdata_fieldwrapper div.wfu_fieldwrapper_overlay, div.file_userdata_fieldwrapper_required div.wfu_fieldwrapper_overlay
{
	position: absolute;
	display: none;
	width: 100%;
	height: 100%;
	margin: 0;
	padding: 0;
	background: none;
	border: none;
	box-shadow: none;
	z-index: 1000001;
}

div.file_userdata_fieldwrapper_required_empty div.wfu_fieldwrapper_overlay
{
	position: absolute;
	display: block;
	width: 100%;
	height: 100%;
	margin: 0;
	padding: 0;
	background: none;
	border: 2px solid red;
	box-shadow: none;
	z-index: 1000001;
}

div.file_userdata_radio_wrapper
{
	position: relative;
	display: inline-block;
	margin: 0;
	padding: 0;
	background: none;
	border: none;
	box-shadow: none;
}

div.file_userdata_container
{
	margin: 0;
	padding: 0;
	white-space: nowrap;
	position: relative;
}

label.file_userdata_label
{
	margin: 0; /*relax*/
	width: 40%;
	display: inline-block;
}

.file_userdata_message, .file_userdata_message_required
{
	width: 100%;
	height: 100%;
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: 1px solid; /*relax*/
	border-color: #BBBBBB; /*relax*/
	background: none; /*relax*/
	color: black; /*relax*/
}

.file_userdata_message:disabled, .file_userdata_message_required:disabled
{
	width: 100%;
	height: 100%;
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: 1px solid; /*relax*/
	border-color: #BBBBBB; /*relax*/
	background: none; /*relax*/
	color: silver; /*relax*/
}

.file_userdata_message_required_empty
{
	width: 100%;
	height: 100%;
	margin: 0px; /*relax*/
	padding: 0px; /*relax*/
	border: 1px solid; /*relax*/
	border-color: #BBBBBB; /*relax*/
	box-shadow: inset 0px 0px 2px 2px red;
	color: black; /*relax*/
}

.file_userdata_message_required_empty::after
{
	content: 'not empty';
}

.file_userdata_checkbox_description
{
	width: 100%;
	height: 100%;
	white-space: normal;
}

.file_userdata_listbox, .file_userdata_dropdown
{
	width: 100%;
	height: 100%;
}

div.file_userdata_hint
{
	position: absolute;
	background: #eee;
	border: 1px solid red;
	border-radius: 6px;
	padding: 6px;
	margin-left: 10px;
	box-shadow: 0 0px 2px rgba(0,0,0,0.2);
	z-index: 1000000;
}

div.file_userdata_hint:before
{
	content: '';
	position: absolute;
	display: inline-block;
	border-top: 7px solid transparent;
	border-bottom: 7px solid transparent;
	border-right: 7px solid #eee;
	border-right-color: red;
	left: -8px;
	top: 6px;
}

div.file_userdata_hint:after
{
	content: '';
	position: absolute;
	display: inline-block;
	border-top: 6px solid transparent;
	border-bottom: 6px solid transparent;
	border-right: 6px solid #eee;
	left: -6px;
	top: 7px;
}

div.file_userdata_hint_none
{
	display: none;
}

div.file_userdata_hint_inline
{
	position: absolute;
	display: table;
	background: #eee;
	border: none;
	padding: 6px;
	margin: auto 0;
	box-shadow: none;
	z-index: 1000000;
}

div.file_userdata_hint_right
{
	position: absolute;
	background: #eee;
	border: 1px solid red;
	border-radius: 6px;
	padding: 6px;
	margin-left: 10px;
	box-shadow: 0 0px 2px rgba(0,0,0,0.2);
	z-index: 1000000;
}

div.file_userdata_hint_right:before
{
	content: '';
	position: absolute;
	display: inline-block;
	border-top: 7px solid transparent;
	border-bottom: 7px solid transparent;
	border-right: 7px solid #eee;
	border-right-color: red;
	left: -8px;
	top: 6px;
}

div.file_userdata_hint_right:after
{
	content: '';
	position: absolute;
	display: inline-block;
	border-top: 6px solid transparent;
	border-bottom: 6px solid transparent;
	border-right: 6px solid #eee;
	left: -6px;
	top: 7px;
}

div.file_userdata_hint_left
{
	position: absolute;
	background: #eee;
	border: 1px solid red;
	border-radius: 6px;
	padding: 6px;
	margin-right: 10px;
	box-shadow: 0 0px 2px rgba(0,0,0,0.2);
	z-index: 1000000;
}

div.file_userdata_hint_left:before
{
	content: '';
	position: absolute;
	display: inline-block;
	border-top: 7px solid transparent;
	border-bottom: 7px solid transparent;
	border-left: 7px solid #eee;
	border-left-color: red;
	right: -8px;
	top: 6px;
}

div.file_userdata_hint_left:after
{
	content: '';
	position: absolute;
	display: inline-block;
	border-top: 6px solid transparent;
	border-bottom: 6px solid transparent;
	border-left: 6px solid #eee;
	right: -6px;
	top: 7px;
}

div.file_userdata_hint_top
{
	position: absolute;
	background: #eee;
	border: 1px solid red;
	border-radius: 6px;
	padding: 6px;
	margin-bottom: 10px;
	box-shadow: 0 0px 2px rgba(0,0,0,0.2);
	z-index: 1000000;
}

div.file_userdata_hint_top:before
{
	content: '';
	position: absolute;
	display: inline-block;
	border-left: 7px solid transparent;
	border-right: 7px solid transparent;
	border-top: 7px solid #eee;
	border-top-color: red;
	bottom: -8px;
	left: 6px;
}

div.file_userdata_hint_top:after
{
	content: '';
	position: absolute;
	display: inline-block;
	border-left: 6px solid transparent;
	border-right: 6px solid transparent;
	border-top: 6px solid #eee;
	bottom: -6px;
	left: 7px;
}

div.file_userdata_hint_bottom
{
	position: absolute;
	background: #eee;
	border: 1px solid red;
	border-radius: 6px;
	padding: 6px;
	margin-top: 10px;
	box-shadow: 0 0px 2px rgba(0,0,0,0.2);
	z-index: 1000000;
}

div.file_userdata_hint_bottom:before
{
	content: '';
	position: absolute;
	display: inline-block;
	border-left: 7px solid transparent;
	border-right: 7px solid transparent;
	border-bottom: 7px solid #eee;
	border-bottom-color: red;
	top: -8px;
	left: 6px;
}

div.file_userdata_hint_bottom:after
{
	content: '';
	position: absolute;
	display: inline-block;
	border-left: 6px solid transparent;
	border-right: 6px solid transparent;
	border-bottom: 6px solid #eee;
	top: -6px;
	left: 7px;
}
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].userdata.init = function() {
/***
 *  The following userdata methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method initField custom template actions to initialize a userdata field
 *  @method attachHandlers make the userdata field execute a specific handler
 *          when its value changes
 *  @method getValue get the value of a userdata field
 *  @method setValue set the value of a userdata field
 *  @method enable enable a userdata field
 *  @method disable disable a userdata field
 *  @method prompt display a hint message for a userdata field
 */
/**
 *  custom template actions to initialize a userdata field
 *  
 *  This function initializes the userdata field defined by the props parameter.
 *  Custom initialization, depending on the field type take place here.
 *  
 *  @param props object the userdata field properties, they are the same as the
 *         $props variable mentioned above
 *  
 *  @return void
 */
this.initField = function(props) {
	var field = document.getElementById("userdata_$ID_field_" + props.key);
	if (props.type == "checkbox") {
		var l = document.getElementById("userdata_$ID_checklabel_" + props.key);
		var p = field.parentNode;
		if (props.format == "top") {
			p.insertBefore(l, field);
			p.insertBefore(document.createElement("BR"), field);
		}
		else if (props.format == "bottom") {
			p.insertBefore(document.createElement("BR"), l);
		}
		else if (props.format == "left") {
			p.insertBefore(l, field);
		}
		field.style.display = "inline-block";
		l.style.display = "inline-block";
	}
	else if (props.type == "radiobutton") {
		var items = props.data.split(",");
		var l = document.getElementById("userdata_$ID_radiolabel_" + props.key);
		var p = field.parentNode;
		var pos = (props.format.indexOf("top") > -1 ? "top" : (props.format.indexOf("bottom") > -1 ? "bottom" : (props.format.indexOf("left") > -1 ? "left" : "right")));
		var or = (props.format.indexOf("vertical") > -1 ? "vertical" : "horizontal");
		for (var i = 0; i < items.length; i++) {
			var f2 = field;
			var l2 = l;
			if (i > 0) {
				var f2 = field.cloneNode(true);
				f2.id += "_item_" + i;
				l2 = l.cloneNode(true);
				l2.id += "_item_" + i;
				l2.setAttribute("for", f2.id);
			}
			f2.value = items[i];
			l2.innerHTML = items[i];
			var w = document.createElement("DIV");
			w.className = "file_userdata_radio_wrapper";
			if (pos == "top") {
				w.appendChild(l2);
				w.appendChild(document.createElement("BR"));
				w.appendChild(f2);
			}
			else if (pos == "right") {
				w.appendChild(f2);
				w.appendChild(l2);
			}
			else if (pos == "bottom") {
				w.appendChild(f2);
				w.appendChild(document.createElement("BR"));
				w.appendChild(l2);
			}
			else if (pos == "left") {
				w.appendChild(l2);
				w.appendChild(f2);
			}
			f2.style.display = "inline-block";
			l2.style.display = "inline-block";
			f2.checked = (props.default == f2.value);
			if (i > 0 && or == "vertical") p.appendChild(document.createElement("BR"));
			p.appendChild(w); 
		}
	}
	else if (props.type == "date") {
		jQuery(function() {
			format = props.format.trim();
			if (format.substr(0, 1) == "(" && format.substr(format.length - 1, 1) == ")")
				format = format.substr(1, format.length - 2);
			else format = "";
			if (format == "") format = "yy-mm-dd";
			def = props.default.trim();
			if (def.substr(0, 1) == "(" && def.substr(def.length - 1, 1) == ")")
				def = def.substr(1, def.length - 2);
			else def = "";
			jQuery(field).datepicker({dateFormat: format, showButtonPanel: true}).datepicker("setDate", def);
		});
	}
	else if (props.type == "time") {
		jQuery(function() {
			format = props.format.trim();
			if (format.substr(0, 1) == "(" && format.substr(format.length - 1, 1) == ")")
				format = format.substr(1, format.length - 2);
			else format = "";
			if (format == "") format = "HH:mm";
			def = props.default.trim();
			if (def.substr(0, 1) == "(" && def.substr(def.length - 1, 1) == ")")
				def = def.substr(1, def.length - 2);
			else def = "";
			jQuery(field).timepicker({timeFormat: format}).datepicker("setTime", def);
		});
	}
	else if (props.type == "datetime") {
		jQuery(function() {
			var dateformat = "yy-mm-dd";
			var timeformat = "HH:mm";
			var re = /(date|time)\((.*?)\)/g;
			var f;
			while ((f = re.exec(props.format)) !== null) {
				if (f[1] == "date") dateformat = f[2];
				else if (f[1] == "time") timeformat = f[2];
			}
			def = props.default.trim();
			if (def.substr(0, 1) == "(" && def.substr(def.length - 1, 1) == ")")
				def = def.substr(1, def.length - 2);
			else def = "";
			jQuery(field).datetimepicker({dateFormat: dateformat, timeFormat: timeformat}).datetimepicker("setDate", def);
		});
	}
	else if (props.type == "list") {
		var items = props.data.split(",");
		var o = document.getElementById("userdata_$ID_listitem_" + props.key);
		for (var i = 0; i < items.length; i++) {
			var o2 = o;
			if (i > 0)
				o2 = o.cloneNode(true);
			o2.value = items[i];
			o2.innerHTML = items[i];
			o2.selected = (o2.value == props.default);
			o2.style.display = "block";
			if (i > 0) field.appendChild(o2);
		}
	}
	else if (props.type == "dropdown") {
		var items = props.data.split(",");
		var o = document.getElementById("userdata_$ID_listitem_" + props.key);
		for (var i = 0; i < items.length; i++) {
			var o2 = o.cloneNode(true);
			o2.value = items[i];
			o2.innerHTML = items[i];
			o2.selected = (o2.value == props.default);
			o2.style.display = "block";
			field.appendChild(o2);
		}
	}
	else if (props.type == "honeypot") {
		var msg_cont = document.getElementById('userdata_$ID_' + props.key);
		msg_cont.style.display = "none";
	}
}

/**
 *  make the userdata field execute a specific handler when its value changes
 *  
 *  This function runs handlerfunc function whenever the userdata field's value
 *  changes, in order to notify the plugin about this change.
 *  
 *  @param props object the userdata field properties, they are the same as the
 *         $props variable mentioned above
 *  @param handlerfunc function the function that must run when the field's
 *         value changes; this is an event function, it takes one parameter, an
 *         event object
 *  
 *  @return void
 */
this.attachHandlers = function(props, handlerfunc) {
	var field = document.getElementById("userdata_$ID_field_" + props.key);
	switch (props.type) {
		case "text":
		case "multitext":
		case "number":
		case "email":
		case "confirmemail":
		case "password":
		case "confirmpassword":
		case "honeypot": wfu_attach_element_handlers(field, handlerfunc); break;
		case "checkbox":
		case "date":
		case "time":
		case "datetime":
		case "list":
		case "dropdown": wfu_addEventHandler(field, 'change', handlerfunc); break;
		case "radiobutton":
			var items = document.getElementsByName(field.name);
				for (var i = 0; i < items.length; i++) wfu_addEventHandler(items[i], 'change', handlerfunc);
	}
}

/**
 *  get the value of a userdata field
 *  
 *  @param props object the userdata field properties, they are the same as the
 *         $props variable mentioned above
 *  
 *  @return mixed the field value
 */
this.getValue = function(props) {
	var field = document.getElementById("userdata_$ID_field_" + props.key);
	var value = "";
	switch (props.type) {
		case "text":
		case "multitext":
		case "number":
		case "email":
		case "confirmemail":
		case "password":
		case "confirmpassword":
		case "date":
		case "time":
		case "datetime":
		case "list":
		case "dropdown":
		case "honeypot": value = field.value; break;
		case "checkbox": value = field.checked; break;
		case "radiobutton":
			var items = document.getElementsByName(field.name);
			for (var i = 0; i < items.length; i++)
				if (items[i].checked) {
					value = items[i].value;
					break;
				}
	}
	return value;
}

/**
 *  set the value of a userdata field
 *  
 *  @param props object the userdata field properties, they are the same as the
 *         $props variable mentioned above
 *  @param value mixed the new field value
 *  
 *  @return void
 */
this.setValue = function(props, value) {
	var field = document.getElementById("userdata_$ID_field_" + props.key);
	switch (props.type) {
		case "text":
		case "multitext":
		case "number":
		case "email":
		case "confirmemail":
		case "password":
		case "confirmpassword":
		case "honeypot": field.value = value; break;
		case "checkbox": field.checked = value; break;
		case "radiobutton":
			var items = document.getElementsByName(field.name);
			for (var i = 0; i < items.length; i++)
				items[i].checked = (items[i].value == value);
			break;
		case "date": jQuery(field).datepicker("setDate", value); break;
		case "time":
			if (value != "") jQuery(field).timepicker("setTime", value);
			else jQuery(field).timepicker("setDate", "");
			break;
		case "datetime": jQuery(field).datetimepicker("setDate", value); break;
		case "list":
		case "dropdown":
			for (var i = 0; i < field.options.length; i++)
				field.options[i].selected = (field.options[i].value == value);
	}
}

/**
 *  enable a userdata field
 *  
 *  @param props object the userdata field properties, they are the same as the
 *         $props variable mentioned above
 *  
 *  @return void
 */
this.enable = function(props) {
	var field = document.getElementById("userdata_$ID_field_" + props.key);
	switch (props.type) {
		case "text":
		case "multitext":
		case "number":
		case "email":
		case "confirmemail":
		case "password":
		case "confirmpassword":
		case "checkbox":
		case "date":
		case "time":
		case "datetime":
		case "list":
		case "dropdown": field.disabled = false; break;
		case "radiobutton":
			var items = document.getElementsByName(field.name);
			for (var i = 0; i < items.length; i++) items[i].disabled = false;
			break;
		case "honeypot": break;
	}
}

/**
 *  disable a userdata field
 *  
 *  @param props object the userdata field properties, they are the same as the
 *         $props variable mentioned above
 *  
 *  @return void
 */
this.disable = function(props) {
	var field = document.getElementById("userdata_$ID_field_" + props.key);
	switch (props.type) {
		case "text":
		case "multitext":
		case "number":
		case "email":
		case "confirmemail":
		case "password":
		case "confirmpassword":
		case "checkbox":
		case "date":
		case "time":
		case "datetime":
		case "list":
		case "dropdown": field.disabled = true; break;
		case "radiobutton":
			var items = document.getElementsByName(field.name);
			for (var i = 0; i < items.length; i++) items[i].disabled = true;
			break;
		case "honeypot": break;
	}
}

/**
 *  display a hint message for a userdata field
 *  
 *  This function displays a hint message next to the userdata field after
 *  validation in order to notify the user that the field is empty or the value
 *  is invalid (e.g the email entered in an email field in not a valid email
 *  address).
 *  
 *  @param props object the userdata field properties, they are the same as the
 *         $props variable mentioned above
 *  @param message string the message test to be displayed
 *  
 *  @return void
 */
this.prompt = function(props, message) {
	var wrapper = document.getElementById('userdata_$ID_fieldwrapper_' + props.key);
	var msg_cont = document.getElementById('userdata_$ID_' + props.key);
	var msg_hint = document.getElementById('userdata_$ID_hint_' + props.key);
	if (props.hintposition == "none") msg_hint.className = "file_userdata_hint_none";
	else {
		wrapper.className = "file_userdata_fieldwrapper_required_empty";
		var cont_rect = msg_cont.getBoundingClientRect();
		var msg_rect = wrapper.getBoundingClientRect();
		if (props.hintposition == "inline") {
			msg_hint.className = "file_userdata_hint_inline";
			msg_hint.style.left = parseInt(msg_rect.left - cont_rect.left) + 'px';
			msg_hint.style.top = parseInt(msg_rect.top - cont_rect.top) + 'px';
			msg_hint.style.right = parseInt(cont_rect.right - msg_rect.right) + 'px';
			msg_hint.style.bottom = parseInt(cont_rect.bottom - msg_rect.bottom) + 'px';
		}
		else if (props.hintposition == "top") {
			msg_hint.className = "file_userdata_hint_top";
			msg_hint.style.left = parseInt(msg_rect.left - cont_rect.left) + 'px';
			msg_hint.style.bottom = parseInt(cont_rect.bottom - msg_rect.top) + 'px';
		}
		else if (props.hintposition == "right") {
			msg_hint.className = "file_userdata_hint_right";
			msg_hint.style.left = parseInt(msg_rect.right - cont_rect.left) + 'px';
			msg_hint.style.top = parseInt(msg_rect.top - cont_rect.top) + 'px';
		}
		else if (props.hintposition == "bottom") {
			msg_hint.className = "file_userdata_hint_bottom";
			msg_hint.style.left = parseInt(msg_rect.left - cont_rect.left) + 'px';
			msg_hint.style.top = parseInt(msg_rect.bottom - cont_rect.top) + 'px';
		}
		else if (props.hintposition == "left") {
			msg_hint.className = "file_userdata_hint_left";
			msg_hint.style.right = parseInt(cont_rect.right - msg_rect.left) + 'px';
			msg_hint.style.top = parseInt(msg_rect.top - cont_rect.top) + 'px';
		}
		msg_hint.innerHTML = message;
		msg_hint.style.display = "block";
	}
}

//************* Internal Function Definitions **********************************

/**
 *  custom actions when the field receives focus
 *  
 *  This function performs custom actions when the field receives focus (e.g.
 *  hides the hint message).
 *  
 *  @return void
 */
this._focused = function(obj) {
	var wrapper = document.getElementById(obj.id.replace('_field_', '_fieldwrapper_'));
	if (wrapper.className == 'file_userdata_fieldwrapper_required_empty') {
		wrapper.className = 'file_userdata_fieldwrapper_required';
		var msg_hint = document.getElementById(obj.id.replace('_field_', '_hint_'));
		msg_hint.style.display = "none";
	}
}
/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML userdata templates 
****************************************************************************/ ?>
<?php foreach ( $props as $p ): ?>
<userdata_<?php echo $p["key"]; ?>_template>
	<div id="userdata_$ID_<?php echo $p["key"]; ?>" class="file_userdata_container" style="<?php echo esc_html($styles1); ?>">
	<?php if ( $p["labelposition"] != "none" ): ?><style>#userdata_$ID_label_<?php echo $p["key"]; ?>:after { content: '<?php echo ( $p["required"] ? esc_html($params["requiredlabel"]) : "" ); ?>'; }</style><?php endif ?>
	<?php if ( $p["labelposition"] == "top" || $p["labelposition"] == "left" ): ?>
		<label id="userdata_$ID_label_<?php echo $p["key"]; ?>" for="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_label" style="<?php echo esc_html($styles2); ?>"><?php echo esc_html($p["label"]); ?></label>
	<?php endif ?>
	<?php if ( $p["labelposition"] == "top" ): ?><br /><?php endif ?>
		<div id="userdata_$ID_fieldwrapper_<?php echo $p["key"]; ?>" class="file_userdata_fieldwrapper<?php echo ( $p["required"] ? '_required' : '' ); ?>" style="<?php echo esc_html($styles3); ?>">
			<div class="wfu_fieldwrapper_overlay" onclick="document.getElementById('userdata_$ID_field_<?php echo $p["key"]; ?>').focus();"></div>
<!-- **** the following lines contain the HTML code of each field type ***** -->		
	<?php if ( !$testmode ): ?>
		<?php if ( $p["type"] == "text" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="<?php echo esc_html($p["default"]); ?>" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" onfocus="GlobalData.WFU[$ID].userdata._focused(this);"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "multitext" ): ?>
				<textarea id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="<?php echo esc_html($p["default"]); ?>" form="dummy_$ID" onfocus="GlobalData.WFU[$ID].userdata._focused(this);"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?>><?php echo esc_html($p["default"]); ?></textarea>
		<?php elseif ( $p["type"] == "number" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="<?php echo esc_html($p["default"]); ?>" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" onfocus="GlobalData.WFU[$ID].userdata._focused(this);"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "email" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message file_userdata_$ID_emailgroup_<?php echo esc_html($p["group"]); ?>" value="<?php echo esc_html($p["default"]); ?>" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" onfocus="GlobalData.WFU[$ID].userdata._focused(this);"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "confirmemail" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="<?php echo esc_html($p["default"]); ?>" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" onfocus="GlobalData.WFU[$ID].userdata._focused(this);"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "password" ): ?>
				<input type="password" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message file_userdata_$ID_passwordgroup_<?php echo esc_html($p["group"]); ?>" value="<?php echo esc_html($p["default"]); ?>" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" onfocus="GlobalData.WFU[$ID].userdata._focused(this);"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "confirmpassword" ): ?>
				<input type="password" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="<?php echo esc_html($p["default"]); ?>" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" onfocus="GlobalData.WFU[$ID].userdata._focused(this);"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "checkbox" ): ?>
				<input type="checkbox" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_checkbox" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" style="display:none;" onfocus="GlobalData.WFU[$ID].userdata._focused(this);" />
				<label id="userdata_$ID_checklabel_<?php echo $p["key"]; ?>" class="file_userdata_checkbox_description" for="userdata_$ID_field_<?php echo $p["key"]; ?>" style="display:none;"><?php echo esc_html($p["data"]); ?></label>
		<?php elseif ( $p["type"] == "radiobutton" ): ?>
				<input type="radio" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_radiobutton" name="userdata_$ID_radiogroup_<?php echo esc_html($p["group"]); ?>" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" style="display:none;" onfocus="GlobalData.WFU[$ID].userdata._focused(document.getElementById('userdata_$ID_field_<?php echo $p["key"]; ?>'));" />
				<label id="userdata_$ID_radiolabel_<?php echo $p["key"]; ?>" class="file_userdata_radiobutton_label" for="userdata_$ID_field_<?php echo $p["key"]; ?>" style="display:none;"><?php echo esc_html($p["data"]); ?></label>
		<?php elseif ( $p["type"] == "date" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" readonly="readonly" onfocus="GlobalData.WFU[$ID].userdata._focused(this);"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "time" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" readonly="readonly" onfocus="GlobalData.WFU[$ID].userdata._focused(this);"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "datetime" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" readonly="readonly" onfocus="GlobalData.WFU[$ID].userdata._focused(this);"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "list" ): ?>
				<select id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_listbox" multiple="multiple" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" value="<?php echo esc_html($p["default"]); ?>" onfocus="GlobalData.WFU[$ID].userdata._focused(this);">
					<option id="userdata_$ID_listitem_<?php echo $p["key"]; ?>" style="display:none;"></option>
				</select>
		<?php elseif ( $p["type"] == "dropdown" ): ?>
				<select id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_dropdown" autocomplete="<?php echo ( $p["donotautocomplete"] ? 'off' : 'on' ); ?>" form="dummy_$ID" value="<?php echo esc_html($p["default"]); ?>" onfocus="GlobalData.WFU[$ID].userdata._focused(this);">
					<option id="userdata_$ID_listitem_<?php echo $p["key"]; ?>" style="display:none;"></option>
				</select>
		<?php elseif ( $p["type"] == "honeypot" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="" autocomplete="off" tabindex="-1" name="<?php echo esc_html($p["label"]); ?>" />
		<?php endif ?>
				<input id="userdata_$ID_props_<?php echo $p["key"]; ?>" type="hidden" value="p:<?php echo esc_html($p["hintposition"]); ?>" />
	<?php else: ?>
		<?php if ( $p["type"] == "text" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="Test value" autocomplete="off" form="dummy_$ID" readonly="readonly"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "multitext" ): ?>
				<textarea id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="Test value" form="dummy_$ID" readonly="readonly"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?>>Test message</textarea>
		<?php elseif ( $p["type"] == "number" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="100" autocomplete="off" form="dummy_$ID" readonly="readonly"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "email" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message file_userdata_emailgroup_<?php echo esc_html($p["group"]); ?>" value="user@domain.com" autocomplete="off" form="dummy_$ID" readonly="readonly"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "confirmemail" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="Test message" autocomplete="off" form="dummy_$ID" readonly="readonly"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "password" ): ?>
				<input type="password" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message file_userdata_passwordgroup_<?php echo esc_html($p["group"]); ?>" value="Test" autocomplete="off" form="dummy_$ID" readonly="readonly"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "confirmpassword" ): ?>
				<input type="password" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="Test" autocomplete="off" form="dummy_$ID" readonly="readonly"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "checkbox" ): ?>
				<input type="checkbox" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_checkbox" autocomplete="off" form="dummy_$ID" readonly="readonly" />
				<label id="userdata_$ID_checklabel_<?php echo $p["key"]; ?>" for="userdata_$ID_field_<?php echo $p["key"]; ?>" style="display:none;">[list]</label>
		<?php elseif ( $p["type"] == "radiobutton" ): ?>
				<input type="radio" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_radiobutton" name="userdata_$ID_radiogroup_<?php echo esc_html($p["group"]); ?>" autocomplete="off" form="dummy_$ID" readonly="readonly" />
				<label id="userdata_$ID_radiolabel_<?php echo $p["key"]; ?>" for="userdata_$ID_field_<?php echo $p["key"]; ?>" style="display:none;">[list]</label>
		<?php elseif ( $p["type"] == "date" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" autocomplete="off" form="dummy_$ID" readonly="readonly"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "time" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" autocomplete="off" form="dummy_$ID" readonly="readonly"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "datetime" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" autocomplete="off" form="dummy_$ID" readonly="readonly"<?php echo ( $p["labelposition"] == "placeholder" ? ' placeholder="'.esc_html($p["label"]).'"' : '' ); ?> />
		<?php elseif ( $p["type"] == "list" ): ?>
				<select id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_listbox" multiple="multiple" autocomplete="off" form="dummy_$ID" readonly="readonly">
					<option>Test value</option>
				</select>
		<?php elseif ( $p["type"] == "dropdown" ): ?>
				<select id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_dropdown" autocomplete="off" form="dummy_$ID" readonly="readonly">
					<option>Test value</option>
				</select>
		<?php elseif ( $p["type"] == "honeypot" ): ?>
				<input type="text" id="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_message" value="" autocomplete="off" tabindex="-1" name="<?php echo esc_html($p["label"]); ?>" />
		<?php endif ?>
	<?php endif ?>
<!-- ***************** end of HTML code of each field type ***************** -->		
		</div>
	<?php if ( $p["labelposition"] == "bottom" ): ?><br /><?php endif ?>
	<?php if ( $p["labelposition"] == "bottom" || $p["labelposition"] == "right" ): ?>
		<label id="userdata_$ID_label_<?php echo $p["key"]; ?>" for="userdata_$ID_field_<?php echo $p["key"]; ?>" class="file_userdata_label" style="<?php echo esc_html($styles2); ?>"><?php echo esc_html($p["label"]); ?></label>
	<?php endif ?>
	<!-- the hint element -->
		<div id="userdata_$ID_hint_<?php echo $p["key"]; ?>" class="file_userdata_hint" style="display:none;" onclick="document.getElementById('userdata_$ID_field_<?php echo $p["key"]; ?>').focus();">empty</div>
	</div>
</userdata_<?php echo $p["key"]; ?>_template>
<?php endforeach ?>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

function wfu_consent_template($data) {?>
<?php /*************************************************************************
          the following lines contain initialization of PHP variables
*******************************************************************************/
/* do not change this line */extract($data);
/*
 *  The following variables are available for use:
 *  
 *  @var $ID int the upload ID
 *  @var $width string assigned width of consent element
 *  @var $height string assigned height of consent element
 *  @var $responsive bool true if responsive mode is enabled
 *  @var $testmode bool true if the plugin is in test mode
 *  @var $index int the index of occurrence of the element inside the plugin,
 *       in case that it appears more than once
 *  @var $format string format of the consent question, it can be 'checkbox' or
 *       'radio' or 'prompt'
 *  @var $preselected string determines if a default answer will be selected, it
 *       can be 'none', 'yes' or 'no'
 *  @var $question string the consent question
 *  @var $params array all plugin's attributes defined through the shortcode
 *  
 *  It is noted that $ID can also be used inside CSS, Javascript and HTML code.
 */
	$styles = "";
	//for responsive plugin adjust container's widths if a % width has been defined
	if ( $responsive && strlen($width) > 1 && substr($width, -1, 1) == "%" ) $styles = 'width: 100%;';
	elseif ( $width != "" ) $styles = "width: $width; ";
	if ( $height != "" ) $styles .= "height: $height; ";
/*******************************************************************************
              the following lines contain CSS styling rules
*********************************************************************/ ?><style>
</style><?php /*****************************************************************
               the following lines contain Javascript code 
*********************************************/ ?><script type="text/javascript">
/* do not change this line */GlobalData.WFU[$ID].consent.init = function() {
/***
 *  The following consent methods can be defined by the template, together
 *  with other initialization actions:
 *
 *  @method consentCompleted checks if the user has completed the consent
 *          question, if it is necessary
 */
/**
 *  attaches necessary actions of the plugin
 *  
 *  This function attaches necessary actions of the plugin that must be run when
 *  the checkbox or radio buttons of the consent question are clicked.
 *  
 *  @param completeaction object this is a function that must be called when the
 *         user completes the consent question
 *  
 *  @return void
 */
this.attachActions = function(completeaction) {
	var box = document.querySelector('#consent_$ID .file_consent_box');
	if (box) box.onchange = function() { completeaction((box.checked ? "yes" : "no")); };
	else {
		var radioyes = document.querySelector('#consent_$ID .file_consent_radio_yes');
		var radiono = document.querySelector('#consent_$ID .file_consent_radio_no');
		if (radioyes && radiono) {
			radioyes.onchange = function() { completeaction((radioyes.checked ? "yes" : (radiono.checked ? "no" : ""))); };
			radiono.onchange = function() { completeaction((radioyes.checked ? "yes" : (radiono.checked ? "no" : ""))); };
		}
	}
}

/**
 *  checks if consent is completed
 *  
 *  Checks if the user has completed the consent question. For checkbox format
 *  it always returns true. For radio format, it will return false of the user
 *  has not selected either Yes or No.
 *  
 *  @return boolean true if consent is completed, false otherwise
 */
this.consentCompleted = function() {
	var box = document.querySelector('#consent_$ID .file_consent_box');
	if (box) return true;
	else {
		var radioyes = document.querySelector('#consent_$ID .file_consent_radio_yes');
		var radiono = document.querySelector('#consent_$ID .file_consent_radio_no');
		if (radioyes && radiono) return (radioyes.checked || radiono.checked);
	}
	return true;
}

/**
 *  updates consent elements status
 *  
 *  Updates the status of the consent elements.
 *  
 *  @param action string the update action. Can be 'init', 'lock', 'unlock' and
 *         'clear'
 *  
 *  @return void
 */
this.update = function(action) {
	var box = document.querySelector('#consent_$ID .file_consent_box');
	var radioyes = document.querySelector('#consent_$ID .file_consent_radio_yes');
	var radiono = document.querySelector('#consent_$ID .file_consent_radio_no');

	if (action == "init") {
		var presel = document.querySelector('#consent_$ID .file_consent_preselected');
		if (box) {
			box.checked = false;
			if (presel) box.checked = (presel.value == "1");
			box.onchange();
		}
		else if (radioyes && radiono) {
			radioyes.checked = false;
			radiono.checked = false;
			if (presel) {
				radioyes.checked = (presel.value == "1");
				radiono.checked = (presel.value == "0");
			}
			radioyes.onchange();
		}
	}
	else if (action == "lock") {
		if (box) box.disabled = true;
		else if (radioyes && radiono) {
			radioyes.disabled = true;
			radiono.disabled = true;
		}
	}
	else if (action == "unlock") {
		if (box) box.disabled = false;
		else if (radioyes && radiono) {
			radioyes.disabled = false;
			radiono.disabled = false;
		}
	}
	else if (action == "clear") {
		var container = document.getElementById('consent_$ID');
		container.style.display = "none";
	}
}

/* do not change this line */}
</script><?php /****************************************************************
               the following lines contain the HTML output 
****************************************************************************/ ?>
<div id="consent_$ID" class="file_consent_container" style="<?php echo esc_html($styles); ?>" />
<?php if ( $preselected == "yes" || $preselected == "no" ): ?>
	<input type="hidden" class="file_consent_preselected" value="<?php echo ( $preselected == "yes" ? "1" : "0" ); ?>" />
<?php endif ?>
<?php if ( !$testmode ): ?>
	<?php if ( $format == "checkbox" ): ?>
		<input type="checkbox" class="file_consent_box" />
		<span class="file_consent_question"><?php echo $question; ?></span>
	<?php else: ?>
		<span class="file_consent_question"><?php echo $question; ?></span><br />
		<input type="radio" class="file_consent_radio_yes" name="file_consent_radio_$ID" value="yes" />
		<span class="file_consent_span_yes"><?php echo WFU_CONSENTYES; ?></span>
		<input type="radio" class="file_consent_radio_no" name="file_consent_radio_$ID" value="no" />
		<span class="file_consent_span_no"><?php echo WFU_CONSENTNO; ?></span>
	<?php endif ?>
<?php else: ?>
	<?php if ( $format == "checkbox" ): ?>
		<input type="checkbox" class="file_consent_box" readonly="readonly" />
		<span class="file_consent_question"><?php echo $question; ?></span>
	<?php else: ?>
		<span class="file_consent_question"><?php echo $question; ?></span><br />
		<input type="radio" class="file_consent_radio_yes" name="file_consent_radio_$ID" value="yes" readonly="readonly" />
		<span class="file_consent_span_yes"><?php echo WFU_CONSENTYES; ?></span>
		<input type="radio" class="file_consent_radio_no" name="file_consent_radio_$ID" value="no" readonly="readonly" />
		<span class="file_consent_span_no"><?php echo WFU_CONSENTNO; ?></span>
	<?php endif ?>
<?php endif ?>
</div>
<?php /*************************************************************************
                            end of HTML output 
*****************************************************************************/ }

}