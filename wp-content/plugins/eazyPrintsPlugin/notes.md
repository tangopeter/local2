class wpdb {};
https://developer.wordpress.org/reference/classes/wpdb/
https://oracle-patches.com/en/web/working-with-a-database-in-wordpress-by-examples

PHP Basics for WordPress - A Beginners Guide to WordPress PHP
https://www.youtube.com/watch?v=mgGAvq9hmyU


https://hookturn.io/custom-wordpress-sql-queries-for-beginners/

https://www.smashingmagazine.com/2011/09/interacting-with-the-wordpress-database/

How to Execute a PHP Function on Button Click:
https://www.youtube.com/watch?v=GUcN9xRpO7U

https://code.tutsplus.com/tutorials/how-to-pass-php-data-and-strings-to-javascript-in-wordpress--wp-34699

https://www.youtube.com/watch?v=DXK9XDN9puY Pass JavaScript Variable to PHP in Wordpress

https://www.advancedcustomfields.com/resources/javascript-api/
  wp_register_script('acf', plugin_dir_url(__FILE__) . 'assets/build/js/acf-input.js', array('acf-input'));
  wp_enqueue_script('acf');

  jQuery(document).ready(function ($) {
  if (typeof acf !== 'undefined') {
    console.log('ACF is defined', acf);
  }
  else {
    console.log('ACF not defined', acf);
  }
});