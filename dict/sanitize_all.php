<?php
require('library.php');

$dicts = dict_list();
foreach($dicts as $o)
{
    echo "\n$o: \n";
    exec("cd '$o' && php sanitize.php");

    if(in_array($o, ['dictio'])) continue;
    
    /* Replace space with dash. */
    $new_lines = [];
    
    $f = fopen("$o/$o.txt", 'r');
    while(! feof($f))
    {
	$line = explode("\t", trim(fgets($f)));
	if(@$line[1])
	{
	    $w = trim($line[0]);
	    $w = str_replace('ه‌', 'ە', $w);
	    $w = preg_replace('/\s+/u', ' ', $w);
	    $m = trim($line[1]);
	    $new_lines[] = "$w\t$m";
	}
    }
    file_put_contents("$o/$o.txt", implode("\n", $new_lines));
}
?>
