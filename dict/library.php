<?php
const meta_file_name = "meta.txt";

function number_of_lines($str) {
	$line_num = 1;
	for($i = 0; @$str[$i] !== ""; $i++)
		if($str[$i] == "\n")
			$line_num++;
	return $line_num;
}
function sanitize_string($str) {
	$str = str_replace(["\r\n", " ",], ["\n", " "], $str);
	return trim($str);
}
function mdict_unpack($path) {
	exec("mdict -x '$path'");
}
function meta($str, $dict_path) {
	$meta = "ئەژماری دێڕەکان\t" .
		ck_nums(number_format(number_of_lines($str))) .
		"\nگەورەیی فەرهەنگ\t" .
		ck_nums(number_format(filesize($dict_path)/1e6, 1)) .
		"MB";
	file_put_contents(meta_file_name, $meta);
}
function dict_list() {
	require("../config.php");
	$dicts = [];
	$d = opendir(DICT_PATH);
	while(($o = readdir($d)) !== false) {
		if(in_array($o, [".", ".."]))
			continue;
		if(is_dir($o))
			$dicts[] = $o;
	}
	return $dicts;
}
function ck_nums($input) {
	return str_replace(["1","2","3","4","5","6","7","8","9","0"],
			   ["١","٢","٣","٤","٥","٦","٧","٨","٩","٠"],
			   $input);
}
function save($where, $str) {
	file_put_contents($where, $str);
	meta($str, $where);
}
?>
