<?php
require("library.php");
$dicts = dict_list();
foreach($dicts as $dict) {
	$string = "";
	$dict_path = "{$dict}/{$dict}.txt";
	$f = fopen($dict_path, "r");
	if(!$f) continue;
	while(!feof($f)) {
		$line_pos = ftell($f);
		$line = explode("\t", trim(fgets($f)));
		$sss = search_sanitize_string($line[0]);
		$sss_len = mb_strlen($sss);
		$string .= "$sss_len\t$sss\t$line_pos\n";
	}
	fclose($f);
	file_put_contents("{$dict_path}_search", trim($string));
	echo "'{$dict_path}' done.\n";
}
function search_sanitize_string ($string) {
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
?>
