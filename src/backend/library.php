<?php
@include_once("../../config.php");

$extras = ["&#34;", "&#39;", "&laquo;", "&raquo;", "&rsaquo;",
	   "&lsaquo;", "&bull;", "&nbsp;", "?", "!", "#", "&",
	   "*", "(", ")", "-", "+", "=", "_", "[", "]", "{",
	   "}","<", ">", "\\", "/", "|", "'", "\"", ";", ":", ",",
	   ".", "~", "`", "؟", "،", "»", "«", "ـ", "؛", "›", "‹", "•"];
$ar_signs =["ِ", "ُ", "ٓ", "ٰ", "ْ", "ٌ", "ٍ", "ً", "ّ", "َ"];
$replace = [
	"from" => [
		"ه‌", "ك", "ي", "ھ",
		"ض", "ص", "ط", "ظ", "ڕ",
		"ڵ", "ذ", "ث", "ة", "أ", "آ",
		"ڤ", "ى", "ھ", "ۍ", "ې", "ۊ",
		
		"ق", "و", "ە", "ر", "ت", "ی", "ێ",
		"ئ", "ء", "ح", "ع", "ۆ", "ؤ", "پ",
		"ث", "ا", "آ", "س", "ش", "د", "ذ",
		"ف", "إ", "گ", "غ", "ه", "ژ", "أ",
		"ک", "ل", "ڵ", "ز", "ض", "خ", "ص",
		"ج", "چ", "ڤ", "ظ", "ب", "ن", "م",
	],
	"to" => [
		"ە", "ک", "ی", "ه",
		"ز", "س", "ت", "ز", "ر",
		"ل", "ز", "س", "ت", "ە", "ا",
		"و", "ی", "ه", "ی", "ی", "و",

		"q", "w", "e", "r", "t", "y", "Y",
		"u", "U", "i", "I", "o", "O", "p",
		"P", "a", "A", "s", "S", "d", "D",
		"f", "F", "g", "G", "h", "j", "J",
		"k", "l", "L", "z", "Z", "x", "X",
		"c", "C", "v", "V", "b", "n", "m",
	],
];

$dicts_list = dict_list();
$dicts_fp = [];
foreach($dicts_list as $dict_name) {
	$dicts_fp[$dict_name] = fopen(dict_path($dict_name), "r");
}

/* Functions */
function dict_path($dict_name) {
	return DICT_PATH . "/{$dict_name}/{$dict_name}.txt";
}
function dict_list() {
	$dicts = [];
	
	$d = opendir(DICT_PATH);
	while(($o = readdir($d)) !== false) {
		if(in_array($o, [".", ".."]))
			continue;
		if(is_dir(DICT_PATH . "/$o"))
			$dicts[] = $o;
	}
	closedir($d);
	
	return $dicts;
}
function get_from_user($request) {
	return @strtolower(trim(filter_var($request,
					   FILTER_SANITIZE_STRING)));
}
function lookup($q, $dicts_name, $limit) {
	if(!($q and $dicts_name))
		return NULL;

	$q_len = mb_strlen($q);
	$dict_list = dict_list();
	$results = array_fill(0, 100, []);

	foreach($dicts_name as $dict_name) {
		if(!in_array($dict_name, $dict_list))
			continue;
		
		$search_path = dict_path($dict_name) . "_search";
		$s = fopen($search_path, "r");
		
		while(!feof($s)) {
			if(!$limit)
				break;
			$o = explode("\t", fgets($s));
			if($o[0] > $q_len) {
				$hs = $o[1];
				$ndl = $q;
			} else {
				$hs = $q;
				$ndl = $o[1];
			}

			if(strpos($hs, $ndl) !== FALSE) {
				$rank = abs($o[0] - $q_len);
				$results[$rank][] = [
					$dict_name,
					trim($o[2])
				];
				if(!$rank)
					$limit--;
			}
		}
		fclose($s);
	}
	
	return $results;
}
function sanitize_string($str) {
	global $extras, $ar_signs, $replace;
	$str = strtolower($str);
	$str = str_replace($extras, "", $str);
	$str = str_replace($ar_signs, "", $str);
	$str = str_replace($replace["from"], $replace["to"], $str);
	$str = str_replace("‌", "", $str);
	$str = preg_replace("/\s+/u", "", $str);
	foreach($replace["to"] as $token) {
		$str = preg_replace("/$token{2,}/ui", $token, $str);
	}
	return $str;
}
function dict($dict_name) {
	$dict = [];
	$dict_path = dict_path($dict_name);
	$f = fopen($dict_path, "r");
	while(!feof($f))
		$dict[] = explode("\t", trim(fgets($f)));
	fclose($f);
	return $dict;
}
function kurdish_numbers($str) {
	return str_replace(
		["1","2","3","4","5","6","7","8","9","0"],
		["١","٢","٣","٤","٥","٦","٧","٨","٩","٠"],
		$str);
}
function slice_results($results, $limit) {
	$new_results = [];
	foreach($results as $arr) {
		foreach($arr as $o) {
			if(!$limit--)
				break 2;
			$new_results[] = fetch_word($o[0], $o[1]);
		}
	}
	return $new_results;
}
function fetch_word($dict_name, $offset) {
	global $dicts_fp;
	$f = $dicts_fp[$dict_name];
	fseek($f, $offset);
	$line = explode("\t", trim(fgets($f)));
	return [$dict_name, $line[0], $line[1]];
}
?>
