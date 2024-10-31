<?php
defined('RY_CHL_VERSION') OR exit('No direct script access allowed');

class RY_CHL {
	public static $options = array();
	public static $textdomain = 'ry-code-highlight';
	public static $option_prefix = 'RY_CHL_';
	public static $meta_prefix = 'RY_CHL_';
	public static $shortcode_code_exist = false;

	public static $code_list = array(
		'as3' => array(
			'file' => 'shBrushAS3.js',
			'name' => 'ActionScript3',
		),
		'bash' => array(
			'file' => 'shBrushBash.js',
			'name' => 'Bash/shell',
		),
		'coldfusion' => array(
			'file' => 'shBrushColdFusion.js',
			'name' => 'ColdFusion',
		),
		'csharp' => array(
			'file' => 'shBrushCSharp.js',
			'name' => 'C#',
		),
		'cpp' => array(
			'file' => 'shBrushCpp.js',
			'name' => 'C++',
		),
		'css' => array(
			'file' => 'shBrushCss.js',
			'name' => 'CSS',
		),
		'delphi' => array(
			'file' => 'shBrushDelphi.js',
			'name' => 'Delphi',
		),
		'diff' => array(
			'file' => 'shBrushDiff.js',
			'name' => 'Diff',
		),
		'erlang' => array(
			'file' => 'shBrushErlang.js',
			'name' => 'Erlang',
		),
		'groovy' => array(
			'file' => 'shBrushGroovy.js',
			'name' => 'Groovy',
		),
		'jscript' => array(
			'file' => 'shBrushJScript.js',
			'name' => 'JavaScript',
		),
		'java' => array(
			'file' => 'shBrushJava.js',
			'name' => 'Java',
		),
		'javafx' => array(
			'file' => 'shBrushJavaFX.js',
			'name' => 'JavaFX',
		),
		'perl' => array(
			'file' => 'shBrushPerl.js',
			'name' => 'Perl',
		),
		'php' => array(
			'file' => 'shBrushPhp.js',
			'name' => 'PHP',
		),
		'plain' => array(
			'file' => 'shBrushPlain.js',
			'name' => 'Plain Text',
		),
		'powershell' => array(
			'file' => 'shBrushPowerShell.js',
			'name' => 'PowerShell',
		),
		'python' => array(
			'file' => 'shBrushPython.js',
			'name' => 'Python',
		),
		'ruby' => array(
			'file' => 'shBrushRuby.js',
			'name' => 'Ruby',
		),
		'scala' => array(
			'file' => 'shBrushScala.js',
			'name' => 'Scala',
		),
		'sql' => array(
			'file' => 'shBrushSql.js',
			'name' => 'SQL',
		),
		'vb' => array(
			'file' => 'shBrushVb.js',
			'name' => 'Visual Basic',
		),
		'xml' => array(
			'file' => 'shBrushXml.js',
			'name' => 'Xml',
		)
	);

	private static $initiated = false;
	private static $add_script = false;

	public static function init() {
		if( !self::$initiated ) {
			self::$initiated = true;

			require_once(RY_CHL_PLUGIN_DIR . 'class.update.php');
			RY_CHL_update::update();

			if( is_admin() ) {
				require_once(RY_CHL_PLUGIN_DIR . 'class.admin.php');
				RY_CHL_admin::init();
			} else {
				self::register_script();
				self::register_shortcode();
			}
			
			add_action('wp_footer', array(__CLASS__, 'footer_scripts'), 21);
		}
	}

	public static function register_script() {
		if( RY_CHL::get_option('use_cdn') ) {
			$load_path = '//cdn.jsdelivr.net/syntaxhighlighter/3.0.83/';
		} else {
			$load_path = RY_CHL_PLUGIN_URL . 'js/syntaxhighlighter-3.0.83/';
		}
		wp_register_script('ry-syntaxhighlighter-core', $load_path . 'scripts/shCore.js', array(), '3.0.83', true);
		foreach( self::$code_list as $code_type => $code_info ) {
			wp_register_script('ry-syntaxhighlighter-' . $code_type, $load_path . 'scripts/' . $code_info['file'], array('ry-syntaxhighlighter-core'), '3.0.83', true);
		}

		wp_register_style('ry-syntaxhighlighter-core', $load_path . 'styles/shCore.css', array(), '3.0.83');
		wp_register_style('ry-syntaxhighlighter-theme-default', $load_path . 'styles/shThemeDefault.css', array('ry-syntaxhighlighter-core'), '3.0.83');
		wp_register_style('ry-syntaxhighlighter-theme-django', $load_path . 'styles/shThemeDjango.css', array('ry-syntaxhighlighter-core'), '3.0.83');
		wp_register_style('ry-syntaxhighlighter-theme-eclipse', $load_path . 'styles/shThemeEclipse.css', array('ry-syntaxhighlighter-core'), '3.0.83');
		wp_register_style('ry-syntaxhighlighter-theme-emacs', $load_path . 'styles/shThemeEmacs.css', array('ry-syntaxhighlighter-core'), '3.0.83');
		wp_register_style('ry-syntaxhighlighter-theme-fadetogrey', $load_path . 'styles/shThemeFadeToGrey.css', array('ry-syntaxhighlighter-core'), '3.0.83');
		wp_register_style('ry-syntaxhighlighter-theme-mdultra', $load_path . 'styles/shThemeMDUltra.css', array('ry-syntaxhighlighter-core'), '3.0.83');
		wp_register_style('ry-syntaxhighlighter-theme-midnight', $load_path . 'styles/shThemeMidnight.css', array('ry-syntaxhighlighter-core'), '3.0.83');
		wp_register_style('ry-syntaxhighlighter-theme-rdark', $load_path . 'styles/shThemeRDark.css', array('ry-syntaxhighlighter-core'), '3.0.83');
	}

	public static function register_shortcode() {
		add_shortcode('rycode', array(__CLASS__, 'parse_shortcode'));

		if( RY_CHL::get_option('use_code_shortcode') ) {
			if( shortcode_exists('code') ) {
				self::$shortcode_code_exist = true;
			}
			add_shortcode('code', array(__CLASS__, 'parse_shortcode'));
		}
	}

	public static function parse_shortcode($atts, $content = null, $shortcode) {
		$a = shortcode_atts(array(
			'name' => ''
		), $atts);

		$post_ID = get_the_ID();
		$code_info = RY_CHL::get_post_meta($post_ID, $a['name']);
		if( !empty($code_info) && is_array($code_info) ) {
			self::$add_script = true;		
			wp_enqueue_script('ry-syntaxhighlighter-' . $code_info['type']);
			wp_enqueue_style('ry-syntaxhighlighter-theme-' . RY_CHL::get_option('color_theme'));

			$class_data = array(
				'brush' => $code_info['type']
			);

			if( isset($atts['autolinks']) ) {
				$atts['autolinks'] = self::string_to_boolean($atts['autolinks']);
				$class_data['auto-links'] = $atts['autolinks'] ? 'true' : 'false';
			}

			if( isset($atts['classname']) ) {
				$atts['classname'] = (string) $atts['classname'];
				$class_data['class-name'] = $atts['classname'];
			}

			if( isset($atts['collapse']) ) {
				$atts['collapse'] = self::string_to_boolean($atts['collapse']);
				$class_data['collapse'] = $atts['collapse'] ? 'true' : 'false';
			}

			if( isset($atts['firstline']) ) {
				$atts['firstline'] = (int) $atts['firstline'];
				if( $atts['firstline'] > 1 ) {
					$class_data['first-line'] = $atts['firstline'];
				}
			}

			if( isset($atts['gutter']) ) {
				$atts['gutter'] = self::string_to_boolean($atts['gutter']);
				$class_data['gutter'] = $atts['gutter'] ? 'true' : 'false';
			}

			if( isset($atts['highlight']) ) {
				if( !empty($atts['highlight']) ) {
					if( strpos($atts['highlight'], ',') === false && strpos($atts['highlight'], '-') === false ) {
						$atts['highlight'] = (int) $atts['highlight'];
						if( $atts['highlight'] > 0 ) {
							$class_data['highlight'] = $atts['highlight'];
						}
					} else {
						$lines = array();
						foreach( explode(',', $atts['highlight']) as $key => $value ) {
							if( strpos($value, '-') === false ) {
								$value = (int) $value;
								if( $value > 0 ) {
									$lines[] = $value;
								}
							} else {
								$value = explode('-', $value);
								list($start, $end) = $value;
								$start = (int) $start;
								$end = (int) $end;
								if( $start > 0 && $start < $end ) {
									while( $start <= $end ) {
										$lines[] = $start++;
									}
								}
							}
						}
						if( count($lines) ) {
							$lines = array_unique($lines);
							natsort($lines);
							$class_data['highlight'] = '[' . implode(',', $lines) . ']';
						}
					}
				}
			}

			if( isset($atts['htmlscript']) ) {
				$atts['htmlscript'] = self::string_to_boolean($atts['htmlscript']);
				wp_enqueue_script('ry-syntaxhighlighter-xml');
				$class_data['html-script'] = $atts['htmlscript'] ? 'true' : 'false';
			}

			if( isset($atts['smarttabs']) ) {
				$atts['smarttabs'] = self::string_to_boolean($atts['smarttabs']);
				$class_data['smart-tabs'] = $atts['smarttabs'] ? 'true' : 'false';
			}

			if( isset($atts['tabsize']) ) {
				$atts['tabsize'] = (int) $atts['tabsize'];
				if( $atts['tabsize'] > 0 ) {
					$class_data['tab-size'] = $atts['tabsize'];
				}
			}

			if( isset($atts['toolbar']) ) {
				$atts['toolbar'] = self::string_to_boolean($atts['toolbar']);
				$class_data['toolbar'] = $atts['toolbar'] ? 'true' : 'false';
			}

			foreach( $class_data as $key => $value ) {
				$class_data[$key] = $key . ': ' . $value;
			}

			$content = '<pre class="' . implode(';', $class_data) . '">' . $code_info['code'] . '</pre>';
		}

		return $content;
	}

	public static function footer_scripts() {
		if( self::$add_script ) {
			?>
<script type="text/javascript">
	SyntaxHighlighter.all();
</script>
			<?php
		}
	}

	protected static function string_to_boolean($value) {
		$value = strtolower($value);
		if( 'true' == $value || '1' == $value ) {
			return true;
		}
		return false;
	}

	public static function get_option($option, $default = false) {
		return get_option(self::$option_prefix . $option, $default);
	}

	public static function update_option($option, $value) {
		return update_option(self::$option_prefix . $option, $value);
	}

	public static function get_post_meta($post_id, $key = '', $single = true) {
		return get_post_meta($post_id, self::$meta_prefix . $key, $single);
	}

	public static function update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '') {
		return update_post_meta($post_id, self::$meta_prefix . $meta_key, $meta_value, $prev_value);
	}

	public static function update_metadata_by_mid($meta_type, $meta_id, $meta_value, $meta_key) {
		return update_metadata_by_mid($meta_type, $meta_id, $meta_value, self::$meta_prefix . $meta_key);
	}

	public static function plugin_activation() {
	}

	public static function plugin_deactivation( ) {
	}
}
