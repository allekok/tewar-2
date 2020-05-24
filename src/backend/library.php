<?php
@include_once('../../config.php');

$extras = ["&#34;","&#39;","&laquo;","&raquo;","&rsaquo;",
	   "&lsaquo;","&bull;","&nbsp;","?", "!", "#", "&",
	   "*", "(", ")", "-","+", "=", "_","[", "]", "{",
	   "}","<",">","\\","/", "|", "'","\"", ";", ":", ",",
	   ".", "~", "`", "؟", "،", "»", "«","ـ","؛","›","‹","•","‌"];
$ar_signs =["ِ", "ُ", "ٓ", "ٰ", "ْ", "ٌ", "ٍ", "ً", "ّ", "َ"];
$replace = [
	"from"=>["ڕ","ڵ","وو","ط","ض","ذ","ظ","یی"],
	"to"=>["ر","ل","و","ت","ز","ز","ز","ی"],
];

function dict_path ($dict_name)
{
	return DICT_PATH . "/$dict_name/{$dict_name}.txt_search";
}

function dict_list ()
{
	$dicts = [];
	
	$d = opendir(DICT_PATH);
	while(false !== ($o = readdir($d)))
	{
		if(in_array($o, ['.','..','dictio']))
			continue;
		if(is_dir(DICT_PATH . "/$o"))
			$dicts[] = $o;
	}
	closedir($d);
	
	return $dicts;
}

function get_from_user ($request)
{
	return @ strtolower (trim (filter_var (
		$request,FILTER_SANITIZE_STRING)));
}

function lookup ($q, $dicts_name, $limit)
{
	if(! ($q and $dicts_name) )
		return NULL;

	$q_len = mb_strlen($q);
	$dict_list = dict_list();
	$results = array_fill(0, 100, []);

	foreach($dicts_name as $dict_name)
	{
		if(! in_array($dict_name, $dict_list))
			continue;
		
		$dict_path = dict_path($dict_name);
		$f = fopen($dict_path, 'r');
		while(! feof($f)) {
			if($limit == 0) break;
			$o = explode("\t", fgets($f));
			if($o[0] > $q_len) {
				$hs = $o[1]; $ndl = $q;
			}
			else {
				$hs = $q; $ndl = $o[1];
			}

			if(strpos($hs, $ndl) !== FALSE) {
				$rank = abs($o[0] - $q_len);
				if($rank == 0) $limit--;
				$results[$rank][] = [
					$dict_name, $o[2], trim($o[3])];
			}
		}
		fclose($f);
	}
	
	return $results;
}

function sanitize_string ($string)
{
	global $extras, $ar_signs, $replace;
	$string = str_replace($extras, "", $string);
	$string = str_replace($ar_signs, "", $string);
	$string = str_replace($replace["from"], $replace["to"], $string);
	$string = preg_replace("/\s+/u", "", $string);
	return $string;
}

function dict ($dict_name)
{
	$dict = [];
	
	$dict_path = dict_path($dict_name);
	$f = fopen($dict_path, 'r');
	while(! feof($f))
		$dict[] = explode("\t", trim(fgets($f)));
	fclose($f);
	
	return $dict;
}

function kurdish_numbers ($string)
{
	return str_replace(
		['1','2','3','4','5','6','7','8','9','0'],
		['١','٢','٣','٤','٥','٦','٧','٨','٩','٠'],
		$string);
}

function slice_results ($results, $limit) {
	$new_results = [];
	foreach($results as $arr) {
		foreach($arr as $o) {
			if($limit-- == 0) break 2;
			$new_results[] = $o;
		}
	}
	return $new_results;
}
?>
