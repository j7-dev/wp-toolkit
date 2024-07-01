<?php
// phpcs:disable

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Callback', false ) ) {
	final class Redux_Callback extends Redux_Field {

		/**
		 * 設定欄位預設 args
		 */
		public function set_defaults(): void {
			$defaults = [];

			$this->field = wp_parse_args( $this->field, $defaults );
		}

		/**
		 * 渲染函數
		 * 可以用 $this->field 取得相關 args 設定
		 *
		 * @return void
		 * @throws Exception If callback field does not have a valid callback function.
		 */
		public function render(): void {
			$callback = $this->field['callback'] ?? '';

			if ( ! is_callable( $callback ) ) {
				throw new Exception( 'Callback field must have a valid callback function' );
			}
			call_user_func( $callback, $this->field );
		}
	}
}
// phpcs:enable