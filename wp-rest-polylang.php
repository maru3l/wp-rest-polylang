<?php
/**
 * Plugin Name: WP REST - Polylang
 * Description: Polylang integration for the WP REST API
 * Author: Marc-Antoine Ruel
 * Author URI: https://www.marcantoineruel.com
 * Version: 1.0.0
 * Plugin URI: https://github.com/maru3l/wp-rest-polylang
 * License: gpl-3.0
 */
function WP_REST_polylang_init() {
    global $polylang;

    if (isset($_GET['lang'])) {
		$current_lang = $_GET['lang'];

		$polylang->curlang = $polylang->model->get_language($current_lang);
	}
}

add_action('rest_api_init', 'WP_REST_polylang_init');

function WP_REST_polylang_prepare_post($data, $post, $context) {
	$data->data['lang'] = (string) pll_get_post_language($post->ID, 'locale');

	$data->data['translations'] = (array) pll_get_post_translations($post->ID);

	return $data;
}

add_filter('rest_prepare_post', 'WP_REST_polylang_prepare_post', 10, 3);

add_filter('rest_prepare_page', 'WP_REST_polylang_prepare_post', 10, 3);
