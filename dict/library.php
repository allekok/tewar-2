<?php
const meta_file_name = 'meta.txt';

function number_of_lines ($string)
{
    $line_num = 1;
    $char_num = 0;
    while(false !== ($c = substr($string, $char_num, 1)))
    {
	if($c == "\n") $line_num++;
	$char_num++;
    }
    return $line_num;
}

function sanitize_string ($string)
{
    $string = str_replace(["\r\n", ' ',],
			  [  "\n", ' '],
			  $string);
    $string = trim($string);
    return $string;
}

function mdict_unpack ($path)
{
    exec("mdict -x '$path'");
}

function meta ($string)
{
    $meta = "ئەژماری دێڕەکان\t" .
	    kurdish_numbers(number_format(number_of_lines($string)));
    file_put_contents(meta_file_name, $meta);
}

function dict_list ()
{
    require('../config.php');
    if(! file_exists(DICT_PATH))
	die('func: dict_list, `DICT_PATH` not found.');
    $d = opendir(DICT_PATH);
    
    $dicts = [];    
    while(false !== ($o = readdir($d)))
    {
	if(in_array($o, ['.','..']))
	    continue;
	if(is_dir($o))
	    $dicts[] = $o;
    }
    return $dicts;
}

function kurdish_numbers ($input)
{
    return str_replace(['1','2','3','4','5','6','7','8','9','0'],
		       ['١','٢','٣','٤','٥','٦','٧','٨','٩','٠'],
		       $input);
}
?>
