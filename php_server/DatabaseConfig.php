<?php
if (preg_match('/localhost/si', $_SERVER['SERVER_NAME']))
{
	$aDbConfig = array(
	"host" => "localhost",
	"name" => "nesstream",
	"user" => "root",
	"pass" => "",
	);
}
else
{
	$aDbConfig = array(
	"host" => "localhost",
	"name" => "adoptebringer",
	"user" => "root",
	"pass" => "exFhtADT8VG2aG6n",
	);
}

$oPDO = new PDO(
		'mysql:host=' . $aDbConfig['host'] . ';dbname=' . $aDbConfig['name'] ,
		$aDbConfig['user'],
		$aDbConfig['pass']
);
$oPDO->exec("SET CHARACTER SET utf8");