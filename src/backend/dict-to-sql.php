<?php
require('library.php');

$dicts = dict_list();
foreach($dicts as $o)
{
    if(in_array($o , ['dictio']))
	continue;
    echo "$o\n";
    dict_to_sql($o);
}
?>
