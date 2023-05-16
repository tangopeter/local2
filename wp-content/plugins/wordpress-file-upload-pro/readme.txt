=== Wordpress File Upload Pro ===
Contributors: nickboss
Donate link: http://www.iptanus.com/support/wordpress-file-upload
Tags: file, upload, ajax, form, page, post, sidebar, responsive, widget, webcam, ftp
Requires at least: 2.9.2
Tested up to: 6.2
Stable tag: "trunk"
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple yet very powerful plugin to allow users to upload files to your website from any page, post or sidebar and manage the uploaded files

== Description ==

Wordpress File Upload Pro is a **[GDPR](https://www.gdpr.eu/) compliant** plugin that enables you, or other users, to upload files to your WordPress website from any page easily and securely by using shortcodes. The characteristics of the plugin are:

* It enables uploads of files from a **page**, **post** or **sidebar** (as a widget).
* It captures **video**/**screenshots** from the **webcam** and uploads it.
* It is **responsive** and uses the latest HTML5 technology, however it will also work with old browsers and mobile phones.
* It supports additional **form fields** (like checkboxes, text fields, email fields, dropdown lists etc).
* It can upload files of **unlimited size**, regardless of web server restrictions.
* It can upload files to a **Dropbox**, **Google Drive**, **Microsoft OneDrive**, **Amazon S3** account or to an **FTP** server.
* **Multiple** instances of the plugin are supported.
* It can work as a simple contact (or any other type of) form and file can be **optional**.
* It allows selection and upload of **many files** at the same time.
* It allows selection and upload of **directories**.
* It includes a file list **viewer** for viewing, downloading or deleting files from a page or post.
* It creates and then displays **thumbnails** of the uploaded files (images and PDFs)
* Users can also **view**, **rename**, **move**, **download** or **delete** their uploaded files from the Dashboard.
* It supports **drag and drop** of files.
* It includes an overall upload **progress bar**.
* It includes details and progress bars for each file individually.
* It includes the **Google Recaptcha** for checking user before file upload.
* It allows image files to be shown as image **gallery** from within the page.
* It includes an **Admin Bar** item that displays the number of new uploaded files.
* It includes an **Uploaded Files** top-level menu item in the Dashboard, from where admins can view the uploaded files.
* It includes a **File Browser** in the Dashboard for administrators to manage the files.
*  It supports localization and **multilingual** character sets.
* Uploaded files can be added to **Media**, or be attached to the current page.
* Uploaded files can be added to a **NextGEN** gallery.
* It is highly **customizable** with many (more than 50) options.
* It produces **notification** messages send through **e-mail** and **Facebook Messenger**.
* It supports **redirection** to another url after successful upload.
* It supports **filters** and **actions** so that programmers can extend the plugin.
* It supports **logging** of upload events or management of files, which can be viewed by admins through the Dashboard.
*  You can create you shortcode very easily by using the included Shortcode Composer in the plugin's settings inside Dashboard.
* It supports the new **Gutenberg** editor and includes custom **blocks** so that it can be easily added in posts and pages.
*  It includes a **css editor** to better style the plugin using custom css.
*  It supports code **hooks** so that filters and actions can be implemented easily.
*  It comes with **full technical support**.

The plugin is translated in the following languages:

* Portuguese, kindly provided by Rui Alao
* German
* French, kindly provided by Thomas Bastide of http://www.omicronn.fr/ and improved by other contributors
* Serbian, kindly provided by Andrijana Nikolic of http://webhostinggeeks.com/
* Dutch, kindly provided by Ruben Heynderycx
* Chinese, kindly provided by Yingjun Li
* Spanish, kindly provided by Marton
* Italian, kindly provided by Enrico Marcolini https://www.marcuz.it/
* Polish
* Swedish, kindly provided by Leif Persson
* Persian, kindly provided by Shahriyar Modami http://chabokgroup.com
* Greek

Please note that old desktop browsers or mobile browsers may not support all of the above functionalities. In order to get full functionality use the latest versions browsers, supporting HTML5, AJAX and CSS3.

== Installation ==

1. First install the plugin by downloading the .zip file from www.iptanus.com and install it from the Plugins section of your Dashboard.
1. Deactivate the Free version of the plugin from Plugins section of your Dashboard, if you have it installed and active.
1. Activate the Professional version from Plugins section of your Dashboard.
1. In order to use the plugin simply go to the Dashboard / Settings / Wordpress File Upload and follow the instructions in Plugin Instances or alternatively put the shortcode [wordpress_file_upload] in the contents of any page.
1. Open the page on your browser and you will see the upload form.
1. You can change the upload directory or any other settings easily by pressing the small edit button found at the left-top corner of the upload form. A new window (or tab) with pop up with plugin options. If you do not see the new window, adjust your browser settings to allow pop-up windows.
1. Full documentation about the plugin options can be found at http://www.iptanus.com/wordpress-plugins/wordpress-file-upload/ (including the Pro version)

A getting started guide can be found at http://www.iptanus.com/getting-started-with-wordpress-file-upload-plugin/

== Frequently Asked Questions ==

= Will the plugin work in a mobile browser? =

Yes, the plugins will work in most mobile phones (has been tested in iOS, Android and Symbian browsers as well as Opera Mobile) 

= Do I need to have Flash to use then plugin? =

No, you do not need Flash to use the plugin.

= I get a SAFE MODE restriction error when I try to upload a file. Is there an alternative?  =

Your domain has probably turned SAFE MODE ON and you have restrictions uploading and accessing files. Wordpress File Upload includes an alternative way to upload files, using FTP access. Simply add the attribute **accessmethod="ftp"** inside the shortcode, together with FTP access information in **ftpinfo** attribute.

= Can I see the progress of the upload? =

Yes, you can see the progress of the upload. During uploading a progress bar will appear showing progress info, however this functionality functions only in browsers supporting HTML5 upload progress bar.

= Can I upload many files at the same time? =

Yes, but not in the free version. If you want to allow multiple file uploads, please consider the [Professional](http://www.iptanus.com/support/wordpress-file-upload/ "Wordpress File Upload support page") version.

= Where do files go after upload? =

Files by default are uploaded inside wp-content directory of your Wordpress website. To change it use attribute uploadpath.

= Can I see and download the uploaded files? =

Administrators can view all uploaded files together with associated field data from the plugin's Settings in Dashboard. The [Professional](http://www.iptanus.com/support/wordpress-file-upload/ "Wordpress File Upload support page") version of the plugin allows users to view their uploaded files, either from the Dashboard, or from a page or post.

= Are there filters to restrict uploaded content? =

Yes, you can control allowed file size and file extensions by using the appropriate attribute (see Other Notes section).

= Are there any upload file size limitations? =

Yes, there are file size limitations imposed by the web server or the host. If you want to upload very large files, please consider the [Professional](http://www.iptanus.com/support/wordpress-file-upload/ "Wordpress File Upload support page") version of the plugin, which surpasses size limitations.

= Who can upload files? =

By default all users can upload files. You can define which user roles are allowed to upload files. Even guests can be allowed to upload files. If you want to allow only specific users to upload files, then please consider the [Professional](http://www.iptanus.com/support/wordpress-file-upload/ "Wordpress File Upload support page") version of the plugin.

= What security is used for uploading files? =

The plugin is designed not to expose website sensitive information. It has been tested by experts and verified that protects against CSRF and XSS attacks. All parameters passing from server to client side are encoded and sanitized. For higher protection, like use of captcha, please consider the [Professional](http://www.iptanus.com/support/wordpress-file-upload/ "Wordpress File Upload support page") version of the plugin.

= What happens if connection is lost during a file upload? =

In the free version the upload will fail. However in the Pro version the upload will resume and will continue until the file is fully uploaded. This is especially useful when uploading very large files.

= The plugin does not look nice with my theme. What can I do? =

There is an option in plugin's settings in Dashboard to relax the CSS rules, so that buttons and text boxes inherit the theme's styles. If additional styling is required, this can be done using CSS. The Professional version of the plugin allows CSS rules to be embed in the shortcode.

== Screenshots ==

1. A screenshot of the plugin in its most simple form.
2. A screenshot of the plugin showing the progress bar.
3. A screenshot of the plugin showing the successful upload message.
4. A screenshot of the plugin with additional form fields.
5. A screenshot of the plugin with subfolder selection.
6. A screenshot of the plugin in a sidebar.
7. A screenshot of the shortcode composer.
8. A screenshot of the file browser.

== Changelog ==

= 4.19.0 =
* added compatibility with block themes
* added shortcode attributes blockcompatibility and browserblockcompatibility for controlling block theme compatibility
* added shortcode attribute uploaderids in front-end file viewer to display files that have been uploaded only by specific upload forms

= 4.18.1 =
* fixed compatibility issues with PHP 8.1 or higher
* changed uploadform logic so that CSS pseudoselectors for Select Files button work
* changed directory upload logic so that files and directories can be selected
* added shortcode attribute "forcedir" to force selection of directories only

= 4.18.0 =
* added the ability to select (or drag) and upload directories
* added attribute "Directory Upload" in the visual editor of the upload form in order to allow/forbid selection of directories

= 4.17.0 =
* modified Google Drive authorization process to comply with Google's guidelines
* added Extensions page in Dashboard to activate/deactivate extensions

= 4.16.4 =
* sanitized page title in all places where it is retrieved to avoid XSS attacks

= 4.16.3 =
* improved sanitization and escaping of shortcode attributes to avoid XSS attacks
* file type .svg moved to blacklist to avoid XSS attacks coming from scripts inside SVG files
* added security check to forbid uploads inside wp-content/plugin directory
* improved handling of videoname and imagename file uploader shortcode attributes to avoid directory traversal attacks
* improved /lib loader to avoid arbitrary code execution through injected image files
* all wfu_blocks.php functions became redeclareable
* corrected bug where uploads were not working when dragdrop was deactivated

= 4.16.2 =
* corrected additional PHP8 warnings: "PHP Deprecated:  Required parameter ... follows optional parameter ... in wfu_gdrive_functions.php"

= 4.16.1 =
* corrected PHP8 warning: "PHP Deprecated:  Required parameter $GService follows optional parameter $parentID in wfu_gdrive_functions.php"
* added IamException.php in Amazon S3 library
* corrected $_SESSION variable problem in maintenance purge function

= 4.16.0 =
* major restructuring of external libraries
* added option to upload files to an Amazon S3 bucket
* added option in Settings to connect to an Amazon S3 account
* added options in upload form's shortcode to upload files to an Amazon S3 bucket
* added options in Maintenance Actions to reset or clear Amazon S3 uploads
* added Remote Files section in Dashboard area of the plugin from where admins can manage remote files stored in cloud services
* dropbox v1 version removed
* visual editor edit button misalignment fixed
* updated Facebook Messenger page access token
* corrected drag-drop bug so that it is not allowed to add files on an upload form by dragging during a running upload
* corrected echo problem when recording from webcam with sound
* Selective's Image Type library functions became an extension so that it is loaded only when necessary

= 4.15.0 =
* added image file check using Selective's Image Type library
* COOKIEHASH bug corrected
* improved support for FTP and SFTP uploads, which are now included in all plugin file viewers
* credentials in FTP and SFTP paths are stripped from the paths
* corrected File Detais to File Details
* corrected WFU_USERFILTER advanced option description
* get_option( "wordpress_file_upload_unfinished_data" ) corrected to get_option( "wordpress_file_upload_unfinished_data", array() ); in functions.php
* regex "/<style>(.*)<\/style><script.*?>(.*)<\/script>(.*)/s" changed to "/<style>(.*)<\/style>.*?<script.*?>(.*)<\/script>(.*)/s" in functions.php
* corrected notice: Undefined index: post in wfu_admin.php when the website has no posts

= 4.14.4 =
* restored .po files in languages so that users can change translations

= 4.14.3 =
* slight change in wfu_get_filtered_recs to handle cases where b.date_from is null
* code improvements to increase loading speed of plugin's file viewers and browsers
* added wfu_mime_content_type() function that uses several methods to get MIME type of a file and avoid errors transferring files to Google Drive and Microsoft OneDrive.

= 4.14.2 =
* corrected warning "Undefined index: upload_data" in front-end file viewer when including Remote Link column
* code improved so that upload message colors correctly adjust to shortcode color settings
* slight modifications to upload message colors while upload is in progress
* bug fix that throwed warnings in front-end file viewer when a userdata value was NULL
* corrected bug that was preventing more than one downloads from the front-end file viewer when restrict front end loading was active
* plugin cookie names adjusted in case COOKIEHASH does not exist
* corrected bug of the new plugin updater causing a warning when there are plugins that do not have their own subdirectory
* closing tags removed from all PHP files to avoid "Headers already sent" errors
* corrected bug where the uploads counter was showing to non-administrators
* wfu_log_action and wfu_process_files functions became redeclarable
* removed debug_log from wfu_process_files_queue
* consent Yes/No question was added in translation
* corrected locale of Greek translation

= 4.14.1 =
* added option in file viewer the define the default sorting column and sort order
* fix webcam play button bug
* fixed bug of the new auto-updater erroneoulsy showing some times that there is an updated version
* corrected issue with implode() function of minifier library appearing in websites having PHP > 7.4.2
* wfu_admin.php modified to use wfu_ajaxurl() function

= 4.14.0 =
* added new auto-updater for Professional version using Iptanus Services endpoints.
* added mechanism to deactivate hooks when one of them breaks the website.
* added filter _wfu_get_params for better customization of the plugin.
* readme file split for Free and Pro version.

= 4.13.1 =
* file checking of uploaded files hardened to better handle xss attacks coming through uploaded image files.

= 4.13.0 =
* corrected security vulnerability where remote code could be executed using directory traversal method. Credits to p4w security expert for identifying the vulnerability.
* added uploader shortcode options 'Share/List Google Drive File', 'Share/List Dropbox File' and 'Share/List OneDrive File' so that when uploaded files are transferred to cloud services, sharable links are stored in plugin's database.
* plugin logic modified so that it can display remote files (files that have been transferred to cloud services and no longer exist in the website).
* added option 'Show Remote Files' in file viewer shortcode, so that remote files are shown in the front-end file viewer.
* added advanced option 'Back-end Browser Show Remote Files', so that remote files are shown in the back-end file viewer.
* added 'Remote Link' column in file viewer shortcode, to display cloud service link of the uploaded file in the front-end file viewer.
* added 'Remove Remote' actions in front-end and back-end file viewers, so that remote files can be removed from the plugin's database.
* Uploaded Files Dashboard menu modified to show remote files and also cloud services links.
* correction of various bugs in file viewers.
* front-end file viewer has been optimized to handle thousands of files.
* improved user check algorithm during upload, related to upload parameters array.
* corrected bug where Restricted Page Loading was working only for pages, all other post types were loading the plugin files as if there was no restriction.

= 4.12.2 =
* Added elementor extension which makes the plugin compatible with Elementor page builder.
* Code modified so that wordpress file upload widget is disabled with Elementor page builder.
* Corrected bug where files could not be downloaded in some server environments when dboption user state handler was enabled.

= 4.12.1 =
* Corrected bug where files could not be downloaded from front-end file viewer after update to version 4.12.0.
* Corrected bug where files could not be downloaded from Dashboard / Uploaded Files page.
* Corrected uploader template uploader-AdvancedFileList.php which was incomplete.

= 4.12.0 =
* Added cookies user state handler that has been integrated with dboption as 'Cookies (DBOption)' to comply with Wordpress directives not to use session.
* 'Cookies (DBOption)' user state handler has been set as the default one.
* Added advanced option WFU_US_DBOPTION_BASE so that dboption can also work with session.
* Added advanced option WFU_US_SESSION_LEGACY to use the old session functionality of the plugin, having session_start() in header.
* Added auto-adjustment of user state handler to 'dboption' during activation (or update) of the plugin, except when there are active hooks using session.
* Added the ability to run PHP processes in queue, which is necessary for correctly handling uploads when user state handler is dboption.
* Bug "Error: [] cURL error 28" in Wordpress Site Health disappears when setting user state handler to 'Cookies (DBOption)' or when WFU_US_SESSION_LEGACY advanced option is false.
* Added warnings in hooks when user state handler configuration does not support session and there are active hooks using session.
* Improved dboption user state handler to better handle parallel transactions occurring during chunked uploads.
* Corrected bug where export data file was not deleted after download.
* Added sftp using phpseclib library.
* Corrected bug where notification email was not sent in case of failed chunks that were resumed.
* Corrected bug in FTP credentials configurator about double backslash (\\) issue.
* Improved functionality and control of file downloader.

= 4.11.2 =
* Corrected bug where Gutenberg editor breaks when a Custom HTML block is added on the page.
* Added easier configuration of FTP Credentials (ftpinfo) attribute of the uploader shortcode.

= 4.11.1 =
* added NextGEN Gallery support, now uploaded files can be added in a NextGEN gallery.
* added upload form shortcode attribute 'nextgen' to enable inclusion of uploaded files in a NextGEN gallery.
* added upload form shortcode attribute 'ngg_description' to add a custom description to the file added in NextGEN gallery.
* added upload form shortcode attribute 'ngg_alttext' to add a custom Alt/title text to the file added in NextGEN gallery.
* added upload form shortcode attribute 'ngg_tags' to add custom tags to the file added in NextGEN gallery.
* added upload form shortcode attribute 'ngg_exclude' to set 'Exclude' property of the file added in NextGEN gallery.
* corrected bug in functions wfu_manage_mainmenu() and wfu_manage_mainmenu_editor() that were echoing and not returning the generated HTML.
* corrected bug in front-end file viewer generating $roleid error when filtering based on users.
* added fix for compatibility with Fast Velocity Minify plugin.

= 4.11.0 =
* added Gutenberg blocks for uploader and browser shortcodes.
* user request to have reusable shortcodes can now be implemented with Gutenberg reusable blocks.
* code improved so that shortcode composer can be used by all users who can edit pages (and not only the admins).
* added environment variable 'Show Shortcode Composer to Non-Admins' to control whether non-admin users can edit the shortcodes.
* added filtering of get_users() function in order to handle websites with many users more efficiently.
* corrected bug where case-insensitive search in front-end file viewer was not working for non-latin characters.
* added notification in shortcode composer if user leaves page without saving.
* corrected bug where restricted frontend loading of the plugin was not working for websites installed in localhost due to wrong calculation of request uri.
* added environment variable 'Google ReCaptchaV2 Host' to define Google ReCaptchaV2 host - fix for China.

= 4.10.3 =
* shortcode composer became more responsive to look better on small screens.
* added the ability to move one or more files to another folder through the File Browser feature in Dashboard area of the plugin.

= 4.10.2 =
* added wordpress_file_upload_preload_check() function in main plugin file to avoid conflicts of variable names with Wordpress.
* updated webcam code to address createObjectURL Javascript error that prevented webcam feature to work in latest versions of browsers.

= 4.10.1 =
* code modified so that vendor libraries are loaded only when necessary.
* improved process of deleting all plugin options.
* OneDrive code modified to work better with Business OneDrive accounts.
* added honeypot field to userdata fields; this is a security feature, in replacement of captchas, invisible to users that prevents bots from uploading files.
* corrected “google drive uploads were successfully reset.” bug in OneDrive Reset button.
* added attribute 'Consent Denial Rejects Upload' in uploader shortcode Personal Data tab to stop the upload if the consent answer is no, as well as 'Reject Message' attribute to customize the upload rejection message shown to the user.
* added attribute 'Do Not Remember Consent Answer' in uploader shortcode Personal Data tab to show the consent question every time (and not only the first time).
* attribute 'Preselected Answer' in uploader shortcode Personal Data tab modified to be compatible with either checkbox or radio Consent Format.
* upload result message adjusted to show the correct upload status in case that files were uploaded but were not saved due to Personal Data policy.
* code improved for sftp uploads to handle PECL ssh2 bug #73597.

= 4.10.0 =
* added Messenger Notifications feature.
* added Facebook Settings section in plugin's Settings.
* added Messenger Notifications option in Settings to subscribe to Wordpress File Upload plugin Messenger.
* added 'Notify by Messenger' attribute in Notifications tab of the uploader shortcode to enable notifications through Messenger when new files are uploaded.
* added 'Messenger Text' attribute in Notifications tab of the uploader shortcode to define the message that Wordpress File Upload facebook app will send to admin's Messenger.
* added 'Upload Details' attribute in Notifications tab of the uploader shortcode to define details about the new upload that will be shown to the recipient if variable %uploaddetails% is included in Messenger Text.
* added several Advanced options in Dashboard area of the plugin to control Facebook Messenger parameters.
* plugin code improved to support filenames containing single quotes (').
* corrected bug where plugin was deactivated after update.

= 4.9.1 =
* added Maintenance action 'Purge All Data' that entirely erases the plugin from the website and deactivates it.
* added advanced option 'Hide Invalid Uploaded Files' so that Uploaded Files page in Dashboard can show only valid uploads.
* added advanced option 'Restrict Front-End Loading' to load the plugin only on specific pages or posts in order to reduce unnecessary workload on pages not containing the plugin.
* code improved for better operation of the plugin when the website works behind a proxy.
* added option in Clean Log to erase the files together with plugin data.
* added compatibility with bbPress through a hook, so that both plugin's shortcodes can work inside bbPress topics and replies.

= 4.9.0 =
* added Microsoft OneDrive integration.
* added Microsoft OneDrive Settings section in plugin's Settings.
* added Microsoft OneDrive Uploads option in Settings to connect to a Google Drive account and activate Microsoft OneDrive uploads.
* added Microsoft OneDrive Default Path option in Settings to define the OneDrive path that will be used when transferring files to Drive through the File Browser.
* added Include Userdata option in Microsoft OneDrive Settings to define whether to include additional userdata (if any) when transferring files to OneDrive through the File Browser.
* added Conflict Policy option in Microsoft OneDrive Settings to define how to handle tranferred files to OneDrive destination through the File Browser when a file with the same name already exists at destination.
* added 'Send to OneDrive' file action in File Browser so that a file can be transferred to Microsoft OneDrive straight from the File Browser.
* added Microsoft OneDrive Actions in Maintenance Actions tab in Dashboard area of the plugin, including buttons for reseting and clearing OneDrive uploads.
* added 'Upload to Microsoft OneDrive' attribute in Interoperability tab of the uploader shortcode to enable uploaded files to be transferred to Microsoft OneDrive.
* added 'OneDrive Path' attribute in Interoperability tab of the uploader shortcode to define the destination folder in Microsoft OneDrive; if it does not exist then it will be automatically created.
* added 'Include Userdata' attribute in Interoperability tab of the uploader shortcode to include userdata in the description of the file transferred to Microsoft OneDrive.
* added 'Conflict Policy' attribute in Interoperability tab of the uploader shortcode to define how to handle tranferred files to OneDrive destination when a file with the same name already exists at destination.
* added several Advanced options in Dashboard area of the plugin to control Microsoft OneDrive upload parameters.
* code further improved to reduce "Iptanus Server unreachable..." errors and inability to activate hooks (better operation of verify_peer http context property).
* file viewer date function corrected to correctly convert UTC to local time.
* corrected bug in File Transfers that was preventing file transfers to work (WFU_TRANSFERMANAGER_MAX_RUNTIME could not reset transfermanager status if it was stuck).
* several improvements to File Transfers code to make it more reliable when transferring multiple files at once..
* checked Weglot Translate compatibility; /wp-admin/admin-ajax.php needs to be added to Exclusion URL list of Weglot configuration so that uploads can work.

= 4.8.0 =
* added item in Admin Bar that displays number of new uploads and redirects to Uploaded Files Dashboard page.
* code improved in Uploaded Files Dashboard page so that download action directly downloads the file, instead of redirecting to File Browser.
* added Advanced option 'WFU_UPLOADEDFILES_COLUMNS' that controls the order and visibility of Uploaded Files Dashboard page columns.
* added Advanced option 'WFU_UPLOADEDFILES_ACTIONS' that controls the order and visibility of Uploaded Files Dashboard page file actions.
* added several filters in Uploaded Files Dashboard page to make it more customizable.
* PHP function redeclaration system significantly improved to support arguments by reference, execution after the original function and redeclaration of variables.
* search filters improved to allow server-side execution when 'Reload on Update' and 'Pagination' attributes are active on the file viewer.
* code improved to reduce "Iptanus Server unreachable..." errors and inability to activate hooks (better operation of verify_peer http context property).
* added a link in Iptanus Unreachable Server error message to an Iptanus article describing how to resolve it.

= 4.7.0 =
* added Uploaded Files top-level Dashboard menu item, showing all the uploaded files and highlighting the new ones.
* added Portuguese translation kindly provided by Rui Alao.
* checked and verified compatibility with Gutenberg.
* plugin initialization actions moved to plugins_loaded filter.
* fixed bug clearing userdata fields in non-Multiple File mode when Select File is pressed.
* File Browser and View Log tables modified to become more responsive especially for small screens.
* Hooks, Advanced and File Transfers tables modified to become more responsive especially for small screens.

= 4.6.2 =
* order of hooks in Hooks tab is reversed; newer hook will be first.
* localized 'Page x of N' phrase in File Viewer Pagination header.
* localized dates in File Viewer date column.
* File Viewer code improved for better handling of large number of files.
* corrected consent_status warning when updating user profile and Personal Data is off.
* user fields code improved for better data autofill behaviour.

= 4.6.1 =
* added uploader shortcode attribute 'resetmode' to control whether the upload form will be reset after an upload.
* added pagination in File Browser tab in Dashboard.

= 4.6.0 =
* added Google Drive integration.
* added Google Drive Settings section in plugin's Settings.
* added Google Drive Uploads option in Settings to connect to a Google Drive account and activate Google Drive uploads.
* added Google Drive Default Path option in Settings to define the Google Drive path that will be used when transferring files to Google Drive through the File Browser.
* added Include Userdata option in Google Drive Settings to define whether to include additional userdata (if any) when transferring files to Google Drive through the File Browser.
* added Trash Duplicates option in Google Drive Settings to define whether to trash files in Google Drive destination that are found to have the same filename as files transferred to Google Drive through the File Browser.
* added 'Send to Google Drive' file action in File Browser so that a file can be transferred to Google Drive straight from the File Browser.
* added Google Drive Actions in Maintenance Actions tab in Dashboard area of the plugin, including buttons for reseting and clearing Google Drive uploads.
* added 'Upload to Google Drive' attribute in Interoperability tab of the uploader shortcode to enable uploaded files to be transferred to Google Drive.
* added 'Google Drive Path' attribute in Interoperability tab of the uploader shortcode to define the destination folder in Google Drive; if it does not exist then it will be automatically created.
* added 'Include Userdata' attribute in Interoperability tab of the uploader shortcode to include userdata in the description of the file transferred to Google Drive.
* added 'Trash Duplicates' attribute in Interoperability tab of the uploader shortcode to trash any duplicate files found in Google Drive destination folder.
* added several Advanced options in Dashboard area of the plugin to control Google Drive upload parameters.
* improved file transfer control so that transfer manager is executed more often when there are pending transfer jobs.
* corrected slash (/) parse Javascript error near 'fakepath' appearring on some situations.
* relaxed excessive sanitization of hook title that was converting capitals to small letters and spaces to dashes.
* added nonces in Maintenance Actions to increase security.
* added File Transfer actions in Maintenance Actions so that all file transfers can be reset or cleared at once.
* improved code in View Log so that no links appear to invalid files.
* improved code in View Log so that when the admin opens a file link to view file details, 'go back' button will lead back to the View Log page and not to File Browser.
* improved code in 'Clean Log' button in Maintenance Actions in Dashboard area of the plugin, so that the admin can select the period of clean-up.

= 4.5.0 =
* added basic compliance with GDPR.
* added several shortcode attributes to configure personal data consent appearance and behaviour.
* added area in User Profile from where users can review and change their consent status.
* added Personal Data option in Settings that enables personal data operations.
* added Personal Data tab in plugin's area in Dashboard from where administrators can export and erase users' personal data.
* corrected bug not accepting subfolder dimensions when subfolder element was active.
* corrected bug not finishing uploads of multiple files occassionally when User State Handler is set to DB Option.

= 4.5.1 =
* code improved in wfu_js_decode_obj function for better compatibility with Safari browser.
* code improved to sanitize all shortcode attributes before uploader form or file viewer is rendered.
* sanitized title and description in hooks.
* removed external references to code.jquery.com and cdnjs.cloudflare.com for better compliance with GDPR.

= 4.5.0 =
* added basic compliance with GDPR.
* added several shortcode attributes to configure personal data consent appearance and behaviour.
* added area in User Profile from where users can review and change their consent status.
* added Personal Data option in Settings that enables personal data operations.
* added Personal Data tab in plugin's area in Dashboard from where administrators can export and erase users' personal data.
* corrected bug not accepting subfolder dimensions when subfolder element was active.
* corrected bug not finishing uploads of multiple files occassionally when User State Handler is set to DB Option.

= 4.4.0 =
* added alternative user state handler using DB Options table in order to overcome problems with session variables appearing on many web servers.
* added support for dropbox transfers of very large files surpassing the max_execution_time restrictions of web servers.
* code improved in file transfer manager scheduler so that it restarts automatically in case of freeze problems without the need to press "Reset Dropbox Uploads" button in Maintenance Actions.
* added progress indicator on File Transfers for big files.
* added an hourly task scheduler that executes repeating plugin's actions such as transfers manager and unfinished files checker.
* added alternative method for creating thumbnails for PDF files relying on Ghostscript, in case inherent Wordpress routines fail.
* fixed bug in file viewer edit column filter.
* fixed bug in file viewer not adjusting correctly time zone in dates.

= 4.3.4 =
* all Settings sanitized correctly to prevent XSS attacks - credits to ManhNho for mentioning this problem.

= 4.3.3 =
* all shortcode attributes sanitized correctly to close a serious security hole.
* plugin code fixes in Dropbox.

= 4.3.2 =
* fixed bug in wfu_before_upload and wfu_after_upload filters that was breaking JS scripts if they contained a closing bracket ']' symbol.
* improved code in wfu_calculate_image_sizes function so that it is compatible with versions of PHP prior to 5.4.
* corrected bug in file viewer not correctly removing rows when deleting single file.
* corrected bug in file viewer where delete of single file failed when there was no sort column.

= 4.3.1 =
* corrected bug in Dropbox code which did not allow Dropbox to reset.
* added screenshots of PDFs in Thumbnails.
* added placeholder option in available label positions of additional fields; label will be the placeholder attribute of the field.

= 4.3.0 =
* added Thumbnails feature in plugin that enables creation and management of thumbnails of uploaded images.
* added File Icon Vectors library from Daniel M. Hendricks providing icons for most file types.
* added 'Create Thumbnails' option in plugin's Settings to enable thumbnails generation.
* added 'Thumbnail Size' attribute in frontend file viewer to control the size of the rendered thumbnails.
* added several Maintenance Actions to manage (clean and update) thumbnails.
* added Thumbnails column in front-end file viewer, which displays the uploaded file if it is an image, otherwide it displays an icon corresponding to its file type.
* added advanced variables to control the operation of Thumbnails feature.
* added hook template to turn file viewer into gallery by converting the table elements to divs.
* fixed serious bug that was preventing Dropbox uploads when Dropbox Path was set to root path.
* file transfers code improved to overcome situations where asynchronous call of transfer functions fail.
* fixed bug where ftp credentials did not work when username or password contained (:) or (@) symbols.
* RegExp fix for wfu_js_decode_obj function for improved compatibility with caching plugins.
* front-end file viewer code improved for higher flexibility.
* added 'Reload on Update' attribute in front-end file browser; when it is activated the browser will not load entirely and will need to reload on updates; this is useful when the browser contains too many files.
* corrected WFU_Original_Template::get_instance() method because it always returned the original class.
* View Log page improved so that displayed additional user fields of an uploaded file are not cropped.
* added Link column in front-end file viewer, which generates a link to the uploaded file.
* corrected bug in frontend file viewer related to error 'wfu_download_file not defined' that was preventing files to be downloaded some times.

= 4.2.0 =
* plugin modified so that the shortcodes render correctly either Javascript loads early (in header) or late (in footer).
* corrected bug where a Javascript error was generated when askforsubfolders was disabled and showtargetfolder was active.
* added css and js minifier in inline code.
* improved file transfers functionality.
* plugin modified so that Media record is deleted when the associated uploaded file is deleted from plugin's database.
* corrected bug where some plugin images were not loaded while Relax CSS option was inactive.

= 4.1.0 =
* several code modifications performed so that more cloud options (like Google Drive and Amazom S3) can be added as extensions in future versions.
* changed logic of file sanitizer; dots in filename are by default converted to dashes, in order to avoid upload failures caused when the plugin detects double extensions.
* added advanced option WFU_SANITIZE_FILENAME_DOTS that determines whether file sanitizer will sanitize dots or not.
* timepicker script and style replaced by most recent version.
* timepicker script and style files removed from plugin and loaded from cdn.
* json2 script removed from plugin and loaded from Wordpress registered script.
* JQuery UI style updated to latest 1.12.1 minified version.
* added wfu_before_admin_scripts filter before loading admin scripts and styles in order to control incompatibilities.
* removed getElementsByClassName-1.0.1.js file from plugin, getElementsByClassName function was replaced by DOM querySelectorAll.
* corrected bug in Free version showing warning "Notice: Undefined variable: page_hook_suffix..." when a non-admin user opened Dashboard.
* corrected fatal error "func_get_args(): Can't be used as a function parameter" appearing in websites with PHP lower than 5.3.
* added _wfu_file_upload_hide_output filter that runs when plugin should not be shown (e.g. for users not inluded in uploadroles), in order to output custom HTML.
* corrected bug where email fields were always validated, even if validate option was not activated.
* corrected bug where number fields did not allow invalid characters, even if typehook option was not activated.
* corrected bug where email fields were not allowed to be ampty when validate option was activated.
* corrected error T_PAAMAYIM_NEKUDOTAYIM appearing when PHP version is lower than 5.3.
* corrected Dropbox PHP dependencies; Dropbox is disabled when API v2 is used and PHP is lower than 5.5.0.
* improved handling of delays of Dropbox uploads; now the user can reset Dropbox uploads from Maintenance Actions section without having to clear the list of pending files.
* corrected bug with random upload fails caused when params_index corresponds to more than one params.

= 4.0.1 =
* translation of the plugin in Persian, kindly provided by Shahriyar Modami http://chabokgroup.com.
* corrected bug where notification email was not sending atachments.
* corrected bug not cleaning log in Maintenance Actions.

= 4.0.0 =
* huge renovation of the plugin, the UI code has been rewritten to render based on templates.
* added auto-update feature for Pro version.
* code modified so that it can correctly handle sites where content dir is explicitly defined.
* corrected bug in Dashboard file editor so that it can work when the website is installed in a subdirectory.
* corrected warnings showing when editing a file that was included in the plugin's database.
* migrated to Dropbox API v2.
* Dropbox transfers process improved in order to be more reliable.
* added File Transfers tab in Dashboard to monitor Dropbox transfers.
* added many Advanced variables to control timing and behaviour of File Transfers.
* added filter in get_posts so that it does not cause problems when there are too many pages/posts.
* bug fixes so that forcefilename works better and does not strip spaces in the filename.
* code improved to protect from hackers trying to use the plugin as email spammer.
* added advanced variable Force Email Notifications so that email can be sent even if no file was uploaded.
* corrected bug not showing sanitized filanames correctly in email.
* corrected bug so that dates show-up in local time and not in UTC in Log Viewer, File Browser and File Editor.
* fixed bug showing "Warning: Missing argument 2 for wpdb::prepare()" when cleaning up the log in Maintenance Actions.
* added filter wfu_file_browser_edit_column-{column} so that file viewer column contents can be customized.
* improved behaviour when multiple file selection is deactivated.
* corrected bug where when configuring subfolders with visual editor the subfolder dialog showed unknown error.
* corrected bug where the Select File button was not locked during upload in case of classical HTML (no-ajax) uploads.
* added cancel button functionality for classic no-ajax uploads.
* added support for Secure FTP (sftp) using SSH2 library.
* successmessagecolor and waitmessagecolors attributes are hidden as they are no longer used.

= 3.11.0 =
* added the ability to submit the upload form without a file, just like a contact form.
* added attribute allownofile in uploader shortcode; if enabled then the upload form can be submitted without selection of a file.
* added wfu_before_data_submit and wfu_after_data_submit filters which are invoked when the upload form is submitted without a file.
* added advanced debug options for more comprehensive and deep troubleshooting.
* added internal filters for advanced hooking of ajax handlers.
* fixed several security problems.
* fixed bug that was generating error when automatic subfolders were activated and the upload folder did not exist.
* corrected bug where single quote, double quote and backslash characters in user fields were not saved correctly (they were escaped).
* fixed bug that was resetting front-end file viewer when 'Only from Current Post' filter was active and viewer was updated.
* fixed bug where any changes made to the user data (e.g. through a filter) were not included in the email message.
* added unique_id variable in wfu_before_file_check and wfu_after_file_upload filters.
* changed column titles in the tables of plugin instances in Main tab in Dashboard.
* fixed bug where if a user field was modified from the file editor, custom columns were changing order.
* translation files updated with some more strings.

= 3.10.0 =
* an alternative Iptanus server is launched in Google Cloud for resolving the notorious error "file_get_contents(https://services2.iptanus.com/wp-admin/admin-ajax.php): failed to open stream: Connection timed out." causing failure of Dropbox and other features.
* added option 'Use Alternative Iptanus Server' in Settings to switch to the alternative Iptanus Server.
* added advanced option 'Alternative Iptanus Server' that points to an alternative Iptanus Server.
* added advanced option 'Alternative Iptanus Version Server' that points to the alternative Iptanus Server URL returning the latest plugin version.
* added advanced option 'Alternative Iptanus Captcha Server' that points to the alternative Iptanus Server URL returning necessary captcha data.
* added advanced option 'Alternative Iptanus Dtopbox Server' that points to the alternative Iptanus Server URL returning necessary Dropbox data.
* an error is shown in the Main page of the plugin in Dashboard if Iptanus Server is unreachable.
* a warning is shown in the Main page of the plugin in Dashboard if an alternative insecure (http) Iptanus Server is used.
* alternative fix of error accessing https://services2.iptanus.com for cURL (by disabling CURLOPT_SSL_VERIFYHOST) and for sockets by employing a better parser of socket response.
* extension filter regex improved so that it translates [ and ] correctly.
* fixes of bugs appearing when a non-admin user edited a file from the Dashboard File Viewer.
* added Swedish translation, kindly provided by Leif Persson.
* improved ftp functionality so that ftp folders can be created recursively.

= 3.9.6 =
* added internal filter _wfu_file_upload_output before echoing uploader shortcode html.
* added internal filter _wfu_file_browser_output before echoing browser shortcode html.
* added shortcode attribute captchaoptions for customizing Google Recaptcha.
* added ability to change the order of additional user fields in shortcode visual editor.

= 3.9.5 =
* added environment variable 'Force Dropbox to Work for 32bit Servers' so that Dropbox API works for 32bit web servers as well, fix provided by Hanneke Hoogstrate http://www.blagoworks.nl/.
* fixed bugs in upload algorithm, so that internal auto-resume function works better.
* added environment variable 'Max Concurrent Connections to Server' to provide better control of upload algorithm.
* added environment variable 'Upload Progress Mode' that defines how upload progress is calculated.
* improved progress bar calculation for the total upload and individual files.
* minor bug fixes in AJAX functions mentioned by Hanneke Hoogstrate http://www.blagoworks.nl/.

= 3.9.4 =
* added option to enable admin to change the upload user of a file.
* code improvements and bug fixes related to file download feature.
* code improvements related to clean database function.
* added Italian translation kindly provided by by Enrico Marcolini https://www.marcuz.it/.

= 3.9.3 =
* added option to allow loading of plugin's styles and scripts on the front-end only for specific posts/pages through wfu_before_frontpage_scripts filter.
* fixed bug where when uploading big files with identical filenames and 'maintain both' option, not all would be saved separately.
* two advanced variables were added to let the admin change the export function separators.

= 3.9.2 =
* fixed bug in file viewer, where if we had 2 viewers on the same page, visual editor would not launch in one of them.
* fixed bug in file viewer where columns were not sortable when using default columns attribute.
* fixed bug in file viewer where Increment column was shown second in visual editor of the shortcode when using default columns attribute.
* added environment variable to enable or disable version check, due to access problems of some users to Iptanus Services server.
* added timeout option to wfu_post_request function.

= 3.9.1 =
* corrected Safari problem with extra spaces in success message coming from force_close_connection.
* corrected bug where when extension has capital letters it is rejected.
* temporary fix to address issue with plugin's Main page in Dashboard not loading, by disabling plugin version check.

= 3.9.0 =
* a big number of extensions have been blacklisted for preventing upload of potentially dangerous files.
* the plugin will not allow inclusion, renaming or downloading of files with blacklisted extensions based on the new list.
* if no upload extensions are defined or the uploadpattern is too generic, then the plugin will allow only specific extensions based on a white list of extensions; if the administrator wants to include more extensions he/she must declare them explicitely.
* the use of the wildcard asterisk symbol has become stricter, asterisk will match all characters except the dot (.), so the default *.* pattern will allow only one extension in the filename (and not more as happened so far).
* added environment variable 'Wildcard Asterisk Mode' for defining the mode of the wildcard asterisk symbol. If it is 'strict' (default) then the asterisk will not match dot (.) symbol. If it is 'loose' then the asterisk will match any characters (including dot).
* slight bug fixes so that wildcard syntax works correctly with square brackets.
* added maximum number of uploads per specific interval in order to avoid DDOS attacks.
* added environment variables related to Denial-Of-Service attacks in order to configure the behaviour of the DOS attack checker.
* bug fix of wfu_before_file_upload filter that was not working correctly with files larger than 1MB.

= 3.8.5 =
* added bulk actions feature in all plugin's browsers/viewers.
* added delete bulk action in front-end file viewer.
* added delete bulk action in back-end file viewer.
* added delete and include bulk actions in admin file browser.
* added bulkactions attribute in file browser shortcode in order to determine if bulk actions are activated in the front-end file viewer or not.
* added deletestrictmode attribute in file browser shortcode so that delete link is not shown for files that the user is not allowed to delete.
* improvement of column sort functionality in all plugin's browsers/viewers.
* added environment variable 'Use Alternative Randomizer' in order to make string randomizer function work for fast browsers.
* uploadedbyuser and userid fields became int to cope with large user ID numbers on some Wordpress environments.

= 3.8.4 =
* fixed serious bug that was cancelling upload of big files when captcha was enabled.
* dublicatespolicy attribute replaced by grammaticaly correct duplicatespolicy, however backward compatibility with the old attribute is maintained.

= 3.8.3 =
* fixed bug of subdirectory selector that was not initializing correctly after upload.
* fixed slight widget incompatibility with customiser.
* fixed bug of drag-n-drop feature that was not working when singlebutton operation was activated.

= 3.8.2 =
* fixed bug in wfu_after_file_loaded filter that was not working and was overriden by obsolete wfu_after_file_completed filter.
* added option in plugin's Settings in Dashboard to include additional files in plugin's database.
* added feature in Dashboard File Browser for admins to include additional files in plugin's database.

= 3.8.1 =
* enabled regex inside patternfilter attribute of File Viewer using "regex:" prefix in order to make it more powerful.
* fixed bug with duplicate userdata IDs in HTML when using more than one userdata occurrences.
* plugin improved so that Code Hooks and Dropbox can work when /wp-admin area is password protected.
* added Environment Variables to define credentials for protected /wp-admin area.
* added Maintenance Action to reset Dropbox uploads.
* added option in plugin Settings to define default Dropbox upload path.
* added capability in Dashboard File Browser for admins to send files to Dropbox.

= 3.8.0 =
* added webcam option that enables webcam capture functionality.
* added webcammode atribute to define capture mode (screenshots, video or both).
* added audiocapture attribute to define if audio will be captured together with video.
* added audiocapture attribute to define if audio will be captured together with video.
* added videowidth, videoheight, videoaspectratio and videoframerate attributes to constrain video dimensions and frame rate.
* added camerafacing attribute to define the camera source (front or back).
* added maxrecordtime attribute to define the maximum record time of video.
* added uploadmediabutton, videoname and imagename attributes to define custom webcam-related labels.
* fixed bug that stripped non-latin characters from filename when downloading files.
* added option in Settings so that Code Hooks and Dropbox can work when mod_security is active on the webserver.
* added $php_version definition in function wfu_dropbox_upload that was missing.

= 3.7.3 =
* improved filename sanitization function.
* added Chinese translation by Yingjun Li.

= 3.7.2 =
* added the ability to cancel the upload during uploading of files.
* option added in plugin's Settings in Dashboard so that upload does not fail when site_url and home_url are different.
* added attribute requiredlabel in uploader's shortcode that defines the required keyword.
* required keyword can now be styled separately from the user field label.
* added attribute browsercss in browser's shortcode that defines custom css for the file browser.
* correction of small bug in hook templates.
* added browser hook wfu_browser_check_file_action for modifying list of allowed downloaders and deleters.
* if the uploaded file is saved in Media Library then any custom field data submitted by user together with the file are also saved as media attachment metadata.
* option added in plugin's Settings in Dashboard so that userdata fields are shown in Media Library or not.
* in frontend file viewer, a user field can now be a separate column.
* added Dutch translation by Ruben Heynderycx.

= 3.7.1 =
* internal code modifications and slight bug corrections.
* addition of Advanced page in plugin's Dashboard area for editing plugin's environment variables.
* upload logic changed so that single-button operation can work with captcha that was not previously possible.

= 3.7.0 =
* significant code modifications to make the plugin pluggable, invisible to users.
* addition of before and after upload filters.
* improvement of Google Recaptcha version 1.
* Google RecaptchaV1 now comes in two options, normal that requires to set public and secret keys in plugin's Settings and (no account) that requires no keys.
* correction of small bug in Shortcode Composer of File Viewer.

= 3.6.1 =
* Iptanus Services server, that provides latest version info and other utilities is now secure.
* Fixed bug with hook code templates not showing in some websites.
* Modifications to dropbox logic so that public and secret keys are hidden.
* Fixed bug with dropboxpath dynamic variables that were not working.
* Fixed bug with wfu_path_abs2rel function when ABSPATH is just a slash.
* Fixed bug in RecaptchaV2 (no account) that was not showing correctly due to Google changes.
* RecaptchaV2 modified so that it checks for a loaded recaptcha script before adding a reference to api.js. An environment variable has been added also, so that administrators can force api.js to be referenced.
* Added option in front-end File List Viewer so that all users can delete files that they can view.

= 3.6.0 =
* French translation improved.
* added Code Hooks feature in Dashboard so that filters and actions can be implemented without the need for 3rd party plugins.
* correction of minor bug at wfu_functions.php.
* code improvements in upload algorithm.
* wp_check_filetype_and_ext check moved after completion of file.
* added wfu_after_file_complete filter that runs right after is fully uploaded.
* improved appearance of plugin's area in Dashboard.

= 3.5.0 =
* textdomain changed to wp-file-upload to support the translation feature of wordpress.org.
* added ability to upload files to a Dropbox account.
* added dropbox attribute to enable or disable Dropbox uploads.
* added dropboxpath attribute to define destination directory in Dropbox account.
* added dropboxlocal attribute to determine if the local file will be deleted or kept after it has been transferred to Dropbox.
* added option in Maintenance Actions of plugin's area in Dashboard to export uploaded file data.
* added pagination of non-admin logged user's Uploaded Files Browser.
* added pagination of front-end File List Viewer.
* added pagination attribute to enable or disable File List Viewer pagination.
* added pagerows attribute to define number of rows per page in File List Viewer.
* added pagination of user permissions table in plugin's Settings.
* added pagination of Log Viewer.
* corrected bug in View Log that was not working when pressing on the link.
* improvements to View Log feature.
* improvements to file download function to avoid corruption of downloaded file due to set_time_limit function that may generate warnings.
* added wfu_before_frontpage_scripts filter that executes right before frontpage scripts and styles are loaded.
* added functionality to resolve incompatibilities with NextGen Gallery plugin.

= 3.4.1 =
* plugin's security improved to reject files that contain .php.js or similar extensions.
* wfu_get_latest_version function of free version modified not to ping wp.org.

= 3.4.0 =
* added fitmode attribute to make the plugin responsive.
* added widget "Wordpress File Upload Form", so that the uploader can be installed in a sidebar.
* changes to Shortcode Composer so that it can edit plugin instances existing in sidebars as widgets.
* changes to Uploader Instances in plugin's area in Dashboard to show also instances existing inside sidebars.
* added the ability to define dimensions (width and height) for the whole plugin.
* dimensioning of plugin's elements improved when fitmode is set to "responsive".
* filter and non-object warnings of front-end file browser, appearing when DEBUG mode is ON, removed.
* bug fixed to front-end file browser to hide Shortcode Composer button for non-admin users.
* logic changed to front-end file browser to allow users to download files uploaded by other users.
* code changed to front-end file browser to show a message when a user attempts to delete a file that was not uploaded by him/her.

= 3.3.1 =
* bug corrected that was breaking plugin operation for php versions prior to 5.3.
* added a "Maintenance Actions" section in plugin's Dashboard page.
* added option in plugin's "Maintenance Actions" to completely clean the database log.

= 3.3.0 =
* userdatalabel attribute changed to allow many field types.
* added the following user data field types: simple text, multiline text, number, email, confirmation email, password, confirmation password, checkbox, radiobutton, date, time, datetime, listbox and dropdown list.
* added several options to configure the new user data fields: label text (to define the label of the field), label position (to define the position of the label in relation to the field), required option (to define if the field needs to be filled before file upload), do-not-autocomplete option (to prevent the browsers for completing the field automatically), validate option (to perform validity checks of the field before file upload depending on its type), default text (to define a default value), group id (to group fields together such as multiple radio buttons), format text (to define field formatting depending on the field type), typehook option (to enable field validation during typing inside the field), hint position (to define the position of the message that will be shown to prompt the user that a required field is empty or is not validated) as well as an option to define additional data depending on the field type (e.g. define list of items of a listbox or dropdown list).
* Shortcode Composer changed to support the new user data fields and options.
* placement attribute can accept more than one instances of userdata.
* fixed bug not showing date selector of date fields in Shortcode Composer when working with Firefox or IE browsers.
* in some cases required userdata input field will turn red if not populated.
* shortcode_exists and wp_slash fixes for working before 3.6 Wordpress version.
* minor bug fixes.

= 3.2.1 =
* added filtering capabilities in front-end browser, per role, user, size, date, extension, pageid, blogid and userdata.
* added two more columns in front-end browser, users and post/pages.
* removed 'form-field' class from admin table tr elements.
* corrected bug that was causing problems in uploadrole and uploaduser attributes when a username or role contained uppercase letters.
* uploadrole and uploaduser attributes logic modified; guests are allowed only if 'guests' word is included in the attribute.
* modifications to the download functionality script to be more robust.
* corrected bug that was not showing options below a line item of admin tables in Internet Explorer.

= 3.2.0 =
* added option in plugin's settings to relax CSS rules so that plugin inherits theme styling.
* modifications in html and css of editable subfolders feature to look better.
* modifications in html and css of prompt message when a required userdata field is empty.
* PLUGINDIR was replaced by WP_PLUGIN_DIR so that the plugin can work for websites where the contents dir is other than wp-content.
* fixed bug that was not allowing Shortcode Composer to launch when the shortcode was too big.
* fixed bug that was causing front-end file list not to work properly when no instance of the plugin existed in the same page / post.

= 3.1.2 =
* important bug fixed that was stripping slashes from post or page content when updating the shortcode using the shortcode composer.

= 3.1.1 =
* the previous version broke the easy creation of shortcodes through the plugin's settings in Dashboard and it has been corrected, together with some improvements.

= 3.1.0 =
* added front-end file list for logged users and visitors, activated through a wordpress_file_upload_browser shortcode. Users can see the files they have uploaded in a table view.
* added ability to define which roles and users are allowed to view the file list, including visitors.
* added ability to select which columns to show in file list and in which order (increment, file name, size, date and user data columns available).
* added ability to sort the files in the front-end file list per filename, size or date, in ascending or descending order.
* added ability to change the column titles of the file list using the Shortcode Composer (or straight through the file list's shortcode).
* the front-end file list is updated dynamically when new files get uploaded from the same page using the plugin, or deleted from the file list.
* added file list instances table in plugin's settings in Dashboard.
* added Shortcode Composer for file list shortcode.
* added port number support for uploads using ftp mode.
* corrected bug that was not showing correctly in file browser files that were uploaded using ftp mode.
* eliminated confirmbox warning showing in page when website's DEBUG mode is ON.
* eliminated warning: "Invalid argument supplied for foreach() in ...plugins/wordpress-file-upload-pro/lib/wfu_admin.php on line ...".
* eliminated warning: "Notice: Undefined index: postmethod in /var/www/wordpress/wp-content/plugins/wordpress-file-upload-pro/lib/wfu_functions.php on line ...".
* eliminated warnings in plugin's settings in Dashboard.

= 3.0.0 =
* major version number has advanced because an important feature has been added; logged users can browse their uploaded files through their Dashboard.
* several code modifications in file browser to make the plugin more secure against hacking.
* some functionalities in file browser of administrator have slightly changed, now file browser cannot edit files that were not uploaded with the plugin and it cannot edit or create folders.
* upload path cannot be outside the wordpress installation root.
* files with extension php, js, pht, php3, php4, php5, phtml, htm, html and htaccess are forbidden for security reasons.

= 2.7.6 =
* added functionality in Dashboard to add the plugin to a page automatically.
* fixed bug that was not showing the Shortcode Composer because the plugin could not find the plugin instance when the shortcode was nested in other shortcodes.

= 2.7.5 =
* added uploaduser atribute.
* added German and Greek translations.

= 2.7.4 =
* added Serbian translation thanks to Andrijana Nikolic from http://webhostinggeeks.com/.
* bug fix with %blogid%, %pageid% and %pagetitle% that where not implemented in notification emails.
* in single button operation selected files are removed in case that a subfolder has not been previously selected or a required user field has not been populated.
* bug fixed in single file operation that allowed selection of multiple files through drag-and-drop.
* bug fixed with files over 1MB that got corrupted when maintaining files with same filename.
* dummy (test) Shortcode Composer button removed from the plugin's Settings as it is no longer useful.
* added support for empty (zero size) files.
* many code optimizations and security enhancements.
* fixed javascript errors in IE8 that were breaking upload operation.
* code improvements to avoid display of session warnings.
* added %username% in redirect link.
* added option in plugin's Settings in Dashboard to select alternative POST Upload method, in order to resolve errors like "http:// wrapper is disabled in the server configuration by allow_url_fopen" or "Call to undefined function curl_init()".
* added filter action wfu_after_upload, where the admin can define additional javascript code to be executed on user's browser after each file is finished.

= 2.7.3 =
* corrected serious bug that was erasing captcha keys from plugin's settings when opening the shortcode composer.
* added wfu_before_email_notification filter.
* corrected bug not showing correctly special characters (double quotes and braces) in email notifications.

= 2.7.2 =
* fixed serious bug that prevented correct operation of the plugin when using the new Google Recaptcha.
* minor improvements in free version.

= 2.7.1 =
* fixed bug with faulty plugin instances appearing when Woocommerce plugin is also installed.
* upload of javascript (.js) files is not allowed for avoiding security issues.
* fixed bug with medialink and postlink attributes that were not working correctly.
* when medialink or postlink is activated, the files will be uploaded to the upload folder of WP website.
* when medialink or postlink is activated, subfolders will be deactivated.
* added option in subfolders to enable the list to populate automatically.
* added option in subfolders the user to be able to type the subfolder.
* wfu_before_file_check filter can modify the target path (not only the file name).
* added option in subfolders to enable the list to populate automatically.
* php version info added in plugin settings and a warning if it is very old.
* fixed bug with RecaptchaV2 (no account) width that was cropped when prompting to select correct images.
* minor captcha css modifications.

= 2.7.0 =
* added the new Google Recaptcha version 2 as the default captcha.
* added option to allow use of Google Recaptcha version 2, even when the user does not have a Google account.
* corrected bug when deleting plugin instance from the Dashboard.
* confirmbox option removed from placements attribute because it is an experimental feature, not yet active.
* corrected captcha warnings when using https, according to Cyrus Leung suggestion.
* corrected bug not finding "loading_icon.gif" in Free version.

= 2.6.0 =
* full redesign of the upload algorithm to become more robust including internal auto-resuming capabilities; the upload will continue even if there are connection problems or interruptions.
* added internal option to impose maximum number of concurrent chunks per file.
* added internal option to impose maximum number of concurrent file uploads.
* improved server-side handling of large files; provisions for file spanning added.
* large files that are uploaded in chunks will get a temporary name until they finish.
* plugin shortcodes can be edited visually using the Shortcode Composer.
* added visual editor button on the plugin to enable administrators to change the plugin settings easily.
* corrected bug causing sometimes database overloads.
* slight improvements of subfolder option.
* improvements to avoid code breaking in ajax calls when there are php warnings or echo from Wordpress environment or other plugins.
* improvements and bug fixes in uploader when classic (no AJAX) upload is selected.
* eliminated php and javascript warnings generated by the plugin.
* corrected bug that was not correctly downloading files from the plugin's File Browser.
* added better security when downloading files from the plugin's File Browser.
* fixed bug not correctly showing the user that uploaded a file in the plugin's File Browser.
* use of curl to perform server http requests was replaced by native php because some web servers do not have CURL installed.
* corrected bug in shortcode composer where userdata fields were not shown in variables drop down.
* added feature that prevents page closing if an upload is on progress.
* added forcefilename attribute to avoid filename sanitization.
* added ftppassivemode attribute for enabling FTP passive mode when FTP method is used for uploading.
* added ftpfilepermissions attribute for defining the permissions of the uploaded file, when using FTP method.
* added internal feature to delete unfinished chunked files that have been aborted.
* files over 2GB will be restricted on 32bit servers because of OS and php problems handling them; a future version will contain a self-check capabilitity to detect if the server can handle files over 2GB.
* Captcha modifications to work with internal auto-resuming capabilities. A new captcha will be implemented on the next release of the plugin.
* javascript and css files are minified for faster loading.

= 2.5.5 =
* fixed serious bug not uploading files when captcha is enabled.
* fixed bug not redirecting files when email notification is enabled.

= 2.5.4 =
* mitigated issue with "Session failed" errors appearing randomly in websites.
* fixed bug not applying %filename% variable inside redirect link.
* fixed bug not applying new filename, which has been modified with wfu_before_file_upload filter, in email notifications and redirects.
* fixed bug where when 2 big files were uploaded at the same time and one failed due to failed chunk, then the progress bar would not go to 100% and the file would not be shown as cancelled.

= 2.5.3 =
* fixed bug not allowing redirection to work.
* fixed bug that was including failed files in email notifications on certain occasions.
* default value for uploadrole changed to "all".

= 2.5.2 =
* fixed bug in free version not correctly showing message after failed upload.

= 2.5.1 =
* fixed important bug in free version giving the same name to all uploaded files.
* fixed bug in free version not clearing completely the plugin cache from previous file upload.

= 2.5.0 =
* major redesign of upload algorithm to address upload issues with Safari for Mac and Firefox.
* files are first checked by server before actually uploaded, in order to avoid uploading of large files that are invalid.
* modifications to progress bar code to make progress bar smoother.
* restrict upload of .php files for security reasons.
* fixed bug not showing correctly userdata fields inside email notifications when using ampersand or other special characters in userdata fields.

= 2.4.6 =
* Variables %blogid%, %pageid% and %pagetitle% added in email notifications and subject and %dq% in subject.
* Corrected bug that was breaking Shortcode Composer when using more than ten attributes.
* Option for very large (chunk) file uploads is now by default enabled.
* Added check for new versions in plugin Settings for Professional version.
* Added prompt in plugin Settings for free version to upgrade to Professional version.
* Added Pro to pro version folder and plugin name in order to distinguish from free version.
* Corrected gallery options title in shortcode composer.
* Corrected bug that was rejecting file uploads when uploadpattern attribute contained blank spaces.
* Several code corrections in order to eliminate PHP warning messages when DEBUG mode is on.
* Several code corrections in order to eliminate warning messages in Javascript.

= 2.4.5 =
* Correction of bug when using userfields inside notifyrecipients.

= 2.4.4 =
* Intermediate update to make the plugin more immune to hackers.

= 2.4.3 =
* Correction of bug to allow uploadpath to receive userdata as parameter.

= 2.4.2 =
* Intermediate update to address some vulnerability issues.

= 2.4.1 =
* Added filters and actions before and after each file upload - check Filters/Actions section for instructions how to use them.
* Added storage of file info, including user data, in database.
* Added logging of file actions in database - admins can view the log from the Dashboard.
* Admins can automatically update the database to reflect the current status of files from the Dashboard.
* File browser improvements so that more information about each file (including any user data) are shown.
* File browser improvements so that files can be downloaded.
* Filelist improvements to display correctly long filenames.
* Filelist improvements to distinguish successful uploads from failed uploads.
* Improvements of chunked uploads so that files that are not allowed to be uploaded are cancelled faster.
* Corrected wrong check of file size limit for chunked files.
* Added postlink attribute so that uploaded files are linked to the current page (or post) as attachments.
* Added subfolderlabel attribute to define the label of the subfolder selection feature.
* Several improvements to subfolder selection feature.
* Default value added to subfolder selection feature.
* Definition of the subfoldertree attribute in the Shortcode Composer is now done visually.
* Variable %userid% added inside uploadpath attribute.
* Userdata variables added inside uploadpath and notifyrecipients attributes.
* uploadfolder_label added to dimension items.
* User fields feature improvements.
* User fields label and input box dimensions are customizable.
* Captcha prompt label dimensions are customizable.
* Added gallery attribute to allow the uploaded files to be shown as image gallery below the plugin.
* Added galleryoptions attribute to define options of the image gallery.
* Added css attribute and a delicate css editor inside Shortcode Composer to allow better styling of the plugin using custom css.
* Email feature improved in conjunction with redirection.
* Improved interoperability with WP-Filebase plugin.
* Improved functionality of free text attributes (like notifymessage or css) by allowing double-quotes and brackets inside the text (using special variables), that were previously breaking the plugin.

= 2.3.1 =
* Added option to restore default value for each attribute in Shortcode Composer.
* Added support for multilingual characters.
* Correction of bug in Shortcode Composer that was not allowing attributes with singular and plural form to be saved.
* Correction of bug that was not changing errormessage attribute in some cases.

= 2.2.3 =
* Correction of bug that was freezing the Shortcode Composer in some cases.
* Correction of bug with successmessage attribute.

= 2.2.2 =
* Serious bug fixed that was breaking operation of Shortcode Composer and File Browser when the Wordpress website is in a subdirectory.

= 2.2.1 =
* Added file browser in Dashboard for admins.
* Added attribute medialink to allow uploaded files to be shown in Media.
* Serious bug fixed that was breaking the plugin because of preg_replace_callback function.
* Corrected error in first attempt to upload file when captcha is enabled.

= 2.1.3 =
* Variables %pagetitle% and %pageid% added in uploadpath.
* Bug fixes when working with IE8.
* Shortcode Composer saves selected options.
* Easier handling of userdata variables in Shortcode Composer.
* Correction of bug that allowed debugdata to be shown in non-admin users.
* The CSS file reset.css removed from plugin as it was causing breaks in theme's css.
* Correction of bug with WPFilebase Manager plugin.

= 2.1.2 =
* Several bug fixes and code reconstruction.
* Code modifications so that the plugin can operate even when DEBUG mode is ON.
* New attribute debugmode added to allow better debugging of the plugin when there are errors.
* Correct bugs with captcha and chunked uploads.
* Improve progress bar performance.

= 2.1.1 =
* Bug fixes with broken images when Wordpress website is in a subdirectory.
* Replacement of glob function because is not allowed by some servers.
* Addition of chunk attribute to enable uploading of very large files.

= 2.0.2 =
* Bug fixes in Dashboard Settings Shortcode Composer.
* Correction of important bug that was breaking page in some cases.
* Minor improvements of user data fields and notification email attributes.

= 2.0.1 =
* Name of the plugin changed to Wordpress File Upload.
* Plugin has been completely restructured to allow additional features.
* A new more advanced message box has been included showing information in a more structured way.
* Error detection and reporting has been improved.
* An administration page has been created in the Dashboard Settings, containing a Shortcode Composer.
* Some more options related to configuration of message showing upload results have been added.
* Several bug fixes.

== Upgrade Notice ==

= 4.19.0 =
Regular update to fix some bugs and introduce some new features.

= 4.18.1 =
Regular update to fix some bugs and introduce some new features and improvements.

= 4.18.0 =
Regular update to introduce some new features.

= 4.17.0 =
Regular update to introduce some code improvements and new features.

= 4.16.4 =
Minor update to address some security issues.

= 4.16.3 =
Regular update to fix some bugs and address some security issues.

= 4.16.2 =
Minor update to fix some bugs.

= 4.16.1 =
Regular update to fix some bugs and introduce some code improvements.

= 4.16.0 =
Regular update to fix some bugs and introduce some code improvements.

= 4.15.0 =
Regular update to fix some bugs and introduce some code improvements.

= 4.14.4 =
Regular update to fix some bugs and introduce some code improvements.

= 4.14.3 =
Regular update to fix some bugs and introduce some code improvements.

= 4.14.2 =
Regular update to fix some bugs and introduce some new features.

= 4.14.1 =
Regular update to fix some bugs and introduce some new features.

= 4.14.0 =
Very important update to introduce new auto-updater and some code improvements.

= 4.13.1 =
Significant update to fix some bugs and security vulnerabilities.

= 4.13.0 =
Significant update to fix some bugs and security vulnerabilities.

= 4.12.2 =
Minor update to fix some bugs.

= 4.12.1 =
Minor update to fix some bugs.

= 4.12.0 =
Significant update to introduce some improvements, new features and fix some bugs.

= 4.11.2 =
Minor update to introduce some improvements.

= 4.11.1 =
Minor update to introduce some improvements and fix some bugs.

= 4.11.0 =
Significant update to introduce some improvements and fix some bugs.

= 4.10.3 =
Minor update to introduce some improvements and fix some bugs.

= 4.10.2 =
Minor update to introduce some improvements and fix some bugs.

= 4.10.1 =
Regular update to introduce some new features and improvements.

= 4.10.0 =
Regular update to introduce some new features and improvements.

= 4.9.1 =
Regular update to introduce some new features and improvements and fix some bugs.

= 4.9.0 =
Significant update to introduce some new features and improvements and fix some bugs.

= 4.8.0 =
Significant update to introduce some new features and improvements and fix some bugs.

= 4.7.0 =
Significant update to introduce some new features and improvements and fix some bugs.

= 4.6.2 =
Minor update to fix some bugs and introduce some code improvements.

= 4.6.1 =
Regular update to introduce some new features.

= 4.6.0 =
Significant update to introduce some new features.

= 4.5.1 =
Minor update to introduce some new features.

= 4.5.0 =
Significant update to introduce new features and fix some bugs.

= 4.4.0 =
Significant update that enables wider web server compatibility.

= 4.3.4 =
Minor update to fix a serious security hole.

= 4.3.3 =
Minor update to fix a serious security hole.

= 4.3.2 =
Minor update to fix some bugs.

= 4.3.1 =
Minor update to introduce a new feature.

= 4.3.0 =
Significant update to introduce some new features and fix some bugs.

= 4.2.0 =
Significant update to introduce some new features and fix some bugs.

= 4.1.0 =
Significant update to fix several bugs and introduce some new features.

= 4.0.1 =
Minor update to fix some bugs.

= 4.0.0 =
Major update to introduce new features, code improvements and fix some bugs.

= 3.11.0 =
Update to introduce some new features and fix some bugs.

= 3.10.0 =
Update to introduce some new features and fix some bugs.

= 3.9.6 =
Update to introduce some new features.

= 3.9.5 =
Update to introduce some new features and fix some minor bugs.

= 3.9.4 =
Update to introduce some new features and fix some bugs.

= 3.9.3 =
Update to introduce some new features and fix some bugs.

= 3.9.2 =
Significant update to improve a temporary fix to an important problem and fix some minor bugs.

= 3.9.1 =
Significant update to introduce a temporary fix to an important problem.

= 3.9.0 =
Significant update to increase the security of the plugin and address potential threats.

= 3.8.5 =
Upgrade to introduce some new features and code improvements.

= 3.8.4 =
Upgrade to fix some bugs.

= 3.8.3 =
Minor upgrade to fix some bugs.

= 3.8.2 =
Minor upgrade to fix some bugs and introduce some new features.

= 3.8.1 =
Minor upgrade to fix some bugs.

= 3.8.0 =
Significant upgrade to introduce some new features and fix some bugs.

= 3.7.3 =
Upgrade to introduce some improvements and new languages.

= 3.7.2 =
Upgrade to introduce some new features and fix some minor bugs.

= 3.7.1 =
Upgrade to fix some minor bugs.

= 3.7.0 =
Upgrade to introduce some new features and fix some minor bugs.

= 3.6.1 =
Upgrade to introduce some new features and fix some minor bugs.

= 3.6.0 =
Upgrade to introduce some new features and fix some minor bugs.

= 3.5.0 =
Important upgrade to introduce some new features and fix some bugs.

= 3.4.1 =
Important upgrade to address a security hole.

= 3.4.0 =
Important upgrade to introduce some new features and fix some bugs.

= 3.3.1 =
Important upgrade to correct a bug of the previous version and introduce a new feature.

= 3.3.0 =
Major upgrade to add some new featuresand fix some minor bugs.

= 3.2.1 =
Upgrade to fix some bugs and add some features.

= 3.2.0 =
Upgrade to fix some bugs and add some features.

= 3.1.2 =
Upgrade to fix an important bug.

= 3.1.1 =
Upgrade to fix a minor bug.

= 3.1.0 =
Upgrade to fix some minor bugs.

= 3.0.0 =
Upgrade to increase protection against hacking.

= 2.7.6 =
Upgrade to add some new features and address some bugs.

= 2.7.5 =
Upgrade to add some new features.

= 2.7.4 =
Upgrade to add some new features and address some bugs.

= 2.7.3 =
Upgrade to add some new features and address some bugs.

= 2.7.2 =
Upgrade to address some bugs.

= 2.7.1 =
Upgrade to add some new features and address some bugs.

= 2.7.0 =
Upgrade to address some minor bugs.

= 2.6.0 =
Important upgrade to add new features and address some bugs.

= 2.5.5 =
Important upgrade to address some bugs.

= 2.5.4 =
Important upgrade to address some bugs.

= 2.5.3 =
Important upgrade to address some bugs.

= 2.5.2 =
Important upgrade to address some bugs.

= 2.5.1 =
Important upgrade to address some bugs.

= 2.5.0 =
Important upgrade to address some bugs.

= 2.4.6 =
Important upgrade to address some bugs.

= 2.4.5 =
Minor upgrade to address some bugs.

= 2.4.4 =
Important upgrade to address some vulnerability issues.

= 2.4.3 =
Upgrade to address some functionality issues.

= 2.4.2 =
Important upgrade to address some vulnerability issues.

= 2.4.1 =
Upgrade to add many features and address some minor bugs.

= 2.3.1 =
Upgrade to add some features and address some minor bugs.

= 2.2.3 =
Upgrade to address some minor bugs.

= 2.2.2 =
Important upgrade to address some serious bugs.

= 2.2.1 =
Important upgrade to address some serious bugs and include some new features.

= 2.1.3 =
Important upgrade to address some serious bugs.

= 2.1.2 =
Important upgrade to address some bugs.

= 2.1.1 =
Important upgrade to address some serious bugs.

= 2.0.2 =
Important upgrade to address some serious bugs.

= 2.0.1 =
Optional upgrade to add new features.

== Plugin Customization Options ==

Please visit the [support page](http://www.iptanus.com/support/wordpress-file-upload/ "Wordpress File Upload support page") of the plugin for detailed description of customization options.

== Requirements ==

The plugin requires to have Javascript enabled in your browser. For Internet Explorer you also need to have Active-X enabled.
Please note that old desktop browsers or mobile browsers may not support all of the plugin's features. In order to get full functionality use the latest versions of browsers, supporting HTML5, AJAX and CSS3.
