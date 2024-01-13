<?php



namespace J7\WpToolkit;

use J7\WpToolkit\Utils;
use J7\WpToolkit\Utils\Renderer;

class Metabox extends Core
{



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
}
