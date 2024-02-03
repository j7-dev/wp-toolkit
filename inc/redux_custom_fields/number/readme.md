
![image](https://github.com/j7-dev/wp-toolkit/assets/9213776/1550fb08-8dea-48b1-b842-26717d4d9ac1)


```php

$this->sections[] = [
			'id'               => 'my_section',
			'fields' => [
				[
					'id'       => 'custom_field_test',
					'type'     => 'number',
					'title'    => 'Custom Field',
					'attributes'       => array(
						'min'         => 0,
						'max'     => 1000,
						'step' => 10,
						'addon_before' => '新台幣',
						'addon_after' => '元整',
					)
				]
			]
]

```

