<?php
defined('RY_CHL_VERSION') OR exit('No direct script access allowed');

class RY_CHL_admin_html {
	public static function setting_page_header($tab) {
		?>
<div class="wrap">
	<h1><?=__('RY Code Highlight Options', RY_CHL::$textdomain); ?></h1>
	<?php if( RY_CHL::$shortcode_code_exist ) { ?>
		<div class="error notice">
			<p><?=__('Shortcode [code] is added by other plugin.', RY_CHL::$textdomain); ?></p>
		</div>
	<?php } ?>
	<h2 class="nav-tab-wrapper">
		<a href="options-general.php?page=ry-code-highlight&tab=base" class="nav-tab<?=(($tab == 'base') ? ' nav-tab-active' : '') ?>"><?=__('Base Options', RY_CHL::$textdomain); ?></a>
		<a href="options-general.php?page=ry-code-highlight&tab=info" class="nav-tab<?=(($tab == 'info') ? ' nav-tab-active' : '') ?>"><?=__('Info', RY_CHL::$textdomain); ?></a>
	</h2>
		<?php
	}

	public static function setting_page_footer() {
		?>
</div>
		<?php
	}

	public static function show_base_setting_page() {
		?>
<form method="post" action="" novalidate="novalidate">
	<h3><?=__('Global Setting', RY_CHL::$textdomain) ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?=__('Use CDN', RY_CHL::$textdomain); ?></th>
			<td>
				<select name="ry_use_cdn" size="1">
					<option value="0"<?php selected('0', RY_CHL::get_option('use_cdn')); ?>><?=__('disable', RY_CHL::$textdomain); ?></option>
					<option value="1"<?php selected('1', RY_CHL::get_option('use_cdn')); ?>><?=__('enable', RY_CHL::$textdomain); ?></option>
				</select>
				<?php printf(__( 'Javascript and css file load from <a href="%s">jsDelivr</a>.', RY_CHL::$textdomain), 'https://www.jsdelivr.com/'); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?=__('Color Theme', RY_CHL::$textdomain); ?></th>
			<td>
				<select name="ry_color_theme" size="1">
					<option value="default"<?php selected('default', RY_CHL::get_option('color_theme')); ?>>Default</option>
					<option value="django"<?php selected('django', RY_CHL::get_option('color_theme')); ?>>Django</option>
					<option value="eclipse"<?php selected('eclipse', RY_CHL::get_option('color_theme')); ?>>Eclipse</option>
					<option value="emacs"<?php selected('emacs', RY_CHL::get_option('color_theme')); ?>>Emacs</option>
					<option value="fadetogrey"<?php selected('fadetogrey', RY_CHL::get_option('color_theme')); ?>>FadeToGrey</option>
					<option value="mdultra"<?php selected('mdultra', RY_CHL::get_option('color_theme')); ?>>MDUltra</option>
					<option value="midnight"<?php selected('midnight', RY_CHL::get_option('color_theme')); ?>>Midnight</option>
					<option value="rdark"<?php selected('rdark', RY_CHL::get_option('color_theme')); ?>>RDark</option>
				</select>
			</td>
		</tr>
	</table>

	<h3><?=__('Shortcode Setting', RY_CHL::$textdomain) ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><?=__('Use [code] shortcode', RY_CHL::$textdomain); ?></th>
			<td>
				<select name="ry_use_code_shortcode" size="1">
					<option value="0"<?php selected('0', RY_CHL::get_option('use_code_shortcode')); ?>><?=__('disable', RY_CHL::$textdomain); ?></option>
					<option value="1"<?php selected('1', RY_CHL::get_option('use_code_shortcode')); ?>><?=__('enable', RY_CHL::$textdomain); ?></option>
				</select>
			</td>
		</tr>
	</table>

	<p class="submit"><input type="submit" name="ry_Update_setting" class="button-primary" value="<?=__('Save Changes', RY_CHL::$textdomain); ?>" /></p>
</form>
		<?php
	}

	public static function code_metabox($post) {
		$len = strlen(RY_CHL::$meta_prefix);
		$metadata = has_meta($post->ID);
		foreach( $metadata as $key => $value ) {
			if( substr($value['meta_key'], 0, $len) != RY_CHL::$meta_prefix ) {
				unset($metadata[$key]);
			}
		}
		?>
<div id="postcustomstuff">
	<div id="ajax-response"></div>
	<table id="list-table">
		<?php self::_codebox_table_header(); ?>
		<tbody id="the-list" data-wp-lists="list:codemeta">
			<?php foreach( $metadata as $value ) { ?>
				<?php self::_list_code_meta($value); ?>
			<?php } ?>
		</tbody>
	</table>

	<p><strong><?=__('Add New Code', RY_CHL::$textdomain) ?></strong></p>
	<table id="newcodemeta">
		<?php self::_codebox_table_header(); ?>
		<tbody>
			<tr>
				<td>
					<select name="codemeta[0][type]">
						<?php foreach( RY_CHL::$code_list as $code_type => $code_info ) { ?>
							<option value="<?=$code_type ?>"<?php selected($code_type, 'plain'); ?>><?=$code_info['name'] ?></option>
						<?php } ?>
					</select>
					<div class="submit">
						<?=get_submit_button(__( 'Add Code', RY_CHL::$textdomain), '', 'addmeta', false); ?>
					</div>
				</td>
				<td>
					<input type="text" name="codemeta[0][name]">
				</td>
				<td>
					<textarea name="codemeta[0][code]" rows="3" cols="30"></textarea>
				</td>
			</tr>
		</tbody>
	</table>
</div>
		<?php
	}

	protected static function _codebox_table_header() {
		?>
<colgroup>
	<col width="120">
	<col width="30%">
	<col>
</colgroup>
<thead>
	<tr>
		<th><?=__('Code Type', RY_CHL::$textdomain) ?></th>
		<th><?=__('Code Name', RY_CHL::$textdomain) ?></th>
		<th><?=__('Code', RY_CHL::$textdomain) ?></th>
	</tr>
</thead>
		<?php
	}

	protected static function _list_code_meta($value) {
		$name = substr($value['meta_key'], strlen(RY_CHL::$meta_prefix));
		$code = unserialize($value['meta_value']);
		if( !is_array($code) ) {
			$code = array(
				'type' => 'plain',
				'code' => ''
			);
		}
		
		$delete_nonce = wp_create_nonce('delete-meta_' . $value['meta_id']);
		?>
<tr id="codemeta-<?=$value['meta_id'] ?>">
	<td>
		<select name="codemeta[<?=$value['meta_id'] ?>][type]">
			<?php foreach( RY_CHL::$code_list as $code_type => $code_info ) { ?>
				<option value="<?=$code_type ?>"<?php selected($code_type, $code['type']); ?>><?=$code_info['name'] ?></option>
			<?php } ?>
		</select>
		<div class="submit">
			<?=get_submit_button(__('Delete', RY_CHL::$textdomain), 'deletemeta small', "deletecodemeta[{$value['meta_id']}]", false, array('data-wp-lists' => "delete:the-list:codemeta-{$value['meta_id']}::_ajax_nonce=$delete_nonce")); ?>
		</div>
		<?=wp_nonce_field('change-meta', '_ajax_nonce', false, false); ?>
	</td>
	<td>
		<input type="text" name="codemeta[<?=$value['meta_id'] ?>][name]" value="<?=$name ?>">
	</td>
	<td>
		<textarea name="codemeta[<?=$value['meta_id'] ?>][code]" rows="3" cols="30"><?=$code['code'] ?></textarea>
	</td>
</tr>
		<?php
	}

	public static function show_info_page() {
		?>
<h3><?=__('Shortcode', RY_CHL::$textdomain) ?></h3>
<p><?=__('Use [rycode name="codebox_name"] in you post content to show the code.', RY_CHL::$textdomain) ?></p>
<p><?=__('You can also use [code name="codebox_name"] to show code (If you enable to use).', RY_CHL::$textdomain) ?></p>
<table width="100%">
	<colgroup>
		<col span="2" width="110">
		<col>
	</colgroup>
	<tr><th><?=__('Parameter', RY_CHL::$textdomain) ?></th><th><?=__('Default Value', RY_CHL::$textdomain) ?></th><th align="left"><?=__('Description', RY_CHL::$textdomain) ?></th></tr>
	<tr><td>autolinks</td><td>true</td><td><?=__('Allows you to turn detection of links in the highlighted element on and off. If the option is turned off, URLs won`t be clickable.', RY_CHL::$textdomain) ?></td></tr>
	<tr><td>classname</td><td>''</td><td><?=__('Allows you to add a custom class (or multiple classes) to every highlighter element that will be created on the page.', RY_CHL::$textdomain) ?></td></tr>
	<tr><td>collapse</td><td>false</td><td><?=__('Allows you to force highlighted elements on the page to be collapsed by default.', RY_CHL::$textdomain) ?></td></tr>
	<tr><td>firstline</td><td>1</td><td><?=__('Allows you to change the first (starting) line number.', RY_CHL::$textdomain) ?></td></tr>
	<tr><td>gutter</td><td>true</td><td><?=__('Allows you to turn gutter with line numbers on and off.', RY_CHL::$textdomain) ?></td></tr>
	<tr><td>highlight</td><td>''</td><td><?=__('Allows you to highlight one or more lines to focus user`s attention.', RY_CHL::$textdomain) ?></td></tr>
	<tr><td>htmlscript</td><td>false</td><td><?=__('Allows you to highlight a mixture of HTML/XML code and a script which is very common in web development.', RY_CHL::$textdomain) ?></td></tr>
	<tr><td>smarttabs</td><td>true</td><td><?=__('Allows you to turn smart tabs feature on and off.', RY_CHL::$textdomain) ?></td></tr>
	<tr><td>tabsize</td><td>4</td><td><?=__('Allows you to adjust tab size.', RY_CHL::$textdomain) ?></td></tr>
	<tr><td>toolbar</td><td>true</td><td><?=__('Toggles toolbar on/off.', RY_CHL::$textdomain) ?></td></tr>
</table>
<h3><?=__('Thinks', RY_CHL::$textdomain) ?></h3>
<p><?php printf(__( 'The Highlighter is powerd by <a href="%s">SyntaxHighlighter</a>.', RY_CHL::$textdomain), 'http://alexgorbatchev.com/SyntaxHighlighter/'); ?></p>
		<?php
	}
}
