<?php
defined('ABSPATH') || exit;

if (!class_exists('Redux_Extension_Number', false)) {

	class Redux_Extension_Number extends Redux_Extension_Abstract
	{
		public static $version = '4.3.15';

		public $extension_name = 'Number';

		/**
		 * Redux_Extension_Number constructor.
		 *
		 * @param object $redux ReduxFramework pointer.
		 */
		public function __construct($redux)
		{
			parent::__construct($redux, __FILE__);

			$this->add_field('number');
		}
	}
}
