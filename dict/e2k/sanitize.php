<?php
require('../library.php');

const dict_name = 'E2K-Thesaurus.mdx';
const dict_path = 'dict/E2KThesaurus/' . dict_name;
const dict_output_name = dict_name . '.txt';
const dict_output_path = dict_output_name;
const output = 'e2k.txt';

mdict_unpack(dict_path);

$input = file_get_contents(dict_output_path);


$input = preg_replace(['/\r\n<link .*"Phonetic">\s*/ui',
		       '/\r\n<link .*"pos">/ui',
		       '/<\/span>.*"pop">/ui',
		       '/<\/span>.*"def">/ui',
		       '/ <\/span>.*\r\n<\/>/ui'],
		      ["\t","\t",', ',', ',''], $input);
$input = sanitize_string($input);

file_put_contents(output, $input);
meta($input);

exec('rm E2K-*');
?>
