<?php



namespace J7\WpToolkit;

use J7\WpToolkit\Utils;
use J7\WpToolkit\Utils\Renderer;

class Metabox extends Core
{

	const REPEATER_INDEX_PLACEHOLDER       = 'CurrentCounter';
	const REPEATER_ITEM_NUMBER_PLACEHOLDER = 'ItemNumber';

	/**
	 * Stores the metabox config that
	 * is supplied to the constructor.
	 *
	 * @var array
	 */
	private $_meta_box;

	private $_folder_name;



	/**
	 * Class constructor.
	 *
	 * @param array $args An associative array with the following keys:
	 *   - 'id'       (string) The metabox ID.
	 *   - 'title'    (string) The title of the metabox.
	 *   - 'screen'   (string) The post type for which to display the metabox.
	 *   - 'context'  (string) The context in which to display the metabox. Options: normal, side, advanced.
	 *   - 'priority' (string) The priority of the metabox. Default is 'default'.
	 * @return void
	 */
	public function __construct($meta_box_config)
	{
		parent::__construct($meta_box_config);
		$this->_fields = [];

		$defaults = array(
			'context'  => 'advanced',
			'priority' => 'default',
		);

		$this->_meta_box    = array_merge($defaults, $meta_box_config);
		$this->_folder_name = 'wp-metabox-constructor-class';

		\add_action('add_meta_boxes', array($this, 'add'));
		\add_action('save_post', array($this, 'save'));
	}

	public static function init($meta_box_config): self
	{
		$instance = new self($meta_box_config);
		$instance->_instance = $instance;
		return $instance->_instance;
	}

	public function add(): void
	{
		// TODO 額外參數要帶入嗎?
		add_meta_box(
			$this->_meta_box['id'],
			$this->_meta_box['title'],
			array($this, 'render'),
			$this->_meta_box['screen'],
			$this->_meta_box['context'],
			$this->_meta_box['priority']
		);
	}

	/**
	 * Saves the data supplied to the metabox.
	 *
	 * @return void
	 */
	public function save(): void
	{
		global $post;

		if (!$this->can_save()) {
			return;
		}

		foreach ($this->_fields as $field) {
			if (isset($_POST[$field['id']])) {
				if ($field['type'] == 'text' || $field['type'] == 'textarea') {
					\update_post_meta($post->ID, $field['id'], \sanitize_text_field($_POST[$field['id']]));
				} else {
					\update_post_meta($post->ID, $field['id'], $_POST[$field['id']]);
				}
			} else {
				delete_post_meta($post->ID, $field['id']);
			}
		}
	}


	public function addField(array $field, bool $repeatable = false)
	{
		if (!$repeatable) {
			$this->_fields[] = $field;
		} else {
			return $field;
		}
	}

	public function addText(array $args, bool $repeatable = false)
	{
		$field = array_merge(array('type' => 'text'), $args);
		return $this->addField($field, $repeatable);
	}

	public function addTextArea(array $args, bool $repeatable = false)
	{
		$field = array_merge(array('type' => 'textarea'), $args);
		return $this->addField($field, $repeatable);
	}

	public function addCheckbox(array $args, bool $repeatable = false)
	{
		$field = array_merge(array('type' => 'checkbox'), $args);
		return $this->addField($field, $repeatable);
	}

	public function addHtml(array $args, bool $repeatable = false)
	{
		$field = array_merge(array('type' => 'html'), $args);
		return $this->addField($field, $repeatable);
	}

	public function addImage($args, $repeatable = false)
	{
		$field = array_merge(array('type' => 'image'), $args);
		return $this->addField($field, $repeatable);
	}

	public function addEditor($args, $repeatable = false)
	{
		$field = array_merge(array('type' => 'Editor'), $args);
		return $this->addField($field, $repeatable);
	}

	public function addRadio($args, $options, $repeatable = false)
	{
		$options = array('options' => $options);
		$field   = array_merge(array('type' => 'radio'), $args, $options);
		return $this->addField($field, $repeatable);
	}

	public function addSelect($args, $options, $repeatable = false)
	{
		$options = array('options' => $options);
		$field   = array_merge(array('type' => 'select'), $args, $options);
		return $this->addField($field, $repeatable);
	}

	public function addRepeaterBlock($args)
	{
		$field           = array_merge(
			array(
				'type'         => 'repeater',
				'single_label' => 'Item',
				'is_sortable'  => true,
			),
			$args
		);

		$this->_fields[] = $field;
	}

	public function render_field_html($field, $meta)
	{
		Renderer::render_before_field($field);
		echo $field['html'];
		Renderer::render_after_field();
	}

	public function render_field_text($field, $meta)
	{
		Renderer::render_before_field($field);
		echo sprintf(
			'<input type="text" class="%1$s" id="%2$s" name="%2$s" value="%3$s">',
			esc_attr(Renderer::get_block_element_class_with_namespace($field['type'])),
			esc_attr($field['id']),
			esc_attr($meta)
		);
		Renderer::render_after_field();
	}

	public function render_field_textarea($field, $meta)
	{
		Renderer::render_before_field($field);
		echo sprintf(
			'<textarea class="%1$s" id="%2$s" name="%2$s">%3$s</textarea>',
			esc_attr(Renderer::get_block_element_class_with_namespace($field['type'])),
			esc_attr($field['id']),
			esc_html($meta)
		);
		Renderer::render_after_field();
	}

	public function render_field_checkbox($field, $meta)
	{
		Renderer::render_before_field($field);
		echo sprintf(
			'<input type="checkbox" class="%1$s" id="%2$s" name="%2$s" %3$s>',
			esc_attr(Renderer::get_block_element_class_with_namespace($field['type'])),
			esc_attr($field['id']),
			checked(!empty($meta), true, false)
		);
		Renderer::render_after_field($field); // pass in $field to render desc below input
	}

	public function render_field_image($field, $meta)
	{
		Renderer::render_before_field($field, $meta); // pass in $meta for preview image
		echo sprintf(
			'<input type="hidden" id="%s" name="%s" value="%s">',
			esc_attr('image-' . $field['id']),
			esc_attr($field['id']),
			(isset($meta) ? $meta : '')
		);
		echo sprintf(
			'<a class="%s button" data-hidden-input="%s">%s</a>',
			esc_attr(sprintf('js-%s-image-upload-button', Utils::KEBAB)),
			esc_attr($field['id']),
			esc_html(sprintf('%s Image', empty($meta) ? 'Upload' : 'Change'))
		);
		Renderer::render_after_field();
	}

	public function render_field_Editor($field, $meta)
	{
		Renderer::render_before_field($field);
		wp_editor($meta, $field['id']);
		Renderer::render_after_field();
	}

	public function render_field_radio($field, $meta)
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
				checked($key == $meta, true, false)
			);
		}
		Renderer::render_after_field($field); // pass in $field to render desc below input
	}

	public function render_field_select($field, $meta)
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
				selected($key == $meta, true, false)
			);
		}
		echo '</select>';
		Renderer::render_after_field($field); // pass in $field to render desc below input
	}

	public function render_field_repeater($field, $meta): void
	{

		Renderer::render_before_field($field);

		echo sprintf(
			'<div id="%s" class="%s">',
			esc_attr(sprintf('js-%s-repeated-blocks', $field['id'])),
			esc_attr(Renderer::get_block_element_class_with_namespace('repeated-blocks', false))
		);

		$count = 0;
		if (is_array($meta)) {
			if (count($meta) > 0) {
				foreach ($meta as $m) {
					$this->render_repeated_block($field, $m, $count);
					$count++;
				}
			}
		} else {
			$this->render_repeated_block($field, '', $count);
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

		sprintf('<div>%s</div>', esc_html($this->render_repeated_block($field, $meta, null, true)));

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

	public function render_repeated_block($field, $meta, $index, $isTemplate = false)
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

			$child_meta = isset($meta[$old_id]) && !$isTemplate ? $meta[$old_id] : '';

			call_user_func(array($this, 'render_field_' . $child_field['type']), $child_field, $child_meta);
		}
		echo '</div></div>';
	}
}
