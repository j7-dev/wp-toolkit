<?php

declare(strict_types=1);

namespace J7\WpToolkit;

if (!\class_exists('J7\WpToolkit\Utils', false)) {



	abstract class Utils
	{
		const APP_NAME            = 'WP Toolkit';
		const KEBAB               = 'wp-toolkit';
		const SNAKE               = 'wp_toolkit';
		const DEFAULT_IMAGE       = 'http://1.gravatar.com/avatar/1c39955b5fe5ae1bf51a77642f052848?s=96&d=mm&r=g';
		const GITHUB_REPO         = 'https://github.com/j7-dev/wp-toolkit';
		const DEV_MODE						= true;

		public static function debug_log($log_line): void
		{
			$default_path = ABSPATH . 'wp-content';
			$default_file_name 	= 'debug.log';

			$log_in_file = file_put_contents("{$default_path}/{$default_file_name}", '[' . date('Y-m-d H:i:s') . ' UTC] - ⭐ ' . $log_line . PHP_EOL, FILE_APPEND);
		}

		public static function get_plugin_dir(): string
		{
			$plugin_dir = \untrailingslashit(\wp_normalize_path(\plugin_dir_path(__DIR__ . '../')));
			return $plugin_dir;
		}

		public static function get_plugin_url(): string
		{
			$plugin_url = \untrailingslashit(\plugin_dir_url(Utils::get_plugin_dir() . '/plugin.php'));
			return $plugin_url;
		}

		public static function get_plugin_ver(): string
		{
			$plugin_data = \get_plugin_data(Utils::get_plugin_dir() . '/plugin.php');
			$plugin_ver  = $plugin_data['Version'];
			return $plugin_ver;
		}
	}
}
