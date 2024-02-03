<?php

declare(strict_types=1);

namespace J7\WpToolkit\Utils;

use J7\WpToolkit\Utils;

class Api
{
	const POSTMETA_API_ENDPOINT = 'postmeta';

	function __construct()
	{
		foreach ([self::POSTMETA_API_ENDPOINT] as $action) {
			\add_action('rest_api_init', [$this, "register_{$action}_api"]);
		}
	}

	public function postmeta_callback($request)
	{
		$post_id = $request['id'];

		// 檢查文章是否存在
		if (\get_post_status($post_id)) {
			$post_meta           = \get_post_meta($post_id);
			$formatted_post_meta = [];
			foreach ($post_meta as $key => $value) {
				$formatted_post_meta[$key] = $value[0];
			}

			// 在此處理 post_meta 資訊，你可以根據需要進行資料處理

			return \rest_ensure_response($formatted_post_meta);
		} else {
			return new \WP_Error('post_not_found', '文章不存在', array('status' => 404));
		}
	}

	public function register_postmeta_api()
	{
		$endpoint = self::POSTMETA_API_ENDPOINT;
		\register_rest_route(Utils::KEBAB, "{$endpoint}/(?P<id>\d+)", array(
			'methods'  => 'GET',
			'callback' => [$this, "{$endpoint}_callback"],
			'permission_callback' => '__return_true'
		));
	}
}

new Api();
