<?php

/**
 * Plugin Name:       WP Toolkit
 * Plugin URI:        https://cloud.luke.cafe/plugins/
 * Description:       方便開發 WordPress 外掛的工具包。
 * Version:           0.3.3
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

declare( strict_types=1 );

namespace J7\WpToolkit;

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;


if ( ! \class_exists( 'J7\WpToolkit\Plugin', false ) ) {
	final class Plugin {
		private static $instance;

		public function __construct() {
			require_once __DIR__ . '/vendor/autoload.php';
			// 這邊超怪的，明明已經用 composer 裝了，不知道為什麼要這樣寫才能 load Redux
			if ( ! \class_exists( 'Redux' ) ) {
				require_once __DIR__ . '/vendor/wpackagist-plugin/redux-framework/redux-framework.php';
			}
			require_once __DIR__ . '/utils/index.php';
			require_once __DIR__ . '/inc/index.php';

			\register_activation_hook( __FILE__, [ $this, 'activate' ] );
			\register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

			if ( ! WP_DEBUG ) {
				\add_action( 'current_screen', [ $this, 'remove_redux_banner' ], 100 );
				\add_action( 'admin_menu', [ $this, 'remove_redux_submenu' ], 100 );
			}

			$this->plugin_update_checker();
		}

		public static function instance() {
			if ( empty( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * wp plugin 更新檢查 update checker
		 */
		public function plugin_update_checker(): void {
			$updateChecker = PucFactory::buildUpdateChecker(
				Utils::GITHUB_REPO,
				__FILE__,
				Utils::KEBAB
			);
			$updateChecker->setBranch( 'master' );
			// $updateChecker->setAuthentication(Utils::GITHUB_PAT);
			$updateChecker->getVcsApi()->enableReleaseAssets();
		}

		public function remove_redux_banner(): void {
			if ( ! \class_exists( 'Redux_Connection_Banner' ) ) {
				return;
			}
			\remove_action( 'admin_notices', [ \Redux_Connection_Banner::init(), 'render_banner' ] );
		}

		public function remove_redux_submenu(): void {
			\remove_submenu_page( 'options-general.php', 'redux-framework' );
		}

		public function activate(): void {
			// 啟用後執行一次
		}

		public function deactivate(): void {
			// 刪除 DB 欄位 或是 transient
		}
	}

	Plugin::instance();
}
