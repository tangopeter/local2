<?php

/*<wfu_before_frontpage_scripts_template>
  title: Before Execution of Frontpage Scripts Filter
  scope: frontend
  This filter runs before the plugin registers frontpage scripts and styles, in
  order to execute custom configuration.
  In addition, through this filter, the user can enable some "hidden" plugin
  settings to resolve incompatibilities and problems with other plugins or
  themes. For the moment, settings are included for correcting incompatibilities
  with JQuery UI css and NextGen Gallery plugin. In the future more settings
  will be added.
  !Note: Scope should be set to "Front-End" for this filter to work properly.
*/
if (!function_exists('wfu_before_frontpage_scripts_handler')) {
	/** Function syntax
	 *  The function takes one parameter, $changable_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the following items:
	 *    > correct_JQueryUI_incompatibility: if this item is set to "true",
	 *      then adjustments will be performed in the plugin so that it does not
	 *      cause incompatibilities with JQuery UI css.
	 *    > correct_NextGenGallery_incompatibility: if this item is set to
	 *      "true", then adjustments will be performed in the plugin so that it
	 *      does not cause incompatibilities with NextGen Gallery plugin.
	 *    > exclude_timepicker: if this item is set to "true", then timepicker
	 *      element's css and js code will not be loaded
	 *  If $changable_data contains the key 'return'value', then no plugin
	 *  scripts and styles will be loaded.
	 *  The function must return the final $changable_data. */
	function wfu_before_frontpage_scripts_handler($changable_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_before_frontpage_scripts', 'wfu_before_frontpage_scripts_handler', 10, 1);
}
/*</wfu_before_frontpage_scripts_template>*/

/*<wfu_before_admin_scripts_template>
  title: Before Execution of Admin Scripts Filter
  scope: dashboard
  This filter runs before the plugin registers and enqueues admin scripts and
  styles, in order to execute custom configuration.
  In addition, through this filter, the user can enable some "hidden" plugin
  settings to resolve incompatibilities and problems with other plugins or
  themes. For the moment, settings are included for correcting incompatibilities
  with JQuery UI css and NextGen Gallery plugin. In the future more settings
  will be added.
  !Note: Scope should be set to "Dashboard" for this filter to work properly.
*/
if (!function_exists('wfu_before_admin_scripts_handler')) {
	/** Function syntax
	 *  The function takes one parameter, $changable_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the following items:
	 *    > correct_JQueryUI_incompatibility: if this item is set to "true",
	 *      then adjustments will be performed in the plugin so that it does not
	 *      cause incompatibilities with JQuery UI css.
	 *    > correct_NextGenGallery_incompatibility: if this item is set to
	 *      "true", then adjustments will be performed in the plugin so that it
	 *      does not cause incompatibilities with NextGen Gallery plugin.
	 *    > exclude_codemirror: if this item is set to "true", then codemirror
	 *      editor's css and js code will not be loaded
	 *    > exclude_datepicker: if this item is set to "true", then datepicker
	 *      element's js code will not be loaded
	 *  If $changable_data contains the key 'return'value', then no plugin
	 *  scripts and styles will be loaded.
	 *  The function must return the final $changable_data. */
	function wfu_before_admin_scripts_handler($changable_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_before_admin_scripts', 'wfu_before_admin_scripts_handler', 10, 1);
}
/*</wfu_before_admin_scripts_template>*/

/*<wfu_before_upload_template>
  title: Before Upload Filter
  scope: everywhere
  This filter runs before the upload starts, in order to perform any preliminary
  custom server actions and allow the upload to start or reject it.  
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_before_upload_handler')) {
	/** Function syntax
	 *  The function takes two parameters, $changable_data and $additional_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the items:
	 *    > error_message: initially it is set to an empty value, if the handler
	 *      sets a non-empty value then upload will be cancelled showing this
	 *      error message
	 *    > js_script: javascript code to be executed on the client's browser
	 *      right after the filter
	 *  - $additional_data is an array with additional data to be used by the
	 *    filter (but cannot be modified) as follows:
	 *    > sid: this is the id of the plugin, as set using uploadid attribute;
	 *      it can be used to apply this filter only to a specific instance of
	 *      the plugin (if it is used in more than one pages or posts)
	 *    > unique_id: this id is unique for each individual upload attempt
	 *      and can be used to identify each separate upload
	 *    > files: holds an array with data about the files that have been
	 *      selected for upload; every item of the array is another array
	 *      with the following items:
	 *      >> filename: the filename of the file
	 *      >> filesize: the size of the file
	 *  The function must return the final $changable_data. */
	function wfu_before_upload_handler($changable_data, $additional_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_before_upload', 'wfu_before_upload_handler', 10, 2);
}
/*</wfu_before_upload_template>*/

/*<wfu_before_file_check_template>
  title: Before Execution of Uploaded File Check Filter
  scope: everywhere
  This filter runs before every individual uploaded file is sent to the server 
  and before the plugin executes file validity checks (filename, extension, size
  etc.). It can be used to perform custom file checks and reject the file if
  checks fail, or customize the upload file path (or filename) taking into
  account data from user data fields.
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_before_file_check_handler')) {
	/** Function syntax
	 *  The function takes two parameters, $changable_data and $additional_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the items:
	 *    > file_path: the full path of the uploaded file
	 *    > user_data: an array of user data values, if userdata are activated
	 *    > error_message: initially it is set to an empty value, if the handler
	 *      sets a non-empty value then upload of the file will be cancelled
	 *      showing this error message
	 *    > admin_message: initially it is set to an empty value, if the handler
	 *      sets a non-empty value then this value will be shown to
	 *      administrators if adminmessages attribute has been activated,
	 *      provided that error_message is also set. You can use it to display
	 *      more information about the error, visible only to admins.
	 *  - $additional_data is an array with additional data to be used by the
	 *    filter (but cannot be modified) as follows:
	 *    > shortcode_id: this is the id of the plugin, as set using uploadid
	 *      attribute; it can be used to apply this filter only to a specific
	 *      instance of the plugin (if it is used in more than one pages or
	 *      posts)
	 *    > unique_id: this id is unique for each individual upload attempt
	 *      and can be used to identify each separate upload
	 *    > file_unique_id: this id is unique for each individual file upload
	 *      and can be used to identify each separate upload
	 *    > file_size: the size of the uploaded file
	 *    > user_id: the id of the user that submitted the file for upload
	 *    > page_id: the id of the page from where the upload was performed
	 *      (because there may be upload plugins in more than one page)
	 *  The function must return the final $changable_data. */
	function wfu_before_file_check_handler($changable_data, $additional_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_before_file_check', 'wfu_before_file_check_handler', 10, 2); 
}
/*</wfu_before_file_check_template>*/

/*<wfu_before_data_submit_template>
  title: Before Submittal of Data Filter
  scope: everywhere
  This filter runs when user data are submitted to the server without a file
  (nofileupload case) before they are saved to the database and before any other
  actions. It can be used to perform custom checks on the submitted data and
  optionally reject them returning a custom error.
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_before_data_submit_handler')) {
	/** Function syntax
	 *  The function takes two parameters, $changable_data and $additional_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the items:
	 *    > user_data: an array of user data values
	 *    > error_message: initially it is set to an empty value, if the handler
	 *      sets a non-empty value then submittal of data will be cancelled
	 *      showing this error message
	 *    > admin_message: initially it is set to an empty value, if the handler
	 *      sets a non-empty value then this value will be shown to
	 *      administrators if adminmessages attribute has been activated,
	 *      provided that error_message is also set. You can use it to display
	 *      more information about the error, visible only to admins.
	 *  - $additional_data is an array with additional data to be used by the
	 *    filter (but cannot be modified) as follows:
	 *    > shortcode_id: this is the id of the plugin, as set using uploadid
	 *      attribute; it can be used to apply this filter only to a specific
	 *      instance of the plugin (if it is used in more than one pages or
	 *      posts)
	 *    > unique_id: this id is unique for each individual submittal attempt
	 *      and can be used to identify it
	 *    > user_id: the id of the user that submitted the data
	 *    > page_id: the id of the page from where the submittal was performed
	 *      (because there may be plugin instances in more than one pages)
	 *  The function must return the final $changable_data. */
	function wfu_before_data_submit_handler($changable_data, $additional_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_before_data_submit', 'wfu_before_data_submit_handler', 10, 2); 
}
/*</wfu_before_data_submit_template>*/

/*<wfu_before_file_upload_template>
  title: Before File Upload Filter
  scope: everywhere
  This filter runs right before every individual uploaded file starts to be
  uploaded in order to make modifications of its filename.
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_before_file_upload_handler')) {
	/** Function syntax
	 *  The function takes two parameters, $file_path and $file_unique_id.
	 *  - $file_path is the filename of the uploaded file (after all internal
	 *    checks have been applied) and can be modified by the filter.
	 *  - $file_unique_id is is unique for each individual file upload and can
	 *    be used to identify each separate upload.
	 *  The function must return the final $file_path.
	 *  If additional data are required (such as user id or userdata) you can
	 *  get them by implementing the previous filter wfu_before_file_check and
	 *  link both filters by $file_unique_id parameter. Please note that no
	 *  filename validity checks will be performed after the filter. The filter
	 *  must ensure that filename is valid. */
	function wfu_before_file_upload_handler($file_path, $file_unique_id) {
		// Add code here...
		return $file_path;
	}
	add_filter('wfu_before_file_upload', 'wfu_before_file_upload_handler', 10, 2);
}
/*</wfu_before_file_upload_template>*/

/*<wfu_after_file_loaded_template>
  title: After File Has Completely Loaded on Server Filter
  scope: everywhere
  This filter runs after every individual file has completely loaded on server.
  It provides the opportunity to perform custom checks on its contents and
  reject or accept it.  
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_after_file_loaded_handler')) {
	/** Function syntax
	 *  The function takes two parameters, $changable_data and $additional_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the items:
	 *    > error_message: initially it is set to an empty value, if the handler
	 *      sets a non-empty value then upload of the file will be cancelled
	 *      showing this error message
	 *    > admin_message: initially it is set to an empty value, if the handler
	 *      sets a non-empty value then this value will be shown to
	 *      administrators if adminmessages attribute has been activated,
	 *      provided that error_message is also set. You can use it to display
	 *      more information about the error, visible only to admins.
	 *  - $additional_data is an array with additional data to be used by the
	 *    filter (but cannot be modified) as follows:
	 *    > file_unique_id: this id is unique for each individual file upload
	 *      and can be used to identify each separate upload
	 *    > file_path: the full path of the uploaded file
	 *    > shortcode_id: this is the id of the plugin, as set using uploadid
	 *      attribute; it can be used to apply this filter only to a specific
	 *      instance of the plugin (if it is used in more than one pages or
	 *      posts)
	 *  The function must return the final $changable_data. */
	function wfu_after_file_loaded_handler($changable_data, $additional_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_after_file_loaded', 'wfu_after_file_loaded_handler', 10, 2); 
}
/*</wfu_after_file_loaded_template>*/

/*<wfu_before_email_notification_template>
  title: Before Sending Email Notification Filter
  scope: everywhere
  This filter runs after upload has finished and right before the notification
  email is sent (if email notifications are enabled). It allows to customize the
  email contents, taking also into account any user data.
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_before_email_notification_handler')) {
	/** Function syntax
	 *  The function takes two parameters, $changable_data and $additional_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the items:
	 *    > recipients: the list of recipients (before dynamic variables are
	 *      applied)
	 *    > subject: the email subject (before dynamic variables are applied)
	 *    > message: the email body (before dynamic variables are applied)
	 *    > headers: the email headers, if exist (before dynamic variables are
	 *      applied)
	 *    > user_data: an array of user data values, if userdata are activated
	 *    > filename: a comma separated list of uploaded file names (only the
	 *      file names)
	 *    > filepath: a comma separated list of uploaded file paths (absolute
	 *      full file paths)
	 *    > error_message: initially it is set to an empty value, if the handler
	 *      sets a non-empty value then email sending will be cancelled showing
	 *      this error message (message will be shown only to administrators if
	 *      adminmessages attribute has been activated)
	 *  - $additional_data is an array with additional data to be used by the
	 *    filter (but cannot be modified) as follows:
	 *    > shortcode_id: this is the id of the plugin, as set using uploadid
	 *      attribute; it can be used to apply this filter only to a specific
	 *      instance of the plugin (if it is used in more than one pages or
	 *      posts)
	 *  The function must return the final $changable_data. */
	function wfu_before_email_notification_handler($changable_data, $additional_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_before_email_notification', 'wfu_before_email_notification_handler', 10, 2); 
}
/*</wfu_before_email_notification_template>*/

/*<wfu_after_file_upload_template>
  title: After File Upload Filter
  scope: everywhere
  This filter is executed after the upload process for each individual file has
  finished, in order to allow additional tasks to be executed and define custom
  javascript code to run in client’s browser. 
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_after_file_upload_handler')) {
	/** Function syntax
	 *  The function takes two parameters, $changable_data and $additional_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the items:
	 *    > ret_value: not used for the moment, it exists for future additions
	 *    > js_script: javascript code to be executed on the client's browser
	 *      after each file is uploaded
	 *  - $additional_data is an array with additional data to be used by the
	 *    filter (but cannot be modified) as follows:
	 *    > shortcode_id: this is the id of the plugin, as set using uploadid
	 *      attribute; it can be used to apply this filter only to a specific
	 *      instance of the plugin (if it is used in more than one pages or
	 *      posts)
	 *    > unique_id: this id is unique for each individual upload attempt
	 *      and can be used to identify each separate upload
	 *    > file_unique_id: this id is unique for each individual file upload
	 *      and can be used to identify each separate upload
	 *    > upload_result: it is the result of the upload process, taking the
	 *      following values:
	 *        success: the upload was successful
	 *        warning: the upload was successful but with warning messages
	 *        error: the upload failed
	 *    > error_message: contains warning or error messages generated during
	 *      the upload process
	 *    > admin_messages: contains detailed error messages for administrators
	 *      generated during the upload process
	 *  The function must return the final $changable_data. */
	function wfu_after_file_upload_handler($changable_data, $additional_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_after_file_upload', 'wfu_after_file_upload_handler', 10, 2);
}
/*</wfu_after_file_upload_template>*/

/*<wfu_after_data_submit_template>
  title: After Submittal of Data Filter
  scope: everywhere
  This filter runs in case that user data are submitted to the server without a
  file (nofileupload case) and after these data have been saved to the database,
  in order to allow additional tasks to be executed and define custom javascript
  code to run in client’s browser. 
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_after_data_submit_handler')) {
	/** Function syntax
	 *  The function takes two parameters, $changable_data and $additional_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the items:
	 *    > ret_value: not used for the moment, it exists for future additions
	 *    > js_script: javascript code to be executed on the client's browser
	 *      after data are submitted
	 *  - $additional_data is an array with additional data to be used by the
	 *    filter (but cannot be modified) as follows:
	 *    > shortcode_id: this is the id of the plugin, as set using uploadid
	 *      attribute; it can be used to apply this filter only to a specific
	 *      instance of the plugin (if it is used in more than one pages or
	 *      posts)
	 *    > unique_id: this id is unique for each individual submittal attempt
	 *      and can be used to identify it
	 *    > submit_result: it is the result of the submit process, taking the
	 *      following values:
	 *        success: the submittal was successful
	 *        error: the submittal failed
	 *    > error_message: contains warning or error messages generated during
	 *      the submit process
	 *    > admin_messages: contains detailed error messages for administrators
	 *      generated during the submit process
	 *  The function must return the final $changable_data. */
	function wfu_after_data_submit_handler($changable_data, $additional_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_after_data_submit', 'wfu_after_data_submit_handler', 10, 2);
}
/*</wfu_after_data_submit_template>*/

/*<wfu_after_upload_template>
  title: After Upload Filter
  scope: everywhere
  This filter runs after the upload completely finishes, in order to perform any
  final custom server actions.  
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_after_upload_handler')) {
	/** Function syntax
	 *  The function takes two parameters, $changable_data and $additional_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the items:
	 *    > js_script: javascript code to be executed on the client's browser
	 *      right after the filter; the script can check upload_status variable
	 *      for checking if upload has succeeded or not and mode variable for
	 *      checking if it was an AJAX or classic upload.
	 *  - $additional_data is an array with additional data to be used by the
	 *    filter (but cannot be modified) as follows:
	 *    > sid: this is the id of the plugin, as set using uploadid attribute;
	 *      it can be used to apply this filter only to a specific instance of
	 *      the plugin (if it is used in more than one pages or posts)
	 *    > unique_id: this id is unique for each individual upload attempt
	 *      and can be used to identify each separate upload
	 *    > files: holds an array with final data about the files that have been
	 *      uploaded (or failed); every item of the array is another array with
	 *      the following items:
	 *      >> file_unique_id: a unique id identifying every individual file
	 *      >> original_filename: the original filename of the file
	 *      >> filepath: the final path of the file (including the filename)
	 *      >> filesize: the size of the file
	 *      >> user_data: an array of user data values, if userdata are
	 *         activated, having the following structure:
	 *         >>> label: the label of the user data field
	 *         >>> value: the value of the user data fields entered by user
	 *      >> upload_result: it is the result of the upload process, taking
	 *         the following values:
	 *           success: the upload was successful
	 *           warning: the upload was successful but with warning messages
	 *           error: the upload failed
	 *      >> error_message: contains warning or error messages generated
	 *         during the upload process
	 *      >> admin_messages: contains detailed error messages for
	 *         administrators generated during the upload process
	 *  The function must return the final $changable_data. */
	function wfu_after_upload_handler($changable_data, $additional_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_after_upload', 'wfu_after_upload_handler', 10, 2);
}
/*</wfu_after_upload_template>*/

/*<wfu_browser_check_file_action_template>
  title: Check File Action of Browser Filter
  scope: everywhere
  This filter runs when the user attempts to download or delete a file of the
  front-end file viewer, in order to determine if the action will be accepted.  
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_browser_check_file_action_handler')) {
	/** Function syntax
	 *  The function takes two parameters, $changable_data and $additional_data.
	 *  - $changable_data is an array that can be modified by the filter and
	 *    contains the items:
	 *    > error_message: initially it is set to an empty value, if the handler
	 *      sets a non-empty value then the download or delete action of the
	 *      user will be rejected showing this error message
	 *  - $additional_data is an array with additional data to be used by the
	 *    filter (but cannot be modified) as follows:
	 *    > file_action: the action attempted by the user (download or delete)
	 *    > filepath: the full path of the file
	 *    > uploaduser: the ID of the user who uploaded the file
	 *    > userdata: an array of user data values, if userdata are activated,
	 *      having the following structure:
	 *      >> label: the label of the user data field
	 *      >> value: the value of the user data fields entered by user
	 *  The function must return the final $changable_data. */
	function wfu_browser_check_file_action_handler($changable_data, $additional_data) {
		// Add code here...
		return $changable_data;
	}
	add_filter('wfu_browser_check_file_action', 'wfu_browser_check_file_action_handler', 10, 2);
}
/*</wfu_browser_check_file_action_template>*/

/*<wfu_file_browser_edit_column_template>
  title: Edit File Viewer Column Contents
  scope: everywhere
  This filter enables to edit the contents of the file viewer columns, so that
  they can be fully customized. The {$column} variable in the filter name needs
  to be replaced by the column name that is to be editted. The filter function
  takes 3 parameters, the cell contents that are editable by the function, file
  information and additional info.
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_file_browser_edit_column_handler')) {
	/** Function syntax
	 *  The function takes three parameters, $cell, $file and $additional_data.
	 *  - $cell is an array that can be modified by the filter and contains the
	 *    items:
	 *    > contents: the contents that the column cell will have for the
	 *      specific file; initially it is takes the default value generated by
	 *      the plugin
	 *    > sort_value: this is the value that will be used to sort the column
	 *      if it is sortable
	 *  - $file is an array with information about the specific file; it
	 *      contains the following properties:
	 *    > name: the file name
	 *    > fullpath: the full path of the file
	 *    > size: the file size
	 *    > mdate: the date of last modification of the file
	 *    > filedata: the database object holding upload information about the
	 *      file
	 *    > deletable: a flag (true or false) determining whether the file can
	 *      be deleted by the user who views the file viewer
	 *  - $additional_data is an array with additional information; it contains
	 *      the following properties:
	 *    > bid: the ID if the specific file viewer
	 *    > column_sortable: a flag (true or false) determining whether the
	 *      column is sortable
	 *    > params: an array holding the shortcode parameters of the file viewer
	 *  The function must return the final $cell. */
	function wfu_file_browser_edit_column_handler($cell, $file, $additional_data) {
		// Add code here...
		return $cell;
	}
	add_filter('wfu_file_browser_edit_column-{$column}', 'wfu_file_browser_edit_column_handler', 10, 3);
}
/*</wfu_file_browser_edit_column_template>*/

/*<wfu_convert_file_viewer_to_gallery_template>
  title: Convert File Viewer to Gallery
  scope: everywhere
  This template will convert the front-end file viewer from a tabular to a tile
  format. If the shortcode contains a thumbnail column, then the viewer will be
  shown as an image gallery. This template has been configured to work properly
  when the shortcode is the following:
           [wordpress_file_upload_browser reloadonupdate="true"
           columns="thumbnail:/Thumbnail,file:s/File"]
  The template implements _wfu_file_browser_output filter and converts the
  table-related elements of the file viewer to divs.
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_file_browser_output_handler')) {
	function wfu_file_browser_output_handler($echo_str, $params) {
		$thumbnail_size = 100;  //set the size of the thumbnail
		$margin_size = 10;      //set the margin between the thumbnails
		$browser_id = "";       //set the ID of the browser you want to convert to gallery, or leave it empty for all browsers
		$size = $thumbnail_size + $margin_size * 2;
		if ( $browser_id == "" || $browser_id == $params["browserid"] ) {
			$echo1 = "";
			$echo2 = $echo_str;
			$pos = strpos($echo_str, '<table class="wfu_browser_table');
			if ( $pos !== false ) {
				$echo1 = substr($echo_str, 0, $pos);
				$echo2 = substr($echo_str, $pos);
			}
			$echo2 = str_replace( array("<table", "<tbody", "<tr", "<td", "</table>", "</tbody>", "</tr>", "</td>"),
				array("<div", "<div", "<div", "<div", "</div>", "</div>", "</div>", "</div>"), $echo2);
			$echo2 .= "<style>
				.wfu_browser_header { display: inline-block; }
				.wfu_browser_header div { float: left !important; }
				.wfu_head_row { display: none; }
				.wfu_browser_tr.wfu_visible { display: inline-block !important; vertical-align: top; }
				.wfu_browser_tr.wfu_visible:hover { box-shadow: 1px 1px 4px silver; }
				.wfu_browser_tr_template, .wfu_browser_tr.wfu_hidden { display: none !important; }
				.wfu_browser_td { text-align: center; width: ".$size."px; }
				.wfu_browser_td.wfu_col-1 { height: ".$size."px; }
				.wfu_display_thumbnail {
					display: table-cell;
					padding: 10px 10px 0 10px;
					width: ".($size - $margin_size * 2)."px;
					height: ".($size - $margin_size)."px;
					vertical-align: middle;
					text-align: center;
				}
				img.wfu_file_thumbnail { max-width: 100%; max-height: 100%; }
				img.wfu_filetype_icon { max-width: 48px; }
				.wfu_actions { display: none !important; }
				.wfu_display_file { word-wrap: break-word; font-size: smaller; }
				</style>";
			$echo_str = $echo1.$echo2;
		}
		return $echo_str;
	}
	add_filter('_wfu_file_browser_output', 'wfu_file_browser_output_handler', 10, 2);
}
/*</wfu_convert_file_viewer_to_gallery_template>*/

/*<wfu_bbPress_compatibility_template>
  title: Make the Plugin Compatible with bbPress
  scope: everywhere
  This template will make both plugin's shortcodes work inside bbPress topics
  and replies. The template file uploader-bbPress.php needs to be inside
  /templates folder of he plugin directory.
  !Note: Scope should be set to "Everywhere" for this filter to work properly.
*/
if (!function_exists('wfu_bbp_shortcodes')) {
	function wfu_bbp_shortcodes( $content, $reply_id ) {
		return do_shortcode( $content );
	}
	add_filter('bbp_get_reply_content', 'wfu_bbp_shortcodes', 10, 2);
	add_filter('bbp_get_topic_content', 'wfu_bbp_shortcodes', 10, 2);
}
if (!function_exists('wfu_uploader_template_handler')) {
	function wfu_uploader_template_handler($template, $params) {
		$template = "bbPress";
		return $template;
	}
	add_filter('_wfu_uploader_template', 'wfu_uploader_template_handler', 10, 2);
}
if (!function_exists('wfu_file_browser_output_handler')) {
	function wfu_file_browser_output_handler($output, $params) {
		$output .= "
			<style>
				.wfu_browser_container br { display: none; }
				.wfu_browser_container p { height: 0; margin: 0; padding: 0; }
				.wfu_browser_header label { display: inline-block; }
				.wfu_browser_header select { height: auto; }
			</style>
		";
		return $output;
	}
	add_filter('_wfu_file_browser_output', 'wfu_file_browser_output_handler', 10, 2);
}
/*</wfu_bbPress_compatibility_template>*/