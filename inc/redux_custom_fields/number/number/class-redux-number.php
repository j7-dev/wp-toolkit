<?php

defined('ABSPATH') || exit;

if (!class_exists('Redux_Number', false)) {

	class Redux_Number extends Redux_Field
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
			if (!empty($this->field['data']) && empty($this->field['options'])) {
				if (empty($this->field['args'])) {
					$this->field['args'] = array();
				}

				$this->field['options'] = $this->parent->wordpress_data->get($this->field['data'], $this->field['args'], $this->parent->args['opt_name'], $this->value);
				$this->field['class']  .= ' hasOptions ';
			}

			if (empty($this->value) && !empty($this->field['data']) && !empty($this->field['options'])) {
				$this->value = $this->field['options'];
			}

			$qtip_title = isset($this->field['text_hint']['title']) ? 'qtip-title="' . esc_attr($this->field['text_hint']['title']) . '" ' : '';
			$qtip_text  = isset($this->field['text_hint']['content']) ? 'qtip-content="' . esc_attr($this->field['text_hint']['content']) . '" ' : '';

			$readonly     = (isset($this->field['readonly']) && $this->field['readonly']) ? ' readonly="readonly"' : '';
			$autocomplete = (isset($this->field['autocomplete']) && false === $this->field['autocomplete']) ? ' autocomplete="off"' : '';

			// 這個 options 還不確定是幹嘛的  先保留
			if (isset($this->field['options']) && !empty($this->field['options'])) {
				$placeholder = '';

				if (isset($this->field['placeholder'])) {
					$placeholder = $this->field['placeholder'];
				}

				foreach ($this->field['options'] as $k => $v) {
					if (!empty($placeholder)) {
						$placeholder = (is_array($this->field['placeholder']) && isset($this->field['placeholder'][$k])) ? ' placeholder="' . esc_attr($this->field['placeholder'][$k]) . '" ' : '';
					}

					echo '<div class="input_wrapper">';
					echo '<label for="' . esc_attr($this->field['id'] . '-text-' . $k) . '">' . esc_html($v) . '</label> ';

					$value = $this->value[$k] ?? '';
					$value = !empty($this->value[$k]) ? $this->value[$k] : '';

					// phpcs:ignore WordPress.Security.EscapeOutput
					echo '<input type="number" id="' . esc_attr($this->field['id'] . '-number-' . $k) . '" ' . esc_attr($qtip_title) . esc_attr($qtip_text) . ' name="' . esc_attr($this->field['name'] . $this->field['name_suffix'] . '[' . esc_attr($k)) . ']" ' . $placeholder . ' value="' . esc_attr($value) . '" class="regular-text ' . esc_attr($this->field['class']) . '" ' . esc_html($readonly) . esc_html($autocomplete) . '/><br />';
					echo '</div>';
				}
			} else {

				$min = isset($this->field['attributes']['min']) ? ' min="' . esc_attr($this->field['attributes']['min']) . '" ' : '';
				$max = isset($this->field['attributes']['max']) ? ' max="' . esc_attr($this->field['attributes']['max']) . '" ' : '';
				$step = isset($this->field['attributes']['step']) ? ' step="' . esc_attr($this->field['attributes']['step']) . '" ' : '';

				$has_addon_before = isset($this->field['attributes']['addon_before']);
				$addon_before = $has_addon_before ? '<span class="tw-bg-gray-100 tw-rounded-l-md tw-flex tw-place-items-center tw-px-2 tw-border-solid tw-border-[1px] tw-border-[#8c8f94]">' . $this->field['attributes']['addon_before'] . '</span>' : '';
				$has_addon_after = isset($this->field['attributes']['addon_after']);
				$addon_after = $has_addon_after ? '<span class="tw-bg-gray-100 tw-rounded-r-md tw-flex tw-place-items-center tw-px-2 tw-border-solid tw-border-[1px] tw-border-[#8c8f94]">' . $this->field['attributes']['addon_after'] . '</span>' : '';

				$addon_class = '';
				$addon_class .= $has_addon_before ? ' tw-rounded-l-none tw-ml-0 tw-border-l-0 ' : '';
				$addon_class .= $has_addon_after ? ' tw-rounded-r-none tw-mr-0 tw-border-r-0 ' : '';

				// phpcs:ignore WordPress.Security.EscapeOutput
				echo "<div class='tw-flex regular-text'>";
				echo $addon_before;
				echo sprintf(
					'<input
				type="number"
				%2s
				%3s
				id="%4s"
				name="%5s"
				%5s
				%6s
				%7s
				%8s
				/>',
					$qtip_title,
					$qtip_text,
					esc_attr($this->field['id']),
					esc_attr($this->field['name'] . $this->field['name_suffix']),
					"placeholder='" . esc_attr($this->field['placeholder']) . "'",
					"value='" . esc_attr($this->value) . "'",
					"class='tw-flex-1 " . esc_attr($this->field['class']) . $addon_class . "'",
					esc_html($readonly) . esc_html($autocomplete) . $min . $max . $step
				);
				echo $addon_after;
				echo "</div>";
			}
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
		// 		'redux-field-number',
		// 		$this->url . 'redux-number' . $min . '.js',
		// 		array('jquery'),
		// 		Redux_Extension_Number::$version,
		// 		true
		// 	);

		// 	if ($this->parent->args['dev_mode']) {
		// 		wp_enqueue_style(
		// 			'redux-field-number',
		// 			$this->url . 'redux-number.css',
		// 			array(),
		// 			time()
		// 		);
		// 	}
		// }
	}
}
