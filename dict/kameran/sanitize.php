<?php
require("../library.php");

const dict_name = "Kurmancî_Kurmancî_Ferhenga_Kameran.mdx";
const dict_path = "dict/Ferhenga Kameran/" . dict_name;
const dict_output_name = dict_name . ".txt";
const dict_output_path = dict_output_name;
const output = "kameran.txt";

mdict_unpack(dict_path);

$input = file_get_contents(dict_output_path);
$input = preg_replace(['/\r\n<link .*<div class="def">\s*/ui',
		       "/<\/div>\r\n<\/>/ui"],
		      ["\t", ""],
		      $input);
$input = filter_var($input, FILTER_SANITIZE_STRING);
$input = sanitize_string($input);

save(output, $input);

exec("rm Kurmancî_*");
?>
