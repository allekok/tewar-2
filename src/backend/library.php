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

function lookup ($q, $dicts_name)
{
    if(! ($q and $dicts_name) )
	return NULL;

    $q_len = mb_strlen($q);
    $dict_list = dict_list();
    $results = [];

    foreach($dicts_name as $dict_name)
    {
	if(! in_array($dict_name, $dict_list))
	    continue;

	$results[$dict_name] = [];
	$dict = dict($dict_name);
	foreach($dict as $o) {
	    if(mb_strpos($o[1], $q) !== FALSE or
		mb_strpos($q, $o[1] !== FALSE)) {
		$results[$dict_name][] = [abs($o[0] - $q_len), $o[2], $o[3]];
	    }
	}
	sort($results[$dict_name]);
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
?>
