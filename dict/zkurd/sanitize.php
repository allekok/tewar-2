<?php
require('../library.php');

const dict_name = 'zkurd.txt';
const dict_path = dict_name;
const dict_output_name = dict_name;
const dict_output_path = dict_output_name;
const output = 'zkurd.txt';

if(! file_exists(dict_path))
    exec('python download.py');

$input = file_get_contents(dict_output_path);
$input = sanitize_string($input);

save(output, $input);
?>
