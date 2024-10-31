<?php
defined('RY_CHL_VERSION') OR exit('No direct script access allowed');

class RY_CHL_admin {
	private static $initiated = false;
	
	public static function init() {
		if( !self::$initiated ) {
			self::$initiated = true;

			require_once(RY_CHL_PLUGIN_DIR . 'class.admin-html.php');

			add_action('admin_init', array(__CLASS__, 'admin_init'));
			add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_style'));

			add_filter('plugin_action_links', array(__CLASS__, 'plugin_action_links'), 10, 2);
			add_action('admin_menu', array(__CLASS__, 'admin_menu'));

			add_action('add_meta_boxes', array(__CLASS__, 'post_metabox'), 10, 2);
			
			add_action('save_post', array(__CLASS__, 'save_code_meta'), 10, 2);
		}
	}

	public static function admin_init() {
		load_plugin_textdomain(RY_CHL::$textdomain, false, dirname(RY_CHL_PLUGIN_BASENAME) . '/languages');
	}

	public static function admin_style($hook) {
		if( $hook == 'post.php' || $hook == 'post-new.php' ) {
			wp_enqueue_style('ry-admin-styles', RY_CHL_PLUGIN_URL . 'css/admin.css');
		}
	}

	public static function plugin_action_links($links, $file) {
		if( $file == RY_CHL_PLUGIN_BASENAME ) {
			$links[] = '<a href="options-general.php?page=ry-code-highlight&tab=base">' . __('Settings', RY_CHL::$textdomain) . '</a>';
		}
		return $links;
	}

	public static function admin_menu() {
		add_submenu_page('options-general.php', 'RY Code Highlight', __('RY Code Highlight', RY_CHL::$textdomain), 'manage_options', 'ry-code-highlight', array(__CLASS__, 'setting_page'));
	}

	public static function setting_page() {
		$tab = $_GET['tab'];
		if( !in_array($tab, array('base', 'info')) ) {
			$tab = 'base';
		}

		RY_CHL_admin_html::setting_page_header($tab);
		switch( $tab ) {
			case 'base':
				if( !empty($_POST['ry_Update_setting']) ) {
					self::save_base_setting();
				}
				RY_CHL_admin_html::show_base_setting_page();
				break;
			case 'info':
				RY_CHL_admin_html::show_info_page();
				break;
		}
		RY_CHL_admin_html::setting_page_footer();
	}

	protected static function save_base_setting() {
		RY_CHL::update_option('use_cdn', intval($_POST['ry_use_cdn']) ? 1 : 0);
		RY_CHL::update_option('use_code_shortcode', intval($_POST['ry_use_code_shortcode']) ? 1 : 0);
		RY_CHL::update_option('color_theme', $_POST['ry_color_theme']);
		$info = '<div id="message" class="updated"><p>' . __('Updated Base Options Success', RY_CHL::$textdomain) . '</p></div>';
		echo $info;
	}

	public static function post_metabox($post_type, $post) {
		add_meta_box('ry-code-metabox', __('Code Box', RY_CHL::$textdomain), array('RY_CHL_admin_html', 'code_metabox'), 'post', 'normal', 'default');
	}
	
	public static function save_code_meta($post_ID, $post) {
		if( isset($_POST['codemeta']) && count($_POST['codemeta']) ) {
			foreach( $_POST['codemeta'] as $meta_id => $info ) {
				if( !empty($info['name']) ) {
					$meta_id = (int) $meta_id;
					$value = array(
						'type' => $info['type'],
						'code' => htmlspecialchars(stripslashes($info['code']))
					);
					if( $meta_id == 0 ) {
						RY_CHL::update_post_meta($post_ID, $info['name'], $value);
					} else {
						RY_CHL::update_metadata_by_mid('post', $meta_id, $value, $info['name']);
					}
				}
			}
		}
	}
}
