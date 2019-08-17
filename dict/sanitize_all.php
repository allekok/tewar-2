<?php
require('library.php');

$dicts = dict_list();
foreach($dicts as $o)
{
    echo "\n$o: \n";
    exec("cd '$o' && php sanitize.php");
}
?>
