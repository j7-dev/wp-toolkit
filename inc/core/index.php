<?php

declare(strict_types=1);

namespace J7\WpToolkit;

use J7\WpToolkit\Utils;

class Core
{

	protected $screen;
	protected $_nonce_action;
	protected $_nonce_name;

	function __construct($meta_box_config)
	{
		$this->screen = $meta_box_config['screen'];
		$this->_nonce_name  = $meta_box_config['id'] . '_nonce';
		$this->_nonce_action  = $meta_box_config['id'] . '_action';
		\add_action('admin_enqueue_scripts', array($this, 'scripts'));
	}

	public function scripts()
	{

		wp_enqueue_media();

		if (Utils::is_in_screens($this->screen)) {
			wp_enqueue_style('wp-metabox-style', Utils::get_plugin_url() . '/assets/style.css', array(), Utils::get_plugin_ver(), null);
			wp_enqueue_script('wp-metabox-script', Utils::get_plugin_url() . '/assets/script.js', array('jquery'), array(), Utils::get_plugin_ver(), true);
		}
	}
}
