<?php

declare(strict_types=1);

namespace J7\WpToolkit\Components;

use J7\WpToolkit\Utils;

abstract class Components
{
	public $key;
	public $instance = null;
	public $default_props = [];
	public $props = [];

	function __construct($key)
	{

		$this->key = $key;
		$this->instance = $this;
		$this->props = $this->default_props;
		// $this->render();
	}

	public function set_props($new_props): void
	{
		$props = array_merge($this->props, $new_props);
		$this->props = $props;
	}

	public function get_props(): array
	{
		return $this->props;
	}

	public function get_instance(): ?self
	{
		return $this->instance;
	}

	public function render()
	{
		\load_template(__DIR__ . '/' . basename(get_class($this)) . '/view.php', false, $this->get_props());
	}
}
