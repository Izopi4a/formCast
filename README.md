Casting forms with php
========

Requirments
--------

php 5.5 +


Installation
--------

simply copy formCast.php into your work directory and dont forget to include it

```php
    require_once(MY_PATH.'/cast.php');
```

How to use it
--------

```php

$_GET['name'] = 'my na"me is\drop database';
$_GET['password'] = 'my@@!$#$SDFpassword';
$_GET['age'] = 151;
$_GET['colors'] = [
    1,2,3,"koko"
];

$vars = (new \Forms\Layer($_GET, array(
    'name'=> 'string',
    'password' => 'password',
    'age' => [
        'type' => 'int', //required
        'limit' => [0, 120],
        'default' => null
    ],
    'colors' = 'ArrayInt'
)))->getData();

```


print_r($vars) will show:

```php

array(
    "name" => 'my name is drop database',
    "password" => 'my@@!$#$SDFpassword',
    "age" => null,
    "colors" => array(
        [0] => 1,
        [1] => 2,
        [2] => 3
    )
)
```