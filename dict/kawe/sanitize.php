<?php
require('../library.php');

const dict_name = 'Farhangi Kawa.mdx';
const dict_path = 'dict/Ferheng Kawe/' . dict_name;
const dict_output_name = dict_name . '.txt';
const dict_output_path = dict_output_name;
const output = 'kawe.txt';

mdict_unpack(dict_path);

$input = file_get_contents(dict_output_path);

$input = preg_replace(['/\r\n<link .*<div class="def">/ui',
		       '/\r\n<\/div><br><br><div class="abbr"><a href="entry:\/\/جدول نشانەهای اختصاری">جدول نشانەهای اختصاری<\/a><\/div><br>/ui',
		       '/\r\n<\/>/ui'],
		      ["\t",'',''], $input);
$input = filter_var($input, FILTER_SANITIZE_STRING);
$input = sanitize_string($input);

file_put_contents(output, $input);
meta($input);

exec('rm Farhangi\ *');
?>
