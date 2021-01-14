<?php
/* Lookup for words in DB 
 * Input: $_REQUEST['q', 'dicts', 'output']
 * Output: Text or JSON */
require('library.php');

$limit = @filter_var(@$_REQUEST['n'], FILTER_VALIDATE_INT) ?
	 $_REQUEST['n'] : 5;

$q = sanitize_string(get_from_user(@$_REQUEST['q']));

$recv_dicts = get_from_user(@$_REQUEST['dicts']);
if($recv_dicts == "all") $dicts = dict_list();
else $dicts = explode(',' , $recv_dicts);
$output_type = get_from_user(@$_REQUEST['output']);

$t0 = microtime(true);
$results = lookup($q, $dicts, $limit);
$t1 = microtime(true);

$dt = kurdish_numbers(number_format($t1-$t0, 3));

if($output_type == 'json')
{
	$results = slice_results($results, $limit);
	$results['time'] = $dt;
	header('Content-type:application/json; charset=utf-8');
	echo json_encode($results);
}
else
{
	$toprint = 'گەڕان ' . $dt . "چرکەی خایاند.\n";
	if($results) {
		foreach($results as $rank=>$arr)
		{
			foreach($arr as $o) {
				if($limit-- == 0) break 2;
				$o = fetch_word($o[0], $o[1]);
				@$toprint .= "{$o[0]}\t{$rank}\t{$o[1]}\t{$o[2]}\n";
			}
		}
	}
	
	header('Content-type:text/plain; charset=utf-8');
	echo trim($toprint);
}
?>
