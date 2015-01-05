Casting forms with php
========

Requirments
--------

php 5.5 +


Installation
--------

`
simply copy formCast.php into your work directory and dont forget to include it<br>
require_once(MY_PATH.'/cast.php');
`

How to use it
--------

`$vars = (new \Forms\Layer($_GET, array(
    'name'=> 'string',
    'password' => 'password',
    'age' => [
        'type' => 'int', //required
        'limit' => [0, 120],
        'default' => null
    ],
)))->getData();`