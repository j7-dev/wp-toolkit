<?php

defined('ABSPATH') || exit;

if (!class_exists('Redux_Example', false)) {

	class Redux_Example extends Redux_Field
	{

		/**
		 * 設定欄位預設 args
		 */
		public function set_defaults()
		{
			$defaults = [];

			$this->field = wp_parse_args($this->field, $defaults);
		}
		/**
		 * 渲染函數
		 * 可以用 $this->field 取得相關 args 設定
		 * @return void
		 */
		public function render()
		{
?>
			This is a Example Field<br />
			<input class="regular-text" type="text" />
<?php
		}

		/**
		 * $this->url 取得目前檔案的路徑
		 * 如果要依照開發模式來載入最小化資源可以使用 $min = Redux_Functions::is_min();
		 * 如果是 開發模式， $min 就會是 .min ，否則是空字串
		 */
		// public function enqueue()
		// {
		// 	$min = Redux_Functions::is_min();

		// 	wp_enqueue_script(
		// 		'redux-field-example',
		// 		$this->url . 'redux-example' . $min . '.js',
		// 		array('jquery'),
		// 		Redux_Extension_Example::$version,
		// 		true
		// 	);

		// 	if ($this->parent->args['dev_mode']) {
		// 		wp_enqueue_style(
		// 			'redux-field-example',
		// 			$this->url . 'redux-example.css',
		// 			array(),
		// 			time()
		// 		);
		// 	}
		// }
	}
}
