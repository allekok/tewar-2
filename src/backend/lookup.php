<?php
/* Lookup for words in DB 
 * Input: $_REQUEST['q', 'dicts', 'output']
 * Output: Text or JSON */
require('library.php');

$q = get_from_user(@$_REQUEST['q']);
$dicts = explode(',' , get_from_user(@$_REQUEST['dicts']));
$output_type = get_from_user(@$_REQUEST['output']);
$results = [];

foreach($dicts as $dict)
{
    $results[$dict] = lookup($q, $dict);
}

if($output_type == 'json')
{
    echo json_encode($results);
}
else
{
    $toprint = '';
    foreach($results as $dict=>$result)
    {
	foreach($result as $res)
	{
	    $toprint .= "$dict\t$res\n";
	}
    }
    echo trim($toprint);
}
?>
