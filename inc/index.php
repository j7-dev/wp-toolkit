<?php

declare(strict_types=1);

namespace J7\WpToolkit;

use J7\WpToolkit\Utils;

final class Bootstrap
{
	function __construct()
	{

		require_once __DIR__ . '/components/index.php';
		require_once __DIR__ . '/api.php';
		require_once __DIR__ . '/ajax.php';


		\add_action('redux/construct', [$this, 'load_extensions']);

		// if in dev mode, add tailwindcss to admin and frontend
		if (Utils::is_dev()) {
			\add_action('wp_enqueue_scripts', [$this, 'add_static_assets']);
			\add_action('wp_head', [$this, 'add_tailwind_config'], 1000);
			\add_filter('body_class', function ($classes) {
				if (in_array('tailwindcss', $classes) === false) {
					$classes[] = 'tailwindcss';
				}
				return $classes;
			});
			\add_action('admin_enqueue_scripts', [$this, 'add_static_assets']);
			\add_action('admin_head', [$this, 'add_tailwind_config'], 1000);
			\add_filter('admin_body_class', function ($classes) {
				if (strpos($classes, 'tailwindcss') === false) {
					$classes .= ' tailwindcss ';
				}
				return $classes;
			});
		}
	}

	public function load_extensions($redux_object): void
	{
		$opt_name = $redux_object->args['opt_name'];

		/**
		 *
		 * 不需要 require php ，用 Redux::set_extensions 直接設定路徑就可以
		 *
		 * 關於 \Redux::set_extensions
		 * @param opt_name string
		 * @param path string - 可以是 目錄路徑 或 檔案路徑，但是檔名有規範，他會分割檔名
		 * 如果是目錄，會找目錄下
		 * 'extension_' . $folder . '.php',
		 * 'class-redux-extension-' . $folder_fix . '.php',
		 * 如果是檔名
		 * 會把 檔名 用 extension_ 分割，然後找對應的檔案來實例化，詳細可以看 \Redux::set_extensions 的代碼
		 */
		\Redux::set_extensions($opt_name, Utils::get_plugin_dir() . '/inc/redux_custom_fields/number');
	}

	public function add_static_assets($hook): void
	{
		\wp_enqueue_script('tailwindcss', 'https://cdn.tailwindcss.com', array(), '3.4.0');
	}

	public function add_tailwind_config(): void
	{
		// WP 後台會與 tailwind css 衝突，所以要加上 prefix
?>
		<script>
			tailwind.config = {
				prefix: 'tw-',
				important: '.tailwindcss',
				corePlugins: {
					preflight: false,
				},
			}
		</script>
<?php
	}
}

new Bootstrap();
