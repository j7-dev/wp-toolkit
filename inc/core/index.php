<?php

declare(strict_types=1);

namespace J7\WpToolkit;

use J7\WpToolkit\Utils;


class Core
{
	protected static $_instance;
	protected static $_config = [];
	protected $_nonce_action;
	protected $_nonce_name;

	/**
	 * Stores the fields supplied to the
	 * metabox.
	 *
	 * @var array
	 */
	protected $_fields;

	function __construct($config)
	{
		$this->_config = $config;
		$this->_nonce_name  = $config['id'] . '_nonce';
		$this->_nonce_action  = $config['id'] . '_action';
		\add_action('admin_enqueue_scripts', array($this, 'scripts'));
	}

	public function get_instance(): self
	{
		return $this->_instance;
	}

	public function scripts(): void
	{
		\wp_enqueue_media();

		if (Utils::is_in_screens($this->_config['screen'])) {
			\wp_enqueue_style('wp-metabox-style', Utils::get_plugin_url() . '/assets/style.css', array(), Utils::get_plugin_ver(), null);
			\wp_enqueue_script('wp-metabox-script', Utils::get_plugin_url() . '/assets/script.js', array('jquery'), array(), Utils::get_plugin_ver(), true);
		}
	}

	protected function can_save(): bool
	{
		global $post_id;
		return !((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || // prevent the data from being auto-saved
			(!\current_user_can('edit_post', $post_id)) || // check user permissions
			((!isset($_POST[$this->_nonce_name]))) || // verify nonce (same with below)
			(!\wp_verify_nonce($_POST[$this->_nonce_name], $this->_nonce_action)));
	}

	/**
	 * An aggregate function that renders tye contents of the metabox
	 * by calling the appropriate, individual function for each
	 * field type.
	 *
	 * @return void
	 */
	public function render($post_or_order_object): void
	{
		global $post;

		$order = ($post_or_order_object instanceof \WP_Post) ? wc_get_order($post_or_order_object->ID) : $post_or_order_object;


		wp_nonce_field($this->_nonce_action, $this->_nonce_name);
		echo sprintf('<div class="%s">', Utils::KEBAB);
		foreach ($this->_fields as $field) {
			$meta = ($order) ? $order->get_meta($field['id']) : get_post_meta($post->ID, $field['id'], true);
			call_user_func(array('J7\WpToolkit\Components\Form', 'render_field_' . $field['type']), $field, $meta);
		}
		echo '</div>';
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
}
