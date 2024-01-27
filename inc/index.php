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



		if (Utils::is_dev()) {
			\add_action('wp_enqueue_scripts', [$this, 'add_static_assets']);
			\add_action('wp_head', [$this, 'add_tailwind_config'], 1000);
			\add_action('admin_enqueue_scripts', [$this, 'add_static_assets']);
			\add_action('admin_head', [$this, 'add_tailwind_config'], 1000);
		}
	}

	public function add_static_assets($hook): void
	{
		\wp_enqueue_script('tailwindcss', 'https://cdn.tailwindcss.com', array(), '3.4.0');
	}

	public function add_tailwind_config(): void
	{
?>
		<script>
			tailwind.config = {
				important: '.tailwindcss',
			}
		</script>
<?php
	}
}

new Bootstrap();
