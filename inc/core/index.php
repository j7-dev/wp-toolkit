<?php

declare(strict_types=1);

namespace J7\WpToolkit;

use J7\WpToolkit\Utils;


class Core
{
	protected static $_instance;
	protected static $_config = [];


	/**
	 * Stores the fields supplied to the
	 * metabox.
	 *
	 * @var array
	 */
	protected $_fields = [];
	protected $_field_types;


	// TODO remove
	function __construct($config)
	{
		$this->_config = $config;
		\add_action('admin_enqueue_scripts', array($this, 'scripts'));
	}

	public function get_instance(): self
	{
		return $this->_instance;
	}

	public function scripts(): void
	{
		$class = get_class($this);

		if (!Utils::is_in_screens($this->_config['screen'], $class)) {
			return;
		}

		$wp_version = get_bloginfo('version');
		if (version_compare($wp_version, '6.3', '>')) {
			$args = [
				'strategy' => 'async', // 'defer' or 'async'
			];
		} else {
			$args = [];
		}


		\wp_enqueue_style(Utils::KEBAB . '-style', Utils::get_plugin_url() . '/assets/style.css', array(), Utils::get_plugin_ver(), null);

		\wp_enqueue_script('jquery');

		// \wp_enqueue_script(Utils::KEBAB . '-script', Utils::get_plugin_url() . '/assets/script.js', array('jquery'), Utils::get_plugin_ver(), $args);

		\wp_enqueue_script(Utils::KEBAB . '-script', Utils::get_plugin_url() . '/assets/script.js', array('jquery'), Utils::get_plugin_ver(), $args);

		if (in_array('media', $this->get_field_types()) || in_array('file', $this->get_field_types())) {
			\wp_enqueue_media();
		}

		// Load Color Picker if required
		if (in_array('color', $this->get_field_types())) {
			\wp_enqueue_style('wp-color-picker');
			\wp_enqueue_script('wp-color-picker');
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

	/*
	 * @return array configured field types
	 */
	public function get_field_types()
	{

		foreach ($this->_fields as $field) {
			if (isset($field['type'])) {
				$this->_field_types[] = \sanitize_key($field['type']);
			}
		}

		return array_unique($this->_field_types);
	}
}
