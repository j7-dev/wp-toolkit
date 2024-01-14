<?php

declare(strict_types=1);

namespace J7\WpToolkit;

use J7\WpToolkit\Components\Form;

if (!defined('ABSPATH')) {
	exit;
}

class Menu extends Core
{
	/**
	 * Stores the Option Page config that
	 * is supplied to the constructor.
	 *
	 * @var array
	 */
	private $_menu_config = [];
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
	public function __construct(string $key)
	{
		parent::__construct($key);
		$this->_config = [
			'screen' => $key
		];
	}

	public function addMenu(array $menu): void
	{
		$this->_menu_config = $menu;
	}

	public function addTab(array $tabs): void
	{
		$this->_menu_config['tabs']     = $tabs;
	}

	public function mount()
	{
		$this->set_properties($this->_menu_config);

		\add_action('admin_init', [$this, 'admin_init']);
		\add_action('admin_menu', [$this, 'set_menu']);
		\add_action('admin_enqueue_scripts', array($this, 'scripts'));
		// \add_action('admin_menu', array($this, 'set'));
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
		$this->render_field_loop();
		$this->register_settings();
	}

	/**
	 * Set Properties of the class
	 */
	private function set_properties($config): void
	{
		$this->_instance = $this;

		$default_tabs_menu_config = [
			[
				'id'    => 'default',
				'title' => __('default Settings', 'plugin-name'),
				'desc'  => __('These are advance settings for Plugin Name', 'plugin-name')
			]
		];

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
			'tabs' => $default_tabs_menu_config
		];

		// TODO 加上 hooks
		$this->_menu_config    = wp_parse_args($config, $default_menu_config);
		if (isset($config['tabs'])) {
			$this->_menu_config['tabs'] = array_replace_recursive($default_tabs_menu_config, $config['tabs']);
		}

		$this->_is_tabs = (count($this->_menu_config['tabs']) > 1) && is_array($this->_menu_config['tabs']);

		$this->set_active_tab();
	}

	/**
	 * Register plugin option page
	 */
	public function set_menu(): void
	{
		// Is it a main menu or sub_menu
		if (!isset($this->_menu_config['parent'])) {
			\add_menu_page(
				$this->_menu_config['page_title'],
				$this->_menu_config['menu_title'],
				$this->_menu_config['capability'],
				$this->_menu_config['id'], // slug
				array($this, 'render'),
				$this->_menu_config['icon'],
				$this->_menu_config['position']
			);
		} else {
			\add_submenu_page(
				$this->_menu_config['parent'],
				$this->_menu_config['page_title'],
				$this->_menu_config['menu_title'],
				$this->_menu_config['capability'],
				$this->_menu_config['id'], // slug
				array($this, 'render'),
				$this->_menu_config['position']
			);
		}
	}

	public function set_active_tab(): void
	{
		if (isset($_GET['tab'])) {
			$this->_active_tab = \sanitize_key($_GET['tab']);
		} else {
			$this->_active_tab = $this->_menu_config['tabs'][0]['id'];
		}
	}

	public function add_settings_section()
	{
		if (!$this->_is_tabs) return;

		$tabs = $this->_menu_config['tabs'] ?? [];

		// register settings sections (tab)
		foreach ($tabs as $index => $tab) {
			if ($tab['id'] !== $this->_active_tab) {
				continue;
			}

			\add_settings_section(
				$tab['id'],
				$tab['title'],
				[$this, 'render_tabs'],
				$this->_menu_config['id'], // page, option_group
				$tabs[$index]
			);
		}
	}

	public function render_tabs($tab)
	{
		if (isset($tab['desc'])) {
			echo "<div class='inside'>" . \esc_html($tab['desc']) . "</div>";
		}
	}

	public function render_field_loop()
	{

		// register settings fields
		foreach ($this->_fields as $field) {
			$tab_id = $field['tab_id'] ?? 'default';

			if ($this->_is_tabs) {
				if ($tab_id !== $this->_active_tab) {
					continue;
				}
			}

			$field['value'] = get_option($field['id'], $field['default']);
			$field['label_for'] = $field['id'];

			\add_settings_field(
				$field['id'],
				$field['label'],
				['J7\WpToolkit\Components\Form', 'render_field_' . $field['type']],
				$this->_menu_config['id'], // page
				$tab_id, // section
				$field  // args
			);
		}
	}

	public function register_settings()
	{

		// creates our settings in the options table
		foreach ($this->_fields as $field) :

			\register_setting(
				($this->_is_tabs) ?
					$this->get_options_group($field['tab_id'])
					: $this->get_options_group(), // page, options_group
				$field['id'], // options_id, `option_name` in `wp_options` table
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

	public function get_options_group($tab_id = '')
	{
		return str_replace('-', '_', $this->_menu_config['id']) . '_' . $tab_id;
	}

	function render()
	{
		echo '<div class="wrap">';
		echo '<h1>' . get_admin_page_title() . '</h1>';
?>
		<div class="metabox-holder">
			<?php
			if ($this->_is_tabs) {
				$this->render_navigation();
			}
			?>
			<form method="post" action="options.php">
				<?php

				if ($this->_is_tabs) {
					foreach ($this->_menu_config['tabs'] as $tab) :
						if ($tab['id'] !== $this->_active_tab) {
							continue;
						}

						// for tabs
						\settings_fields($this->get_options_group($tab['id']));
						\do_settings_sections($this->_menu_config['id']);
					endforeach; // end foreach

				} else {
					// for tab-less
					\settings_fields($this->get_options_group());
					\do_settings_sections($this->_menu_config['id']);
				}

				?>
				<div>
					<?php submit_button(); ?>
				</div>

			</form>
		</div>

		</div>
<?php
	}

	/**
	 * Show navigations as tab
	 *
	 * Shows all the settings section labels as tab
	 */
	function render_navigation(): void
	{

		$settings_page = $this->get_default_settings_url();

		$count = count($this->_menu_config['tabs']);

		// don't show the navigation if only one section exists
		if ($count === 1) {
			return;
		}

		$html = '<h2 class="nav-tab-wrapper">';

		foreach ($this->_menu_config['tabs'] as $tab) {
			$active_class = ($tab['id'] == $this->_active_tab) ? 'nav-tab-active' : '';
			$html        .= sprintf('<a href="%3$s&tab=%1$s" class="nav-tab %4$s" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title'], $settings_page, $active_class);
		}

		$html .= '</h2>';

		echo $html;
	}

	public function get_default_settings_url(): string
	{

		if (isset($this->_menu_config['parent'])) {
			$options_base_file_name = $this->_menu_config['parent'];
			if (in_array(
				$options_base_file_name,
				array(
					'options-general.php',
					'edit-comments.php',
					'plugins.php',
					'edit.php',
					'upload.php',
					'themes.php',
					'users.php',
					'tools.php',
				)
			)) {
				return admin_url("{$options_base_file_name}?page={$this->_menu_config['id']}");
			} else {
				return admin_url("admin.php?page={$this->_menu_config['id']}");
			}
		} else {
			return admin_url("admin.php?page={$this->_menu_config['id']}");
		}
	}
}
