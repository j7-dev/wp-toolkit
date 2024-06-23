<?php
// phpcs:disable

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Extension_Callback', false ) ) {
	final class Redux_Extension_Callback extends Redux_Extension_Abstract {
		public static $version = '4.3.15';

		public string $extension_name = 'Callback';

		/**
		 * Redux_Extension_Callback constructor.
		 *
		 * @param object $redux ReduxFramework pointer.
		 */
		public function __construct( $redux ) {
			parent::__construct( $redux, __FILE__ );

			$this->add_field( 'callback' );
		}
	}
}
// phpcs:enable
