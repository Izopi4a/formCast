formCast
========

cast forms with php



example usage: 

$_GET['query'] = 'a"\sd';

$form = new Forms\Cast($_GET, array(
    'query' => array('name', 'string')
));

$vars = $form->getData();

it will print: 
[name] = "asd";

or just use

$form = new Forms\Cast($_GET, array(
    'query' => 'string'
));

$vars = $form->getData();

it will print

[query] = "asd"
