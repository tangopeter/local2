<?php

/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */

function hello_elementor_child_enqueue_scripts()
{
  wp_enqueue_style(
    'hello-elementor-child-style',
    get_stylesheet_directory_uri() . '/style.css',
    [
      'hello-elementor-theme-style',
    ],
    '1.0.0'
  );
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20);


wp_register_script(
  'currency',
  plugin_dir_url(__FILE__) . 'currency.js',
  array('jQuery'),
  null,
  true
);
wp_enqueue_script('currency');
