<?php
[
	'percent' => $percent,
	'color' => $color,
	'size' => $size,
	'with_label' => $with_label,
] = $args;

[
	'height' => $height,
	'fontSize' => $fontSize,
] = (function () use ($size) {
	switch ($size) {
		case 'small':
			$height = '0.375rem';
			$fontSize = '0.25rem';
			break;
		case 'medium':
			$height = '0.625rem';
			$fontSize = '0.5rem';
			break;
		case 'large':
			$height = '1rem';
			$fontSize = '0.75rem';
			break;
		default:
			$height = '0.625rem';
			$fontSize = '0.5rem';
			break;
	}
	return [
		'height' => $height,
		'fontSize' => $fontSize,
	];
})();

$style = "";
$style .= "width: {$percent};";
$style .= "background-color: {$color};";
$style .= "height: {$height};";
$style .= "font-size: {$fontSize};";

?>

<div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden" style="height: <?= $height ?>;">
	<div class="h-2.5 rounded-full text-center text-white" style="<?= $style ?>">
		<?= $percent ?></div>
</div>