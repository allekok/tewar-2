<?php
require('../library.php');

const dict_name = 'Xal Dictionary.mdx';
const dict_path = 'dict/Xal Dictionary/' . dict_name;
const dict_output_name = dict_name . '.txt';
const dict_output_path = dict_output_name;
const output = 'xal.txt';

mdict_unpack(dict_path);

$input = file_get_contents(dict_output_path);
$input = preg_replace(['/\r\n<link rel="stylesheet" type="text\/css" href="XalD.css" \/><div class="entry">.*<\/div><br><div class="def">/ui',
		       '/<\/div>\r\n<\/>/ui'],
		      ["\t",''], $input);
$input = sanitize_string($input);

save(output, $input);

exec('rm Xal\ *');
?>
