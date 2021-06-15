<?php
@include_once('../../config.php');

$extras = ["&#34;","&#39;","&laquo;","&raquo;","&rsaquo;",
	   "&lsaquo;","&bull;","&nbsp;","?", "!", "#", "&",
	   "*", "(", ")", "-","+", "=", "_","[", "]", "{",
	   "}","<",">","\\","/", "|", "'","\"", ";", ":", ",",
	   ".", "~", "`", "؟", "،", "»", "«","ـ","؛","›","‹","•"];
$ar_signs =["ِ", "ُ", "ٓ", "ٰ", "ْ", "ٌ", "ٍ", "ً", "ّ", "َ"];
$replace = [
	"to" => [
		"ە","ک","ی","ه",
		"ز","س","ت","ز","ر",
		"ل","ز","س","ت","ە","ا",
		"و","ی","ه","ی","ی","و",

		"q","w","e","r","t","y","Y",
		"u","U","i","I","o","O","p",
		"P","a","A","s","S","d","D",
		"f","F","g","G","h","j","J",
		"k","l","L","z","Z","x","X",
		"c","C","v","V","b","n","m",
	],
	"from" => [
		"ه‌","ك","ي","ھ",
		"ض","ص","ط","ظ","ڕ",
		"ڵ","ذ","ث","ة","أ","آ",
		"ڤ","ى","ھ","ۍ","ې","ۊ",
		
		"ق","و","ە","ر","ت","ی","ێ",
		"ئ","ء","ح","ع","ۆ","ؤ","پ",
		"ث","ا","آ","س","ش","د","ذ",
		"ف","إ","گ","غ","ه","ژ","أ",
		"ک","ل","ڵ","ز","ض","خ","ص",
		"ج","چ","ڤ","ظ","ب","ن","م",
	],

];

$dicts_list = dict_list();
$dicts_fp = [];
foreach($dicts_list as $dict_name) {
	$dicts_fp[$dict_name] = fopen(dict_path($dict_name), "r");
}

/* Functions */
function dict_path ($dict_name)
{
	return DICT_PATH . "/$dict_name/{$dict_name}.txt";
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
		
		$search_path = dict_path($dict_name) . "_search";
		$s = fopen($search_path, "r");
		
		while(! feof($s)) {
			if($limit == 0) break;
			$o = explode("\t", fgets($s));
			if($o[0] > $q_len) {
				$hs = $o[1]; $ndl = $q;
			}
			else {
				$hs = $q; $ndl = $o[1];
			}

			if(strpos($hs, $ndl) !== FALSE) {
				$rank = abs($o[0] - $q_len);
				if($rank == 0) $limit--;
				$results[$rank][] = [$dict_name, trim($o[2])];
			}
		}
		fclose($s);
	}
	
	return $results;
}

function sanitize_string ($string)
{
	global $extras, $ar_signs, $replace;
	$string = strtolower($string);
	$string = str_replace($extras, "", $string);
	$string = str_replace($ar_signs, "", $string);
	$string = str_replace($replace["from"], $replace["to"], $string);
	$string = str_replace("‌", "", $string);
	$string = preg_replace("/\s+/u", "", $string);
	foreach($replace["to"] as $tl)
	if($tl)	$string = preg_replace("/$tl{2,}/ui", $tl, $string);
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
			$new_results[] = fetch_word($o[0], $o[1]);
		}
	}
	return $new_results;
}

function fetch_word ($dict_name, $offset) {
	global $dicts_fp;
	$f = $dicts_fp[$dict_name];
	fseek($f, $offset);
	$line = explode("\t", trim(fgets($f)));
	return [$dict_name, $line[0], $line[1]];
}
?>
