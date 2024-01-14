<?php

declare(strict_types=1);

namespace J7\WpToolkit;

class Metabox extends Core
{
	/**
	 * Stores the metabox config that
	 * is supplied to the constructor.
	 *
	 * @var array
	 */
	private $_meta_box_config;
	private $_nonce_action;
	private $_nonce_name;

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
		$this->_config = $meta_box_config;
		$this->_fields = [];
		$this->_nonce_name  = $meta_box_config['id'] ?? '' . '_nonce';
		$this->_nonce_action  = $meta_box_config['id'] ?? '' . '_action';

		$defaults = array(
			'context'  => 'advanced',
			'priority' => 'default',
		);

		$this->_meta_box_config    = array_merge($defaults, $meta_box_config);

		\add_action('add_meta_boxes', array($this, 'add'));
		\add_action('save_post', array($this, 'save'));
		\add_action('admin_enqueue_scripts', array($this, 'scripts'));
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
			$this->_meta_box_config['id'],
			$this->_meta_box_config['title'],
			array($this, 'render'),
			$this->_meta_box_config['screen'],
			$this->_meta_box_config['context'],
			$this->_meta_box_config['priority']
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
			$field['value'] = ($order) ? $order->get_meta($field['id']) : \get_post_meta($post->ID, $field['id'], true);

			\call_user_func(['J7\WpToolkit\Components\Form', 'render_field_' . $field['type']], $field);
		}
		echo '</div>';
	}

	protected function can_save(): bool
	{
		global $post_id;
		return !((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || // prevent the data from being auto-saved
			(!\current_user_can('edit_post', $post_id)) || // check user permissions
			((!isset($_POST[$this->_nonce_name]))) || // verify nonce (same with below)
			(!\wp_verify_nonce($_POST[$this->_nonce_name], $this->_nonce_action)));
	}
}
