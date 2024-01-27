<?php

declare(strict_types=1);

namespace J7\WpToolkit\Utils;

use J7\WpToolkit\Utils;

class Functions
{
	/**
	 * Register CPT
	 *
	 * @param string $label - the name of CPT
	 * @param array $meta_keys - the meta keys of CPT ex ['meta', 'settings']
	 * @return void
	 */
	public static function register_cpt($label, $text_domain): void
	{

		$kebab = str_replace(' ', '-', strtolower($label));

		$labels = [
			'name'                     => \esc_html__($label, $text_domain),
			'singular_name'            => \esc_html__($label, $text_domain),
			'add_new'                  => \esc_html__('Add new', $text_domain),
			'add_new_item'             => \esc_html__('Add new item', $text_domain),
			'edit_item'                => \esc_html__('Edit', $text_domain),
			'new_item'                 => \esc_html__('New', $text_domain),
			'view_item'                => \esc_html__('View', $text_domain),
			'view_items'               => \esc_html__('View', $text_domain),
			'search_items'             => \esc_html__('Search ' . $label, $text_domain),
			'not_found'                => \esc_html__('Not Found', $text_domain),
			'not_found_in_trash'       => \esc_html__('Not found in trash', $text_domain),
			'parent_item_colon'        => \esc_html__('Parent item', $text_domain),
			'all_items'                => \esc_html__('All', $text_domain),
			'archives'                 => \esc_html__($label . ' archives', $text_domain),
			'attributes'               => \esc_html__($label . ' attributes', $text_domain),
			'insert_into_item'         => \esc_html__('Insert to this ' . $label, $text_domain),
			'uploaded_to_this_item'    => \esc_html__('Uploaded to this ' . $label, $text_domain),
			'featured_image'           => \esc_html__('Featured image', $text_domain),
			'set_featured_image'       => \esc_html__('Set featured image', $text_domain),
			'remove_featured_image'    => \esc_html__('Remove featured image', $text_domain),
			'use_featured_image'       => \esc_html__('Use featured image', $text_domain),
			'menu_name'                => \esc_html__($label, $text_domain),
			'filter_items_list'        => \esc_html__('Filter ' . $label . ' list', $text_domain),
			'filter_by_date'           => \esc_html__('Filter by date', $text_domain),
			'items_list_navigation'    => \esc_html__($label . ' list navigation', $text_domain),
			'items_list'               => \esc_html__($label . ' list', $text_domain),
			'item_published'           => \esc_html__($label . ' published', $text_domain),
			'item_published_privately' => \esc_html__($label . ' published privately', $text_domain),
			'item_reverted_to_draft'   => \esc_html__($label . ' reverted to draft', $text_domain),
			'item_scheduled'           => \esc_html__($label . ' scheduled', $text_domain),
			'item_updated'             => \esc_html__($label . ' updated', $text_domain),
		];
		$args = [
			'label'                 => \esc_html__($label, $text_domain),
			'labels'                => $labels,
			'description'           => '',
			'public'                => true,
			'hierarchical'          => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'     => false,
			'show_in_rest'          => true,
			'query_var'             => false,
			'can_export'            => true,
			'delete_with_user'      => true,
			'has_archive'           => false,
			'rest_base'             => '',
			'show_in_menu'          => true,
			'menu_position'         => 6,
			'menu_icon'             => 'dashicons-store',
			'capability_type'       => 'post',
			'supports'              => ['title', 'editor', 'thumbnail', 'custom-fields', 'author'],
			'taxonomies'            => [],
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'rewrite'               => [
				'with_front' => true,
			],
		];

		\register_post_type($kebab, $args);
	}

	/**
	 * JSON Parse
	 */
	public static function json_parse($stringfy, $default = [])
	{
		$out_put = '';
		try {
			$out_put = json_decode(str_replace('\\', '', $stringfy));
		} catch (\Throwable $th) {
			$out_put = $default;
		} finally {
			return $out_put;
		}
	}
}
