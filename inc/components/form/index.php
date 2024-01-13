<?php

declare(strict_types=1);

namespace J7\WpToolkit\Components;

use J7\WpToolkit\Utils\Renderer;
use J7\WpToolkit\Utils;

class Form
{
	const REPEATER_INDEX_PLACEHOLDER       = 'CurrentCounter';
	const REPEATER_ITEM_NUMBER_PLACEHOLDER = 'ItemNumber';



	public static function render_field_text($field)
	{

		Renderer::render_before_field($field);
		echo sprintf(
			'<input type="text" class="%1$s" id="%2$s" name="%2$s" value="%3$s" placeholder="%4$s">',
			esc_attr($field['size'] . '-text ' . $field['class'] . ' ' . Renderer::get_block_element_class_with_namespace($field['type'])),
			esc_attr($field['id']),
			esc_attr($field['value']),
			esc_attr($field['placeholder'])
		);
		Renderer::render_after_field();
	}


	public static function render_field_textarea($field)
	{
		Renderer::render_before_field($field);
		echo sprintf(
			'<textarea class="%1$s" id="%2$s" name="%2$s">%3$s</textarea>',
			esc_attr(Renderer::get_block_element_class_with_namespace($field['type'])),
			esc_attr($field['id']),
			esc_html($field['value'])
		);
		Renderer::render_after_field();
	}

	public static function render_field_checkbox($field)
	{
		Renderer::render_before_field($field);
		echo sprintf(
			'<input type="checkbox" class="%1$s" id="%2$s" name="%2$s" %3$s>',
			esc_attr(Renderer::get_block_element_class_with_namespace($field['type'])),
			esc_attr($field['id']),
			checked(!empty($field['value']), true, false)
		);
		Renderer::render_after_field($field); // pass in $field to render desc below input
	}

	public static function render_field_html($field)
	{
		Renderer::render_before_field($field);
		echo $field['html'];
		Renderer::render_after_field();
	}

	public static function render_field_image($field)
	{
		Renderer::render_before_field($field); // pass in $meta for preview image
		echo sprintf(
			'<input type="hidden" id="%s" name="%s" value="%s">',
			esc_attr('image-' . $field['id']),
			esc_attr($field['id']),
			(isset($field['value']) ? $field['value'] : '')
		);
		echo sprintf(
			'<a class="%s button" data-hidden-input="%s">%s</a>',
			esc_attr(sprintf('js-%s-image-upload-button', Utils::KEBAB)),
			esc_attr($field['id']),
			esc_html(sprintf('%s Image', empty($field['value']) ? 'Upload' : 'Change'))
		);
		Renderer::render_after_field();
	}

	public static function render_field_Editor($field)
	{
		Renderer::render_before_field($field);
		wp_editor($field['value'], $field['id']);
		Renderer::render_after_field();
	}

	public static function render_field_radio($field)
	{
		Renderer::render_before_field($field);
		foreach ($field['options'] as $key => $value) {
			echo sprintf(
				'
                    <label for="%1$s">%2$s</label>
                    <input type="radio" class="%3$s" id="%1$s" name="%4$s" value="%5$s" %6$s>
                ',
				esc_attr($field['id'] . '_' . $key),
				esc_html($value),
				esc_attr(Renderer::get_block_element_class_with_namespace($field['type'])),
				esc_attr($field['id']),
				esc_attr($key),
				checked($key == $field['value'], true, false)
			);
		}
		Renderer::render_after_field($field); // pass in $field to render desc below input
	}

	public static function render_field_select($field)
	{
		Renderer::render_before_field($field);
		echo '<select name="' . esc_attr($field['id']) . '">';
		foreach ($field['options'] as $key => $value) {
			echo sprintf(
				'
                    <option class="%3$s" id="%1$s" name="%4$s" value="%5$s" %6$s>%2$s</option>
                ',
				esc_attr($field['id'] . '_' . $key),
				esc_html($value),
				esc_attr(Renderer::get_block_element_class_with_namespace($field['type'])),
				esc_attr($field['id']),
				esc_attr($key),
				selected($key == $field['value'], true, false)
			);
		}
		echo '</select>';
		Renderer::render_after_field($field); // pass in $field to render desc below input
	}


	public static function render_field_repeater($field): void
	{

		Renderer::render_before_field($field);

		echo sprintf(
			'<div id="%s" class="%s">',
			esc_attr(sprintf('js-%s-repeated-blocks', $field['id'])),
			esc_attr(Renderer::get_block_element_class_with_namespace('repeated-blocks', false))
		);

		$count = 0;
		if (is_array($field['value'])) {
			if (count($field['value']) > 0) {
				foreach ($field['value'] as $m) {
					self::render_repeated_block($field, $m, $count);
					$count++;
				}
			}
		} else {
			self::render_repeated_block($field, '', $count);
		}

		echo '</div>';

		// "add" button
		echo sprintf(
			'<a id="%s" class="%s button">
                    <span class="dashicons dashicons-plus"></span>
                    %s
                </a>',
			esc_attr(sprintf('js-%s-add', $field['id'])),
			esc_attr(Renderer::get_block_element_class_with_namespace('add', false)),
			esc_html(sprintf('Add %s', $field['single_label']))
		);

		Renderer::render_after_field();

		// create a repeater block to use for the "add" functionality
		ob_start();

		sprintf('<div>%s</div>', esc_html(self::render_repeated_block($field, $field['value'], null, true)));

		$js_code = ob_get_clean();
		$js_code = str_replace("\n", '', $js_code);
		$js_code = str_replace("\r", '', $js_code);
		$js_code = str_replace("'", '"', $js_code);

		/**
		 * JS to add another repeated block
		 */
		echo '<script>
                    jQuery(document).ready(function($) {
                        var count = ' . max(1, $count) . '; // we use max() because we want count to be at least 1

                        $("#js-' . $field['id'] . '-add").on("click", function() {
                            var repeater = \'' . $js_code . '\'
                                .replace(/' . self::REPEATER_INDEX_PLACEHOLDER . '/g, count)
                                .replace(/' . self::REPEATER_ITEM_NUMBER_PLACEHOLDER . '/g, count + 1);
                            $("#js-' . $field['id'] . '-repeated-blocks").append(repeater);
                            count++;
                            return false;
                        });
                    });
            </script>';
	}

	public static function render_repeated_block($field, $meta, $index, $isTemplate = false)
	{
		echo sprintf(
			'<div class="%s">',
			esc_attr(Renderer::get_block_element_class_with_namespace('repeated', false))
		);

		// block header
		echo sprintf(
			'<div class="%s %s">
                    <p class="%s">%s</p>
                    <ul class="%s">
                        <li>
                            <a class="%s %s" title="%s">
                                <span class="dashicons dashicons-no"></span>
                            </a>
                        </li>
                        <li>
                            <a class="%s %s" title="Click and drag to sort">
                                <span class="dashicons dashicons-menu"></span>
                            </a>
                        </li>
                    </ul>
                </div>',
			esc_attr(Renderer::get_element_class_with_namespace('repeated-header', false)),
			esc_attr(Renderer::get_element_class_with_namespace('clearfix')),
			esc_attr(sprintf('%s %s %s', Renderer::get_block_element_class('repeated-header', 'title'), Renderer::get_element_class_with_namespace('col'), Renderer::get_element_class_with_namespace('col-6'))),
			esc_html(sprintf('%s ' . ($isTemplate ? '%s' : '%d'), $field['single_label'], ($isTemplate ? self::REPEATER_ITEM_NUMBER_PLACEHOLDER : $index + 1))),
			esc_attr(sprintf('%s %s %s', Renderer::get_block_element_class('repeated-header', 'nav'), Renderer::get_element_class_with_namespace('col'), Renderer::get_element_class_with_namespace('col-6'))),
			esc_attr(Renderer::get_block_element_class_with_namespace('repeater-button', false)),
			esc_attr(Renderer::get_block_element_class_with_namespace('remove', false)),
			esc_attr(sprintf('Remove %s', $field['single_label'])),
			esc_attr(Renderer::get_block_element_class_with_namespace('repeater-button', false)),
			esc_attr(sprintf('js-%s-sort', Utils::KEBAB))
		);

		echo sprintf('<div class="%s is-hidden">', esc_attr(Renderer::get_block_element_class_with_namespace('repeated-content', false)));
		// populate block with fields

		foreach ($field['fields'] as $child_field) {
			$old_id = $child_field['id'];

			$child_field['id'] = sprintf(
				'%s[%s][%s]',
				$field['id'],
				($isTemplate ? self::REPEATER_INDEX_PLACEHOLDER : $index),
				$child_field['id']
			);

			$child_field['value'] = isset($meta[$old_id]) && !$isTemplate ? $meta[$old_id] : '';

			call_user_func(array('self', 'render_field_' . $child_field['type']), $child_field);
		}
		echo '</div></div>';
	}
}
