# ArrayDiff

Compute the changes between two arrays.

Work in progress.

Given two arrays:
```php
$old = [
	'a' => 1,
	'b' => 2,
	'c' => 3
];
$new = [
	'b' => 2,
	'c' => 5
]
```

We should expect an output of:
```php
$differ = new \Differ\ArrayDiff();
$difference = $differ->diff($old, $new);

var_dump($difference);


```
