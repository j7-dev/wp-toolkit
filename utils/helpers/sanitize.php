<?php

declare(strict_types=1);

namespace J7\WpToolkit\Utils;

abstract class Sanitize
{

	public static function sanitize_text($value)
	{
		return (!empty($value)) ? sanitize_text_field($value) : '';
	}

	public static function sanitize_number($value)
	{
		return (is_numeric($value)) ? $value : 0;
	}

	public static function sanitize_editor($value)
	{
		return wp_kses_post($value);
	}

	public static function sanitize_textarea($value)
	{
		return sanitize_textarea_field($value);
	}

	public static function sanitize_checkbox($value)
	{
		return ($value === '1') ? 1 : 0;
	}

	public static function sanitize_select($value)
	{
		return self::sanitize_text($value);
	}

	public static function sanitize_user_role($value)
	{
		return sanitize_key($value);
	}

	public static function sanitize_radio($value)
	{
		return self::sanitize_text($value);
	}

	public static function sanitize_multicheck($value)
	{
		return (is_array($value)) ? array_map('sanitize_text_field', $value) : array();
	}

	public static function sanitize_color($value)
	{

		if (false === strpos($value, 'rgba')) {
			return sanitize_hex_color($value);
		} else {
			// By now we know the string is formatted as an rgba color so we need to further sanitize it.

			$value = trim($value, ' ');
			$red   = $green = $blue = $alpha = '';
			sscanf($value, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha);

			return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
		}
	}

	public static function sanitize_password($value)
	{

		$password_get_info = password_get_info($value);

		if (isset($password_get_info['algo']) && $password_get_info['algo']) {
			unset($password_get_info);

			return $value;
			// do nothing, we have got already stored hashed password
		} else {
			unset($password_get_info);

			return password_hash($value, PASSWORD_DEFAULT);
		}
	}

	public static function sanitize_url($value)
	{
		return esc_url_raw($value);
	}

	public static function sanitize_file($value)
	{
		// TODO: if the option to store file as file url
		return esc_url_raw($value);
	}

	public static function sanitize_html($value)
	{
		// TODO nothing to save
		return '';
	}

	public static function sanitize_posts($value)
	{
		// Only store post id
		return absint($value);
	}

	public static function sanitize_pages($value)
	{
		// Only store page id
		return absint($value);
	}

	public static function sanitize_media($value)
	{
		// Only store media id
		return absint($value);
	}
}

// @codingStandardsIgnoreEnd