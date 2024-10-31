<?php
defined('RY_CHL_VERSION') OR exit('No direct script access allowed');

class RY_CHL_update {
	public static function update() {
		$now_version = RY_CHL::get_option('version');

		if( $now_version === FALSE ) {
			$now_version = '0.0.0';
		}
		if( $now_version == RY_CHL_VERSION ) {
			return;
		}
		if( version_compare($now_version, '1.0.0', '<' ) ) {
			RY_CHL::update_option('use_cdn', 1);
			RY_CHL::update_option('use_code_shortcode', 1);
			RY_CHL::update_option('color_theme', 'default');

			RY_CHL::update_option('version', '1.0.0');
		}
		if( version_compare($now_version, '1.0.2', '<' ) ) {
			RY_CHL::update_option('version', '1.0.2');
		}
	}
}
