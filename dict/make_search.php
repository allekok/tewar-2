<?php
require("library.php");
$dicts = dict_list();
foreach($dicts as $dict) {
    $string = "";
    $dict_path = "{$dict}/{$dict}.txt";
    $f = fopen($dict_path, "r");
    if(!$f) continue;
    while(!feof($f)) {
	$line = explode("\t", trim(fgets($f)));
	$string .= search_sanitize_string($line[0]) .
		   "\t{$line[0]}\t{$line[1]}\n";
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
	       ".", "~", "`", "؟", "،", "»", "«","ـ","؛","›","‹","•","‌"];
    $ar_signs =["ِ", "ُ", "ٓ", "ٰ", "ْ", "ٌ", "ٍ", "ً", "ّ", "َ"];
    $replace = [
	"from"=>["ڕ","ڵ","وو","ط","ض","ذ","ظ"],
	"to"=>["ر","ل","و","ت","ز","ز","ز"],
    ];
    $string = str_replace($extras, "", $string);
    $string = str_replace($ar_sign, "", $string);
    $string = str_replace($replace["from"], $replace["to"], $string);
    return $string;
}
?>
