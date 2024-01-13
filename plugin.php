<?php

/**
 * Plugin Name:       WP Toolkit
 * Plugin URI:        https://cloud.luke.cafe/plugins/
 * Description:       方便開發 WordPress 外掛的工具包。
 * Version:           0.0.1
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Author:            J7
 * Author URI:        https://github.com/j7-dev
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       power-partner-server
 * Domain Path:       /languages
 * Tags:
 */

declare(strict_types=1);

namespace J7\WpToolkit;

// TODO DELETE
// require_once __DIR__ . '/wp-metabox/src/Metabox.php';


use J7\WpToolkit\Utils;
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

if (!\class_exists('J7\WpToolkit\Init')) {

	class Init
	{
		private static $instance;

		public function __construct()
		{
			require_once __DIR__ . '/vendor/autoload.php';
			require_once __DIR__ . '/utils/index.php';
			require_once __DIR__ . '/inc/index.php';

			\register_activation_hook(__FILE__, [$this, 'activate']);
			\register_deactivation_hook(__FILE__, [$this, 'deactivate']);

			$this->plugin_update_checker();
		}

		public static function instance()
		{
			if (empty(self::$instance)) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * wp plugin 更新檢查 update checker
		 */
		public function plugin_update_checker(): void
		{
			$updateChecker = PucFactory::buildUpdateChecker(
				Utils::GITHUB_REPO,
				__FILE__,
				Utils::KEBAB
			);
			$updateChecker->setBranch('master');
			// $updateChecker->setAuthentication(Utils::GITHUB_PAT);
			$updateChecker->getVcsApi()->enableReleaseAssets();
		}

		public function activate(): void
		{
			// 啟用後執行一次
		}

		public function deactivate(): void
		{
			// 刪除 DB 欄位 或是 transient
		}
	}

	Init::instance();
}


add_action('init', __NAMESPACE__ . '\option_test');

function option_test()
{
	$option = new \J7\WpToolkit\Option('j7');
	$option->addMenu(
		array(
			'page_title' => __('Plugin Name Settings', 'plugin-name'),
			'menu_title' => __('Plugin Name', 'plugin-name'),
			'capability' => 'manage_options',
			'slug'       => 'plugin-name',
			'icon'       => 'dashicons-performance',
			'position'   => 10,
			'submenu'    => true,
			'parent'     => 'edit.php',
		)
	);
	$option->addText(
		'advance_section',
		array(
			'id'                => 'text_field_id',
			'label'             => __('Hello World', 'plugin-name'),
			'desc'              => __('Some description of my field', 'plugin-name'),
			'placeholder'       => 'This is Placeholder',
			'show_in_rest'      => true,
			'class'             => 'my_custom_css_class',
			'size'              => 'regular',
		),
	);
	$option->addTab(
		array(
			array(
				'id'    => 'general_section',
				'title' => __('General Settings', 'plugin-name'),
				'desc'  => __('These are general settings for Plugin Name', 'plugin-name'),
			),
			array(
				'id'    => 'advance_section',
				'title' => __('Advanced Settings', 'plugin-name'),
				'desc'  => __('These are advance settings for Plugin Name', 'plugin-name')
			)
		)
	);
	$option->register();
}

add_action('init', __NAMESPACE__ . '\metabox_test');

function metabox_test()
{
	$metabox = Metabox::init(array(
		'id' => 'metabox_id',
		'title' => 'My awesome metabox',
		'screen' => 'post', // post type
		'context' => 'advanced', // Options normal, side, advanced.
		'priority' => 'default'
	));

	$metabox->addText(array(
		'id' => 'metabox_text_field',
		'label' => 'Text',
		'desc' => 'An example description paragraph that appears below the label.'
	));
	$metabox_repeater_block_fields[] = $metabox->addText(array(
		'id' => 'metabox_repeater_text_field',
		'label' => 'Photo Title'
	), true);
	$metabox_repeater_block_fields[] = $metabox->addTextArea(array(
		'id' => 'metabox_repeater_textarea_field',
		'label' => 'Photo Description'
	), true);

	$metabox_repeater_block_fields[] = $metabox->addImage(array(
		'id' => 'metabox_repeater_image_field',
		'label' => 'Upload Photo'
	), true);

	$metabox->addRepeaterBlock(array(
		'id' => 'metabox_repeater_block',
		'label' => 'Photo Gallery',
		'fields' => $metabox_repeater_block_fields,
		'desc' => 'Photos in a photo gallery.',
		'single_label' => 'Photo'
	));
}
