<?php

declare ( strict_types=1 );

namespace J7\WpToolkit;

final class Bootstrap {
	function __construct() {
		require_once __DIR__ . '/debug/index.php';
		require_once __DIR__ . '/power_plugins/index.php';
		require_once __DIR__ . '/components/index.php';
		require_once __DIR__ . '/api.php';
		require_once __DIR__ . '/ajax.php';

		\add_action( 'redux/construct', [ $this, 'load_extensions' ] );

		\add_action( 'admin_enqueue_scripts', [ $this, 'add_static_assets' ] );
	}

	public function load_extensions( $redux_object ): void {
		$opt_name = $redux_object->args['opt_name'];

		/**
		 *
		 * 不需要 require php ，用 Redux::set_extensions 直接設定路徑就可以
		 *
		 * ⭐ 自訂欄位的 php 檔名，如果是 _ 要改成 -
		 *
		 * 關於 \Redux::set_extensions
		 *
		 * @param opt_name string
		 * @param path string - 可以是 目錄路徑 或 檔案路徑，但是檔名有規範，他會分割檔名
		 * 如果是目錄，會找目錄下
		 * 'extension_' . $folder . '.php',
		 * 'class-redux-extension-' . $folder_fix . '.php',
		 * 如果是檔名
		 * 會把 檔名 用 extension_ 分割，然後找對應的檔案來實例化，詳細可以看 \Redux::set_extensions 的代碼
		 */
		// \Redux::set_extensions($opt_name, Utils::get_plugin_dir() . '/inc/redux_custom_fields/example');

		\Redux::set_extensions( $opt_name, Utils::get_plugin_dir() . '/inc/redux_custom_fields/number' );
	}

	public function add_static_assets( $hook ): void {
		if ( ! WP_DEBUG ) {
			\wp_enqueue_style(
				'tailwindcss',
				Utils::get_plugin_url() . '/inc/redux_custom_fields/bundle-min.css',
				[],
				Utils::get_plugin_ver()
			);
		}
	}
}

new Bootstrap();
