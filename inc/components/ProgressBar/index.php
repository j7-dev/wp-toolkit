<?php

declare(strict_types=1);

namespace J7\WpToolkit\Components;

use J7\WpToolkit\Utils;

/**
 * @property string $percent 0% - 100%
 * @property string $color hex color
 * @property string $size small, medium, large
 * @property bool $with_label show label
 */
class ProgressBar extends Components
{
	public $default_props = [
		'percent' => '30%', // 0% - 100%
		'color' => '#3668e6', // hex color
		'size' => 'small', // small, medium, large
		'with_label' => false,
	];
}
