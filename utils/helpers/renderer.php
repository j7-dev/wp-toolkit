<?php

declare(strict_types=1);

namespace J7\WpToolkit\Utils;

use J7\WpToolkit\Utils;

abstract class Renderer
{


	// TODO 目前沒有輸出到畫面上，CSS中也只有 $width = 6 有用
	// public static function column($width, $contents)
	// {
	// 	if (isset($width, $contents)) {
	// 		return sprintf(
	// 			'<div class="%s %s">%s</div>',
	// 			esc_attr(self::get_element_class_with_namespace('col')),
	// 			esc_attr(self::get_element_class_with_namespace(sprintf('col-%d', $width))),
	// 			esc_html($contents)
	// 		);
	// 	}
	// }

	/**
	 * Returns a formatted string for a block-element (block__element) class name.
	 *
	 * @param string $block
	 * @param string $element
	 * @return string
	 */
	public static function get_block_element_class(string $block, string $element): string
	{
		if (isset($block, $element)) {
			return trim(sprintf('%s__%s', $block, $element));
		}
	}

	/**
	 * Returns a formatted string for a block-element (block__element) class name
	 * of a field element or non-field element prefixed with the namespace.
	 *
	 * @param string  $element
	 * @param boolean $isField
	 * @return string
	 */
	public static function get_block_element_class_with_namespace(string $element, $isField = true): string
	{
		if (isset($element)) {
			return trim(
				sprintf(
					'%s %s%s',
					($isField
						? (sprintf('%s__%s', Utils::KEBAB, 'field'))
						: ''
					),
					sprintf('%s__%s', Utils::KEBAB, ($isField ? 'field-' : '')),
					$element
				)
			);
		}
	}

	/**
	 * Returns a formatted string for a class name prefixed with
	 * the namespace.
	 *
	 * @param string $suffix
	 * @return string
	 */
	public static function get_element_class_with_namespace(string $suffix): string
	{
		if (isset($suffix)) {
			return trim(
				sprintf(
					'%s-%s',
					Utils::KEBAB,
					$suffix
				)
			);
		}
	}

	/**
	 * Echos some HTML that preceeds a field (container, label, description, etc.)
	 *
	 * @param array         $field
	 * @param string | null $meta
	 */
	public static function render_before_field($field = null): void
	{
		echo sprintf(
			'<div class="%s %s">',
			\esc_attr(self::get_block_element_class_with_namespace('field-container', false)),
			\esc_attr(self::get_block_element_class_with_namespace($field['type'] . '-container', false))
		);

		if (isset($field['label']) && $field['instance_class'] != 'J7\WpToolkit\Menu') {
			echo sprintf(
				'<label class="%s" for="%s">%s</label><br />',
				\esc_attr(self::get_block_element_class_with_namespace('label', false)),
				\esc_attr($field['id']),
				\esc_html($field['label'])
			);
		}

		if ($field['type'] == 'image') {
			self::render_image_preview($field);
		}
	}

	/**
	 * Echos HTML that comes after a field (container, description, etc).
	 *
	 * @param array | null $field
	 */
	public static function render_after_field($field = null): void
	{
		self::render_field_description($field['desc']);

		echo '</div>';
	}

	/**
	 * Echos a paragraph element with some description text that
	 * serves as an assistant to the operator of the metabox.
	 *
	 * @param string $desc
	 */
	public static function render_field_description(?string $desc): void
	{
		if (isset($desc)) {
			return;
		}
		echo sprintf(
			'<p class="%s">%s</p>',
			\esc_attr(self::get_block_element_class_with_namespace('description', false)),
			\esc_html($desc)
		);
	}

	/**
	 * Echos an image tag that serves as preview.
	 *
	 * @param array  $field
	 * @param string $src
	 */
	public static function render_image_preview($field): void
	{
		echo sprintf(
			'<img id="%1$s" class="%2$s" src="%3$s" alt="%4$s">',
			\esc_attr(sprintf('js-%s-image-preview', $field['id'])),
			\esc_attr(sprintf('%s %s', self::get_block_element_class_with_namespace('image-preview', false), empty($field['value']) ? 'is-hidden' : '')),
			\esc_attr($field['value']),
			\esc_attr('')
		);
	}
}
