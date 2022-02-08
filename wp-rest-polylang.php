<?php
/**
 * Plugin Name: WP REST - Polylang
 * Description: Polylang integration for the WP REST API
 * Author: Marc-Antoine Ruel
 * Author URI: https://www.marcantoineruel.com
 * Version: 1.1.0
 * Plugin URI: https://github.com/maru3l/wp-rest-polylang
 * License: gpl-3.0
 */


class WP_REST_polylang
{

	static $instance = false;

	private function __construct() {
		// Check if polylang is installed
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		if (!is_plugin_active('polylang/polylang.php')) {
			return;
		}

		add_action('rest_api_init', array($this, 'init'), 0);
	}

	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

	public function init() {
		global $polylang;

		if (isset($_GET['lang'])) {
			$current_lang = $_GET['lang'];

			$polylang->curlang = $polylang->model->get_language($current_lang);
		}

		$post_types = get_post_types( array( 'public' => true ), 'names' );
		
		foreach( $post_types as $post_type ) {
			if (pll_is_translated_post_type( $post_type )) {
				self::register_api_field($post_type);
			}
		}

		$taxonomies = get_taxonomies(['show_in_rest' => true], 'names');

		foreach ($taxonomies as $taxonomy) {
			if (pll_is_translated_taxonomy($taxonomy)) {
				 self::register_api_taxonomy($taxonomy);
			}
	  }
	}

	public function register_api_field($post_type) {
		register_rest_field(
			$post_type,
			"polylang_current_lang",
			array(
				"get_callback" => array( $this, "get_current_lang" ),
				"schema" => null
			)
		);

		register_rest_field(
			$post_type,
			"polylang_translations",
			array(
				"get_callback" => array( $this, "get_translations"  ),
				"schema" => null
			)
		);
	}

	public function register_api_taxonomy($taxonomy)
	{
		 register_rest_field(
			  $taxonomy,
			  "polylang_current_lang",
			  array(
					"get_callback" => array($this, "get_current_taxonomy_lang"),
					"schema" => null,
			  )
		 );
	}

	public function get_current_lang( $object ) {
		return pll_get_post_language($object['id'], 'locale');
	}

	public function get_current_taxonomy_lang($object)
	{
		return pll_get_term_language($object['id'], 'locale');
	}

	public function get_translations( $object ) {
		$translations = pll_get_post_translations($object['id']);

		return array_reduce($translations, function ($carry, $translation) {
			$item = array(
				'locale' => pll_get_post_language($translation, 'locale'),
				'id' => $translation
			);

			array_push($carry, $item);

			return $carry;
		}, array());
	}
}

$WP_REST_polylang = WP_REST_polylang::getInstance();
