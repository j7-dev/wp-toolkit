<?php

declare(strict_types=1);

namespace J7\WpToolkit;

require_once 'functions.php';

if (!\class_exists('J7\WpToolkit\Utils', false)) {

	class Utils
	{
		const APP_NAME            = 'WP Toolkit';
		const KEBAB               = 'wp-toolkit';
		const SNAKE               = 'wp_toolkit';
		const DEFAULT_IMAGE       = 'http://1.gravatar.com/avatar/1c39955b5fe5ae1bf51a77642f052848?s=96&d=mm&r=g';
		const GITHUB_REPO         = 'https://github.com/j7-dev/wp-toolkit';

		public static function debug_log($log_line): void
		{
			$default_path = ABSPATH . 'wp-content';
			$default_file_name 	= 'debug.log';

			$log_in_file = file_put_contents("{$default_path}/{$default_file_name}", '[' . date('Y-m-d H:i:s') . ' UTC] - ⭐ ' . $log_line . PHP_EOL, FILE_APPEND);
		}

		public static function get_plugin_dir(): string
		{
			$plugin_dir = \untrailingslashit(\wp_normalize_path(ABSPATH . 'wp-content/plugins/wp-toolkit'));
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

		/*
     * 在 wp_admin 中使用do_shortcode
     */
		public static function admin_do_shortcode($content, $ignore_html = false): mixed
		{
			global $shortcode_tags;

			if (false === strpos($content, '[')) {
				return $content;
			}

			if (empty($shortcode_tags) || !is_array($shortcode_tags)) {
				return $content;
			}

			// Find all registered tag names in $content.
			preg_match_all('@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches);
			$tagnames = array_intersect(array_keys($shortcode_tags), $matches[1]);

			if (empty($tagnames)) {
				return $content;
			}

			$content = do_shortcodes_in_html_tags($content, $ignore_html, $tagnames);

			$pattern = get_shortcode_regex($tagnames);
			$content = preg_replace_callback("/$pattern/", 'do_shortcode_tag', $content);

			// Always restore square braces so we don't break things like <!--[if IE ]>.
			$content = unescape_invalid_shortcodes($content);

			return $content;
		}
	}
}
