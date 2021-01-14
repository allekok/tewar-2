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
		if($dict == "kawe")
			$line[1] = str_replace("،جدول نشانەهای اختصاری",
					       "", $line[1]);
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
		],
		"from" => [
			"ه‌","ك","ي","ھ",
			"ض","ص","ط","ظ","ڕ",
			"ڵ","ذ","ث","ة","أ","آ",
			"ڤ","ى","ھ","ۍ","ې","ۊ",
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
