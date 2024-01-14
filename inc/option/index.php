<?php

declare(strict_types=1);

namespace J7\WpToolkit;

if (!defined('ABSPATH')) {
	exit;
}

class Option extends Core
{
	/**
	 * Stores the Option Page config that
	 * is supplied to the constructor.
	 *
	 * @var array
	 */
	private $_option_config = [];
	private $_is_tabs = false;
	private $_active_tab;

	/**
	 * Class constructor.
	 *
	 * @param array $args An associative array with the following keys:
	 *   - 'id'       (string) The metabox ID.
	 *   - 'title'    (string) The title of the metabox.
	 *   - 'screen'   (string) The post type for which to display the metabox.
	 *   - 'context'  (string) The context in which to display the metabox. Options: normal, side, advanced.
	 *   - 'priority' (string) The priority of the metabox. Default is 'default'.
	 * @return void
	 */
	public function __construct($menu_config)
	{
		parent::__construct($menu_config);
		$this->set_properties($menu_config);

		\add_action('admin_init', [$this, 'admin_init']);
		\add_action('admin_menu', [$this, 'set_menu']);
		// \add_action('admin_menu', array($this, 'set'));
	}

	public static function init($option_config): self
	{
		$instance = new self($option_config);
		$instance->_instance = $instance;
		return $instance->_instance;
	}

	/**
	 * Initialize and registers the settings sections and fileds to WordPress
	 *
	 * Usually this should be called at `admin_init` hook.
	 *
	 * This function gets the initiated settings sections and fields. Then
	 * registers them to WordPress and ready for use.
	 */
	function admin_init()
	{
		$this->add_settings_section();
		$this->add_settings_field_loop();
		$this->register_settings();
	}

	/**
	 * Set Properties of the class
	 */
	private function set_properties(array $menu_config): void
	{
		$this->_fields = [];

		$default_menu_config = [
			'id' 			 => 'plugin-options',
			// The name of this page
			'page_title' => __('Plugin Options'),
			// //The Menu Title in Wp Admin
			'menu_title' => __('Plugin Options'),
			// The capability needed to view the page
			'capability' => 'manage_options',
			// dashicons id or url to icon
			// https://developer.wordpress.org/resource/dashicons/
			'icon'       => '',
			// position
			'position'   => 100,
			// For sub menu, we can define parent menu slug (Defaults to Options Page)
			'parent'     => 'options-general.php',
			'tabs' => false
		];
		// TODO 加上 hooks
		$this->_option_config    = array_merge($default_menu_config, $menu_config);

		ob_start();
		print_r($this->_option_config);
		Utils::debug_log('' . ob_get_clean());

		$this->_is_tabs = isset($this->_option_config['tabs']) && is_array($this->_option_config['tabs']);

		$this->set_active_tab();
	}

	/**
	 * Register plugin option page
	 */
	public function set_menu(): void
	{



		// Is it a main menu or sub_menu
		if (!isset($this->_option_config['parent'])) {
			\add_menu_page(
				$this->_option_config['page_title'],
				$this->_option_config['menu_title'],
				$this->_option_config['capability'],
				$this->_option_config['id'], // slug
				array($this, 'display_page'),
				$this->_option_config['icon'],
				$this->_option_config['position']
			);
		} else {
			\add_submenu_page(
				$this->_option_config['parent'],
				$this->_option_config['page_title'],
				$this->_option_config['menu_title'],
				$this->_option_config['capability'],
				$this->_option_config['id'], // slug
				array($this, 'display_page'),
				$this->_option_config['position']
			);
		}
	}

	public function set_active_tab(): void
	{
		if (isset($_GET['tab'])) {
			$this->_active_tab = \sanitize_key($_GET['tab']);
		}
	}

	public function add_settings_section()
	{
		if (!$this->_is_tabs) return;

		$tabs = $this->_option_config['tabs'] ?? [];

		// register settings sections (tab)
		foreach ($tabs as $tab) {
			if ($tab['id'] !== $this->_active_tab) {
				continue;
			}

			// Callback for Section Description
			if (isset($tab['callback']) && is_callable($tab['callback'])) {
				$callback = $tab['callback'];
			} elseif (isset($tab['desc']) && !empty($tab['desc'])) {
				$callback = function () use ($tab) {
					echo "<div class='inside'>" . \esc_html($tab['desc']) . '</div>';
				};
			} else {
				$callback = null;
			}

			\add_settings_section(
				$tab['id'],
				$tab['title'],
				$callback,
				$this->_option_config['id'] // page
				// TODO args 要加嗎?
			);
		}
	}

	public function add_settings_field_loop()
	{

		// register settings fields
		foreach ($this->_fields as $field) {
			$section_id = $field['section_id'] ?? 'default';

			if ($this->_is_tabs) {
				if ($section_id !== $this->_active_tab) {
					continue;
				}
			}

			$field['value'] = get_option($field['name'], $field['default']);
			$field['label_for'] = $field['name'];


			\add_settings_field(
				$field['name'],
				$field['label'],
				['J7\WpToolkit\Components\Form', 'render_field_' . $field['type']],
				$this->_option_config['id'], // page
				$section_id, // section
				$field  // args
			);
		}
	}

	public function register_settings()
	{

		// creates our settings in the options table
		foreach ($this->_fields as $field) :

			\register_setting(
				$this->get_options_group(), // options_group
				$field['name'], // options_id, `option_name` in `wp_options` table
				array(
					// 'type'              => $field['type'],
					'description' => $field['desc'],
					'sanitize_callback' => ['J7\WpToolkit\Utils\Sanitize', 'sanitize_' . $field['type']],
					'show_in_rest'      => $field['show_in_rest'],
					'default'           => $field['default'],
				)
			);

		endforeach;
	}

	public function get_options_group($section_id = '')
	{
		if ($this->_is_tabs) {
			return $this->_option_config['id'] . '_' . $section_id;
		} else {
			return $this->_option_config['id'];
		}
	}
}
