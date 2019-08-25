<?php
require('../../config.php');

function dict_path ($dict_name)
{
    return DICT_PATH . "/$dict_name/$dict_name.txt";
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

    $dict_list = dict_list();
    $results = [];

    foreach($dicts_name as $dict_name)
    {
	if(! in_array($dict_name, $dict_list))
	    continue;

	$dict = dict($dict_name);
	
	foreach($q as $w)
	{
	    if(! @isset($results[$dict_name][$w]))
	    {
		$results[$dict_name][$w] = @$dict[$w];
	    }
	}
    }
    
    return $results;
}

function match_words ($string)
{
    $string = sanitize_string($string);
    $string = preg_replace('/\s+/u', ' ', $string);
    $string = explode(' ', $string);
    return $string;
}

function sanitize_string ($string)
{
    // Remove Punctuation Marks
    $to_remove = [
	'!','@','#','$','%','^','&','*','(',')',
	'=','_','+','\\','|','[',']','{','}',
	'"',"'",';',':','/','?','.',',','<','>',
	'،','؟','؛',
    ];

    $string = str_replace($to_remove, '', $string);
    $string = str_replace('‌', '-', $string);
    
    return $string;
}

function dict ($dict_name)
{
    $dict = [];
    
    $dict_path = dict_path($dict_name);
    $f = fopen($dict_path, 'r');
    while(! feof($f))
    {
	$line = explode("\t", trim(fgets($f)));
	if(@$line[1])
	{
	    $dict[$line[0]] = $line[1];
	}
    }
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
