# ArrayDiff

![](https://travis-ci.org/charliekassel/array-diff.svg?branch=master) [![Coverage Status](https://coveralls.io/repos/github/charliekassel/array-diff/badge.svg?branch=master)](https://coveralls.io/github/charliekassel/array-diff?branch=master)

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

array(3) {
  'added' =>
  array(0) {
  }
  'removed' =>
  array(1) {
    'a' =>
    int(1)
  }
  'changed' =>
  array(1) {
    'c' =>
    array(2) {
      'old' =>
      int(3)
      'new' =>
      int(5)
    }
  }
}

```
