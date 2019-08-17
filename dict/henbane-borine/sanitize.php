<?php
require('../library.php');

const dict_name = 'Henbane Borine.mdx';
const dict_path = 'dict/HenbaneBorine/' . dict_name;
const dict_output_name = dict_name . '.txt';
const dict_output_path = dict_output_name;
const output = 'henbane-borine.txt';

mdict_unpack(dict_path);

$input = file_get_contents(dict_output_path);

$input = preg_replace(['/\r\n<div .* face="Tahoma،  Arial">/ui',
		       '/\r\n<div .* face="Tahoma, Arial">/ui',
		       '/<\/font><\/div>\r\n<\/>/ui'],
		      ["\t","\t",''], $input);
$input = sanitize_string($input);

file_put_contents(output, $input);
meta($input);

exec('rm Henbane\ *');
?>
