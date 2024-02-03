<?php
defined('ABSPATH') || exit;

if (!class_exists('Redux_Extension_Example', false)) {

	class Redux_Extension_Example extends Redux_Extension_Abstract
	{
		public static $version = '4.3.15';

		public $extension_name = 'Example';

		/**
		 * Redux_Extension_Example constructor.
		 *
		 * @param object $redux ReduxFramework pointer.
		 */
		public function __construct($redux)
		{
			parent::__construct($redux, __FILE__);

			$this->add_field('example');
		}
	}
}
