<?php

add_filter('_wfu_get_content_shortcodes', 'wfu_get_elementor_content_shortcodes', 10, 3);
add_filter('_wfu_get_post_content', 'wfu_get_elementor_post_content', 10, 2);
add_filter('_wfu_add_shortcode', 'wfu_add_elementor_shortcode', 10, 3);
add_filter('_wfu_replace_shortcode', 'wfu_replace_elementor_shortcode', 10, 3);
add_filter('_wfu_can_open_composer', 'wfu_can_open_composer_in_elementor', 10, 2);
add_filter('_register_wfu_widget', 'register_wfu_widget_for_elementor', 10, 1);

function wfu_get_elementor_content_shortcodes($found_shortcodes, $post, $tag) {
	if ( class_exists("Elementor\\Plugin") && /*<wfu_ExTeRnAlReF*/Elementor\Plugin/*wfu_ExTeRnAlReF/>*/::$instance->db->is_built_with_elementor( $post->ID ) ) {
		global $shortcode_tags;
		$found_shortcodes = array();
		$document = /*<wfu_ExTeRnAlReF*/Elementor\Plugin/*wfu_ExTeRnAlReF/>*/::$instance->documents->get( $post->ID );
		$content = get_post_meta( $post->ID, '_elementor_data', true );
		$hash = hash('md5', $content);
		if ( array_key_exists( $tag, $shortcode_tags ) ) wfu_match_shortcode_nested($tag, $post, $hash, $content, 0, $found_shortcodes);
		foreach ( $found_shortcodes as $ind => $data )
			$found_shortcodes[$ind]['shortcode'] = json_decode('"'.$data['shortcode'].'"');
	}
	return $found_shortcodes;
}

function wfu_get_elementor_post_content($content, $post) {
	if ( class_exists("Elementor\\Plugin") && /*<wfu_ExTeRnAlReF*/Elementor\Plugin/*wfu_ExTeRnAlReF/>*/::$instance->db->is_built_with_elementor( $post->ID ) ) {
		$document = /*<wfu_ExTeRnAlReF*/Elementor\Plugin/*wfu_ExTeRnAlReF/>*/::$instance->documents->get( $post->ID );
		$content = get_post_meta( $post->ID, '_elementor_data', true );
	}
	return $content;
}

function wfu_add_elementor_shortcode($result, $postid, $tag) {
	if ( $result == null && class_exists("Elementor\\Plugin") && /*<wfu_ExTeRnAlReF*/Elementor\Plugin/*wfu_ExTeRnAlReF/>*/::$instance->db->is_built_with_elementor( $postid ) ) {
		$document = /*<wfu_ExTeRnAlReF*/Elementor\Plugin/*wfu_ExTeRnAlReF/>*/::$instance->documents->get( $postid );
		$elements = get_post_meta( $postid, '_elementor_data', true );
		if ( is_string( $elements ) && ! empty( $elements ) ) $elements = json_decode( $elements, true );
		if ( empty( $elements ) ) $elements = array();
		$new_elements = sprintf('[{"id":"%s","elType":"section","settings":[],"elements":[{"id":"%s",'.
			'"elType":"column","settings":{"_column_size":100},"elements":[{"id":"%s",'.
			'"elType":"widget","settings":{"shortcode":"[%s]"},'.
			'"elements":[],"widgetType":"shortcode"}],"isInner":false}],"isInner":false}]',
			wfu_create_random_string(7, true),
			wfu_create_random_string(7, true),
			wfu_create_random_string(7, true),
			$tag);
		$new_elements = json_decode( $new_elements, true );
		$elements = array_merge( $new_elements, $elements );
		$settings = $document->get_settings();
		$result = $document->save(array( 'settings' => $settings, 'elements' => $elements ));
	}
	return $result;
}
	
function wfu_replace_elementor_shortcode($result, $data, $new_shortcode) {
	if ( $result == null && class_exists("Elementor\\Plugin") && /*<wfu_ExTeRnAlReF*/Elementor\Plugin/*wfu_ExTeRnAlReF/>*/::$instance->db->is_built_with_elementor( $data['post_id'] ) ) {
		$document = /*<wfu_ExTeRnAlReF*/Elementor\Plugin/*wfu_ExTeRnAlReF/>*/::$instance->documents->get( $data['post_id'] );
		$content = get_post_meta( $data['post_id'], '_elementor_data', true );
		$old_shortcode = preg_replace("/^\"(.*)\"$/", "$1", json_encode($data['shortcode']));
		$new_shortcode = preg_replace("/^\"(.*)\"$/", "$1", json_encode($new_shortcode));
		$new_content = substr($content, 0, $data['position']).$new_shortcode.substr($content, (int)$data['position'] + strlen($old_shortcode));
		$elements = json_decode( $new_content, true );
		$settings = $document->get_settings();
		$result = $document->save(array( 'settings' => $settings, 'elements' => $elements ));
	}
	return $result;
}
	
function wfu_can_open_composer_in_elementor($can_open_composer, $params) {
	global $post;
	return ( $can_open_composer && class_exists("Elementor\\Plugin") && 
		/*<wfu_ExTeRnAlReF*/Elementor\Plugin/*wfu_ExTeRnAlReF/>*/::$instance->db->is_built_with_elementor( $post->ID ) &&
		/*<wfu_ExTeRnAlReF*/Elementor\Plugin/*wfu_ExTeRnAlReF/>*/::$instance->editor->is_edit_mode() ? false : $can_open_composer );
}
	
function register_wfu_widget_for_elementor($processed) {
	global $post;
	return ( !$processed && class_exists("Elementor\\Plugin") ? true : $processed );
}