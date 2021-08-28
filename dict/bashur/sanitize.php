<?php
require("../library.php");

const dict_name = "Bashur Dictionary.mdx";
const dict_path = "dict/Bashur Dictionary/" . dict_name;
const dict_output_name = dict_name . ".txt";
const dict_output_path = dict_output_name;
const output = "bashur.txt";

mdict_unpack(dict_path);

$input = file_get_contents(dict_output_path);
$input = preg_replace(["/\r\n@@@LINK=/ui",
		       "/\r\n<\/>/ui",
		       '/\r\n<div * color="#FF8C00" face="Tahoma, Arial">/ui',
		       '/<\/font><font * color="#242120" face="Tahoma, Arial">/ui',
		       "/<br><br>*<b>/ui",
		       "/<\/b>*<br>/ui",
		       "/<\/font><\/div>/ui",
		       "/<div .*><b>/ui",
		       "/<\/b><\/font><br>/ui"],
		      ["\t", "", "", "", "", "", "", "\t", ":"],
		      $input);
$input = str_replace("\r\n\tفارسی:",
		     "\tفارسی:",
		     $input);
$input = filter_var($input, FILTER_SANITIZE_STRING);
$input = sanitize_string($input);

save(output, $input);

exec("rm Bashur\ *");
?>
