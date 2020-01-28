<?php
/* Lookup for words in DB 
 * Input: $_REQUEST['q', 'dicts', 'output']
 * Output: Text or JSON */
require('library.php');

$q = match_words(get_from_user(@$_REQUEST['q']));
$dicts = explode(',' , get_from_user(@$_REQUEST['dicts']));
$output_type = get_from_user(@$_REQUEST['output']);

$t0 = microtime(true);
$results = lookup($q, $dicts);
$t1 = microtime(true);

$dt = kurdish_numbers(number_format($t1-$t0, 3));

if($output_type == 'json')
{
    $results['time'] = $dt;
    
    header('Content-type:application/json; charset=utf-8');
    echo json_encode($results);
}
else
{
    $toprint = 'گەڕان ' . $dt . "چرکەی خایاند.\n";
    foreach($results as $dict=>$result)
    {
	if($result)
	{
	    foreach($result as $w => $meaning)
	    {
		$toprint .= "$dict\t$w\t$meaning\n";
	    }
	}
    }
    
    header('Content-type:text/plain; charset=utf-8');
    echo trim($toprint);
}
?>
