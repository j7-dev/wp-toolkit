<?php

declare ( strict_types=1 );

namespace J7\WpToolkit;

final class PowerPlugins {

	const OPT_NAME                       = 'power_plugins_settings';
	const MENU_NAME                      = 'Power Plugins';
	const ENABLE_SIMPLE_ADMIN_FIELD_NAME = 'power_plugins_enable_simple_admin';
	public static $instance;
	public $args     = array();
	public $sections = array();

	public function __construct() {
		\add_action( 'setup_theme', array( $this, 'do_redux' ), 100 );
	}

	public static function get_instance(): PowerPlugins {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function do_redux(): void {
		$instance = \Redux::instance( self::OPT_NAME );

		// 如果已經實例化過，就不再實例化
		if ( empty( $instance->args ) ) {
			\Redux::set_args(
				self::OPT_NAME,
				wp_parse_args(
					$this->args,
					$this->get_default_args()
				)
			);
			\Redux::set_sections(
				self::OPT_NAME,
				array(
					$this->get_default_section(),
					...$this->sections,
				)
			);
			\Redux::init( self::OPT_NAME );

			if ( ! WP_DEBUG ) {
				\Redux::remove_demo();
			}
		}
	}

	private function get_default_args(): array {
		$args = array(
			// This is where your data is stored in the database and also becomes your global variable name.
			'opt_name'                  => self::OPT_NAME,

			// Name that appears at the top of your panel.
			'display_name'              => self::MENU_NAME,

			// Version that appears at the top of your panel.
			// 'display_version'           => Utils::get_plugin_ver(),

			// Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only).
			'menu_type'                 => 'menu',

			// Show the sections below the admin menu item or not.
			'allow_sub_menu'            => true,

			// The text to appear in the admin menu.
			'menu_title'                => esc_html__( self::MENU_NAME, Utils::TEXT_DOMAIN ),

			// The text to appear on the page title.
			'page_title'                => esc_html__( self::MENU_NAME, Utils::TEXT_DOMAIN ),

			// Disable to create your own Google fonts loader.
			'disable_google_fonts_link' => false,

			// Show the panel pages on the admin bar.
			'admin_bar'                 => false,

			// Icon for the admin bar menu.
			'admin_bar_icon'            => 'dashicons-superhero',

			// Priority for the admin bar menu.
			'admin_bar_priority'        => 100,

			// Sets a different name for your global variable other than the opt_name.
			'global_variable'           => self::OPT_NAME,

			// Show the time the page took to load, etc. (forced on while on localhost or when WP_DEBUG is enabled).
			'dev_mode'                  => WP_DEBUG,

			// Enable basic customizer support.
			'customizer'                => false,

			// Allow the panel to open expanded.
			'open_expanded'             => false,

			// Disable the save warning when a user changes a field.
			'disable_save_warn'         => false,

			// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
			'page_priority'             => 70,

			// For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters.
			// 'page_parent'               => 'themes.php',

			// Permissions needed to access the options panel.
			'page_permissions'          => 'manage_options',

			// Specify a custom URL to an icon.
			'menu_icon'                 => 'dashicons-superhero',

			// Force your panel to always open to a specific tab (by id).
			'last_tab'                  => '',

			// Icon displayed in the admin panel next to your menu_title.
			'page_icon'                 => 'dashicons-superhero',

			// Page slug used to denote the panel, will be based off page title, then menu title, then opt_name if not provided.
			'page_slug'                 => self::OPT_NAME,

			// On load save the defaults to DB before user clicks save.
			'save_defaults'             => true,

			// Display the default value next to each field when not set to the default value.
			'default_show'              => true,

			// What to print by the field's title if the value shown is default.
			// 'default_mark'              => '*',

			// Shows the Import/Export panel when not used as a field.
			'show_import_export'        => false,

			// The time transients will expire when the 'database' arg is set.
			'transient_time'            => 60 * MINUTE_IN_SECONDS,

			// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output.
			'output'                    => false,

			// Allows dynamic CSS to be generated for customizer and google fonts,
			// but stops the dynamic CSS from going to the page head.
			'output_tag'                => false,

			// Disable the footer credit of Redux. Please leave if you can help it.
			'footer_credit'             => ' ',

			// If you prefer not to use the CDN for ACE Editor.
			// You may download the Redux Vendor Support plugin to run locally or embed it in your code.
			'use_cdn'                   => true,

			// Set the theme of the option panel.  Use 'wp' to use a more modern style, default is classic.
			'admin_theme'               => 'blue',

			// Enable or disable flyout menus when hovering over a menu with submenus.
			'flyout_submenus'           => false,

			// Mode to display fonts (auto|block|swap|fallback|optional)
			// See: https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display.
			'font_display'              => 'swap',

			// HINTS.
			'hints'                     => array(
				'icon'          => 'el el-question-sign',
				'icon_position' => 'right',
				'icon_color'    => 'lightgray',
				'icon_size'     => 'normal',
				'tip_style'     => array(
					'color'   => 'red',
					'shadow'  => true,
					'rounded' => false,
					'style'   => '',
				),
				'tip_position'  => array(
					'my' => 'top left',
					'at' => 'bottom right',
				),
				'tip_effect'    => array(
					'show' => array(
						'effect'   => 'slide',
						'duration' => '500',
						'event'    => 'mouseover',
					),
					'hide' => array(
						'effect'   => 'slide',
						'duration' => '500',
						'event'    => 'click mouseleave',
					),
				),
			),

			// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
			// Possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
			'database'                  => '',
			'network_admin'             => true,
			'search'                    => true,
		);

		// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
		// PLEASE CHANGE THESE SETTINGS IN YOUR THEME BEFORE RELEASING YOUR PRODUCT!!
		// If these are left unchanged, they will not display in your panel!
		$args['share_icons'][] = array(
			'url'   => Utils::GITHUB_REPO,
			'title' => '你的五星好評是給開發者的最大的鼓勵',
			'icon'  => 'el el-github',
		);
		$args['share_icons'][] = array(
			'url'   => 'https://cloud.luke.cafe/',
			'title' => '網站速度不夠快？ 我們的主機代管服務 ⚡ 提供 30 天免費試用',
			'icon'  => 'el el-globe',
		);

		// Panel Intro text -> before the form.
		if ( ! isset( $args['global_variable'] ) || false !== $args['global_variable'] ) {
			if ( ! empty( $args['global_variable'] ) ) {
				$v = $args['global_variable'];
			} else {
				$v = str_replace( '-', '_', $args['opt_name'] );
			}
		}

		// Add content after the form.
		$args['footer_text'] = '<p class="mt-10 text-center text-sm text-gray-400">
		網站速度不夠快？
		<a target="_blank" href="https://cloud.luke.cafe/"
			class="font-semibold leading-6 text-primary hover:text-primary-400">我們的主機代管服務</a> ⚡ 提供30天免費試用
	</p>';

		return $args;
	}

	public function set_sections( $section = [] ): void {
		$this->sections[] = $section;
	}

	private function get_default_section(): array {
		$section = array(
			'title'  => esc_html__( '一般設定', Utils::TEXT_DOMAIN ),
			'id'     => 'general',
			'icon'   => 'el el-home',
			'fields' => array(
				array(
					'id'       => self::ENABLE_SIMPLE_ADMIN_FIELD_NAME,
					'type'     => 'switch',
					'title'    => esc_html__( '啟用簡易後台', Utils::TEXT_DOMAIN ),
					'subtitle' => esc_html__( '關閉後，會顯示 Gamipress 的外掛選單和所有設定項', Utils::TEXT_DOMAIN ),
					'on'       => esc_html__( '啟用', Utils::TEXT_DOMAIN ),
					'off'      => esc_html__( '關閉', Utils::TEXT_DOMAIN ),
					'default'  => 1,
				),
			),
		);

		return $section;
	}
}
