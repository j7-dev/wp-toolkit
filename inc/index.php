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
