<?php

$host = 'ec2-54-170-109-0.eu-west-1.compute.amazonaws.com';
$dbname = 'ded5ilv847si7p';
$password = 'ab24bc7dd1cc89dc59a86014b3052f6eb1f3ec98af61244cccc4b2de76ed318d';
$user = 'avvtbqcnoiqpbg';
$port = '5432';

$databaseURL = getenv('DATABASE_URL');

$db = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
//$db = pg_connect($databaseURL);
