<?php
require('../../config.php');

const _dicts = [
    'bashur',
    //'dictio',
    'e2k',
    'henbane-borine',
    'kameran',
    'kawe',
    'xal',
    'zkurd',
];

function mysql_connection ($db = MYSQL_DB)
{
    $mysql = mysqli_connect(MYSQL_HOST,
			    MYSQL_USER,
			    MYSQL_PASS,
			    $db);
    mysqli_set_charset($mysql, 'utf8');
    
    return $mysql;
}

function dict_path ($dict_name)
{
    return DICT_PATH . "/$dict_name/$dict_name.txt";
}

function dict_parse ($dict_name)
{
    $entries = [];
    $dict_path = dict_path($dict_name);
    if(! file_exists($dict_path))
	die("func: dict_parse, dict_path not found.\n");
    $f = fopen($dict_path, 'r');
    while(!feof($f))
    {
	$line = fgets($f);
	$entry = explode("\t", $line);
	if(@trim($entry[0]) and @trim($entry[1]))
	{
	    $entries[] = [
		'word' => trim($entry[0]),
		'meaning' => trim($entry[1]),
	    ];
	}
    }
    fclose($f);
    return $entries;
}

function dict_to_sql ($dict_name)
{
    $entries = dict_parse($dict_name);
    $mysql = mysql_connection();
    $table = mysql_table($dict_name);
    foreach($entries as $o)
    {
	$word = addslashes($o['word']);
	$meaning = addslashes($o['meaning']);
	$q = "INSERT INTO `$table` (word, meaning) 
VALUES('$word', '$meaning')";
	if(! mysqli_query($mysql, $q))
	    die("func: dict_to_sql, Query error: `$q`.\n");
    }
}

function mysql_table ($name)
{
    $mysql = mysql_connection();
    // Check if: table existed: TRUNCATE
    $q = "ALTER TABLE `$name`";
    if(mysqli_query($mysql, $q))
    {
	$q = "TRUNCATE TABLE `$name`";
	if(! mysqli_query($mysql, $q))
	    die("func: mysql_table, Query error: `$q`.\n");
    }
    // Else: create a new table.
    else
    {
	$q = "CREATE TABLE `$name` ( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT , `word` VARCHAR(1000) NOT NULL , `meaning` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;";
	if(! mysqli_query($mysql, $q))
	    die("func: mysql_table, Query error: `$q`.\n");
    }
    // return table name.
    return $name;
}

function dict_list ()
{
    $dicts = [];
    if(! file_exists(DICT_PATH))
	die('func: dict_list, `DICT_PATH` not found.');

    $d = opendir(DICT_PATH);
    while(false !== ($o = readdir($d)))
    {
	if(in_array($o, ['.','..']))
	    continue;
	if(is_dir(DICT_PATH . "/$o"))
	    $dicts[] = $o;
    }
    closedir($d);
    return $dicts;
}

function get_from_user ($request)
{
    return @ strtolower (trim (filter_var (
	$request,FILTER_SANITIZE_STRING )));
}

function lookup ($q, $dict_name)
{
    if(! ($q and $dict_name) )
	return NULL;

    $mysql = mysql_connection();
    $query = "ALTER TABLE `$dict_name`";
    if(! mysqli_query($mysql, $query))
	return NULL;
    
    $query = "SELECT meaning FROM `$dict_name` WHERE word='$q'";
    $mysql_result = mysqli_query($mysql, $query);
    if(! $mysql_result)
	return false;
    $result = [];
    while($_ = mysqli_fetch_assoc($mysql_result))
	$result[] = $_['meaning'];
    
    return $result;
}
?>
