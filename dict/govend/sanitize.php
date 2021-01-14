<?php
require('../library.php');

const dict_name = 'govend_u_zinar.mdx';
const dict_path = 'dict/Govend_u_Zinar Dictionary/' . dict_name;
const dict_output_name = dict_name . '.txt';
const dict_output_path = dict_output_name;
const output = 'govend.txt';

mdict_unpack(dict_path);

$input = file_get_contents(dict_output_path);

$input = preg_replace(["/\r\n@@@LINK=/",
		       "/\n<\/>/",
		       "/\r\n<link rel=\"stylesheet\" href=\"gz\.css\"><br><div class=\"dir\"><div class=\"entry\">([^<]+)<\/div><div class=\"pos\">([^<]*)<\/div><div class=\"meaning\">([^<]*)<\/div><\/div><br>/"],
		      ["\t",
		       "",
		       "\t$2 - $3"], $input);
$input = sanitize_string($input);

save(output, $input);

exec('rm govend_u*');
?>
